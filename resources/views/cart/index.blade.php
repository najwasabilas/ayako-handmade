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
      <div class="cart-row {{ $item->product->stok == 0 ? 'disabled-row' : '' }}">
        <div class="cart-col col-product">
            <input 
                type="checkbox" 
                name="selected_items[]" 
                value="{{ $item->id }}" 
                class="select-item"
                {{ $item->product->stok == 0 ? 'disabled' : '' }}
            >

            <img src="{{ asset('assets/catalog/images/' . ($item->product->images->first()->gambar ?? 'no-image.jpg')) }}" class="cart-img">

            <div class="cart-info">
                <p class="cart-name">{{ $item->product->nama }}</p>

                @if ($item->product->stok == 0)
                    <p class="cart-out">Stok habis</p>
                @else
                    <p class="cart-variasi">Stok: {{ $item->product->stok }}</p>
                @endif
            </div>
        </div>

        <div class="cart-col">
            @if ($item->product->stok == 0)
                <span class="price-disabled">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
            @else
                Rp {{ number_format($item->harga, 0, ',', '.') }}
            @endif
        </div>

        <div class="cart-col">
            <div class="qty-box">
                <button 
                    type="button" 
                    class="qty-btn"
                    onclick="changeQty({{ $item->id }}, -1)"
                    {{ $item->product->stok == 0 ? 'disabled' : '' }}
                >−</button>

                <input 
                    type="number" 
                    id="qty-{{ $item->id }}" 
                    value="{{ $item->qty }}" 
                    min="1"
                    max="{{ $item->product->stok }}"
                    readonly
                >

                <button 
                    type="button" 
                    class="qty-btn"
                    onclick="changeQty({{ $item->id }}, 1)"
                    {{ $item->product->stok == 0 ? 'disabled' : '' }}
                >+</button>
            </div>
        </div>

        <div class="cart-col total" data-price="{{ $item->product->stok == 0 ? 0 : $item->harga }}">
            @if ($item->product->stok == 0)
                <span class="price-disabled">Rp 0</span>
            @else
                Rp {{ number_format($item->qty * $item->harga, 0, ',', '.') }}
            @endif
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
  <!-- Popup Konfirmasi -->
  <div id="confirmPopup" class="confirm-popup hidden">
    <div class="confirm-box">
      <p>Yakin ingin menghapus produk ini dari keranjang?</p>
      <div class="confirm-actions">
        <button id="confirmYes" class="btn-yes">Hapus</button>
        <button id="confirmNo" class="btn-no">Batal</button>
      </div>
    </div>
  </div>

</div>

<script>
const selectAll = document.getElementById('select-all');
const checkboxes = document.querySelectorAll('.select-item');
const totalPrice = document.getElementById('total-price');

function updateTotal() {
  let total = 0;
  checkboxes.forEach(cb => {
    if (cb.checked) {
      const row = cb.closest('.cart-row');
      if (row.classList.contains('disabled-row')) return;
      const price = parseInt(row.querySelector('.total').getAttribute('data-price'));
      const qty = parseInt(row.querySelector('input[type="number"]').value);
      total += price * qty;
    }
  });
  totalPrice.textContent = "Rp " + total.toLocaleString('id-ID');
}

selectAll.addEventListener('change', () => {
  checkboxes.forEach(cb => cb.checked = selectAll.checked);
  updateTotal();
});

checkboxes.forEach(cb => cb.addEventListener('change', updateTotal));

function changeQty(itemId, change) {
  const input = document.getElementById('qty-' + itemId);
  let qty = parseInt(input.value) + change;
  const max = parseInt(input.getAttribute('max'));

  // JIKA QTY < 1 → TAMPILKAN POPUP KONFIRMASI
  if (qty < 1) {
    showConfirm(itemId);
    return;
  }

  if (qty > max) qty = max;
  input.value = qty;

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

<script>
let pendingDeleteId = null;

// Tampilkan popup
function showConfirm(id) {
  pendingDeleteId = id;
  document.getElementById("confirmPopup").classList.remove("hidden");
}

// Tombol batal
document.getElementById("confirmNo").addEventListener("click", () => {
  document.getElementById("confirmPopup").classList.add("hidden");
});

// Tombol hapus
document.getElementById("confirmYes").addEventListener("click", () => {

  fetch("{{ route('cart.remove') }}", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    body: JSON.stringify({ id: pendingDeleteId })
  })
  .then(res => res.json())
  .then(() => {
    document.getElementById("confirmPopup").classList.add("hidden");
    location.reload();
  });
});
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
/* Popup background */
.confirm-popup {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.45);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

/* Sembunyikan popup */
.hidden {
  display: none !important;
}

/* Box */
.confirm-box {
  background: #fff;
  padding: 20px 25px;
  border-radius: 12px;
  width: 300px;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0,0,0,0.15);
  animation: zoomIn .25s ease-out;
}

/* Tombol */
.confirm-actions {
  margin-top: 15px;
  display: flex;
  justify-content: center;
  gap: 12px;
}

.btn-yes {
  background: #a96327;
  color: white;
  padding: 8px 18px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn-no {
  background: #ddd;
  padding: 8px 18px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

/* Animasi */
@keyframes zoomIn {
  from { transform: scale(0.8); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

/* Row saat stok habis */
.disabled-row {
  opacity: 0.65;
  position: relative;
}

/* Label stok habis */
.cart-out {
  color: #d9534f;
  font-weight: bold;
  font-size: 13px;
}

/* Harga nonaktif */
.price-disabled {
  color: #a1a1a1;
}

/* Disable tombol qty */
.qty-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* Checkbox disable */
.select-item:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

</style>
@endsection
