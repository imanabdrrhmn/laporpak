<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GisController extends Controller
{
    public function getPopulationStats(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:100|max:10000',
            // 'year' => 'required|integer|in:2020,2025,2030', // Validasi tahun dinonaktifkan
        ]);

        try {
            $pointWkt = "POINT(" . $validated['longitude'] . " " . $validated['latitude'] . ")";

            $query = "
                SELECT
                    SUM(population) as total_population
                FROM
                    population_points
                WHERE
                    ST_Distance_Sphere(
                        geom,
                        ST_PointFromText(?, 4326)
                    ) <= ?
            ";

            $stats = DB::select($query, [
                $pointWkt,
                $validated['radius']
            ]);
            
            $total_population = $stats[0]->total_population ?? 0;
            
            $radius_km = $validated['radius'] / 1000.0;
            $coverage_area_km2 = pi() * ($radius_km ** 2);
            $density_per_km2 = ($coverage_area_km2 > 0) ? $total_population / $coverage_area_km2 : 0;

            return response()->json([
                'total_population' => (int)$total_population,
                'average_income' => 0, 
                'density_per_km2' => round($density_per_km2),
                'coverage_area' => round($coverage_area_km2, 2),
                'data_year' => 2030 
            ]);

        } catch (\Exception $e) {
            Log::error('MySQL Spatial query failed: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data dari database.'], 500);
        }
    }
}
