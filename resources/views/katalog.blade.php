@extends('layouts.app')
@include('components.product-popup')

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

  <div class="products-grid-catalog" id="products-container">
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
            <i class="fas fa-shopping-cart cart-icon"
            onclick="event.preventDefault(); event.stopPropagation(); openPopup({
                id: {{ $product->id }},
                nama: '{{ $product->nama }}',
                harga: {{ $product->harga }},
                stok: {{ $product->stok }},
                image: '{{ asset('assets/catalog/images/' . ($product->images->first()->gambar ?? 'no-image.jpg')) }}'
            }, 'keranjang')">
            </i>
          </div>
        </div>
      </div>
    </a>
  @empty
    <p class="no-product">Produk tidak ditemukan.</p>
  @endforelse
</div>


  <div id="loading-spinner" class="loading-spinner" style="display: none;">
      <div class="loading-dots">
          <span></span><span></span><span></span>
      </div>
  </div>


</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const productsContainer = document.getElementById("products-container");
    const loadingSpinner = document.getElementById("loading-spinner");

    let skip = 12;
    let isLoading = false;

    function loadMoreProducts() {
      if (isLoading) return;
      isLoading = true;
      loadingSpinner.style.display = "block";

      setTimeout(() => {   // ⏳ Delay 2 detik disini

        const urlParams = new URLSearchParams(window.location.search);
        const kategori = urlParams.get('kategori') ?? 'all';
        const search = urlParams.get('search') ?? '';

        fetch(`/katalog/load-more?kategori=${kategori}&search=${search}&skip=${skip}`)
          .then(response => response.json())
          .then(data => {
            if (data.products.length === 0) {
              window.removeEventListener('scroll', handleScroll);
              loadingSpinner.style.display = "none";
              return;
            }

            data.products.forEach(product => {
              const img = product.images[0]?.gambar ?? 'no-image.jpg';

              const html = `
                <a href="/produk/${product.id}" class="product-link">
                  <div class="product-card-catalog">
                    <img src="/assets/catalog/images/${img}" alt="${product.nama}">
                    <div class="product-info">
                      <h3>${product.nama}</h3>
                      <div class="product-footer">
                        <span class="price">Rp ${product.harga.toLocaleString()}</span>
                        <i class="fas fa-shopping-cart cart-icon"
                          onclick="event.preventDefault(); event.stopPropagation(); openPopup({
                            id: ${product.id},
                            nama: '${product.nama}',
                            harga: ${product.harga},
                            stok: ${product.stok},
                            image: '/assets/catalog/images/${img}'
                          }, 'keranjang')"
                        ></i>
                      </div>
                    </div>
                  </div>
                </a>
              `;

              productsContainer.insertAdjacentHTML("beforeend", html);
            });

            skip = data.next_skip;
            isLoading = false;
            loadingSpinner.style.display = "none";
          })
          .catch(() => {
            isLoading = false;
            loadingSpinner.style.display = "none";
          });

      }, 650); // ⏳ Delay 2 detik
    }


    function handleScroll() {
      const nearBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight - 200;
      if (nearBottom) loadMoreProducts();
    }

    window.addEventListener("scroll", handleScroll);
  });

</script>
<style>
  .loading-spinner {
    width: 100%;
    display: block;           /* memastikan tidak ikut flex parent */
    text-align: center;       /* memaksa isi ke tengah */
    margin: 40px 0;
  }

  .loading-dots {
    display: inline-flex;     /* agar tetap center di text-align */
    gap: 8px;
  }

  .loading-dots span {
    width: 10px;
    height: 10px;
    background: #a86b32;
    border-radius: 50%;
    animation: bounce 0.9s infinite;
  }

  .loading-dots span:nth-child(2) {
    animation-delay: 0.15s;
  }

  .loading-dots span:nth-child(3) {
    animation-delay: 0.3s;
  }

  @keyframes bounce {
    0%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-8px); }
  }

</style>
@endsection
