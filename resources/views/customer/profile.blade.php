@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp

@section('title', 'Kelola Profil')
@section('body-class', 'profile-page')

@section('content')
  <!-- Kembali -->
  <div class="back">
    <img src="{{ asset('assets/back.svg') }}" alt="Kembali" />
    <a href="{{ url('/') }}" style="text-decoration: none;color:black">Kembali</a>
  </div>

  <div class="profile-container">
    <div class="profile-content">
      
      <!-- Form -->
      <div class="profile-form">
        <h2>Profile saya</h2>
        <hr>
        <form method="POST" action="{{ url('/profile') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="action" value="update_info">
          <label>Nama</label>
          <input type="text" name="name" value="{{ $user->name }}">

          <label>Email</label>
          <input type="email" name="email" value="{{ $user->email }}">

          <label>Nomor Telepon</label>
          <input type="text" name="telepon" value="{{ $user->phone }}">

          <button type="submit" class="btn-save">Simpan Perubahan</button>
        </form>
        <h2>Alamat Utama</h2>
        <hr>
        <form method="POST" action="{{ url('/profile/address') }}">
          @csrf

          @php
            $alamat = $user->mainAddress;
          @endphp

          <label>Nama Penerima</label>
          <input type="text" name="nama_penerima" value="{{ $alamat->nama_penerima ?? '' }}">

          <label>Nomor Telepon</label>
          <input type="text" name="no_hp" value="{{ $alamat->no_hp ?? '' }}">

          <label>Alamat Lengkap</label>
          <textarea name="alamat_lengkap" rows="3">{{ $alamat->alamat_lengkap ?? '' }}</textarea>

          <button type="submit" class="btn-save">Simpan Alamat</button>
        </form>
      </div>

      <!-- Edit Foto -->
       <form method="POST" action="{{ url('/profile') }}" enctype="multipart/form-data" id="photoUploadForm">
        @csrf
        <input type="hidden" name="action" value="update_photo">
        
        @php
            $profilePicture = $user->profile_picture;

            // Kalau URL (misalnya dari Google)
            if (Str::startsWith($profilePicture, ['http://', 'https://'])) {
                $profileUrl = $profilePicture;
            } else {
                // Kalau gambar lokal
                $profileUrl = asset($profilePicture ?? 'assets/profile.jpg');
            }
        @endphp

        <div class="profile-photo">
            <h3>Edit Profile</h3>
            <img src="{{ $profileUrl }}" alt="Foto Profil" class="avatar">

            <!-- Custom file input -->
            <label for="fileInput" class="btn-upload">Pilih Gambar</label>
            <input type="file" name="profile_picture" id="fileInput" style="display: none;">
        </div>
        </form>



    <script>
    document.getElementById('fileInput').addEventListener('change', function () {
        document.getElementById('photoUploadForm').submit();
    });
    </script>


    </div>
  </div>
  <style>
    .profile-form textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-family: inherit;
    }
  </style>
@endsection
