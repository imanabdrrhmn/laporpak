<x-mail::message>
# Top Up Ditolak

Halo {{ $user->name }},

Maaf, top up sebesar **Rp {{ number_format($topUp->amount, 0, ',', '.') }}** Anda ditolak.

Jika ada pertanyaan, silakan hubungi layanan pelanggan kami.

Terima kasih.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
