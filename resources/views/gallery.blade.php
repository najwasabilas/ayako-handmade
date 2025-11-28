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
          <p>seluruh bahan dasar seperti kain dan aksesoris pendukung diseleksi untuk memastikan kualitas terbaik sebelum proses produksi dimulai.
          </div>
      </div>
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery2.png') }}" alt="Pemotongan Kain">
        <div class="gallery-info">
          <h3>Pemotongan Kain</h3>
          <p>Kain dan material lainnya dipotong sesuai pola agar setiap bagian tas memiliki ukuran yang presisi dan siap dirangkai ke tahap berikutnya.</p>
        </div>
      </div>
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery3.png') }}" alt="Penjahitan">
        <div class="gallery-info">
          <h3>Penjahitan</h3>
          <p>Setiap potongan bahan dijahit dengan teliti untuk menghasilkan tas yang kuat, rapi, dan memiliki nilai estetika tinggi.</p>
        </div>
      </div>
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery4.png') }}" alt="Pemasangan Fitur Tambahan">
        <div class="gallery-info">
          <h3>Pemasangan Fitur Tambahan</h3>
          <p>Fitur tambahan seperti resleting, tali, dan ornamen dekoratif dipasang untuk memberikan fungsi ekstra sekaligus mempercantik tampilan tas.</p>
        </div>
      </div>
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery5.png') }}" alt="Penempelan Logo">
        <div class="gallery-info">
          <h3>Penempelan Logo</h3>
          <p>Logo merek ditempel secara hati-hati agar melekat sempurna dan menjadi identitas resmi pada setiap produk yang dihasilkan.</p>
        </div>
      </div>
      <div class="gallery-card">
        <img src="{{ asset('assets/gallery6.png') }}" alt="Pengemasan">
        <div class="gallery-info">
          <h3>Pengemasan</h3>
          <p>Produk yang telah selesai melalui proses pengecekan kualitas kemudian dikemas rapi untuk memastikan aman sampai ke tangan pelanggan.</p>
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
       Setiap tas yang kami hasilkan adalah cerminan budaya Riau dan kerja tangan penuh ketulusan dari ibu-ibu rumah tangga yang kami berdayakan. Kami percaya, produk terbaik lahir dari hati yang bekerja bersama.
      </p>
      <p class="quote-icon right">”</p>
    </div>
  </div>
</section>

@endsection
