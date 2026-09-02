<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
        }

        p {
            margin: 0 0 2px 0;
        }

        th {
            text-align: left;
            padding: 10px 0;
        }
    </style>
</head>

<body style="border:2px solid #C4C4C4; padding:12px; border-radius:10px">
    <div style="padding: 10px; background-color:#F2F5F9; border-radius:10px">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <div style="border-bottom:2px solid #C4C4C4; padding: 0 0 10px 0">
                        @if(isset($NamaTravel) && false) {{-- Assuming we want to keep the hardcoded logic but updated --}}
                            <img src="{{ asset('assets/haromainv0.png') }}" style="width:200px" alt="">
                        @else
                            <img src="https://haromain.id/assets/haromainv0.png" style="width:200px" alt="">
                        @endif
                    </div>
                </td>
                <td style="width: 50%;text-align:right; color: #60737D;">
                    Nomor Invoice <br>
                    <b style="color:black">{{ $NoInvoice }}</b>
                    <br><br>
                    Tanggal <br>
                    <b style="color:black">{{ date_indo(date('Y-m-d')) }}</b>
                </td>
            </tr>
            <tr>
                <td>
                    <br><br>
                    <p style="width: 50%;color: #60737D">Ditagih kepada:</p>
                    <p style="font-size: 15pt"> <b>{{ $NamaTravel }}</b> </p>
                    <p>{{ $Alamat }}</p>
                </td>
                <td></td>
            </tr>
        </table>
    </div>

    <div style="margin:1rem 0 0 0">
        <table style="width: 100%;">
            <tr>
                <th>Deskripsi</th>
                <th>Jumlah Jamaah</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
            <tr>
                <td style="color:#60737D">{{ $NamaPaket }}</td>
                <td style="color:#60737D">{{ $JumlahJamaah }}</td>
                <td style="color:#60737D">{{ $NominalPaid }}</td>
                <td style="color:#60737D">{{ $Total }}</td>
            </tr>
        </table>
    </div>

    <div style="width:100%">
        <div style="width: 60%;"></div>
        <div style="padding: 10px; background-color:#F2F5F9; border-radius:10px; width:40%; float:right; text-align:right; margin:2rem 0 0 0">
            Total (Rupiah): <b style="font-size: 16pt;">{{ $Total }}</b>
        </div>
    </div>

    <div style="margin:8rem 0 0 0;padding: 10px; background-color:#F2F5F9; border-radius:10px;">
        <b>Daftar Jama'ah:</b>
        <table style="width: 100%;">
            <tr>
                <td style="padding: 0 10px 0 10px;">
                    @foreach ($JamaahPaket as $key => $row)
                        <div>{{ $loop->iteration }}. {{ $row->NamaJamaah }}</div>
                    @endforeach
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
