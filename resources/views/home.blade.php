@extends('layouts.app')

@section('title', 'Beranda | Ayako Ethnic Handbag')
@section('body-class', 'home-page')

@section('content')
  <!-- 🔹 Hero Section -->
  <section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>AYAKO</h1>
        <h2>ETHNIC HANDBAG</h2>
        <p>Ayako adalah sebuah usaha kreatif yang mengangkat nilai budaya lokal melalui produk-produk handmade seperti tas, dompet, dan aksesoris wanita khas motif etnik.</p>
        <div class="hero-buttons">
        <a href="{{ url('/katalog') }}" class="btn-primary">Belanja Sekarang</a>
        <a href="{{ url('/galeri') }}" class="btn-secondary">Pelajari Selengkapnya</a>
        </div>
    </div>
    </section>


  <!-- 🔹 Katalog Produk -->
  <section class="highlight-section">
    <h2>KATALOG PRODUK</h2>
    <div class="product-grid">
      <div class="product-card">
        <img src="{{ asset('assets/tas1.png') }}" alt="Tote Bag Coklat">
        <h3>Tote Bag Coklat Kombinasi Songket</h3>
        <p>Rp 350.000</p>
      </div>
      <div class="product-card">
        <img src="{{ asset('assets/tas2.png') }}" alt="Hobo Bag Songket">
        <h3>Hobo Bag Kombinasi Songket</h3>
        <p>Rp 300.000</p>
      </div>
      <div class="product-card">
        <img src="{{ asset('assets/tas3.png') }}" alt="Tote Bag Hijau">
        <h3>Tote Bag Songket</h3>
        <p>Rp 100.000</p>
      </div>
      <div class="product-card">
        <img src="{{ asset('assets/tas1.png') }}" alt="Tote Bag Coklat">
        <h3>Tote Bag Coklat Kombinasi Songket</h3>
        <p>Rp 350.000</p>
      </div>
      <div class="product-card">
        <img src="{{ asset('assets/tas2.png') }}" alt="Hobo Bag Songket">
        <h3>Hobo Bag Kombinasi Songket</h3>
        <p>Rp 300.000</p>
      </div>
      <div class="product-card">
        <img src="{{ asset('assets/tas3.png') }}" alt="Tote Bag Hijau">
        <h3>Tote Bag Songket</h3>
        <p>Rp 100.000</p>
      </div>
    </div>
    <a href="{{ url('/katalog') }}" class="btn-view">Lihat Lebih Banyak</a>
  </section>

      <!-- 🔹 Gambar Tengah (Pattern Background) -->
  <section class="middle-banner">
    <div class="overlay"></div>
    <div class="middle-content">
      <a href="{{ url('/katalog') }}" class="btn-secondary">See More</a>
    </div>
  </section>

  <!-- 🔹 Galeri -->
  <section class="gallery-section">
    <h2>GALERI KEGIATAN</h2>
    <div class="gallery-grid">
      <img src="{{ asset('assets/gallery1.png') }}" alt="Galeri 1">
      <img src="{{ asset('assets/gallery2.png') }}" alt="Galeri 2">
      <img src="{{ asset('assets/gallery3.png') }}" alt="Galeri 3">
      <img src="{{ asset('assets/gallery4.png') }}" alt="Galeri 4">
      <img src="{{ asset('assets/gallery5.png') }}" alt="Galeri 5">
      <img src="{{ asset('assets/gallery6.png') }}" alt="Galeri 6">
    </div>
    <a href="{{ url('/galeri') }}" class="btn-view">Lihat Lebih Banyak</a>
  </section>
@endsection
