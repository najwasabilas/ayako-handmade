@extends('layouts.admin')

@section('title', 'Kelola Pesanan')

@section('content')
<div class="orders-container">
    <h2>Kelola Pesanan</h2>

    <div class="table-container-fixed">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ route('admin.orders', [
                            'sort' => 'id',
                            'direction' => ($sort === 'id' && $direction === 'asc') ? 'desc' : 'asc'
                        ]) }}">
                            Order ID
                            @if ($sort === 'id')
                                <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>

                    <th>
                        <a href="{{ route('admin.orders', [
                            'sort' => 'created_at',
                            'direction' => ($sort === 'created_at' && $direction === 'asc') ? 'desc' : 'asc'
                        ]) }}">
                            Tanggal
                            @if ($sort === 'created_at')
                                <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>

                    <th>
                        <a href="{{ route('admin.orders', [
                            'sort' => 'users.name',
                            'direction' => ($sort === 'users.name' && $direction === 'asc') ? 'desc' : 'asc'
                        ]) }}">
                            Pelanggan
                            @if ($sort === 'users.name')
                                <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>

                    <th>
                        <a href="{{ route('admin.orders', [
                            'sort' => 'alamat',
                            'direction' => ($sort === 'alamat' && $direction === 'asc') ? 'desc' : 'asc'
                        ]) }}">
                            Alamat
                            @if ($sort === 'alamat')
                                <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>

                    <th>
                        <a href="{{ route('admin.orders', [
                            'sort' => 'produk',
                            'direction' => ($sort === 'produk' && $direction === 'asc') ? 'desc' : 'asc'
                        ]) }}">
                            Nama Produk
                        </a>
                    </th>

                    <th>
                        <a href="{{ route('admin.orders', [
                            'sort' => 'jumlah',
                            'direction' => ($sort === 'jumlah' && $direction === 'asc') ? 'desc' : 'asc'
                        ]) }}">
                            Jumlah
                        </a>
                    </th>

                    <th>
                        <a href="{{ route('admin.orders', [
                            'sort' => 'total',
                            'direction' => ($sort === 'total' && $direction === 'asc') ? 'desc' : 'asc'
                        ]) }}">
                            Total
                            @if ($sort === 'total')
                                <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>

                    <th>
                        <a href="{{ route('admin.orders', [
                            'sort' => 'status',
                            'direction' => ($sort === 'status' && $direction === 'asc') ? 'desc' : 'asc'
                        ]) }}">
                            Status
                            @if ($sort === 'status')
                                <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>

                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>ORD{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td><strong>{{ $order->user->name ?? '-' }}</strong></td>
                        <td>{{ $order->alamat }}</td>
                        <td>{{ optional($order->items->first()->product)->nama ?? '-' }}</td>
                        <td>{{ $order->items->sum('qty') }}</td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="status 
                                {{ $order->status === 'Dikirim' ? 'status-sent' :
                                ($order->status === 'Dikemas' ? 'status-pending' :
                                ($order->status === 'Selesai' ? 'status-shipped' :
                                ($order->status === 'Belum Dibayar' ? 'status-unpaid' :
                                ($order->status === 'Dibatalkan' ? 'status-cancelled' : '')))) }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>
                            <button 
                                class="edit-btn" 
                                data-id="{{ $order->id }}"
                                data-status="{{ $order->status }}">
                                <i class="fas fa-pen"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-container">
            {{ $orders->appends(request()->query())->links('vendor.pagination.ayako') }}
        </div>
    </div>
</div>

<!-- 🔹 POPUP EDIT STATUS -->
<div id="statusModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <button class="close-modal">&times;</button>
        <h3>Update Status Pesanan</h3>
        <p>Ubah status pesanan dengan nomor ID <span id="orderIdText" class="order-id">ORDxxxx</span></p>
        
        <form id="updateStatusForm" method="POST" action="{{ route('orders.updateStatus') }}">
            @csrf
            <input type="hidden" name="order_id" id="orderIdInput">
            
            <label for="statusSelect">Status Baru</label>
            <select name="status" id="statusSelect" required>
                <option value="Belum Dibayar">Belum Dibayar</option>
                <option value="Dikemas">Dikemas</option>
                <option value="Dikirim">Dikirim</option>
                <option value="Selesai">Selesai</option>
                <option value="Dibatalkan">Dibatalkan</option>
            </select>

            <button type="submit" class="save-btn">Simpan</button>
        </form>
    </div>
</div>

<!-- 🔹 SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('statusModal');
    const closeModal = document.querySelector('.close-modal');
    const orderIdText = document.getElementById('orderIdText');
    const orderIdInput = document.getElementById('orderIdInput');
    const statusSelect = document.getElementById('statusSelect');

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const status = this.dataset.status;

            orderIdText.textContent = `ORD${id.toString().padStart(4, '0')}`;
            orderIdInput.value = id;
            statusSelect.value = status;

            modal.style.display = 'flex';
        });
    });

    closeModal.addEventListener('click', () => modal.style.display = 'none');

    window.addEventListener('click', function(e) {
        if (e.target === modal) modal.style.display = 'none';
    });
});
</script>

<style>
/* ======================== BASE STYLING ======================== */
.orders-container {
    font-family: 'Poppins', sans-serif;
    color: #3b2e2a;
    min-height: 100vh;
}

.orders-container h2 {
    font-weight: 700;
    font-size: 1.4rem;
    margin-bottom: 20px;
}

/* ======================== TABLE ======================== */
.table-container-fixed {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.08);
    overflow: hidden;
    width: 100%;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    font-size: 13px;
}

.orders-table thead {
    background-color: #fff6e9;
    font-weight: 600;
}

.orders-table th, .orders-table td {
    padding: 10px 14px;
    text-align: left;
    border-bottom: 1px solid #eaeaea;
}

.orders-table tbody tr:nth-child(even) {
    background-color: #fffdf9;
}

.orders-table th:nth-child(1), .orders-table td:nth-child(1) { width: 7%; }   /* Order ID */
.orders-table th:nth-child(2), .orders-table td:nth-child(2) { width: 10%; }  /* Tanggal */
.orders-table th:nth-child(3), .orders-table td:nth-child(3) { width: 15%; }  /* Pelanggan */
.orders-table th:nth-child(4), .orders-table td:nth-child(4) { width: 20%; }  /* Alamat */
.orders-table th:nth-child(5), .orders-table td:nth-child(5) { width: 15%; }  /* Produk */
.orders-table th:nth-child(6), .orders-table td:nth-child(6) { width: 5%; }   /* Jumlah */
.orders-table th:nth-child(7), .orders-table td:nth-child(7) { width: 12%; }  /* Total */
.orders-table th:nth-child(8), .orders-table td:nth-child(8) { width: 13%; }  /* Status */
.orders-table th:nth-child(9), .orders-table td:nth-child(9) { width: 3%; }   /* Tombol */

/* ======================== STATUS ======================== */
.status {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.status-sent { background-color: #d9fdd3; color: #2a7028; }
.status-shipped { background-color: #d9fdd3; color: #123711ff; }
.status-pending { background-color: #ffecc4; color: #a56700; }
.status-unpaid { background-color: #ffd7d7; color: #a22a2a; }
.status-cancelled {
    background-color: #b0b0b0;
    color: #3a3a3a;
}


/* ======================== EDIT BUTTON ======================== */
.edit-btn {
    border: none;
    background: transparent;
    cursor: pointer;
    color: #5c4033;
    font-size: 0.9rem;
    transition: 0.3s;
}

.edit-btn:hover { color: #d69d5c; }

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

/* ======================== MODAL ======================== */
.modal-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(59, 46, 42, 0.35);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
}

.modal-box {
    background: #fff;
    border-radius: 15px;
    padding: 25px 30px;
    width: 380px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    position: relative;
    text-align: left;
}

.modal-box h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #5a3b23;
    margin-bottom: 8px;
}

.modal-box p {
    font-size: 13px;
    color: #8a7c6f;
    margin-bottom: 20px;
}

.modal-box label {
    font-weight: 600;
    font-size: 13px;
    color: #5a3b23;
    display: block;
    margin-bottom: 6px;
}

.modal-box select {
    width: 100%;
    padding: 10px;
    border: 1px solid #d8c8b3;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    color: #3b2e2a;
    background-color: #fff;
    margin-bottom: 25px;
    outline: none;
}

.modal-box .save-btn {
    background-color: #a8672c;
    color: #fff;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    float: right;
    transition: 0.3s;
}

.modal-box .save-btn:hover {
    background-color: #8c5323;
}

.close-modal {
    position: absolute;
    top: 12px;
    right: 15px;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #6b5b4a;
    transition: 0.3s;
}

.close-modal:hover { color: #a8672c; }

.order-id {
    color: #d27b26;
    font-weight: 600;
}

.orders-table th a {
    color: #3b2e2a;
    text-decoration: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.orders-table th a:hover {
    color: #d69d5c;
}

</style>
@endsection
