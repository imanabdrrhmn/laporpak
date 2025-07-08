<x-mail::message>
@if($logoBase64)
<img src="{{ $logoBase64 }}" alt="LaporPak.com" style="max-width: 150px; height: auto; margin-bottom: 20px;">
@else
# LaporPak.com
@endif

# Top Up Gagal

Halo {{ $user->name }},  
Maaf, pembayaran Anda tidak dapat diproses.

## Rp. {{ number_format($topUp->amount, 0, ',', '.') }}

@component('mail::button', ['url' => route('top-ups.index')])
Lihat Riwayat Top Up
@endcomponent

Salam hangat,  
**LaporPak.com**
</x-mail::message>
