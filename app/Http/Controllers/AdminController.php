<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Fabric;
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
        $orders = Order::with('user')->get();

        $totalPendapatan = Order::where('status', 'selesai')->sum('total');
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
    public function orders(Request $request)
    {
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $orders = Order::with(['user', 'items.product'])
            ->orderBy($sort, $direction)
            ->paginate(10);

        return view('admin.orders', compact('orders', 'sort', 'direction'));
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:Belum Dibayar,Dikemas,Dikirim,Selesai',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
    public function products(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category', 'all');

        $query = Product::query();

        if ($search) {
            $query->where('nama', 'like', "%$search%");
        }

        if ($category !== 'all') {
            $query->where('kategori', $category);
        }

        $products = $query->paginate(10);

        // Statistik untuk card
        $totalProduk = Product::count();
        $stokHabis = Product::where('stok', 0)->count();
        $kategoriList = Product::select('kategori')->distinct()->pluck('kategori');
        $kategoriCount = $kategoriList->count();

        return view('admin.products', compact(
            'products',
            'kategoriList',
            'totalProduk',
            'stokHabis',
            'kategoriCount',
            'search',
            'category'
        ));
    }
    public function createProduct()
    {
        $kategoriList = Product::select('kategori')->distinct()->pluck('kategori');
        return view('admin.products.create', compact('kategoriList'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'deskripsi' => 'required|string',
            'stok' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Simpan produk
        $product = Product::create($validated);

        // Simpan gambar (jika ada)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // buat nama unik
                $filename = time() . '_' . $file->getClientOriginalName();

                // pindahkan file ke folder public/assets/catalog/images
                $file->move(public_path('assets/catalog/images/'), $filename);

                // simpan nama file ke database (kolom 'gambar')
                $product->images()->create(['gambar' => $filename]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function editProduct(Product $product)
    {
        $kategoriList = Product::select('kategori')->distinct()->pluck('kategori');
        return view('admin.products.edit', compact('product', 'kategoriList'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'deskripsi' => 'required|string',
            'stok' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $product->update($validated);

        if ($request->hasFile('images')) {
            foreach ($product->images as $img) {
                $filePath = public_path('assets/catalog/images/' . $img->gambar);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $img->delete();
            }

            foreach ($request->file('images') as $img) {
                $filename = time() . '_' . $img->getClientOriginalName();
                $img->move(public_path('assets/catalog/images/'), $filename);

                $product->images()->create(['gambar' => $filename]);
            }
        }



        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }
    public function fabrics(Request $request)
    {
        $search = $request->get('search');
        $kategori = $request->get('kategori', 'all');

        $query = Fabric::query();

        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        if ($kategori !== 'all') {
            $query->where('kategori', $kategori);
        }

        $fabrics = $query->paginate(6);
        $totalFabric = Fabric::count();
        $totalKategori = Fabric::distinct('kategori')->count('kategori');
        $kategoris = Fabric::select('kategori')->distinct()->pluck('kategori');

        return view('admin.fabric', compact('fabrics', 'totalFabric', 'totalKategori', 'kategoris'));
    }

    public function storeFabric(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'kategori_baru' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Gunakan kategori baru jika diisi
        if ($request->filled('kategori_baru')) {
            $validated['kategori'] = $request->kategori_baru;
        }

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/fabric/images/'), $filename);
            $validated['gambar'] = $filename;
        }

        Fabric::create([
            'nama' => $validated['nama'],
            'kategori' => $validated['kategori'],
            'gambar' => $validated['gambar'] ?? null,
        ]);

        return redirect()->route('admin.fabric.index')->with('success', 'Fabric berhasil ditambahkan!');
    }


    public function editFabric(Fabric $fabric)
    {
        return response()->json($fabric);
    }

    public function updateFabric(Request $request, Fabric $fabric)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            // hapus gambar lama
            $oldPath = public_path('assets/fabric/images/' . $fabric->gambar);
            if ($fabric->gambar && file_exists($oldPath)) {
                unlink($oldPath);
            }

            // simpan baru
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/fabric/images/'), $filename);
            $validated['gambar'] = $filename;
        }

        $fabric->update($validated);

        return redirect()->route('admin.fabric.index')->with('success', 'Fabric berhasil diperbarui!');
    }
    public function deleteFabric(Fabric $fabric)
    {
        // Cek dan hapus file gambar dari folder public/assets/fabric/images/
        if ($fabric->gambar) {
            $filePath = public_path('assets/fabric/images/' . $fabric->gambar);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus data fabric dari database
        $fabric->delete();

        return redirect()->route('admin.fabric.index')->with('success', 'Fabric berhasil dihapus!');
    }

}
