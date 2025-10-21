<?php

namespace App\Http\Controllers;

use App\Models\Fabric;
use Illuminate\Http\Request;

class FabricController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->get('kategori', 'all');
        $search = $request->get('search');

        $query = Fabric::query();

        if ($kategori !== 'all') {
            $query->where('kategori', $kategori);
        }

        if ($search) {
            $query->where('nama', 'like', "%$search%");
        }

        $fabrics = $query->latest()->paginate(12);

        $categories = Fabric::select('kategori')
            ->distinct()
            ->whereNotNull('kategori')
            ->pluck('kategori');

        return view('fabric', compact('fabrics', 'categories', 'kategori'));
    }
}
