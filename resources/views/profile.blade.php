@extends('layouts.app')

@section('title', 'Profil | Ayako')

@section('body-class', 'profile-page')

@section('content')

<section class="profile-section">
  <div class="profile-container">

    <h2 class="profile-title">Tentang Ayako</h2>
    <div class="profile-block">
      <div class="profile-image">
        <img src="{{ asset('assets/logo_profil.jpg') }}" alt="Logo Filosofi Ayako">
      </div>
      <div class="profile-text">
        <h3>Makna Logo Ayako</h3>
        <p>
          Logo Ayako terinspirasi dari perpaduan huruf A, Y, K, dan O yang membentuk simbol menyerupai motif tradisional.
          Lingkaran utama melambangkan <strong>kesatuan dan keharmonisan</strong>,
          sedangkan pola geometris di dalamnya menggambarkan filosofi anyaman khas <strong>Melayu Riau</strong>.
        </p>
        <p>
          Warna <strong>cokelat</strong> mencerminkan keteguhan dan kedekatan dengan alam,
          sementara <strong>emas</strong> melambangkan kemewahan dan keanggunan.
          Logo ini menjadi identitas visual Ayako yang menggabungkan <strong>kearifan lokal dengan keanggunan modern</strong>.
        </p>
      </div>
    </div>

    <div class="profile-block">
      <div class="profile-text">
        <h3>Berawal dari Cinta terhadap Budaya</h3>
        <p>
          Ayako lahir dari kecintaan terhadap <strong>songket Riau</strong>—kain tradisional yang sarat makna dan filosofi.
          Kami berkomitmen untuk menghadirkan nilai tradisi dalam bentuk tas dan aksesori yang <strong>elegan dan fungsional</strong>.
        </p>
        <p>
          Setiap produk dibuat dengan penuh dedikasi oleh <strong>pengrajin lokal</strong>, menciptakan harmoni antara
          <strong>budaya dan inovasi</strong>.
        </p>
      </div>
      <div class="profile-image">
        <img src="{{ asset('assets/profil_2.jpg') }}" alt="Songket Riau">
      </div>
    </div>

    <div class="profile-block">
      <div class="profile-image">
        <img src="{{ asset('assets/profil_3.jpg') }}" alt="Proses Pembuatan Tas Ayako">
      </div>
      <div class="profile-text">
        <h3>Misi Kami</h3>
        <p>
          Menjadi brand tas etnik asal Riau yang <strong>berdaya saing tinggi</strong> di tingkat nasional maupun internasional,
          serta mengangkat <strong>keindahan songket Riau</strong> melalui produk fashion modern yang
          <strong>berkualitas dan berkelanjutan</strong>.
        </p>
        <p>
          Kami juga berkomitmen untuk <strong>memberdayakan perempuan lokal</strong> melalui pelatihan dan kolaborasi kreatif.
        </p>
      </div>
    </div>

    <div class="profile-block">
      <div class="profile-text">
        <h3>Nilai Utama Ayako</h3>
        <ul>
          <li><strong>Mengangkat Budaya Lokal</strong> – menghadirkan desain modern tanpa meninggalkan akar tradisi.</li>
          <li><strong>Memberdayakan Masyarakat</strong> – melibatkan ibu rumah tangga dalam proses produksi.</li>
          <li><strong>Menjaga Lingkungan</strong> – menerapkan prinsip <em>zero waste</em> dan pemanfaatan limbah kain songket.</li>
          <li><strong>Menjamin Kualitas</strong> – memastikan setiap produk dibuat dengan detail dan presisi.</li>
        </ul>
      </div>
      <div class="profile-image">
        <img src="{{ asset('assets/profil_4.jpg') }}" alt="Nilai dan Prinsip Ayako">
      </div>
    </div>

    <div class="profile-block">
      <div class="profile-image">
        <img src="{{ asset('assets/profil_5.jpg') }}" alt="Visi Ayako">
      </div>
      <div class="profile-text">
        <h3>Visi Kami</h3>
        <p>
          Ayako ingin dikenal sebagai <strong>Eco Innovator</strong> yang terus berinovasi dalam desain berbasis kain tradisional
          dan bahan daur ulang. Setiap tas Ayako bukan hanya produk fashion,
          tetapi juga <strong>perwujudan nilai budaya dan kepedulian lingkungan</strong>.
        </p>
        <p>
          Kami percaya bahwa keindahan sejati hadir dari karya yang memiliki <strong>cerita, makna, dan dampak sosial positif</strong>.
        </p>
      </div>
    </div>

  </div>
</section>

@endsection
