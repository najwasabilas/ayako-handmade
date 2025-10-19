@extends('layouts.app')

@section('title', 'Fabric Pilihanmu')

@section('content')
<section class="fabric-hero">
  <img src="{{ asset('assets/fabric/banner_fabric.png') }}" alt="Hero Fabric" class="hero-img">
</section>

<div class="fabric-products">
  <div class="fabric-header">
    <form action="{{ route('fabric.index') }}" method="GET" class="search-form">
      <input type="text" name="search" placeholder="Cari fabric..." value="{{ request('search') }}">
      <button type="submit" class="search-btn">
        <i class="fas fa-search"></i>
      </button>
    </form>

    <div class="category-buttons">
      <a href="{{ route('fabric.index', ['kategori' => 'all']) }}"
         class="cat-btn {{ $kategori == 'all' ? 'active' : '' }}">Semua</a>

      @foreach($categories as $cat)
        <a href="{{ route('fabric.index', ['kategori' => $cat]) }}"
           class="cat-btn {{ $kategori == $cat ? 'active' : '' }}">
          {{ ucfirst($cat) }}
        </a>
      @endforeach
    </div>
  </div>

  <h2 class="section-title">Pilih Fabricmu Disini</h2>

  <div class="products-grid-fabric">
    @forelse($fabrics as $fabric)
      <div class="fabric-card">
        <img src="{{ asset('assets/fabric/images/'.$fabric->gambar) }}" alt="{{ $fabric->nama }}">
        <div class="fabric-info">
          <h3>{{ $fabric->nama }}</h3>
          <a href="https://wa.me/6282284471620?text=Halo%20Ayako,%20saya%20tertarik%20dengan%20fabric%20{{ urlencode($fabric->nama) }}"
             target="_blank" class="wa-btn">
            <i class="fab fa-whatsapp"></i>
          </a>
        </div>
      </div>
    @empty
      <p class="no-product">Fabric tidak ditemukan.</p>
    @endforelse
  </div>

  <div class="pagination-wrapper">
    {{ $fabrics->appends(request()->query())->links('vendor.pagination.ayako') }}
  </div>
</div>

<style>
body { background: #FAF6EF; }
.fabric-hero img {
  width: 100%;
  height: 300px;
  object-fit: cover;
}
.fabric-products {
  max-width: 1200px;
  margin: 40px auto;
  padding: 0 20px;
}
.fabric-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 20px;
}
.search-form {
  display: flex;
  width: 70%;
  background: #fff;
  border-radius: 30px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.search-form input {
  flex: 1;
  border: none;
  padding: 12px 20px;
  outline: none;
}
.search-btn {
  background: #A8682A;
  color: #fff;
  padding: 0 18px;
  border: none;
  cursor: pointer;
}
.category-buttons {
  margin-top: 15px;
}
.cat-btn {
  border: 1px solid #A8682A;
  border-radius: 20px;
  padding: 6px 18px;
  margin: 4px;
  text-decoration: none;
  color: #3F2A1C;
  font-weight: 500;
}
.cat-btn.active {
  background: #A8682A;
  color: #fff;
}
.section-title {
  text-align: center;
  font-weight: 600;
  color: #3F2A1C;
  margin: 25px 0 10px;
}
.products-grid-fabric {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
}
.fabric-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  overflow: hidden;
  transition: transform .2s;
}
.fabric-card:hover {
  transform: translateY(-4px);
}
.fabric-card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
}
.fabric-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
}
.wa-btn {
  background: #25D366;
  color: #fff;
  border-radius: 50%;
  width: 34px;
  height: 34px;
  display: flex;
  justify-content: center;
  align-items: center;
  text-decoration: none;
}
.wa-btn:hover {
  background: #1ebe5b;
}
.no-product {
  text-align: center;
  color: #777;
  margin-top: 40px;
}
</style>
@endsection
