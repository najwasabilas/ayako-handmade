@extends('layouts.app')

@section('content')
<style>
    .order-container {
        max-width: 900px;
        margin: 40px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .tabs {
        display: flex;
        justify-content: center;
        background: #FAF6EF;
        padding: 20px 0;
    }
    .tabs a {
        border: 1px solid #A8682A;
        border-radius: 20px;
        padding: 6px 20px;
        margin: 0 8px;
        text-decoration: none;
        color: #000;
        font-weight: 500;
    }
    .tabs a.active {
        background: #A8682A;
        color: #fff;
    }
    .order-card {
        border-radius: 8px;
        overflow: hidden;
        margin: 20px;
        background: #fff;
        border: 1px solid #E7D5C4;
    }
    .order-header {
        background: #E7D5C4;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 500;
    }
    .order-item {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #EEE;
    }
    .order-item:last-child {
        border-bottom: none;
    }
    .order-item img {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        background: #ccc;
        object-fit: cover;
        margin-right: 15px;
    }
    .order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-top: 1px solid #EEE;
    }
    .order-footer .total {
        font-size: 16px;
        font-weight: 600;
        color: #3F2A1C;
    }
    .btn-wa {
        border: 1px solid #A8682A;
        border-radius: 8px;
        padding: 6px 14px;
        text-decoration: none;
        color: #3F2A1C;
        font-weight: 500;
        background: #fff;
    }
    .btn-wa:hover {
        background: #A8682A;
        color: #fff;
    }
</style>

<div class="order-container">
    <div class="tabs">
        @foreach($statuses as $s)
            <a href="{{ route('orders.index', ['status' => $s]) }}"
               class="{{ $status == $s ? 'active' : '' }}">
               {{ $s }}
            </a>
        @endforeach
    </div>

    @forelse($orders as $order)
        <div class="order-card">
            <div class="order-header">
                <div>No. Pesanan <strong>{{ 'ORD' . $order->id }}</strong></div>
                <span>{{ $order->status }}</span>
            </div>

            @foreach($order->items as $item)
                <div class="order-item">
                    <img src="{{ asset('assets/catalog/images/' . ($item->product->images->first()->gambar ?? 'no-image.jpg')) }}" alt="Product">
                    <div>
                        <div><strong>{{ $item->product->nama_produk }}</strong></div>
                        <small>x{{ $item->qty }}</small>
                    </div>
                    <div style="margin-left:auto;">Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
                </div>
            @endforeach

            <div class="order-footer">
                <div class="total">Total Pesanan: Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                <a href="https://wa.me/6282284471620?text=Halo%20Ayako,%20saya%20ingin%20menanyakan%20status%20pesanan%20dengan%20ID%20{{ $order->id }}%20atas%20nama%20{{ urlencode($order->user->name ?? '') }}%20dan%20alamat%20{{ urlencode($order->alamat ?? '') }}"
                   target="_blank" class="btn-wa">
                    Hubungi Penjual
                </a>
            </div>
        </div>
    @empty
        <div style="text-align:center; padding: 50px; color:#777;">
            Belum ada pesanan pada status ini.
        </div>
    @endforelse
</div>
@endsection
