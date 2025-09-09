<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function profile() {
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if ($request->action === 'update_info') {
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:50',
                'telepon' => 'nullable|string|max:20',
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->telepon;
            $user->save();

            return back()->with('success', 'Profil berhasil diperbarui.');
        }

        if ($request->action === 'update_photo') {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:4096',
            ]);

            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('profiles');

            // Pastikan folder 'public/profiles' ada
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            // Simpan path relatif untuk digunakan di blade
            $user->profile_picture = 'profiles/' . $filename;
            $user->save();

            return back()->with('success', 'Foto profil berhasil diunggah.');
        }

        return back()->with('error', 'Aksi tidak dikenali.');
    }


}
