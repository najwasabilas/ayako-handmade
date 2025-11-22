@extends('layouts.admin')

@section('title', 'Kelola Produk')

@section('content')
<div class="products-container">
    <h2>Kelola Produk</h2>

    <div class="summary-cards">
        <div class="card"><i class="fas fa-box"></i><p>Total Produk</p><h3>{{ $totalProduk }}</h3></div>
        <div class="card"><i class="fas fa-box-open"></i><p>Stok Habis</p><h3>{{ $stokHabis }}</h3></div>
        <div class="card"><i class="fas fa-tags"></i><p>Kategori</p><h3>{{ $kategoriCount }}</h3></div>
    </div>

    <div class="table-container-fixed">
        <form method="GET" class="table-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari Produk">
            </div>

            <div class="filters">
                <select name="category" onchange="this.form.submit()">
                    <option value="all" {{ $category === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach ($kategoriList as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                <a href="{{route('admin.products.create') }}" style="text-decoration:none"  class="btn-add"><i class="fas fa-plus"></i> Tambah Produk</a>
                <a href="{{ url('/katalog') }}" style="text-decoration:none" class="btn-view"><i class="fas fa-eye"></i> Lihat Halaman</a>
            </div>
        </form>

        <table class="products-table">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Deskripsi</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->nama }}</td>
                        <td>{{ Str::limit($product->deskripsi, 50) }}</td>
                        <td>{{ $product->stok }}</td>
                        <td>{{ $product->kategori }}</td>
                        <td>Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}" style="text-decoration:none" class="edit-btn"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-delete" onclick="openDeleteModal({{ $product->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">Tidak ada produk ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-container">
            {{ $products->appends(request()->query())->links('vendor.pagination.ayako') }}
        </div>
    </div>
    <form id="deleteProductForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content" style="max-width: 320px; text-align:center;">
        <span class="close" onclick="closeDeleteModal()">&times;</span>
        
        <h3 style="margin-bottom:10px;">Hapus Produk?</h3>
        <p style="font-size:14px; color:#6b3e1e; margin-bottom:20px;">
            Produk yang dihapus tidak dapat dikembalikan.
        </p>

        <div style="display:flex; justify-content:center; gap:12px;">
            <button type="button" class="btn-submit" 
                    onclick="closeDeleteModal()" 
                    style="background:#c1b09b;">
                Batal
            </button>

            <button type="button" class="btn-submit" id="confirmDeleteBtn"
                    style="background:#c0392b;">
                Hapus
            </button>
        </div>
    </div>
</div>

<script>
    let deleteProductId = null;

    // buka modal
    function openDeleteModal(id) {
        deleteProductId = id;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    // tutup modal
    function closeDeleteModal() {
        deleteProductId = null;
        document.getElementById('deleteModal').style.display = 'none';
    }

    // submit penghapusan
    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (!deleteProductId) return;

        const form = document.getElementById('deleteProductForm');
        form.action = `/admin/products/${deleteProductId}`; 
        form.submit();
    });
</script>


<style>
#deleteModal .btn-submit {
    padding: 8px 14px;
    border-radius: 8px;
    border: none;
    color: white;
    cursor: pointer;
    font-weight: 600;
}

.products-container {
    padding-bottom: 40px;
}

.products-container h2 {
    font-weight: 700;
    font-size: 1.4rem;
    margin-bottom: 20px;
}

/* summary cards */
.summary-cards {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
}

.summary-cards .card {
    flex: 1;
    background-color: #d39a46;
    color: #fff;
    padding: 15px 25px;
    border-radius: 10px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.summary-cards .card i {
    font-size: 20px;
    margin-bottom: 5px;
}

.summary-cards .card p {
    margin: 0;
    font-size: 13px;
}

.summary-cards .card h3 {
    margin: 3px 0 0;
    font-size: 20px;
    font-weight: 700;
}

/* Table container */
.table-container-fixed {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.08);
    overflow: hidden;
}

.products-table  th:nth-child(1), .products-table     td:nth-child(1) { width: 25%; } 
.products-table  th:nth-child(2), .products-table     td:nth-child(2) { width: 35%; }  
.products-table  th:nth-child(3), .products-table     td:nth-child(3) { width: 5%; }  
.products-table  th:nth-child(4), .products-table     td:nth-child(4) { width: 15%; }  
.products-table  th:nth-child(5), .products-table     td:nth-child(5) { width: 15%; }  
.products-table  th:nth-child(6), .products-table     td:nth-child(6) { width: 5%; }  

/* Toolbar */
.table-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}

/* search box */
.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 10px;
    color: #a7895d;
}

.search-box input {
    padding: 8px 30px;
    border: 1px solid #e0d3c1;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    width: 180px;
}

/* Filters */
.filters {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filters select {
    padding: 8px 12px;
    border: 1px solid #e0d3c1;
    border-radius: 8px;
    background: #fff6e9;
    color: #3b2e2a;
    font-size: 13px;
    cursor: pointer;
}

.btn-add, .btn-view {
    display: flex;
    align-items: center;
    gap: 5px;
    background: #c9954a;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 13px;
    cursor: pointer;
    transition: 0.3s;
}

.btn-view {
    background: #a6783a;
}

.btn-add:hover, .btn-view:hover {
    opacity: 0.9;
}

/* Table */
.products-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    table-layout: fixed;
}

.products-table thead {
    background-color: #fff6e9;
    font-weight: 600;
}

.products-table th, .products-table td {
    padding: 10px 14px;
    text-align: left;
    border-bottom: 1px solid #eaeaea;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.products-table tbody tr:nth-child(even) {
    background-color: #fffdf9;
}

.edit-btn {
    border: none;
    background: transparent;
    color: #5c4033;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
}

.edit-btn:hover {
    color: #d69d5c;
}

/* ======================== PAGINATION ======================== */
.pagination-container {
    display: flex;
    justify-content: flex-end;
    padding: 15px 25px;
    background: #fff;
}

.pagination {
    list-style: none;
    display: flex;
    gap: 6px;
    font-size: 13px;
}

.pagination li a, .pagination li span {
    padding: 6px 10px;
    border-radius: 6px;
    background-color: #fff6e9;
    color: #3b2e2a;
    text-decoration: none;
    transition: 0.3s;
    font-weight: 500;
}

.pagination li a:hover { background-color: #e8d3b8; }
.pagination li.active span { background-color: #c9954a; color: white; font-weight: 600; }

.btn-delete {
    background: none;
    border: none;
    color: #a22a2a;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
}

.btn-delete:hover {
    color: #e74c3c;
}
/* ================= MODAL DELETE ================= */
.modal {
    display: none; 
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: #fff;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    position: relative;
    animation: fadeIn 0.2s ease-out;
}

.modal .close {
    position: absolute;
    top: 8px;
    right: 12px;
    font-size: 22px;
    cursor: pointer;
    color: #444;
}

@keyframes fadeIn {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

</style>
@endsection
