<x-mail::message>
# Laporan Ditolak

Halo {{ $user->name }},

Mohon maaf, laporan Anda dengan ID **{{ $report->id }}** tidak dapat kami terima.

Berikut detail laporannya:

- **Kategori**: {{ $report->category }}
- **Deskripsi**: {{ $report->description }}
- **Waktu Pengiriman**: {{ $report->created_at->format('d M Y H:i') }}

Jika Anda merasa ini sebuah kesalahan, silakan hubungi tim kami.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
