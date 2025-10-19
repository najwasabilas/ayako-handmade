@extends('layouts.app')
@section('title', 'Pembayaran')

@section('content')
<div class="payment-container">
  <div class="payment-header">
    <a href="/" class="back-btn">← Kembali</a>
    <h2>Pembayaran</h2>
  </div>

  <div class="payment-summary">
    <div class="summary-row"><span>Subtotal Pesanan</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span></div>
    <hr>
    <div class="summary-row total"><span>Total Pesanan:</span><span><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></span></div>
  </div>

  <div class="payment-info">
    <h3>Transfer Pembayaran</h3>
    <p>Silakan transfer ke rekening berikut:</p>
    <div class="rekening">
      <strong>Bank BCA</strong><br>
      <span>No. Rekening: 1234567890</span><br>
      <span>Atas Nama: Ayako Store</span>
    </div>
  </div>

    @php
    // Pisahkan alamat yang disimpan menjadi: nama | no_hp | alamat lengkap
    $alamatParts = explode(' | ', $order->alamat);
    $nama = $alamatParts[0] ?? '-';
    $no_hp = $alamatParts[1] ?? '-';
    $alamatLengkap = $alamatParts[2] ?? '-';

    // Susun pesan WhatsApp otomatis
    $waMessage = urlencode(
        "Halo Ayako , saya sudah melakukan pemesanan dengan detail berikut:\n\n" .
        "Order ID: {$order->id}\n" .
        "Nama: {$nama}\n" .
        "No HP: {$no_hp}\n" .
        "Alamat: {$alamatLengkap}\n\n" .
        "Mohon konfirmasi ya 🙏"
    );
    @endphp

  <div class="whatsapp-section">
    <a href="https://wa.me/6282284471620?text={{ $waMessage }}"
       target="_blank" class="btn-wa">Hubungi via WhatsApp</a>
  </div>
</div>

<style>
.payment-container{padding:20px;background:#f7f3ea;min-height:85vh;text-align:center}
.payment-header{display:flex;align-items:center;justify-content:center;gap:10px;position:relative;margin-bottom:30px}
.back-btn{position:absolute;left:0;text-decoration:none;color:white;background:#a5642e;padding:5px 12px;border-radius:6px}
.payment-summary{background:#fff;padding:25px;border-radius:12px;display:inline-block;text-align:left}
.summary-row{display:flex;justify-content:space-between;margin:8px 0;font-size:1rem;color:#3b2a1a}
.summary-row.total{font-weight:bold;margin-top:15px;font-size:1.2rem}
.payment-info{margin-top:30px;text-align:center}
.rekening{background:#fffaf3;padding:15px;border-radius:10px;display:inline-block;margin-top:10px}
.whatsapp-section{margin-top:30px}
.btn-wa{background:#25D366;color:white;padding:12px 25px;border:none;border-radius:8px;text-decoration:none;font-weight:bold;cursor:pointer}
.notif.success{background:#d2f8d2;color:#2b7a2b;padding:10px 15px;border-radius:8px;margin-bottom:20px}
</style>
@endsection
