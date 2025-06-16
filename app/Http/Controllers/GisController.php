<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;

class GisController extends Controller
{
    public function getPopulationStats(Request $request)
    {
        // 1. Validasi input dari frontend
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:100|max:10000',
        ]);

        // 2. Tentukan path ke eksekutor python di venv dan ke script python
        $python_executable = base_path('python_env\Scripts\python.exe'); // Untuk Windows
        // $python_executable = base_path('python_env/bin/python'); // Untuk Linux/macOS
        
        $python_script_path = app_path('Scripts/process_ghsl_direct.py');

        // 3. Tentukan path ke file raster yang sudah di-clip/merge
        $raster_file = 'indonesia_population_100m.tif';
        $file_path_relative_to_app = 'geodata/' . $raster_file;
        
        // --- PERBAIKAN DI SINI ---
        // Menggunakan helper `storage_path()` untuk mendapatkan path absolut yang pasti benar,
        // menghindari masalah konfigurasi `Storage::disk()`.
        $raster_path_absolute = storage_path('app/' . $file_path_relative_to_app);
        
        // Tulis path ini ke log agar kita bisa memeriksanya secara manual
        Log::info('Mencoba memeriksa file di path absolut (metode baru):', ['path' => $raster_path_absolute]);

        // Periksa apakah file raster benar-benar ada di path tersebut
        if (!file_exists($raster_path_absolute)) {
            Log::error('File raster GHSL tidak ditemukan setelah pengecekan `file_exists`.', [
                'path_dicek' => $raster_path_absolute
            ]);
            return response()->json([
                'message' => 'File data sumber tidak ditemukan. Silakan periksa file log Laravel di `storage/logs` untuk detail path.'
            ], 500);
        }

        // 4. Siapkan dan jalankan proses Python
        $process = new Process([
            $python_executable,
            $python_script_path,
            $validated['latitude'],
            $validated['longitude'],
            $validated['radius'],
            $raster_path_absolute // Kirim path absolut ke script Python
        ]);
        
        $process->setTimeout(60); // Timeout 60 detik

        try {
            $process->mustRun();

            $output = json_decode($process->getOutput());

            // Periksa jika ada flag error dari script Python
            if (isset($output->error) && $output->error) {
                Log::warning('Script Python mengembalikan error.', ['output' => (array)$output]);
                return response()->json(['message' => 'Gagal menganalisis data.'], 500);
            }

            return response()->json($output);

        } catch (ProcessFailedException $exception) {
            // Tangani jika proses Python gagal total (misal: timeout, error syntax)
            Log::error('Proses Python gagal.', [
                'error' => $exception->getMessage(),
            ]);
            
            return response()->json(['message' => 'Terjadi kesalahan pada server saat memproses data.'], 500);
        }
    }
}
