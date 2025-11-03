@extends('layouts.admin')

@section('content')
<div class="fabric-container">
    <h2>Kelola Fabric</h2>
    <div class="summary-cards">
        <div class="card"><i class="fas fa-box"></i><p>Total Fabric</p><h3>{{ $totalFabric }}</h3></div>
        <div class="card"><i class="fas fa-tags"></i><p>Kategori</p><h3>{{ $totalKategori }}</h3></div>
    </div>

    <div class="fabric-controls">
        <form method="GET" action="{{ route('admin.fabric.index') }}" class="fabric-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Cari Fabric" value="{{ request('search') }}">
            </div>

            <div class="filters">
                <select name="kategori" onchange="this.form.submit()">
                    <option value="all" {{ request('kategori') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                            {{ $kat }}
                        </option>
                    @endforeach
                </select>

                <a href="#" class="btn-add" onclick="openModal(); return false;">
                    <i class="fas fa-plus"></i> Tambah Fabric
                </a>

                <a href="{{ url('/fabric') }}" class="btn-view">
                    <i class="fas fa-eye"></i> Lihat Halaman
                </a>
            </div>
        </form>
    </div>

    <table class="fabric-table">
        <thead>
            <tr>
                <th>Nama Fabric</th>
                <th>Gambar</th>
                <th>Kategori</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($fabrics as $fabric)
                <tr>
                    <td>{{ $fabric->nama }}</td>
                    <td>
                        @if($fabric->gambar)
                            <img src="{{ asset('assets/fabric/images/'.$fabric->gambar) }}" alt="{{ $fabric->nama }}" class="fabric-img">
                        @else
                            <span class="no-img">Tidak ada gambar</span>
                        @endif
                    </td>
                    <td><span class="badge">{{ $fabric->kategori }}</span></td>
                    <td class="aksi">
                        <button class="btn-edit" onclick='openModal(true, @json($fabric))'> 
                            <i class="fas fa-pen"></i>
                        </button>
                        <form action="{{ route('admin.fabric.destroy', $fabric) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" onclick="return confirm('Yakin ingin menghapus fabric ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-container">
        {{ $fabrics->appends(request()->query())->links('vendor.pagination.ayako') }}
    </div>

    <div id="fabricModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle">Tambah Fabric</h3>
        <form id="fabricForm" action="{{ route('admin.fabric.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="fabric_id" name="fabric_id">

            <label>Nama Fabric</label>
            <input type="text" name="nama" id="nama" placeholder="Masukkan nama fabric" required>

            <label>Kategori</label>
            <select name="kategori" id="kategoriSelect" onchange="toggleKategoriBaru(this)">
                @foreach($kategoris as $kat)
                    <option value="{{ $kat }}">{{ $kat }}</option>
                @endforeach
                <option value="__new__">+ Tambah Kategori Baru</option>
            </select>

            {{-- Input tersembunyi untuk kategori baru --}}
            <div id="kategoriBaruWrapper" class="hidden">
                <input type="text" name="kategori_baru" id="kategoriBaru" placeholder="Masukkan kategori baru">
            </div>

            <label>Gambar</label>
            <div class="img-preview-wrapper">
                <img id="previewImg" src="#" alt="Preview" class="hidden">
            </div>
            <input type="file" name="gambar" id="gambar" accept="image/*" onchange="previewImage(event)">

            <button type="submit" class="btn-submit">Simpan</button>
        </form>

    </div>
</div>
<script>
function toggleKategoriBaru(select) {
    const wrapper = document.getElementById('kategoriBaruWrapper');
    const input = document.getElementById('kategoriBaru');

    if (select.value === '__new__') {
        wrapper.classList.remove('hidden');
        input.required = true;
    } else {
        wrapper.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}

function openModal(edit = false, fabric = null) {
    const modal = document.getElementById('fabricModal');
    const form = document.getElementById('fabricForm');
    const title = document.getElementById('modalTitle');
    const idField = document.getElementById('fabric_id');
    const namaField = document.getElementById('nama');
    const kategoriSelect = document.getElementById('kategoriSelect');
    const kategoriBaruWrapper = document.getElementById('kategoriBaruWrapper');
    const kategoriBaruInput = document.getElementById('kategoriBaru');
    const previewImg = document.getElementById('previewImg');

    form.reset();
    const oldMethod = form.querySelector('input[name="_method"]');
    if (oldMethod) oldMethod.remove();

    kategoriBaruWrapper.classList.add('hidden');
    kategoriBaruInput.value = '';
    previewImg.classList.add('hidden');

    if (edit && fabric) {
        title.innerText = 'Edit Fabric';
        form.action = `/admin/fabric/${fabric.id}`;
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);

        idField.value = fabric.id;
        namaField.value = fabric.nama;

        // Set kategori
        const existingOption = [...kategoriSelect.options].find(opt => opt.value === fabric.kategori);
        if (existingOption) {
            kategoriSelect.value = fabric.kategori;
        } else {
            kategoriSelect.value = '__new__';
            kategoriBaruWrapper.classList.remove('hidden');
            kategoriBaruInput.value = fabric.kategori;
        }

        if (fabric.gambar) {
            previewImg.src = `/assets/fabric/images/${fabric.gambar}`;
            previewImg.classList.remove('hidden');
        }
    } else {
        title.innerText = 'Tambah Fabric';
        form.action = '{{ route("admin.fabric.store") }}';
    }

    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('fabricModal').style.display = 'none';
}

function previewImage(event) {
    const preview = document.getElementById('previewImg');
    if (event.target.files.length > 0) {
        preview.src = URL.createObjectURL(event.target.files[0]);
        preview.classList.remove('hidden');
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('fabricModal');
    if (event.target === modal) closeModal();
}
</script>


</div>

<style>
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
    .fabric-container {
    padding: 20px;
    background-color: #faf7f2;
    min-height: 100vh;
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

    /* ===== FABRIC CONTROLS ===== */
.fabric-controls {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 20px;
}

.fabric-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}

/* Search box */
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

/* Filter dropdown dan tombol */
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

/* Tombol */
.btn-add, .btn-view {
    display: flex;
    align-items: center;
    gap: 5px;
    border: none;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 13px;
    cursor: pointer;
    transition: 0.3s;
    text-decoration: none;
    color: white;
}

.btn-add {
    background: #c9954a;
}

.btn-view {
    background: #a6783a;
}

.btn-add:hover, .btn-view:hover {
    opacity: 0.9;
}



    .fabric-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    }

    .fabric-table th, .fabric-table td {
    text-align: center;
    padding: 10px;
    border-bottom: 1px solid #eee;
    }

    .fabric-table th {
    background-color: #f6f2eb;
    font-weight: bold;
    }

    .fabric-img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    }

    .badge {
    background: #f3f3f3;
    border-radius: 8px;
    padding: 3px 8px;
    font-size: 12px;
    }

    .aksi button {
    border: none;
    background: none;
    cursor: pointer;
    font-size: 16px;
    }

    .pagination-container {
    margin-top: 15px;
    text-align: center;
    }

    .btn-primary {
    background-color: #d39c3f;
    color: #fff;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    }

/* ======================== MODAL ======================== */
.modal {
    display: none;
    position: fixed;
    z-index: 99;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.3);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: #fefcf8;
    padding: 25px;
    border-radius: 12px;
    width: 400px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    position: relative;
    animation: fadeIn 0.2s ease-in-out;
}

.modal-content h3 {
    margin-bottom: 15px;
    color: #6b3e1e;
    font-weight: 700;
}

.modal-content label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: #6b3e1e;
    margin-top: 10px;
    margin-bottom: 4px;
}

.modal-content input[type="text"],
.modal-content input[type="file"] {
    width: 94%;
    padding: 8px 10px;
    border: 1px solid #d4b89a;
    border-radius: 8px;
    font-size: 14px;
    background-color: #fffdf8;
}

.img-preview-wrapper {
    margin: 10px 0;
    display: flex;
    justify-content: center;
}

#previewImg {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #e4d1b0;
}

.hidden { display: none; }

.btn-submit {
    background-color: #a46b2e;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 15px;
    cursor: pointer;
    margin-top: 15px;
    float: right;
}

.btn-submit:hover {
    background-color: #8d5c26;
}

.close {
    position: absolute;
    top: 12px;
    right: 15px;
    color: #b27d45;
    font-size: 22px;
    cursor: pointer;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
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
#kategoriBaruWrapper {
    margin-top: 8px;
}

#kategoriSelect {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #d4b89a;
    border-radius: 8px;
    background: #fffdf8;
    font-size: 14px;
}

</style>
@endsection
