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
}
