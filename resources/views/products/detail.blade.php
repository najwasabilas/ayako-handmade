@extends('layouts.app')
@include('components.product-popup')

@section('title', $product->nama)

@section('content')
<div class="product-detail-container">

  <div class="back-detail">
    <img src="{{ asset('assets/back.svg') }}" alt="Kembali" />
    <a href="{{ url('/katalog') }}" style="text-decoration: none; color:black;">Kembali</a>
  </div>

  <div class="product-detail">
    <div class="product-gallery">
      <div id="carouselExample" class="carousel">
        <div class="carousel-inner">
          @foreach ($product->images as $index => $image)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
              <img src="{{ asset('assets/catalog/images/' . $image->gambar) }}" alt="{{ $product->nama }}">
            </div>
          @endforeach
        </div>

        @if($product->images->count() > 1)
        <button class="carousel-control prev">&#10094;</button>
        <button class="carousel-control next">&#10095;</button>
        @endif
      </div>

      <div class="thumbnail-container">
        @foreach ($product->images as $index => $image)
          <img class="thumbnail" src="{{ asset('assets/catalog/images/' . $image->gambar) }}" data-index="{{ $index }}" alt="Thumbnail">
        @endforeach
      </div>
    </div>

    <div class="product-info">
      <p class="kategori">Kategori</p>
      <h2 class="product-name">{{ $product->nama }}</h2>
      <p class="price">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>

      <div class="button-group">
        <button class="btn-cart"
        onclick="openPopup({
            id: {{ $product->id }},
            nama: '{{ $product->nama }}',
            harga: {{ $product->harga }},
            stok: {{ $product->stok }},
            image: '{{ asset('assets/catalog/images/' . $product->images->first()->gambar ?? 'assets/catalog/no-image.jpg') }}'
        }, 'keranjang')">
        Masukkan Keranjang
        </button>

        <button class="btn-buy"
        onclick="openPopup({
            id: {{ $product->id }},
            nama: '{{ $product->nama }}',
            harga: {{ $product->harga }},
            stok: {{ $product->stok }},
            image: '{{ asset('assets/catalog/images/' . $product->images->first()->gambar ?? 'assets/catalog/no-image.jpg') }}'
        }, 'beli')">
        Beli Sekarang
        </button>
      </div>

      <h4>Deskripsi</h4>
      <p class="deskripsi">{{ $product->deskripsi ?? 'Belum ada deskripsi untuk produk ini.' }}</p>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const items = document.querySelectorAll(".carousel-item");
  const nextBtn = document.querySelector(".next");
  const prevBtn = document.querySelector(".prev");
  const thumbnails = document.querySelectorAll(".thumbnail");
  const carouselInner = document.querySelector(".carousel-inner");
  let index = 0;

  function updateCarousel() {
    items.forEach((item, i) => item.classList.toggle("active", i === index));
  }

  if(nextBtn && prevBtn) {
    nextBtn.addEventListener("click", () => {
      index = (index + 1) % items.length;
      updateCarousel();
    });

    prevBtn.addEventListener("click", () => {
      index = (index - 1 + items.length) % items.length;
      updateCarousel();
    });
  }

  thumbnails.forEach((thumb) => {
    thumb.addEventListener("click", () => {
      index = parseInt(thumb.dataset.index);
      updateCarousel();
    });
  });

  // Swipe gesture for mobile
  let startX = 0;
  carouselInner.addEventListener('touchstart', e => startX = e.touches[0].clientX);
  carouselInner.addEventListener('touchend', e => {
    let endX = e.changedTouches[0].clientX;
    if (startX - endX > 50) {
      index = (index + 1) % items.length;
      updateCarousel();
    } else if (endX - startX > 50) {
      index = (index - 1 + items.length) % items.length;
      updateCarousel();
    }
  });
});
</script>

<style>
.product-detail-container {
  max-width: 1250px;
  padding: 20px;
  margin: 0 auto;
}

.back-detail {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 18px;
  font-weight: 500;
  cursor: pointer;
  margin: 20px 0;
}

.product-detail {
  display: flex;
  gap: 40px;
  align-items: flex-start;
  padding-left: 50px;
  padding-right: 50px;
}

.product-gallery {
  flex: 1;
}

.carousel {
  position: relative;
  width: 100%;
  overflow: hidden;
}

.carousel-inner {
  display: flex;
  transition: transform 0.4s ease-in-out;
}

.carousel-item {
  flex: 0 0 100%;
  display: none;
  justify-content: center;
  align-items: center;
}

.carousel-item.active {
  display: flex;
}

.carousel-item img {
  width: 100%;
  border-radius: 8px;
  object-fit: cover;
}

.carousel-control {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255,255,255,0.8);
  border: none;
  font-size: 24px;
  cursor: pointer;
  padding: 5px 10px;
  border-radius: 50%;
}

.carousel-control.prev { left: 10px; }
.carousel-control.next { right: 10px; }

.thumbnail-container {
  display: flex;
  gap: 10px;
  margin-top: 15px;
  overflow-x: auto;
  padding-bottom: 5px;
}

.thumbnail {
  width: 80px;
  height: 80px;
  border-radius: 6px;
  object-fit: cover;
  cursor: pointer;
  border: 2px solid transparent;
  flex-shrink: 0;
}

.thumbnail:hover,
.thumbnail.active {
  border-color: #a86b32;
}

.product-info {
  flex: 1;
}

.kategori {
  color: #8b8b8b;
  font-size: 14px;
}

.product-name {
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 8px;
}

.price {
  color: #a86b32;
  font-size: 22px;
  font-weight: 600;
  margin-bottom: 20px;
}

.button-group {
  display: flex;
  gap: 15px;
  margin-bottom: 30px;
}

.btn-cart,
.btn-buy {
  border: 1px solid #a86b32;
  border-radius: 25px;
  padding: 10px 25px;
  cursor: pointer;
  font-size: 14px;
}

.btn-cart {
  background: #fff;
  color: #a86b32;
}

.btn-buy {
  background: #a86b32;
  color: #fff;
}

.btn-cart:hover {
  background: #f5e3d1;
}

.btn-buy:hover {
  background: #8c5c29;
}

.deskripsi {
  line-height: 1.6;
  color: #444;
}

@media (max-width: 900px) {
  .product-detail {
    flex-direction: column;
    align-items: center;
    padding-left: 0;
    padding-right: 0;
  }

  .product-gallery,
  .product-info {
    width: 100%;
  }

  .carousel-item img {
    max-height: 350px;
    object-fit: cover;
  }

  .thumbnail-container {
    justify-content: start;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
}
</style>
@endsection
