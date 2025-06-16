# app/Scripts/process_ghsl_direct.py

import sys
import json
import math
import geopandas as gpd
import rasterio
from rasterio.windows import from_bounds
import numpy as np
from shapely.geometry import Point, box

def get_stats_from_in_memory_array(data_array, nodata_value):
    """Fungsi helper untuk menghitung statistik dari numpy array yang sudah ada di memori."""
    valid_data = data_array[data_array != nodata_value].flatten()
    return valid_data

def process_data_from_raster(lat, lon, radius_meters, raster_path):
    """
    Membaca sebagian kecil dari file raster secara langsung (windowed reading)
    dan menghitung statistik populasi.
    """
    try:
        # --- Bagian 1: Mendefinisikan Area of Interest (AOI) ---
        # Membuat titik geografis dari input lat/lon (asumsi dalam WGS84)
        initial_crs = "EPSG:4326"
        point_geom = Point(lon, lat)
        gdf_point = gpd.GeoDataFrame([1], geometry=[point_geom], crs=initial_crs)

        # Buka raster sekali untuk mendapatkan sistem koordinatnya (CRS)
        with rasterio.open(raster_path) as src:
            target_crs = src.crs
            pop_nodata = src.nodata

        # Transformasi titik input ke sistem koordinat yang sama dengan raster
        gdf_point_proj = gdf_point.to_crs(target_crs)
        point_proj_geom = gdf_point_proj.geometry.iloc[0]
        
        # Membuat kotak pembatas (bounding box) di sekitar titik sebagai area baca awal
        bounding_box = box(
            point_proj_geom.x - radius_meters, 
            point_proj_geom.y - radius_meters, 
            point_proj_geom.x + radius_meters, 
            point_proj_geom.y + radius_meters
        )

        # --- Bagian 2: Membaca Data dengan Jendela (Windowed Reading) ---
        # Ini adalah langkah kunci efisiensi: hanya membaca data di dalam bounding box dari disk
        with rasterio.open(raster_path) as src:
            # Tentukan jendela baca berdasarkan bounding box
            window = from_bounds(*bounding_box.bounds, src.transform)
            # Baca data hanya dari jendela tersebut
            pop_data_window = src.read(1, window=window)

        # --- Bagian 3: Kalkulasi pada Data yang Sudah di Memori ---
        # Lakukan kalkulasi pada data yang jauh lebih kecil ini
        pop_data = get_stats_from_in_memory_array(pop_data_window, pop_nodata)
        total_population = int(np.sum(pop_data)) if pop_data.size > 0 else 0
        
        # Kalkulasi data turunan yang dibutuhkan frontend
        radius_km = radius_meters / 1000.0
        coverage_area_km2 = math.pi * (radius_km ** 2)
        density_per_km2 = (total_population / coverage_area_km2) if coverage_area_km2 > 0 else 0

        # Siapkan hasil akhir dalam format JSON
        result = {
            "total_population": total_population,
            "average_income": 0, # Placeholder, karena kita hanya memproses populasi
            "density_per_km2": round(density_per_km2),
            "coverage_area": round(coverage_area_km2, 2),
            "data_year": 2030 # Bisa dibuat dinamis jika perlu
        }

    except Exception as e:
        # Menangani jika terjadi error selama proses
        result = {"error": True, "message": f"Terjadi kesalahan di script Python: {str(e)}"}

    # Cetak hasil sebagai string JSON agar bisa ditangkap oleh Laravel
    print(json.dumps(result))

if __name__ == "__main__":
    # Script akan menerima 4 argumen dari command line:
    # 1: latitude, 2: longitude, 3: radius, 4: path_ke_file_raster
    if len(sys.argv) == 5:
        latitude = float(sys.argv[1])
        longitude = float(sys.argv[2])
        radius = int(sys.argv[3])
        raster_file_path = sys.argv[4]
        
        process_data_from_raster(latitude, longitude, radius, raster_file_path)
    else:
        print(json.dumps({"error": True, "message": "Jumlah argumen tidak sesuai."}))

