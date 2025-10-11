@extends('layouts.app')

@section('title', 'Katalog Produk')

@section('content')
<section class="catalog-hero">
    <img src="{{ asset('assets/catalog/hero_banner.jpg') }}" alt="Hero Katalog" class="hero-img">
</section>

<div class="catalog-products">
  <div class="catalog-header">
    <form action="{{ route('katalog') }}" method="GET" class="search-form">
      <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}">
      <button type="submit" class="search-btn">
        <i class="fas fa-search"></i>
      </button>
    </form>

    <div class="category-buttons">
      <a href="{{ route('katalog', ['kategori' => 'all']) }}" class="cat-btn {{ request('kategori') == 'all' || !request('kategori') ? 'active' : '' }}">Semua</a>
      @foreach($categories as $category)
        <a href="{{ route('katalog', ['kategori' => $category]) }}" class="cat-btn {{ request('kategori') == $category ? 'active' : '' }}">
          {{ ucfirst($category) }}
        </a>
      @endforeach
    </div>
  </div>

  <div class="products-grid-catalog">
  @forelse($products as $product)
    <a href="{{ route('produk.show', $product->id) }}" class="product-link">
      <div class="product-card-catalog">
        @if($product->images->isNotEmpty())
          <img src="{{ asset('assets/catalog/images/' . $product->images->first()->gambar) }}" alt="{{ $product->nama }}">
        @else
          <img src="{{ asset('assets/catalog/no-image.jpg') }}" alt="Default">
        @endif

        <div class="product-info">
          <h3>{{ $product->nama }}</h3>
          <div class="product-footer">
            <span class="price">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
            <i class="fas fa-shopping-cart cart-icon"></i>
          </div>
        </div>
      </div>
    </a>
  @empty
    <p class="no-product">Produk tidak ditemukan.</p>
  @endforelse
</div>


  <div class="pagination-wrapper">
    {{ $products->appends(request()->query())->links('vendor.pagination.ayako') }}
  </div>
</div>
@endsection
