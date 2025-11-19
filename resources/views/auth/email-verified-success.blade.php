<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Diverifikasi</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('/assets/bg.jpg') center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        .modal-box {
            width: 70%;
            max-width: 650px;
            background: #f8f3eb;
            border-radius: 22px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 25px;
            font-size: 22px;
            font-weight: 700;
            color: #333;
        }

        .icon-circle {
            width: 85px;
            height: 85px;
            margin: 0 auto 25px;
            background: #f3e3cf;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .icon-circle span {
            font-size: 38px;
            color: #7b562c;
        }

        p {
            color: #333;
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 35px;
        }

        .btn-primary {
            width: 60%;
            max-width: 300px;
            padding: 12px 20px;
            background: #a96327;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-primary:hover {
            background: #8d541f;
        }
    </style>
</head>

<body>

<div class="modal-box">
    <h2>Email Berhasil Diverifikasi</h2>

    <div class="icon-circle">
        <span>✓</span>
    </div>

    <p>Email anda berhasil diverifikasi.<br>Silakan Masuk ke Akun Anda.</p>

    <a href="{{ route('login') }}">
        <button class="btn-primary">Masuk</button>
    </a>
</div>

</body>
</html>
