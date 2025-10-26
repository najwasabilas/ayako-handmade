@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="checkout-container">
  <div class="checkout-header">
    <a href="{{ route('cart.index') }}" class="back-btn">← Kembali</a>
    <h2>Checkout</h2>
  </div>

  <form action="{{ route('checkout.place') }}" method="POST" class="checkout-form">
    @csrf

    {{-- Alamat Pengiriman --}}
    <div class="section address-section">
      <h3><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h3>

      {{-- Daftar alamat yang sudah ada --}}
      @if($addresses->isNotEmpty())
        @foreach($addresses as $address)
          <label class="address-item">
            <input type="radio" name="address_id" value="{{ $address->id }}" 
              {{ $address->utama ? 'checked' : '' }}>
            <div class="address-detail">
              <p class="receiver">{{ $address->nama_penerima }}</p>
              <p class="phone">{{ $address->no_hp }}</p>
              <p class="full-address">{{ $address->alamat_lengkap }}</p>
            </div>
            @if($address->utama)
              <span class="default-label">Alamat Utama</span>
            @else
              <button type="button" 
                      class="btn-delete" 
                      onclick="deleteAddress({{ $address->id }})">
                Hapus
              </button>
            @endif
          </label>
        @endforeach
      @else
        <p style="color: #777;">Belum ada alamat tersimpan. Silakan tambah alamat baru.</p>
      @endif

      @if($addresses->count() < 5)
      {{-- Tombol tambah alamat baru --}}
      <button type="button" id="toggleAddAddress" class="btn-add-address">
        + Tambah Alamat Baru
      </button>
      @endif

      {{-- Form tambah alamat baru (hidden dulu) --}}
      <div id="addAddressForm" class="add-address" style="display:none;">
        <div class="form-vertical">
          <label>Nama Penerima</label>
          <input type="text" name="nama_penerima_baru" placeholder="Nama penerima">

          <label>Nomor Telepon</label>
          <input type="text" name="telepon_baru" placeholder="Nomor telepon">

          <label>Alamat Lengkap</label>
          <textarea name="alamat_lengkap_baru" rows="3" placeholder="Alamat lengkap"></textarea>

          <div class="default-inline">
            <label for="is_default_baru" class="inline-label">
              <input type="checkbox" id="is_default_baru" name="is_default_baru">
              Jadikan alamat utama
            </label>
          </div>
        </div>
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
      <div class="summary-row total">
        <span>Total Pesanan:</span><span><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></span>
      </div>
      <button type="submit" class="btn-submit">Buat Pesanan</button>
    </div>
  </form>


  {{-- Form DELETE terpisah di luar form utama --}}
  <form id="deleteAddressForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
  </form>

</div>

<script>
function deleteAddress(id) {
  if (confirm('Yakin hapus alamat ini?')) {
    const form = document.getElementById('deleteAddressForm');
    form.action = `/checkout/address/${id}`; // sesuaikan dengan route kamu
    form.submit();
  }
}

document.getElementById('toggleAddAddress').addEventListener('click', function() {
  const form = document.getElementById('addAddressForm');
  form.style.display = form.style.display === 'none' ? 'block' : 'none';
  this.textContent = form.style.display === 'none' ? '+ Tambah Alamat Baru' : '− Tutup Form Alamat';
});
</script>

<style>
.checkout-container{padding:20px;background:#f7f3ea;min-height:85vh}
.checkout-header{display:flex;align-items:center;justify-content:center;gap:10px;position:relative;margin-bottom:20px}
.back-btn{position:absolute;left:0;text-decoration:none;color:#b86b32;font-weight:600}
.section{background:#fffaf3;padding:20px;border-radius:12px;margin-bottom:20px}
.section h3{margin-bottom:15px;font-weight:700;color:#5a3b17}
.address-list{display:flex;flex-direction:column;gap:10px;margin-bottom:15px}
.address-card{display:flex;align-items:flex-start;gap:10px;border:1px solid #c7a77b;border-radius:10px;padding:10px;background:white;cursor:pointer}
.address-card input[type=radio]{margin-top:5px}
.address-info{flex:1;font-size:14px;color:#3a2a18}
.default-badge{background:#b86b32;color:white;padding:2px 6px;border-radius:6px;font-size:11px;margin-left:6px}
.btn-add{background:none;border:1px dashed #b86b32;color:#b86b32;padding:8px 12px;border-radius:8px;cursor:pointer;margin-top:10px}
.new-address{margin-top:15px;background:#fff;padding:15px;border:1px solid #e5c6a0;border-radius:10px}
.new-address input,.new-address textarea{width:100%;border:1px solid #c7a77b;border-radius:8px;padding:8px;margin-bottom:10px;}
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
.address-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  background: #fffaf3;
  border: 1px solid #e5cdaa;
  padding: 12px;
  border-radius: 10px;
  margin-bottom: 10px;
  position: relative;
}

.address-item input[type="radio"] {
  margin-top: 5px;
}

.address-detail {
  flex: 1;
}

.receiver {
  font-weight: 600;
  color: #3a2a18;
}

.phone, .full-address {
  font-size: 14px;
  color: #6e573d;
  margin: 2px 0;
}

.default-label {
  position: absolute;
  top: 8px;
  right: 12px;
  background: #b86b32;
  color: #fff;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 8px;
}

.btn-add-address {
  display: inline-block;
  background: #a5642e;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 8px 12px;
  cursor: pointer;
  font-weight: 600;
  margin-top: 10px;
}

.add-address {
  margin-top: 12px;
  background: #fffaf3;
  padding: 15px;
  border-radius: 10px;
  border: 1px solid #e5cdaa;
}

.default-inline {
  display: flex;
  align-items: center;
  margin-top: 6px;
}

.inline-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  color: #3a2a18;
  cursor: pointer;
}

.inline-label input[type="checkbox"] {
  width: 16px;
  height: 16px;
  accent-color: #a5642e; /* warna sesuai tema */
}

.form-vertical {
  display: flex;
  flex-direction: column;
}

.form-vertical label {
  font-weight: 600;
  color: #3a2a18;
  margin-bottom: 4px;
}

.form-vertical input,
.form-vertical textarea {
  width: 100%;
  border: 1px solid #c7a77b;
  border-radius: 8px;
  padding: 10px;
  background: white;
  margin-bottom: 12px;
  font-size: 14px;
}

.btn-delete {
  background: #c0392b;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 6px 10px;
  cursor: pointer;
  font-weight: bold;
  font-size: 10px;
}
</style>
@endsection
