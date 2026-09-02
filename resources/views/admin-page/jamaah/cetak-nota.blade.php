<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Nota</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-width: 100px; max-height: 100px; }
        .company-name { font-size: 24px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .company-info { font-size: 14px; margin-bottom: 2px; }
        .title { text-align: center; font-weight: bold; margin: 20px 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; max-width: 800px; margin-left: auto; margin-right: auto; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f9f9f9; width: 35%; color: #555; font-weight: bold; }
        .print-btn { margin: 20px; text-align: center; }
        .footer-line { border-top: 3px double #333; margin-top: 10px; margin-bottom: 20px; }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <table style="border: none; width: 100%; max-width: 800px;">
            <tr style="border: none;">
                <td style="border: none; width: 20%; text-align: center; vertical-align: middle;">
                    @if(isset($travel->LogoKantor) && $travel->LogoKantor)
                        <img src="{{ asset('assets/logo-kantor/' . $travel->LogoKantor) }}" alt="Logo">
                    @else
                        <img src="https://haromain.id/assets/haromainv0.png" alt="Logo">
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

    <div class="title">Pembayaran Jamaah</div>

    <table>
        <tr>
            <th>Tanggal</th>
            <td>{{ date('d F Y') }}</td>
        </tr>
        <tr>
            <th>Nama Jamaah</th>
            <td>{{ $data->NamaJamaah }}</td>
        </tr>
        <tr>
            <th>Nama Paket</th>
            <td>{{ $data->NamaPaket }}</td>
        </tr>
        <tr>
            <th>Harga Paket</th>
            <td>Rp {{ number_format($data->InfoTagihan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Potongan Voucher</th>
            <td>Rp {{ number_format($data->PotonganVoucher ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Terbayar</th>
            <td>Rp {{ number_format($data->InfoTerbayar ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Sisa Tagihan</th>
            <td>Rp {{ number_format($data->InfoSisaTagihan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td>{{ $data->Keterangan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Nominal Pembayaran</th>
            <td>Rp 0</td>
        </tr>
    </table>
</body>
</html>
