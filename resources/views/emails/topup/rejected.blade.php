<x-mail::message>
{{-- Logo Perusahaan --}}
<div style="text-align: center; margin: 20px 0;">
    @if($logoBase64)
        <img src="{{ $logoBase64 }}" alt="LaporPak.com" style="max-width: 150px; height: auto; margin-bottom: 20px;">
    @else
        {{-- Fallback text jika logo tidak tersedia --}}
        <h1 style="color: #00C896; font-size: 24px; margin-bottom: 20px;">LaporPak.com</h1>
    @endif
</div>

# Top Up Gagal

<div style="text-align: center; margin: 20px 0;">
    <div style="width: 60px; height: 60px; background-color: #87CEEB; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
        <span style="color: white; font-size: 24px; font-weight: bold;">✓</span>
    </div>
</div>

Halo {{ $user->name }}, Maaf, pembayaran Anda tidak dapat diproses

<div style="text-align: center; margin: 30px 0;">
    <h2 style="color: #00C896; font-size: 48px; margin: 0; font-weight: bold;">
        Rp. {{ number_format($topUp->amount, 0, ',', '.') }}
    </h2>
</div>

Untuk mengecek riwayat transaksi Anda, silakan klik  
tombol di bawah ini:

<x-mail::button :url="route('top-ups.index')" color="primary">
Lihat Riwayat Top Up
</x-mail::button>

salam hangat,  
LaporPak.com
</x-mail::message>