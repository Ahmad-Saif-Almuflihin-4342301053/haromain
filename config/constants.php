<?php

return [
    'PERSON_AGEN' => 'Agen',
    'PERSON_JAMAAH' => 'Jamaah',
    'PERSON_KELUARGA' => 'Keluarga',
    'ST_JAMAAH_JAMAAH' => 'JAMAAH',
    'ST_JAMAAH_LEADER' => 'LEADER',

    'X_CALLBACK_TOKEN' => env('X_CALLBACK_TOKEN') ||'',
    'AUTH_XENDIT' => env('XENDIT_KEY'),
    'AUTH_MAPS' => env('GOOGLE_MAPS_KEY'),
    'JENIS_PENERBANGAN' => ['direct', 'transit'],
    'KATEGORI_PAKET' => ['UMRAH', 'HAJI PLUS', 'DOMESTIK', 'MANCANEGARA'],
    'BANK' => [
        [
            "name" => "Bank Negara Indonesia (BNI)",
            "code" => "BNI",
        ],
        [
            "name" => "Bank Mandiri",
            "code" => "MANDIRI",
        ],
        [
            "name" => "Bank Rakyat Indonesia (BRI)",
            "code" => "BRI",
        ],
        [
            "name" => "Bank Syariah Indonesia (BSI)",
            "code" => "BSI",
        ],
        [
            "name" => "Bank Permata",
            "code" => "PERMATA",
        ],
    ],

];
