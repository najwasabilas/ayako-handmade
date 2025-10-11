<div id="productPopup" class="popup-overlay" style="display:none;">
  <div class="popup-box">
    <button type="button" class="popup-close">&times;</button>

    <div class="popup-content">
      <!-- Baris 1: Gambar + Nama + Harga + Stok -->
      <div class="popup-row popup-header">
        <img id="popupImage" src="{{ asset('assets/catalog/no-image.jpg') }}" alt="Produk">
        <div class="popup-text">
          <p class="popup-name" id="popupName">Nama Produk</p>
          <p class="popup-price" id="popupPrice">Rp 0</p>
          <p class="popup-stock">Stok: <span id="popupStock">0</span></p>
        </div>
      </div>

      <hr>

      <!-- Baris 2: Input jumlah -->
      <div class="popup-row popup-qty">
        <label for="popupQty">Jumlah</label>
        <div class="qty-control">
          <button type="button" class="qty-btn" id="qtyMinus">−</button>
          <input type="number" id="popupQty" value="1" min="1">
          <button type="button" class="qty-btn" id="qtyPlus">+</button>
        </div>
      </div>

      <hr>

      <!-- Baris 3: Tombol aksi -->
      <button type="button" id="popupActionBtn" class="popup-btn">Masukkan Keranjang</button>
    </div>
  </div>
</div>

<style>
.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 99999;
}

.popup-box {
  background: #fff;
  border-radius: 12px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  position: relative;
  animation: fadeIn 0.3s ease;
  overflow: hidden;
}

.popup-close {
  position: absolute;
  top: 8px;
  right: 12px;
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #555;
}

.popup-content {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.popup-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.popup-header img {
  width: 70px;
  height: 70px;
  border-radius: 8px;
  object-fit: cover;
}

.popup-text {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  flex: 1;
  margin-left: 15px;
}

.popup-name {
  font-weight: 600;
  font-size: 16px;
  margin: 0 0 5px 0;
  color: #333;
}

.popup-price {
  color: #7a4a15;
  font-weight: 600;
  font-size: 18px;
  margin: 0;
}

.popup-stock {
  color: #666;
  font-size: 14px;
  margin: 2px 0 0 0;
}

hr {
  border: none;
  border-top: 1px solid #eee;
  margin: 10px 0;
}

.popup-qty label {
  font-size: 15px;
  color: #333;
}

.qty-control {
  display: flex;
  align-items: center;
  border: 1px solid #a86b32;
  border-radius: 8px;
  overflow: hidden;
}

.qty-btn {
  background: none;
  border: none;
  color: #a86b32;
  font-size: 18px;
  padding: 6px 12px;
  cursor: pointer;
}

#popupQty {
  width: 45px;
  text-align: center;
  border: none;
  font-size: 16px;
}

/* Tombol utama */
.popup-btn {
  width: 100%;
  background: #d19a54;
  color: white;
  border: none;
  border-radius: 10px;
  padding: 12px 0;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.popup-btn:hover {
  background: #b87e35;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

@media (max-width: 480px) {
  .popup-box { width: 95%; }
  .popup-header img { width: 60px; height: 60px; }
  .popup-name { font-size: 15px; }
  .popup-price { font-size: 16px; }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const popup = document.getElementById("productPopup");
  const closeBtn = document.querySelector(".popup-close");
  const qtyInput = document.getElementById("popupQty");
  const minusBtn = document.getElementById("qtyMinus");
  const plusBtn = document.getElementById("qtyPlus");
  const popupBtn = document.getElementById("popupActionBtn");

  let maxStock = 1; // default

  // Fungsi buka popup
  window.openPopup = function(product, action = 'keranjang') {
    document.getElementById("popupImage").src = product.image;
    document.getElementById("popupName").innerText = product.nama;
    document.getElementById("popupPrice").innerText = 'Rp ' + product.harga.toLocaleString('id-ID');
    document.getElementById("popupStock").innerText = product.stok;
    document.getElementById("popupQty").value = 1;

    maxStock = product.stok || 1;
    qtyInput.max = maxStock;

    popupBtn.innerText = action === 'beli' ? 'Beli Sekarang' : 'Masukkan Keranjang';
    popupBtn.dataset.action = action;

    popup.style.display = "flex";
    document.body.style.overflow = "hidden";
  };

  function closePopup() {
    popup.style.display = "none";
    document.body.style.overflow = "auto";
  }

  closeBtn.addEventListener("click", closePopup);
  popup.addEventListener("click", e => {
    if (e.target === popup) closePopup();
  });

  // Tombol qty dengan pembatas stok
  minusBtn.addEventListener("click", () => {
    let val = parseInt(qtyInput.value) || 1;
    if (val > 1) qtyInput.value = val - 1;
  });

  plusBtn.addEventListener("click", () => {
    let val = parseInt(qtyInput.value) || 1;
    if (val < maxStock) {
      qtyInput.value = val + 1;
    } else {
      alert("Jumlah melebihi stok yang tersedia!");
    }
  });

  // Tombol aksi
  popupBtn.addEventListener("click", function() {
    const qty = parseInt(qtyInput.value);
    const action = this.dataset.action;

    if (qty > maxStock) {
      alert("Jumlah melebihi stok yang tersedia!");
      return;
    }

    alert(`${action === 'beli' ? 'Beli Sekarang' : 'Tambah ke Keranjang'} - Jumlah: ${qty}`);
    closePopup();
  });
});
</script>
