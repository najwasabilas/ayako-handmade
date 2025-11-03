@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="cart-container">
  <div class="cart-header">
    <h2>Keranjang Belanja</h2>
  </div>

  <div class="cart-table">
    <div class="cart-row cart-header-row">
      <div class="cart-col col-product">
        <input type="checkbox" id="select-all">
        <label for="select-all" style="margin-left:6px;">Pilih Semua</label>
      </div>
      <div class="cart-col">Harga Satuan</div>
      <div class="cart-col">Kuantitas</div>
      <div class="cart-col">Total Harga</div>
    </div>

    <form action="{{ route('cart.checkout') }}" method="POST" id="cartForm">
      @csrf
      @forelse($items as $item)
      <div class="cart-row">
        <div class="cart-col col-product">
          <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" class="select-item">
          <img src="{{ asset('assets/catalog/images/' . ($item->product->images->first()->gambar ?? 'no-image.jpg')) }}" class="cart-img">
          <div class="cart-info">
            <p class="cart-name">{{ $item->product->nama }}</p>
            <p class="cart-variasi">Stok: {{ $item->product->stok }}</p>
          </div>
        </div>

        <div class="cart-col">
          Rp {{ number_format($item->harga, 0, ',', '.') }}
        </div>

        <div class="cart-col">
          <div class="qty-box">
            <button type="button" class="qty-btn" onclick="changeQty({{ $item->id }}, -1)">−</button>
            <input type="number" id="qty-{{ $item->id }}" value="{{ $item->qty }}" min="1" max="{{ $item->product->stok }}" readonly>
            <button type="button" class="qty-btn" onclick="changeQty({{ $item->id }}, 1)">+</button>
          </div>
        </div>

        <div class="cart-col total" data-price="{{ $item->harga }}">
          Rp {{ number_format($item->qty * $item->harga, 0, ',', '.') }}
        </div>
      </div>
      @empty
      <p class="empty-cart">Keranjang kamu masih kosong.</p>
      @endforelse
    </form>
  </div>

  @if($items->count() > 0)
  <div class="cart-footer">
    <div class="cart-total">
      Total (produk dipilih): 
      <span id="total-price">Rp 0</span>
    </div>
    <button class="btn-checkout" onclick="document.getElementById('cartForm').submit()">Checkout</button>
  </div>
  @endif
</div>

<script>
const selectAll = document.getElementById('select-all');
const checkboxes = document.querySelectorAll('.select-item');
const totalPrice = document.getElementById('total-price');

// Fungsi update total hanya dari item yang dicentang
function updateTotal() {
  let total = 0;
  checkboxes.forEach(cb => {
    if (cb.checked) {
      const row = cb.closest('.cart-row');
      const price = parseInt(row.querySelector('.total').getAttribute('data-price'));
      const qty = parseInt(row.querySelector('input[type="number"]').value);
      total += price * qty;
    }
  });
  totalPrice.textContent = "Rp " + total.toLocaleString('id-ID');
}

// Pilih semua
selectAll.addEventListener('change', () => {
  checkboxes.forEach(cb => cb.checked = selectAll.checked);
  updateTotal();
});

// Update total kalau item dicentang/dibatalkan
checkboxes.forEach(cb => cb.addEventListener('change', updateTotal));

function changeQty(itemId, change) {
  const input = document.getElementById('qty-' + itemId);
  let qty = parseInt(input.value) + change;
  const max = parseInt(input.getAttribute('max'));

  if (qty < 1) {
    if (confirm('Hapus produk ini dari keranjang?')) {
      fetch("{{ route('cart.remove') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ id: itemId })
      })
      .then(res => res.json())
      .then(() => location.reload());
    }
    return;
  }

  if (qty > max) qty = max;
  input.value = qty;

  // Update total harga per produk secara langsung
  const row = input.closest('.cart-row');
  const price = parseInt(row.querySelector('.total').getAttribute('data-price'));
  const newSubtotal = price * qty;
  row.querySelector('.total').textContent = "Rp " + newSubtotal.toLocaleString('id-ID');

   updateTotal();

  fetch("{{ route('cart.update') }}", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    body: JSON.stringify({ item_id: itemId, qty: qty })
  }).then(res => res.json())
    .then(data => {
      if (data.error) alert(data.error);
      else updateTotal();
    });
}
</script>

<style>
.cart-container { padding: 20px; background: #f7f3ea; min-height: 80vh; }
.cart-header { display: flex; justify-content:center; align-items: center; gap: 10px; }
.cart-table { background: white; border-radius: 12px; margin-top: 20px; overflow: hidden; }
.cart-row { display: grid; grid-template-columns: 40% 20% 20% 20%; align-items: center; border-bottom: 1px solid #eee; padding: 10px; }
.cart-col { text-align: center; }
.col-product { display: flex; align-items: center; gap: 10px; justify-content: flex-start; }
.cart-img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
.cart-info { text-align: left; }
.qty-box { display: flex; align-items: center; justify-content: center; gap: 6px; }
.qty-btn { border: 1px solid #b8860b; background: none; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; }
input[type=number] { width: 40px; text-align: center; border: none; background: #f5f5f5; }
.cart-footer { margin-top: 20px; display: flex; justify-content: space-between; align-items: center; background: #a5642e; padding: 15px 20px; color: white; border-radius: 12px; }
.btn-checkout { background: white; color: #b8860b; padding: 10px 30px; border-radius: 10px; border: none; font-weight: bold; cursor: pointer; }
@media(max-width:768px){
  .cart-row { grid-template-columns: 60% 40%; grid-row-gap: 10px; }
  .cart-col:nth-child(2), .cart-col:nth-child(3), .cart-col:nth-child(4) { text-align: left; }
  .cart-footer { flex-direction: column; gap: 10px; }
}
</style>
@endsection
