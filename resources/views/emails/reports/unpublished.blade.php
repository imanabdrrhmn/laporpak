<x-mail::message>
# Publikasi Laporan Dibatalkan

Halo {{ $user->name }},

Publikasi laporan Anda dengan ID **{{ $report->id }}** telah dibatalkan oleh administrator.

Alasan pembatalan:
> {{ $report->reason ?? 'Tidak ada alasan spesifik diberikan.' }}

Berikut detail laporannya:

- **Kategori**: {{ $report->category }}
- **Deskripsi**: {{ $report->description }}
- **Waktu Pengiriman**: {{ $report->created_at->format('d M Y H:i') }}

Jika Anda memiliki pertanyaan, jangan ragu menghubungi kami.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
