<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #4B2C20; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; text-align: left; }
        .summary { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan</h1>
    <p>Tanggal Cetak: {{ date('d F Y') }}</p>

    <div class="summary">
        <strong>Total Pendapatan:</strong> Rp {{ number_format($totalPendapatan,0,',','.') }} <br>
        <strong>Total Pesanan:</strong> {{ $totalPesanan }} <br>
        <strong>Produk Terjual:</strong> {{ $produkTerjual }} <br>
        <strong>Pelanggan Unik:</strong> {{ $pelangganUnik }} <br>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Pelanggan</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $i => $order)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>Rp {{ number_format($order->total,0,',','.') }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
