<x-mail::message>
# Laporan Anda Telah Selesai

Halo {{ $user->name }},

Laporan Anda dengan ID **{{ $report->id }}** telah berhasil diproses dan ditandai sebagai **selesai**.

Berikut detail laporannya:

- **Kategori**: {{ $report->category }}
- **Deskripsi**: {{ $report->description }}
- **Waktu Pengiriman**: {{ $report->created_at->format('d M Y H:i') }}

Terima kasih atas laporan Anda dan kontribusinya.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
