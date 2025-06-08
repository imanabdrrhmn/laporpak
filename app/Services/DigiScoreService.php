<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiScoreService
{
    protected string $appKey;
    protected string $secret;
    protected array $apiEndpoints;

    public function __construct()
    {
        $this->appKey = env('DIGISCORE_APP_KEY');
        $this->secret = env('DIGISCORE_SECRET');

        $this->apiEndpoints = [
            'identity_check'         => env('DIGISCORE_IDENTITY_CHECK_FORMAL_API_URL'),
            'npwp_check'             => env('DIGISCORE_NPWP_CHECK_FORMAL_API_URL'),
            'blacklist_check_v1'     => env('DIGISCORE_BLACKLIST_CHECK_V1_FORMAL_API_URL'),
            'multi_apply_check'      => env('DIGISCORE_MULTI_APPLY_CHECK_FORMAL_API_URL'),
            'multi_loan_feature_set' => env('DIGISCORE_MULTI_LOAN_FEATURE_SET_FORMAL_API_URL'),
            'portrait_feature_set_v21' => env('DIGISCORE_PORTRAIT_FEATURE_SET_V21_FORMAL_API_URL'),
            'sms_feature_set_v21'    => env('DIGISCORE_SMS_FEATURE_SET_V21_FORMAL_API_URL'),
            'telco_feature_set_v2'   => env('DIGISCORE_TELCO_FEATURE_SET_V2_FORMAL_API_URL'),
            // ... tambahkan sisa 3 fitur lainnya di sini jika ada
        ];
    }

    /**
     * Generate MD5 token for DigiScore API.
     *
     * @param array $params Parameter yang akan digunakan untuk MD5 (termasuk secret, jika ada)
     * @return string
     */
    protected function generateToken(array $params): string
    {
        // Secret harus selalu ditambahkan untuk generasi token
        $params['secret'] = $this->secret;

        // Sort parameters by key
        ksort($params);

        // Build the string for MD5 hash
        $str = '';
        foreach ($params as $key => $value) {
            $str .= "$key=$value&";
        }
        $str = rtrim($str, '&'); // Remove trailing '&'

        return md5($str);
    }

    /**
     * Sanitasi nomor telepon sesuai format API DigiScore (hapus prefix negara, mulai dengan '8').
     *
     * @param string $phone
     * @return string
     */
    protected function sanitizePhoneNumber(string $phone): string
    {
        // Hapus '+62' atau '0' di awal
        $phone = preg_replace('/^(\+62|0)/', '', $phone);
        // Pastikan nomor telepon dimulai dengan '8' jika belum
        // Ini adalah asumsi berdasarkan dokumentasi "start with '8'".
        // Jika ada nomor yang valid tapi tidak dimulai dengan '8' (misal: 7xxxx), perlu konfirmasi API.
        if (substr($phone, 0, 1) !== '8' && strlen($phone) > 0) {
            // Ini bisa jadi error atau perlu penanganan khusus
            // Untuk sementara, kita biarkan saja jika tidak dimulai 8, API mungkin akan reject.
            // Atau Anda bisa lempar exception di sini.
             Log::warning("Phone number '{$phone}' does not start with '8' after sanitation. API may reject.");
        }
        return $phone;
    }

    /**
     * Generic method to call DigiScore API for any feature.
     *
     * @param string $featureType Kunci dari $this->apiEndpoints (contoh: 'identity_check')
     * @param array $requestPayload Data yang akan dikirim ke API
     * @param array $tokenParams Parameter yang digunakan untuk generate token (biasanya subset dari $requestPayload + appKey + secret)
     * @return array
     * @throws \Exception
     */
    public function callApi(string $featureType, array $requestPayload, array $tokenParams): array
    {
        if (!isset($this->apiEndpoints[$featureType])) {
            throw new \InvalidArgumentException("API endpoint for feature '{$featureType}' not configured.");
        }

        $apiUrl = $this->apiEndpoints[$featureType];
        $token = $this->generateToken($tokenParams);

        // appKey dan token selalu disertakan dalam payload yang dikirim ke API
        $finalPayload = array_merge(['appKey' => $this->appKey, 'token' => $token], $requestPayload);

        try {
            $response = Http::post($apiUrl, $finalPayload);
            return $response->json();
        } catch (\Exception $e) {
            Log::error("DigiScore API Error ({$featureType}): " . $e->getMessage(), [
                'url' => $apiUrl,
                'payload' => $finalPayload,
                'error_trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception("Failed to connect to DigiScore API for feature '{$featureType}'.");
        }
    }

    // --- Method Spesifik per Fitur ---

    public function getIdentityCheck(string $name, string $idNumber): array
    {
        $requestPayload = ['name' => $name, 'idNumber' => $idNumber];
        // Sesuai dokumentasi, token string: appKey, idNumber, name, phone, secret
        // Tapi di API ini phone tidak diminta. Kita ikuti contoh token description yang diberikan:
        // "appKey=test_key&idNumber=idNumber&name=name&phone=phone&secret=20200101"
        // Ini sedikit ambigu. Jika 'phone' tidak relevan untuk ID Check, mungkin tokenparams-nya hanya 'appKey', 'idNumber', 'name'.
        // Jika API token generation-nya sangat ketat, kita mungkin harus menambahkan 'phone' kosong.
        // Untuk amannya, kita akan menggunakan 'appKey', 'idNumber', 'name' sebagai token params.
        $tokenParams = ['appKey' => $this->appKey, 'name' => $name, 'idNumber' => $idNumber];
        return $this->callApi('identity_check', $requestPayload, $tokenParams);
    }

    public function getNpwpCheck(string $idNumber): array
    {
        $requestPayload = ['idNumber' => $idNumber];
        // Sesuai dokumentasi, token string: appKey, idNumber, name, phone, secret
        // API ini hanya butuh idNumber. Kita asumsikan token params sama dengan request payload.
        // Ambigu seperti Identity Check. Ikuti pola yang sama.
        $tokenParams = ['appKey' => $this->appKey, 'idNumber' => $idNumber];
        return $this->callApi('npwp_check', $requestPayload, $tokenParams);
    }

    public function getBlacklistCheckV1(string $name, string $idNumber, string $phone): array
    {
        $sanitizedPhone = $this->sanitizePhoneNumber($phone);
        $requestPayload = ['name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        // Sesuai dokumentasi: "appKey=test_key&idNumber=idNumber&name=name&phone=phone&secret=20200101"
        $tokenParams = ['appKey' => $this->appKey, 'name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        return $this->callApi('blacklist_check_v1', $requestPayload, $tokenParams);
    }

    public function getMultiApplyCheck(string $name, string $idNumber, string $phone): array
    {
        $sanitizedPhone = $this->sanitizePhoneNumber($phone);
        $requestPayload = ['name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        // Sesuai dokumentasi: "appKey=test_key&idNumber=idNumber&name=name&phone=phone&secret=20200101"
        $tokenParams = ['appKey' => $this->appKey, 'name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        return $this->callApi('multi_apply_check', $requestPayload, $tokenParams);
    }

    public function getMultiLoanFeatureSet(string $name, string $idNumber, string $phone): array
    {
        $sanitizedPhone = $this->sanitizePhoneNumber($phone);
        $requestPayload = ['name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        // Sesuai dokumentasi: "appKey=test_key&idNumber=idNumber&name=name&phone=phone&secret=20200101"
        $tokenParams = ['appKey' => $this->appKey, 'name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        return $this->callApi('multi_loan_feature_set', $requestPayload, $tokenParams);
    }

    public function getPortraitFeatureSetV21(string $name, string $idNumber, string $phone): array
    {
        $sanitizedPhone = $this->sanitizePhoneNumber($phone);
        $requestPayload = ['name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        // Sesuai dokumentasi: "appKey=test_key&idNumber=idNumber&name=name&phone=phone&secret=20200101"
        $tokenParams = ['appKey' => $this->appKey, 'name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        return $this->callApi('portrait_feature_set_v21', $requestPayload, $tokenParams);
    }

    public function getSmsFeatureSetV21(string $name, string $idNumber, string $phone): array
    {
        $sanitizedPhone = $this->sanitizePhoneNumber($phone);
        $requestPayload = ['name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        // Sesuai dokumentasi: "appKey=test_key&idNumber=idNumber&name=name&phone=phone&secret=20200101"
        $tokenParams = ['appKey' => $this->appKey, 'name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        return $this->callApi('sms_feature_set_v21', $requestPayload, $tokenParams);
    }

    public function getTelcoFeatureSetV2(string $name, string $idNumber, string $phone): array
    {
        $sanitizedPhone = $this->sanitizePhoneNumber($phone);
        $requestPayload = ['name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        // Sesuai dokumentasi: "appKey=test_key&idNumber=idNumber&name=name&phone=phone&secret=20200101"
        $tokenParams = ['appKey' => $this->appKey, 'name' => $name, 'idNumber' => $idNumber, 'phone' => $sanitizedPhone];
        return $this->callApi('telco_feature_set_v2', $requestPayload, $tokenParams);
    }

    // ... (Tambahkan method untuk sisa 3 fitur lainnya di sini jika ada)
}