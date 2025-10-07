@extends('layouts.app')

@section('title', 'Galeri | Ayako')

@section('body-class', 'gallery-page')

@section('content')

<section class="gallery-hero">
  <img src="{{ asset('assets/ayako_logo_big.png') }}" alt="Ayako Logo" class="hero-image">
</section>

<section class="gallery-section">
  <div class="gallery-container">
    <h2 class="gallery-title">GALERI KEGIATAN</h2>

    <div class="gallery-grid">
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery1.png') }}" alt="Persiapan Bahan">
        <div class="gallery-info">
          <h3>Persiapan Bahan</h3>
          <p>Lorem ipsum dolor sit amet consectetur. In sagittis lacus gravida scelerisque elementum posuere est sit.</p>
        </div>
      </div>
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery2.png') }}" alt="Pemotongan Kain">
        <div class="gallery-info">
          <h3>Pemotongan Kain</h3>
          <p>Lorem ipsum dolor sit amet consectetur. In sagittis lacus gravida scelerisque elementum posuere est sit.</p>
        </div>
      </div>
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery3.png') }}" alt="Penjahitan">
        <div class="gallery-info">
          <h3>Penjahitan</h3>
          <p>Lorem ipsum dolor sit amet consectetur. In sagittis lacus gravida scelerisque elementum posuere est sit.</p>
        </div>
      </div>
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery4.png') }}" alt="Pemasangan Fitur Tambahan">
        <div class="gallery-info">
          <h3>Pemasangan Fitur Tambahan</h3>
          <p>Lorem ipsum dolor sit amet consectetur. In sagittis lacus gravida scelerisque elementum posuere est sit.</p>
        </div>
      </div>
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery5.png') }}" alt="Penempelan Logo">
        <div class="gallery-info">
          <h3>Penempelan Logo</h3>
          <p>Lorem ipsum dolor sit amet consectetur. In sagittis lacus gravida scelerisque elementum posuere est sit.</p>
        </div>
      </div>
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery6.png') }}" alt="Pengemasan">
        <div class="gallery-info">
          <h3>Pengemasan</h3>
          <p>Lorem ipsum dolor sit amet consectetur. In sagittis lacus gravida scelerisque elementum posuere est sit.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Testimonial Section -->
<section class="testimonial-section">
  <div class="testimonial-wrapper">
    <div class="testimonial-image">
      <img src="{{ asset('assets/owner.png') }}" alt="Customer">
    </div>
    <div class="testimonial-text">
      <p class="quote-icon">“</p>
      <p class="quote-text">
        Lorem ipsum dolor sit amet consectetur. In sagittis lacus gravida scelerisque elementum posuere est sit. Porta cras est vulputate sit at. Facilisis scelerisque a quam sed. Euismod nec turpis justo aliquam bibendum.
      </p>
      <p class="quote-icon right">”</p>
    </div>
  </div>
</section>

@endsection
