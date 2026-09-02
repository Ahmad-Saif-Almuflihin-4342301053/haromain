<?php

if (!function_exists('currency')) {
    function currency($value)
    {
        $currency = number_format($value, 0, ".", ".");
        return $currency;
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($number)
    {
        if ($number == 0) return '';
        $formatted_number = number_format($number, 0, ',', '.');
        return 'Rp ' . $formatted_number;
    }
}

if (!function_exists('convert_img64')) {

    function convert_img64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}

if (!function_exists('tgl_indo')) {
    function date_indo($tgl)
    {
        if ($tgl != null && $tgl != '') {
            $ubah = gmdate($tgl, time() + 60 * 60 * 8);
            $pecah = explode("-", $ubah);
            $tanggal = $pecah[2];
            $bulan = bulan($pecah[1]);
            $tahun = $pecah[0];
            return $tanggal . ' ' . $bulan . ' ' . $tahun;
        } else {
            return '-';
        }
    }
}

if (!function_exists('bulan')) {
    function bulan($bln)
    {
        switch ($bln) {
            case 1:
                return "Januari";
                break;
            case 2:
                return "Februari";
                break;
            case 3:
                return "Maret";
                break;
            case 4:
                return "April";
                break;
            case 5:
                return "Mei";
                break;
            case 6:
                return "Juni";
                break;
            case 7:
                return "Juli";
                break;
            case 8:
                return "Agustus";
                break;
            case 9:
                return "September";
                break;
            case 10:
                return "Oktober";
                break;
            case 11:
                return "November";
                break;
            case 12:
                return "Desember";
                break;
        }
    }
}
if (!function_exists('datetime_to_date')) {
    function datetime_to_date($dateString)
    {
        if ($dateString != '0000-00-00 00:00:00' && $dateString != '') {
            $timestamp = strtotime($dateString);
            $formattedDate = date("Y-m-d", $timestamp);
            return $formattedDate;
        } else {
            return '';
        }
    }
}
