<!-- ===== Popup Tambah ke Keranjang / Beli Sekarang ===== -->
<div id="productPopup" class="popup-overlay">
  <div class="popup-content">
    <button class="popup-close">&times;</button>

    <div class="popup-row image-row">
      <img id="popupImage" src="" alt="Product Image">
      <div class="popup-info">
        <h3 id="popupName">Nama Produk</h3>
        <p class="price" id="popupPrice">Rp 0</p>
        <p class="stok">Stok: <span id="popupStock">0</span></p>
      </div>
    </div>

    <div class="popup-row qty-row">
      <label for="popupQty">Jumlah:</label>
      <div class="qty-control">
        <button id="qtyMinus">−</button>
        <input type="number" id="popupQty" min="1" value="1">
        <button id="qtyPlus">+</button>
      </div>
    </div>

    <div class="popup-row button-row">
      <button id="popupActionBtn">Masukkan Keranjang</button>
    </div>
  </div>
</div>

<!-- ===== Popup Notifikasi ===== -->
<div id="notifPopup" class="notif-popup">
  <i class="fas fa-check-circle"></i>
  <span id="notifMessage">Produk berhasil ditambahkan ke keranjang!</span>
</div>

<style>
/* ========== Popup Utama ========== */
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 99999;
  padding: 15px;
}

.popup-content {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  width: 100%;
  max-width: 420px;
  display: flex;
  flex-direction: column;
  gap: 15px;
  position: relative;
}

.popup-close {
  position: absolute;
  top: 10px;
  right: 15px;
  background: transparent;
  border: none;
  font-size: 22px;
  cursor: pointer;
  color: #555;
}

.image-row {
  display: flex;
  align-items: center;
  gap: 15px;
}

.image-row img {
  width: 100px;
  height: 100px;
  border-radius: 10px;
  object-fit: cover;
}

.popup-info h3 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 5px;
}

.popup-info .price {
  color: #a86b32;
  font-weight: 600;
  margin: 0;
}

.popup-info .stok {
  font-size: 13px;
  color: #555;
}

.qty-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.qty-control {
  display: flex;
  align-items: center;
  gap: 8px;
}

.qty-control button {
  width: 30px;
  height: 30px;
  background: #eee;
  border: none;
  border-radius: 5px;
  font-size: 18px;
  cursor: pointer;
}

.qty-control input {
  width: 60px;
  text-align: center;
  border: 1px solid #ddd;
  border-radius: 5px;
  height: 30px;
}

.button-row {
  display: flex;
  justify-content: center;
}

#popupActionBtn {
  background: #a86b32;
  color: #fff;
  border: none;
  border-radius: 25px;
  padding: 10px 30px;
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
  transition: 0.3s;
}

#popupActionBtn:hover {
  background: #8b5a28;
}

/* ========== Popup Notifikasi ========== */
.notif-popup {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) scale(0.9);
  background: #4CAF50;
  color: white;
  padding: 15px 25px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
  font-size: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  z-index: 9999999;
  opacity: 0;
  transition: all 0.3s ease;
  pointer-events: none;
}

.notif-popup.show {
  opacity: 1;
  transform: translate(-50%, -50%) scale(1);
}

.notif-popup i {
  font-size: 20px;
}

/* ========== Responsif ========== */
@media (max-width: 600px) {
  .popup-content {
    max-width: 90%;
    padding: 15px;
  }

  .image-row img {
    width: 80px;
    height: 80px;
  }

  .popup-info h3 {
    font-size: 14px;
  }

  #popupActionBtn {
    width: 100%;
  }
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
  const notifPopup = document.getElementById("notifPopup");
  const notifMsg = document.getElementById("notifMessage");

  let maxStock = 1;
  let currentProduct = null;

  window.openPopup = function(product, action = 'keranjang') {
    currentProduct = product;
    document.getElementById("popupImage").src = product.image;
    document.getElementById("popupName").innerText = product.nama;
    document.getElementById("popupPrice").innerText = 'Rp ' + product.harga.toLocaleString('id-ID');
    document.getElementById("popupStock").innerText = product.stok;
    qtyInput.value = 1;
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
  popup.addEventListener("click", e => { if (e.target === popup) closePopup(); });

  minusBtn.addEventListener("click", () => {
    let val = parseInt(qtyInput.value) || 1;
    if (val > 1) qtyInput.value = val - 1;
  });

  plusBtn.addEventListener("click", () => {
    let val = parseInt(qtyInput.value) || 1;
    if (val < maxStock) qtyInput.value = val + 1;
  });

  function showNotif(message, type="success") {
    notifMsg.innerText = message;
    notifPopup.classList.add("show");
    notifPopup.style.display = "flex";

    if (type === "error") {
        notifPopup.style.backgroundColor = "#e74c3c"; // merah
    } else {
        notifPopup.style.backgroundColor = "#2ecc71"; // hijau
    }

    setTimeout(() => notifPopup.style.opacity = "1", 10);

    setTimeout(() => {
      notifPopup.style.opacity = "0";
      setTimeout(() => {
        notifPopup.classList.remove("show");
        notifPopup.style.display = "none";
      }, 300);
    }, 1500);
  }

  popupBtn.addEventListener("click", function() {
    const qty = parseInt(qtyInput.value);
    const action = this.dataset.action;

    if (qty > maxStock) {
      showNotif("Jumlah melebihi stok!");
      return;
    }

    if (action === 'beli') {
    // langsung ke checkout
    window.location.href = `/checkout-now?product_id=${currentProduct.id}&qty=${qty}`;
    } else {
    // kirim AJAX ke controller
    fetch('/cart/add', {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
        product_id: currentProduct.id,
        qty: qty
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
        showNotif(data.success, 'sucess');
        } else {
        showNotif(data.error || 'Gagal menambahkan produk ke keranjang', 'error');
        }
    })
    .catch(() => {
        showNotif('Terjadi kesalahan. Silakan coba lagi.', 'error');
    });
    }

    closePopup();
  });
});
</script>
