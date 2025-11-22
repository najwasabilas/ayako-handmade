<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(12);

        $categories = Product::select('kategori')->distinct()->pluck('kategori');

        return view('katalog', compact('products', 'categories'));
    }
    
    public function loadMore(Request $request)
    {
        $query = Product::query();

        if ($request->filled('kategori') && $request->kategori !== '' && $request->kategori !== 'all') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $products = $query->with('images')->skip($request->skip)->take(12)->get();

        return response()->json([
            'products' => $products,
            'next_skip' => $request->skip + 12,
        ]);
    }


}
