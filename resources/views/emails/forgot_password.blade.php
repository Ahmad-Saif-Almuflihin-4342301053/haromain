<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #FF9800;
            margin: 0;
        }
        .content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            background-color: #FF9800;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #F57C00;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .warning {
            background-color: #FFF3CD;
            border-left: 4px solid #FFC107;
            padding: 10px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $appName }}</h1>
            <p>Atur Ulang Kata Sandi</p>
        </div>
        
        <div class="content">
            <p>Halo {{ $NamaJamaah }},</p>
            
            <p>Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda di {{ $appName }}.</p>
            
            <p>Untuk melanjutkan proses atur ulang kata sandi, silakan klik tombol di bawah ini:</p>
            
            <div style="text-align: center;">
                <a href="{{ $urlResetPass }}" class="button">Atur Ulang Kata Sandi</a>
            </div>
            
            <div class="warning">
                <strong>Perhatian:</strong> Jika Anda tidak melakukan permintaan ini, segera hubungi kami untuk keamanan akun Anda.
            </div>
            
            <p>Link ini akan kadaluarsa dalam 24 jam untuk alasan keamanan.</p>
            
            <p>Jika Anda mengalami kesulitan, silakan hubungi kami di: <a href="mailto:{{ $EmailAdmin }}">{{ $EmailAdmin }}</a></p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
