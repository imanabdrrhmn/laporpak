<x-mail::message>
# Laporan Telah Dipublikasikan

Halo {{ $user->name }},

Laporan Anda dengan ID **{{ $report->id }}** telah berhasil dipublikasikan.

Berikut detail laporannya:

- **Kategori**: {{ $report->category }}
- **Deskripsi**: {{ $report->description }}
- **Waktu Pengiriman**: {{ $report->created_at->format('d M Y H:i') }}

<x-mail::button :url="url('/reports/'.$report->id)">
Lihat Laporan
</x-mail::button>

Terima kasih telah berkontribusi!

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
