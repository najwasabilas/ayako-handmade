@extends('layouts.admin')

@section('title', isset($product) ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<div class="add-product-container">
    <div class="header">
        <a href="{{ route('admin.products.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
        <h2>{{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}</h2>
    </div>

    <form 
        action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}" 
        method="POST" enctype="multipart/form-data" 
        class="product-form">
        @csrf
        @if(isset($product))
            @method('PUT')
        @endif

        <div class="form-section">
            <h3>Informasi Produk</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Foto Produk*</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*">
                    <p class="hint">Maks. 5 gambar</p>
                    <div id="preview-existing" class="flex gap-3 mt-3 flex-wrap">
                        @foreach($product->images as $img)
                            <div class="img-wrapper">
                                <img src="{{ asset('assets/catalog/images/' . $img->gambar) }}" class="preview-img">

                                <button type="button" class="delete-image-btn"
                                    onclick="openDeleteImageModal({{ $img->id }}, this)">
                                    &times;
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div id="preview-new" class="flex gap-3 mt-3 flex-wrap"></div>


                </div>

                <div class="form-group">
                    <label>Nama Produk*</label>
                    <input type="text" name="nama" value="{{ old('nama', $product->nama ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Kategori*</label>
                    <select name="kategori" id="kategori-select" onchange="toggleCustomCategory(this)">
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoriList as $kategori)
                            <option value="{{ $kategori }}" 
                                {{ old('kategori', $product->kategori ?? '') == $kategori ? 'selected' : '' }}>
                                {{ $kategori }}
                            </option>
                        @endforeach
                        <option value="new">+ Tambah kategori baru</option>
                    </select>
                    <input type="text" name="kategori_baru" id="kategori-baru" placeholder="Kategori baru" style="display:none; margin-top:8px;">
                </div>

                <div class="form-group">
                    <label>Harga*</label>
                    <input type="number" name="harga" value="{{ old('harga', $product->harga ?? '') }}" required>
                </div>

                <div class="form-group full">
                    <label>Deskripsi*</label>
                    <textarea name="deskripsi" required>{{ old('deskripsi', $product->deskripsi ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Stok*</label>
                    <input type="number" name="stok" value="{{ old('stok', $product->stok ?? '') }}" required>
                </div>
            </div>

            <button type="submit" class="btn-save">Simpan</button>
        </div>
    </form>
 <!-- MODAL HAPUS GAMBAR -->
<div id="deleteImageModal" class="modal">
    <div class="modal-box">
        <h3 class="modal-title">Hapus Gambar?</h3>

        <p class="modal-message">
            Gambar yang dihapus tidak dapat dipulihkan.
        </p>

        <div class="modal-actions">
            <button class="modal-btn cancel" onclick="closeDeleteImageModal()">Batal</button>
            <button class="modal-btn delete" id="confirmDeleteImageBtn">Hapus</button>
        </div>
    </div>
</div>

<!-- MODAL ALERT -->
<div id="alertModal" class="modal">
    <div class="modal-box">
        <h3 class="modal-title">Peringatan</h3>
        <p class="modal-message" id="alertMessage"></p>

        <button class="modal-btn" onclick="closeAlertModal()">OK</button>
    </div>
</div>
<style>
body {
    background-color: #f9f4ec;
    font-family: 'Poppins', sans-serif;
}
.add-product-container {
    padding-bottom: 40px;
}

.header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.back-btn {
    color: #a6783a;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.add-product-container h2 {
    font-weight: 700;
    font-size: 1.3rem;
}

.form-section {
    background: #fff;
    border: 1px solid #e1d4c3;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.form-section h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px 25px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full {
    grid-column: span 2;
}

.form-group label {
    font-weight: 500;
    margin-bottom: 6px;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 10px;
    border: 1px solid #d3bfa8;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
}

.form-group textarea {
    height: 100px;
    resize: none;
}

.hint {
    font-size: 12px;
    color: #9c8a7c;
    margin-top: 5px;
}

.btn-save {
    background: #a86b2f;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 25px;
    font-size: 14px;
    float: right;
    margin-top: 25px;
    cursor: pointer;
    transition: 0.3s;
}

.btn-save:hover {
    background: #8d5926;
}

/* --- Preview Gambar --- */
#preview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

#preview img {
    width: 90px;               /* ukuran kecil */
    height: 90px;              /* kotak proporsional */
    object-fit: cover;         /* supaya gambar tidak gepeng */
    border-radius: 8px;
    border: 1px solid #d3bfa8;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

#preview img:hover {
    transform: scale(1.05);
}

.img-wrapper {
    position: relative;
    display: inline-block;
}

.preview-img {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #d3bfa8;
}

.delete-image-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4d4d;
    border: none;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 14px;
    cursor: pointer;
}
/* ==== UNIVERSAL MODAL ==== */
.modal {
    display: none;
    position: fixed;
    z-index: 99999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.45);
    justify-content: center;
    align-items: center;
}

.modal-box {
    width: 320px;
    background: #fff;
    padding: 22px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    animation: fadeIn 0.25s ease;
}

@keyframes fadeIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.modal-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #5a4028;
}

.modal-message {
    font-size: 14px;
    color: #6d5440;
}

.modal-actions {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 12px;
}

/* Buttons */
.modal-btn {
    padding: 8px 20px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    border: none;
    font-weight: 600;
}

.modal-btn.cancel {
    background: #d4c6b6;
    color: #4a3c2f;
}

.modal-btn.delete {
    background: #c0392b;
    color: white;
}

.modal-btn:hover {
    opacity: .85;
}

</style>

<script>
    let jumlahGambarLama = {{ isset($product) ? $product->images->count() : 0 }};
</script>

<script>
    function openAlertModal(message) {
        document.getElementById("alertMessage").innerText = message;
        document.getElementById("alertModal").style.display = "flex";
    }

    function closeAlertModal() {
        document.getElementById("alertModal").style.display = "none";
    }
    document.getElementById('images').addEventListener('change', function(e) {

        const previewNew = document.getElementById('preview-new');
        previewNew.innerHTML = '';

        let files = Array.from(e.target.files);

        const total = jumlahGambarLama + files.length;

        if (total > 5) {
            openAlertModal(`Maksimal 5 gambar! Saat ini sudah ada ${jumlahGambarLama} gambar lama.`);
            e.target.value = "";
            return;
        }

        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = ev => {
                const img = document.createElement('img');
                img.src = ev.target.result;
                img.classList.add('preview-img');
                previewNew.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });


function toggleCustomCategory(select) {
    const input = document.getElementById('kategori-baru');
    if (select.value === 'new') {
        input.style.display = 'block';
        input.required = true;
        select.removeAttribute('name');
        input.name = 'kategori';
    } else {
        input.style.display = 'none';
        input.required = false;
        select.name = 'kategori';
        input.removeAttribute('name');
    }
}
</script>
<script>
let imageToDelete = null;
let buttonRef = null;

function openDeleteImageModal(id, btn) {
    imageToDelete = id;
    buttonRef = btn;
    document.getElementById('deleteImageModal').style.display = 'flex';
}

function closeDeleteImageModal() {
    document.getElementById('deleteImageModal').style.display = 'none';
    imageToDelete = null;
    buttonRef = null;
}

document.getElementById('confirmDeleteImageBtn').addEventListener('click', function () {

    fetch(`/admin/products/delete-image/${imageToDelete}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (buttonRef && buttonRef.closest(".img-wrapper")) {
                buttonRef.closest(".img-wrapper").remove();
            }

            if (typeof jumlahGambarLama !== "undefined") {
                jumlahGambarLama--;
            }
            location.reload();
        }
    });

    closeDeleteImageModal();
});
</script>
@endsection
