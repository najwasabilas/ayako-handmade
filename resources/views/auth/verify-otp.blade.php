<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi OTP</title>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background: url("/assets/bg.jpg") center/cover no-repeat;
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .otp-container {
        width: 520px;
        background: #f7f2eb;
        padding: 40px;
        border-radius: 25px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    .otp-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 2px solid #c98748;
    }

    .otp-inputs {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin: 20px 0;
    }

    .otp-inputs input {
        width: 50px;
        height: 55px;
        border-radius: 10px;
        font-size: 20px;
        border: 2px solid #c58b46;
        text-align: center;
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        background: #a66a32;
        color: white;
        font-size: 16px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
    }

    .btn-submit:hover {
        background: #8f592b;
    }

    .resend {
        margin-top: 18px;
    }

    .resend a {
        color: #a66a32;
        font-weight: 600;
        text-decoration: none;
    }

    .resend a:hover {
        text-decoration: underline;
    }
</style>
</head>

<body>

<div class="otp-container">

    <div class="otp-icon">
        <img src="/assets/upload.svg" width="40">
    </div>

    <h2>Masukkan Kode Verifikasi</h2>
    <p>Kode verifikasi telah dikirim ke email Anda</p>

    <form action="{{ route('verify.otp') }}" method="POST">
        @csrf

        <input type="hidden" name="email" value="{{ $email }}">

        <div class="otp-inputs">
            @for ($i = 1; $i <= 6; $i++)
                <input type="text" maxlength="1" name="digit{{ $i }}" class="otp-field">
            @endfor
        </div>

        <button class="btn-submit">Verifikasi</button>
    </form>

    <div class="resend">
        Belum menerima kode?  
        <a href="{{ route('resend.otp', ['email' => $email]) }}">Kirim Ulang</a>
    </div>
</div>

<script>
    const fields = document.querySelectorAll(".otp-field");

    fields.forEach((field, i) => {
        field.addEventListener("input", () => {
            if (field.value.length === 1 && i < fields.length - 1) {
                fields[i + 1].focus();
            }
        });
    });
</script>

</body>
</html>
