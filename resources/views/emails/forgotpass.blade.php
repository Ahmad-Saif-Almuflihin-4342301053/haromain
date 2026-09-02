<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f7f7f7;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 40px auto;
        }

        .logo {
            display: block;
            margin: 0 auto;
            max-width: 150px;
            margin-bottom: 20px;
        }

        .btn-reset {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white !important;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-reset:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="{{ asset('assets/haromainv0.png') }}" alt="Logo Aplikasi" class="logo">
        <h2>Permintaan Atur Ulang Kata Sandi Anda</h2>
        <p>Halo {{ $NamaJamaah }},</p>
        <p>Kami menerima permintaan untuk mengatur ulang kata sandi Anda untuk aplikasi {{ $appName }}. Kami mengerti bahwa terkadang kata sandi bisa dilupakan, jadi kami di sini untuk membantu Anda mengatur ulangnya.</p>
        <a href="{{ $urlResetPass }}" class="btn-reset">Atur Ulang Kata Sandi</a>
        <p>(Tautan ini akan kadaluwarsa dalam 24 jam. Jika Anda tidak mengajukan permintaan ini, silakan abaikan email ini.)</p>
        <p>Setelah mengklik tautan, Anda akan dibawa ke halaman di mana Anda dapat memasukkan kata sandi baru. Setelah mengatur ulang, Anda dapat login dengan kata sandi baru Anda.</p>
        <p>Jika Anda menghadapi masalah atau memiliki pertanyaan, silakan hubungi tim dukungan kami di <a href="mailto:{{ $EmailAdmin }}">Admin {{ $appName }}</a> atau kunjungi halaman bantuan kami.</p>
        <p>Terima kasih telah menggunakan {{ $appName }}.<br> Kami menghargai kepercayaan Anda.</p>
        <p>Salam hangat,<br>Tim {{ $appName }}</p>
    </div>
</body>

</html>
