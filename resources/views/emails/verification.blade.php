<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pendaftaran Akun</title>
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
            color: #4CAF50;
            margin: 0;
        }
        .content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
        }
        .verification-code {
            background-color: #4CAF50;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            letter-spacing: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $appName }}</h1>
            <p>Verifikasi Pendaftaran Akun</p>
        </div>
        
        <div class="content">
            <p>Terima kasih telah mendaftar di {{ $appName }}!</p>
            
            <p>Untuk menyelesaikan proses pendaftaran Anda, silakan gunakan kode verifikasi berikut:</p>
            
            <div class="verification-code">
                {{ $KodeVerifikasi }}
            </div>
            
            <p>Masukkan kode verifikasi ini pada aplikasi untuk mengaktifkan akun Anda.</p>
            
            <p>Jika Anda tidak melakukan pendaftaran ini, abaikan email ini.</p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
