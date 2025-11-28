@extends('layouts.admin')
@section('title', 'Laporan Penjualan')

@section('content')
<div class="dashboard-header">
    <h1>Laporan Penjualan</h1>
    <div class="filters">
        <a href="{{ route('admin.export.pdf') }}" class="download-btn" style="text-decoration: none">⬇ Unduh Laporan</a>
    </div>
</div>

<div class="cards">
    <div class="card">
        <h3>Total Pendapatan</h3>
        <p class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
    </div>

    <div class="card">
        <h3>Total Pesanan</h3>
        <p class="value">{{ $totalPesanan }}</p>
    </div>

    <div class="card">
        <h3>Produk Terjual</h3>
        <p class="value">{{ $produkTerjual }}</p>
    </div>

    <div class="card">
        <h3>Repeat Order</h3>
        <p class="value">{{ $pelangganUnik }}</p>
    </div>
</div>

<div class="chart-section">
    <h2>Total Revenue (Tahun {{ date('Y') }})</h2>
    <canvas id="revenueChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($dataRevenue) !!},
            borderWidth: 3,
            borderColor: '#4B2C20',
            backgroundColor: 'rgba(75,44,32,0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

<style>
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.filters select, .download-btn {
    padding: 10px;
    border-radius: 8px;
    border: none;
    background-color: #D49C47;
    color: white;
    font-weight: bold;
    cursor: pointer;
}
.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 30px;
}
.card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.value {
    font-size: 24px;
    color: #4B2C20;
}
.growth.up {
    color: green;
}
.growth.down {
    color: red;
}
.chart-section {
    background: white;
    border-radius: 12px;
    margin-top: 30px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
</style>
@endsection
