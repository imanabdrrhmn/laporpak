<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodeController extends Controller
{
    public function reverseGeocode(Request $request)
    {
        // Validate the request
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
        ]);

        try {
            // Make request to Nominatim API
            $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $request->lat,
                'lon' => $request->lon,
                'zoom' => 18, // Detail level (18 is for street-level)
                'addressdetails' => 1,
            ]);

            // Check if the request was successful
            if ($response->successful()) {
                $data = $response->json();

                // Check if address data is available
                if (isset($data['address'])) {
                    // Format the address
                    $address = $this->formatAddress($data['address']);
                    return response()->json(['address' => $address]);
                }

                return response()->json(['address' => 'Alamat tidak ditemukan'], 404);
            }

            return response()->json(['error' => 'Gagal mengambil data dari Nominatim'], 500);
        } catch (\Exception $e) {
            Log::error('Reverse geocoding error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil alamat'], 500);
        }
    }

    /**
     * Format the address from Nominatim response
     */
    private function formatAddress(array $address): string
    {
        $parts = [];

        // Add relevant address components
        if (!empty($address['road'])) {
            $parts[] = $address['road'];
        }
        if (!empty($address['village'])) {
            $parts[] = $address['village'];
        }
        if (!empty($address['city'])) {
            $parts[] = $address['city'];
        }
        if (!empty($address['state'])) {
            $parts[] = $address['state'];
        }
        if (!empty($address['postcode'])) {
            $parts[] = $address['postcode'];
        }
        if (!empty($address['country'])) {
            $parts[] = $address['country'];
        }

        // Join parts with commas
        return implode(', ', $parts);
    }
}