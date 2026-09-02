<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Pengeluaran Kas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 12px;
            margin-bottom: 2px;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
            padding: 10px 0;
            border-top: 2px solid #000;
            border-bottom: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #f9f9f9;
            width: 30%;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        .footer-line {
            border-bottom: 3px solid #000; 
            margin-bottom: 2px;
        }
        .footer-line-thin {
            border-bottom: 1px solid #000; 
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <table style="border: none; width: 100%; max-width: 800px; margin: 0 auto;">
            <tr style="border: none;">
                <td style="border: none; width: 20%; text-align: center; vertical-align: middle;">
                    @if(isset($travel->LogoKantor) && $travel->LogoKantor)
                        <img src="{{ asset('assets/logo-kantor/' . $travel->LogoKantor) }}" alt="Logo" style="max-width: 100px; max-height: 100px;">
                    @else
                        <img src="https://haromain.id/assets/haromainv0.png" alt="Logo" style="max-width: 100px; max-height: 100px;">
                    @endif
                </td>
                <td style="border: none; text-align: center; vertical-align: middle;">
                    <div class="company-name">{{ $travel->NamaTravel ?? 'NURANI TOURS' }}</div>
                    <div class="company-info">Umroh & Haji Travel</div>
                    <div class="company-info">
                        {{ $travel->Alamat ?? '' }}
                        @if(isset($travel->NamaKab)), {{ $travel->NamaKab }}@endif
                        @if(isset($travel->NamaProv)), {{ $travel->NamaProv }}@endif
                    </div>
                    <div class="company-info">
                        Telp: {{ $travel->TelpCP ?? $travel->NoTelpPemilik ?? '-' }}
                        @if(isset($travel->Email)), Email: {{ $travel->Email }}@endif
                    </div>
                    @if(isset($travel->Website))
                    <div class="company-info">Website: {{ $travel->Website }}</div>
                    @endif
                </td>
            </tr>
        </table>
        <div class="footer-line"></div>
    </div>

    <div class="title">Pengeluaran Kas</div>

    <table style="max-width: 800px; margin: 0 auto;">
        <tr>
            <th>Tanggal</th>
            <td>{{ \Carbon\Carbon::parse($data->TanggalTransaksi)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <th>Cabang</th>
            <td>{{ $namaCabang ?? '-' }}</td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td>{{ $data->UraianTransaksi ?? '-' }}</td>
        </tr>
        <tr>
            <th>Nominal</th>
            <td>Rp {{ number_format($data->Kredit, 0, ',', '.') }}</td>
        </tr>
    </table>

</body>
</html>
