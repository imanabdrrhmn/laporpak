<x-mail::message>
# Top Up Berhasil

Halo {{ $user->name }},

Top up sebesar **Rp {{ number_format($topUp->amount, 0, ',', '.') }}** telah berhasil diverifikasi.

<x-mail::button :url="route('top-ups.index')">
Lihat Riwayat Top Up
</x-mail::button>

Terima kasih telah menggunakan layanan kami.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
