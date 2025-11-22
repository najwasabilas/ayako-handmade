<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;    

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile');
    }
    public function updateAddress(Request $request)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat_lengkap' => 'required|string|max:1000',
        ]);

        $user = auth()->user();

        $alamatUtama = $user->addresses()->where('utama', true)->first();
       
        if ($alamatUtama) {
            // Jika sudah ada alamat utama, update datanya
            $alamatUtama->update([
                'nama_penerima' => $request->nama_penerima,
                'no_hp' => $request->no_hp,
                'alamat_lengkap' => $request->alamat_lengkap,
            ]);
        } else {
            // Jika belum ada, buat baru sebagai alamat utama
            $user->addresses()->create([
                'nama_penerima' => $request->nama_penerima,
                'no_hp' => $request->no_hp,
                'alamat_lengkap' => $request->alamat_lengkap,
                'utama' => true,
            ]);
        }

        return back()->with('success', 'Alamat utama berhasil diperbarui.');
    }
    public function deleteAddress($id)
    {
        $address = Address::findOrFail($id);

        // Pastikan milik user yang login
        if ($address->user_id !== Auth::id()) {
            return back()->with('error', 'Tidak diizinkan menghapus alamat ini.');
        }

        $address->delete();

        return back()->with('success', 'Alamat berhasil dihapus.');
    }
}