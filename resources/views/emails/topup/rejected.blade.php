<x-mail::message>
# Top Up Gagal

Halo {{ $user->name }}, Maaf, pembayaran Anda tidak dapat diproses

## Rp. {{ number_format($topUp->amount, 0, ',', '.') }}

**Saldo tidak mencukupi atau kartu ditolak**

Silakan coba lagi atau gunakan metode pembayaran lain.

<x-mail::button :url="$topUpHistoryUrl" color="error">
Lihat Riwayat Top Up
</x-mail::button>

Salam hangat,<br>
{{ config('app.name') }}
</x-mail::message>