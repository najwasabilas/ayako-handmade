@extends('layouts.app')
@section('title', 'Pembayaran')

@section('content')
<div class="payment-container">
  <div class="payment-header">
    <a href="/" class="back-btn">← Kembali</a>
    <h2>Pembayaran</h2>
  </div>

  <div class="payment-summary">
    <h3>Rincian Pesanan</h3>
    <table class="summary-table">
      <thead>
        <tr>
          <th>Produk</th>
          <th>Harga</th>
          <th>Qty</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($items as $item)
          <tr>
            <td>{{ $item['nama'] }}</td>
            <td>Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
            <td>{{ $item['qty'] }}</td>
            <td>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <hr>
    <div class="summary-row total">
      <span>Total Pesanan:</span>
      <span><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></span>
    </div>
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
  $waMessage = urlencode(
      "Halo Ayako, saya sudah melakukan pemesanan dengan detail berikut:\n\n" .
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
.summary-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  font-size: 0.95rem;
}

.summary-table th,
.summary-table td {
  border-bottom: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

.summary-table th {
  background-color: #f7f3ea;
  font-weight: bold;
}

.summary-table tr:last-child td {
  border-bottom: none;
}

</style>
@endsection
