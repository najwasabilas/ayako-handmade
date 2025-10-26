<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use DB;
use PDF;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Ambil data dasar
        $totalPendapatan = Order::where('status', 'selesai')->sum('total');
        $totalPesanan = Order::count();
        $produkTerjual = OrderItem::sum('qty');
        $pelangganUnik = User::whereHas('orders')->count();

        // Data revenue bulanan
        $revenueBulanan = Order::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('SUM(total) as total')
            )
            ->where('status', 'selesai')
            ->whereYear('created_at', date('Y'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $labels = [];
        $dataRevenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->locale('id')->monthName;
            $dataRevenue[] = $revenueBulanan->firstWhere('bulan', $i)->total ?? 0;
        }

        // Hitung growth (perbandingan bulan ini dan sebelumnya)
        $bulanIni = Carbon::now()->month;
        $bulanLalu = $bulanIni - 1;
        $pendapatanBulanIni = $revenueBulanan->firstWhere('bulan', $bulanIni)->total ?? 0;
        $pendapatanBulanLalu = $revenueBulanan->firstWhere('bulan', $bulanLalu)->total ?? 0;
        $growth = $pendapatanBulanLalu > 0
            ? (($pendapatanBulanIni - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100
            : 0;

        // Kirim ke view
        return view('admin.dashboard', [
            'totalPendapatan' => $totalPendapatan,
            'totalPesanan' => $totalPesanan,
            'produkTerjual' => $produkTerjual,
            'pelangganUnik' => $pelangganUnik,
            'labels' => $labels,
            'dataRevenue' => $dataRevenue,
            'growth' => round($growth, 2),
        ]);
    }
    public function exportPdf()
    {
        $orders = Order::with('user')->where('status', 'selesai')->get();

        $totalPendapatan = $orders->sum('total');
        $totalPesanan = $orders->count();
        $produkTerjual = \App\Models\OrderItem::sum('qty');
        $pelangganUnik = \App\Models\User::whereHas('orders')->count();

        $pdf = PDF::loadView('admin.laporan-pdf', [
            'orders' => $orders,
            'totalPendapatan' => $totalPendapatan,
            'totalPesanan' => $totalPesanan,
            'produkTerjual' => $produkTerjual,
            'pelangganUnik' => $pelangganUnik,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan_penjualan_' . date('Y_m_d') . '.pdf');
    }
}
