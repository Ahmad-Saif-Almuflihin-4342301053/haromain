<?php

namespace App\Models;

class LocationLog
{

    public function getLogData($date = null)
    {

        if (!$date) {
            $date = now()->format('j.n.Y');
        }

        $filePath = storage_path("logs/log_location_{$date}.txt");

        if (!file_exists($filePath)) {
            return null;
        }

        $fileContent = file_get_contents($filePath);
        $lines = explode("\n", $fileContent);

        $data = [];
        $currentName = '';

        foreach ($lines as $line) {
            if (strpos($line, 'Nama Jamaah : ') !== false) {
                $currentName = trim(substr($line, strpos($line, 'Nama Jamaah : ') + 14));
            }

            if (strpos($line, 'Data : ') !== false) {

                $jsonString = trim(substr($line, strpos($line, 'Data : ') + 7));
                $jsonData = json_decode($jsonString, true);

                if ($jsonData !== null) {
                    $jsonData['NamaJamaah'] = $currentName;
                    $data[] = $jsonData;
                }
            }
        }

        return $data;
    }

    public function groupLogData(array $data)
    {
        $groupedData = [];

        foreach ($data as $entry) {
            $idPerson = $entry['IDPerson'] ?? null;
            $namaJamaah = $entry['NamaJamaah'] ?? '';

            if (!$idPerson) {
                continue;
            }

            $found = false;
            foreach ($groupedData as &$group) {
                if ($group['IDPerson'] === $idPerson && $group['NamaJamaah'] === $namaJamaah) {
                    $group['Lokasi'][] = [
                        'KoorLat' => $entry['KoorLat'] ?? null,
                        'KoorLong' => $entry['KoorLong'] ?? null,
                        'LastModified' => $entry['LastModified'] ?? null,
                    ];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $groupedData[] = [
                    'IDPerson' => $idPerson,
                    'NamaJamaah' => $namaJamaah,
                    'Lokasi' => [
                        [
                            'KoorLat' => $entry['KoorLat'] ?? null,
                            'KoorLong' => $entry['KoorLong'] ?? null,
                            'LastModified' => $entry['LastModified'] ?? null,
                        ]
                    ]
                ];
            }
        }

        return $groupedData;
    }

    public function getGroupedLogData($date = null)
    {
        $data = $this->getLogData($date);

        if (!$data) {
            return [];
        }

        return $this->groupLogData($data);
    }

    public function logFileExists($date = null)
    {
        if (!$date) {
            $date = now()->format('j.n.Y');
        }

        $filePath = storage_path("logs/log_location_{$date}.txt");
        return file_exists($filePath);
    }

    public function getAvailableLogDates($days = 30)
    {
        $dates = [];

        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i)->format('j.n.Y');
            if ($this->logFileExists($date)) {
                $dates[] = [
                    'date' => $date,
                    'formatted' => now()->subDays($i)->format('d M Y')
                ];
            }
        }

        return $dates;
    }


    public function getLogStatistics($date = null)
    {
        $data = $this->getLogData($date);

        if (!$data) {
            return [
                'total_entries' => 0,
                'unique_persons' => 0,
                'total_updates' => 0
            ];
        }

        $uniquePersons = array_unique(array_column($data, 'IDPerson'));

        return [
            'total_entries' => count($data),
            'unique_persons' => count($uniquePersons),
            'total_updates' => count($data),
            'date' => $date ?? now()->format('j.n.Y')
        ];
    }
}
