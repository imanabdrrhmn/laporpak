<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Verifikasi;
use App\Services\DigiScoreService;

class VerificationController extends Controller
{
    const FEATURE_COSTS = [
        'identity_check'         => 5000, // ini salah
        'npwp_check'             => 7200,
        'blacklist_check_v1'     => 45000,
        'multi_apply_check'      => 3600,
        'multi_loan_feature_set' => 3600, 
        'portrait_feature_set_v21' => 5600, 
        'sms_feature_set_v21'    => 4800, 
        'telco_feature_set_v2'   => 3600, 
        // ... tambahkan biaya untuk sisa 3 fitur lainnya di sini
    ];

    protected DigiScoreService $digiScoreService;

    public function __construct(DigiScoreService $digiScoreService)
    {
        $this->digiScoreService = $digiScoreService;
    }

    /**
     * Handles generic verification requests for any feature.
     *
     * @param Request $request
     * @param string $featureType Identifier for the feature (e.g., 'identity_check', 'npwp_check')
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleVerification(Request $request, string $featureType)
    {
        $user = Auth::user();
        $cost = self::FEATURE_COSTS[$featureType] ?? 0;

        $rules = [];
        $sanitizedPhone = null; // Akan digunakan untuk fitur yang butuh phone number

        $inputDataForVerifikasiTable = [
            'name' => $request->input('name'),
            'id_number' => $request->input('id_number'),
            'phone_number' => $request->input('phone'),
        ];

        try {
            switch ($featureType) {
                case 'identity_check':
                    $rules = ['name' => 'required|string|max:255', 'id_number' => 'required|string|regex:/^[0-9]{16}$/'];
                    $request->validate($rules);
                    $apiResponse = $this->digiScoreService->getIdentityCheck($request->name, $request->id_number);
                    break;

                case 'npwp_check':
                    $rules = ['id_number' => 'required|string|regex:/^[0-9]{16}$/'];
                    $request->validate($rules);
                    $apiResponse = $this->digiScoreService->getNpwpCheck($request->id_number);
                    break;

                case 'blacklist_check_v1':
                case 'multi_apply_check':
                case 'multi_loan_feature_set':
                case 'portrait_feature_set_v21':
                case 'sms_feature_set_v21':
                case 'telco_feature_set_v2':
                    // Validasi umum untuk fitur yang butuh name, idNumber, phone
                    $rules = [
                        'name' => 'required|string|max:255',
                        'id_number' => 'required|string|regex:/^[0-9]{16}$/',
                        'phone' => 'required|string|regex:/^(\+62|0)?[0-9]{8,15}$/', // Regex lebih longgar untuk input awal
                    ];
                    $request->validate($rules);
                    // Sanitasi telepon hanya di service, di sini kirim nilai asli
                    $sanitizedPhone = $this->digiScoreService->sanitizePhoneNumber($request->phone);

                    // Panggil method service yang sesuai
                    if ($featureType === 'blacklist_check_v1') {
                        $apiResponse = $this->digiScoreService->getBlacklistCheckV1($request->name, $request->id_number, $request->phone);
                    } elseif ($featureType === 'multi_apply_check') {
                        $apiResponse = $this->digiScoreService->getMultiApplyCheck($request->name, $request->id_number, $request->phone);
                    } elseif ($featureType === 'multi_loan_feature_set') {
                        $apiResponse = $this->digiScoreService->getMultiLoanFeatureSet($request->name, $request->id_number, $request->phone);
                    } elseif ($featureType === 'portrait_feature_set_v21') {
                        $apiResponse = $this->digiScoreService->getPortraitFeatureSetV21($request->name, $request->id_number, $request->phone);
                    } elseif ($featureType === 'sms_feature_set_v21') {
                        $apiResponse = $this->digiScoreService->getSmsFeatureSetV21($request->name, $request->id_number, $request->phone);
                    } elseif ($featureType === 'telco_feature_set_v2') {
                        $apiResponse = $this->digiScoreService->getTelcoFeatureSetV2($request->name, $request->id_number, $request->phone);
                    }
                    // ... tambahkan else if untuk sisa 3 fitur lainnya yang menggunakan pola ini
                    break;

                default:
                    return response()->json(['code' => 400, 'msg' => 'Invalid feature type.', 'data' => null, 'isFee' => 0], 400);
            }

            // 1. Periksa Saldo Pengguna
            if ($user->balance < $cost) {
                return response()->json([
                    'code' => 403, 'msg' => 'Insufficient balance.', 'data' => null, 'isFee' => 0,
                ], 403);
            }

            // 2. Jalankan Transaksi Database
            return DB::transaction(function () use ($request, $user, $cost, $featureType, $apiResponse, $inputDataForVerifikasiTable) {
                $isPaid = false;
                // Logika pengurangan saldo hanya jika API call sukses dan dikenakan biaya
                if (isset($apiResponse['code']) && $apiResponse['code'] === 200 && isset($apiResponse['isFee']) && $apiResponse['isFee'] === 1) {
                    $user->balance -= $cost;
                    $user->save();
                    $isPaid = true;
                }

                // Siapkan data lengkap untuk disimpan ke tabel 'verifikasis'
                $finalVerifikasiData = array_merge(
                    [
                        'user_id' => $user->id,
                        'type' => $featureType,
                        'request_data' => $request->all(), // Simpan semua input asli dari frontend
                        'api_response' => $apiResponse,
                        'is_paid' => $isPaid,
                    ],
                    $inputDataForVerifikasiTable // Tambahkan kolom-kolom untuk pencarian cepat
                );

                Verifikasi::create($finalVerifikasiData);

                return response()->json($apiResponse);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'code' => 422, 'msg' => 'Validation Error', 'errors' => $e->errors(), 'data' => null, 'isFee' => 0,
            ], 422);
        } catch (\Exception $e) {
            Log::error("Verification Error ({$featureType}): " . $e->getMessage(), [
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'error_trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'code' => 500, 'msg' => 'Internal server error or API connection failed.', 'data' => null, 'isFee' => 0,
            ], 500);
        }
    }
}