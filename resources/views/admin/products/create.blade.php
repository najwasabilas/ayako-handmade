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
                    <div id="preview" class="flex gap-3 mt-3 flex-wrap"></div>
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

</style>

<script>
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('preview');
    preview.innerHTML = '';
    const files = Array.from(e.target.files).slice(0, 5);
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = ev => {
            const img = document.createElement('img');
            img.src = ev.target.result;
            img.classList.add('w-24', 'h-24', 'object-cover', 'rounded-lg', 'border');
            preview.appendChild(img);
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
@endsection
