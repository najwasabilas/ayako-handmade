@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="checkout-container">
  <div class="checkout-header">
    <a href="{{ route('cart.index') }}" class="back-btn">← Kembali</a>
    <h2>Checkout</h2>
  </div>

  {{-- Notifikasi sukses --}}
  @if(session('success'))
    <div class="notif success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="notif error">{{ session('error') }}</div>
  @endif

  <form action="{{ route('checkout.place') }}" method="POST" class="checkout-form">
    @csrf

    {{-- Alamat --}}
    <div class="section address-section">
      <h3><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h3>
      <div class="form-grid">
        <input type="text" name="nama" placeholder="Nama Lengkap" required>
        <input type="text" name="telepon" placeholder="Nomor Telepon" required>
        <textarea name="alamat_lengkap" rows="4" placeholder="Alamat lengkap" required></textarea>
      </div>
    </div>

    {{-- Produk --}}
    <div class="section product-section">
      <h3>Produk Dipesan</h3>
      @foreach($items as $item)
      <div class="product-row">
        <img src="{{ asset('assets/catalog/images/' . $item['image']) }}" class="product-img">
        <div class="product-info">
          <p class="product-name">{{ $item['nama'] }}</p>
          <p class="product-qty">x{{ $item['qty'] }}</p>
        </div>
        <div class="product-price">Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</div>
      </div>
      @endforeach
    </div>

    {{-- Total --}}
    <div class="section total-section">
      <div class="summary-row"><span>Subtotal Pesanan</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
      <hr>
      <div class="summary-row total">
        <span>Total Pesanan:</span><span><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></span>
      </div>
      <button type="submit" class="btn-submit">Buat Pesanan</button>
    </div>
  </form>
</div>

<style>
.checkout-container{padding:20px;background:#f7f3ea;min-height:85vh}
.checkout-header{display:flex;align-items:center;justify-content:center;gap:10px;position:relative;margin-bottom:20px}
.back-btn{position:absolute;left:0;text-decoration:none;color:#b86b32;font-weight:600}
.section{background:#fffaf3;padding:20px;border-radius:12px;margin-bottom:20px}
.section h3{margin-bottom:15px;font-weight:700;color:#5a3b17}
.form-grid input,.form-grid textarea{width:100%;border:1px solid #c7a77b;border-radius:8px;padding:10px;background:white;margin-bottom:14px;}
.product-row{display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #eee;padding:10px 0}
.product-img{width:60px;height:60px;border-radius:8px;object-fit:cover}
.product-info{flex-grow:1;margin-left:10px}
.product-name{font-weight:bold;color:#3a2a18}
.product-price{font-weight:bold;color:#5a3b17}
.summary-row{display:flex;justify-content:space-between;margin:8px 0}
.summary-row.total{font-size:1.1em;font-weight:bold}
.btn-submit{width:100%;background:#a5642e;color:white;padding:12px;border:none;border-radius:10px;margin-top:15px;cursor:pointer;font-weight:600}
.notif{padding:10px 15px;border-radius:8px;margin-bottom:15px}
.notif.success{background:#d2f8d2;color:#2b7a2b}
.notif.error{background:#ffe2e2;color:#c0392b}
</style>
@endsection
