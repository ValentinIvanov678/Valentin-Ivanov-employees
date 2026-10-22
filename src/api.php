<?php
include 'config.php';

class APIController {

    private $cfg;

    public function __construct(array $cfg) {
        $this->cfg = $cfg;
    }

    public function createStatistics($csvFile): array{
        $records = $this->parseCsv($csvFile, $this->cfg['columnNames']);
        $this->unifyTimestamps($records);
        $overlapPairs = $this->findEmployeeProjectOverlaps($records);

        $this->sendApiResponse($this->groupAndSortPairs($overlapPairs));
    }
    
    protected function parseCsv(string $csvFile, array $columnKeys): array {
        $rows = [];

        // Check if input is a valid file path on the server
        if (file_exists($csvFile) && is_readable($csvFile)) {
            if (($handle = fopen($csvFile, 'r')) !== false) {
                while (($row = fgetcsv($handle, 1000, ',', "\"", "")) !== false) {
                    $rows[] = $row;
                }
                fclose($handle);
            }
        } else {
            // Otherwise, treat $csvFile as raw CSV string content
            $lines = preg_split('/\r\n|\r|\n/', trim($csvFile));
            foreach ($lines as $line) {
                if (trim($line) === '') continue;
                $rows[] = str_getcsv($line, ',', "\"", "");
            }
        }

        $records = [];
        foreach ($rows as $row) {
            if ($row === [null] || $row === [''] || (count($row) === 1 && trim($row[0]) === '')) {
                continue;
            }

            // Align column count to prevent array_combine errors
            if (count($row) !== count($columnKeys)) {
                $row = array_pad(array_slice($row, 0, count($columnKeys)), count($columnKeys), null);
            }

            $records[] = array_combine($columnKeys, $row);
        }

        if (empty($records)) {
            throw new Exception("The CSV data is empty or contains no valid records.");
        }

        return $records;
    }

    protected function unifyTimestamps(array &$records): void {
        foreach ($records as &$record) {
            if (!empty($record['DateFrom']) && strtoupper($record['DateFrom']) !== 'NULL') {
                $timestampFrom = $this->parseTimestamp($record['DateFrom']);
                if ($timestampFrom !== false) {
                    $record['DateFrom'] = $timestampFrom;
                }
            }
            
            if (!empty($record['DateTo']) && strtoupper($record['DateTo']) !== 'NULL') {
                $timestampTo = $this->parseTimestamp($record['DateTo']);
                if ($timestampTo !== false) {
                    $record['DateTo'] = $timestampTo;
                }
            }
        }

        unset($record);
    }

    protected function parseTimestamp($dateVal): ?int {
        if (empty($dateVal) || strtoupper(trim((string)$dateVal)) === 'NULL') {
            return null;
        }

        $dateStr = trim((string)$dateVal);

        if (ctype_digit($dateStr)) {
            return (int)$dateStr;
        }

        $formats = [
            'Y-m-d H:i:s',      // SQL Standard (2026-05-21 14:30:00)
            'Y-m-d\TH:i:s',     // ISO 8601 Extended (2026-05-21T14:30:00)
            'Ymd\THis',         // ISO 8601 Basic (20260521T143000)
            'Y-m-d',            // Date only (2026-05-21)
            'd/m/Y H:i:s',      // EU 24-Hour (21/10/2026 12:00:00)
            'd/m/Y H:i',        // EU without seconds
            'm/d/Y h:i:s A',    // US 12-Hour AM/PM (10/21/2026 02:30:00 PM)
            'm/d/Y H:i:s',      // US 24-Hour
            'D, d M Y H:i:s T'  // RFC 2822
        ];

        foreach ($formats as $fmt) {
            $dt = DateTime::createFromFormat($fmt, $dateStr);
            if ($dt !== false) {
                $errors = DateTime::getLastErrors();
                if (!is_array($errors)) {
                    return $dt->getTimestamp();
                }
            }
        }

        $fallbackTs = strtotime($dateStr);
        return ($fallbackTs !== false) ? $fallbackTs : null;
    }

    protected function findEmployeeProjectOverlaps(array $records): array {
        $now = time();
        $projectsMap = [];

        // Group records by project so we work only with real overlaps
        foreach ($records as $record) {
            $projectId = $record['ProjectID'];
            $dateTo = (empty($record['DateTo']) || strtoupper($record['DateTo']) === 'NULL') ? $now : (int)$record['DateTo'];
            
            $projectsMap[$projectId][] = [
                'EmpID'    => (int)$record['EmpID'],
                'DateFrom' => (int)$record['DateFrom'],
                'DateTo'   => $dateTo
            ];
        }

        $overlaps = [];

        // Compare pairs within projects 
        foreach ($projectsMap as $projectId => $assignments) {
            $count = count($assignments);
            if ($count < 2) {
                continue;
            }

            // Overlapping logic
            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $empA = $assignments[$i];
                    $empB = $assignments[$j];
                    if ($empA['EmpID'] === $empB['EmpID']) {
                        continue;
                    }

                    $overlapStart = max($empA['DateFrom'], $empB['DateFrom']);
                    $overlapEnd = min($empA['DateTo'], $empB['DateTo']);

                    if ($overlapStart <= $overlapEnd) {
                        $durationSeconds = $overlapEnd - $overlapStart;
                        $durationDays = max(1, round($durationSeconds / 86400));

                        // Sorting 
                        $pair = [$empA['EmpID'], $empB['EmpID']];
                        sort($pair);

                        // Result
                        $overlaps[] = [
                            'Employee1'      => $pair[0],
                            'Employee2'      => $pair[1],
                            'ProjectID'      => $projectId,
                            'OverlapDays'    => $durationDays,
                            'OverlapStart'   => date('Y-m-d', $overlapStart),
                            'OverlapEnd'     => date('Y-m-d', $overlapEnd)
                        ];
                    }
                }
            }
        }

        return $overlaps;
    }

    protected function groupAndSortPairs(array $overlapPairs): array {
        $aggregatedPairs = [];

        foreach ($overlapPairs as $record) {
            $e1 = (int)$record['Employee1'];
            $e2 = (int)$record['Employee2'];
            $days = (int)$record['OverlapDays'];
            $projectId = $record['ProjectID'];

            // Create unique key to avoid duplicates like (1,5) (5,1)
            $empA = min($e1, $e2);
            $empB = max($e1, $e2);
            $pairKey = "{$empA}_{$empB}";

            if (!isset($aggregatedPairs[$pairKey])) {
                $aggregatedPairs[$pairKey] = [
                    'Employee1'        => $empA,
                    'Employee2'        => $empB,
                    'TotalOverlapDays' => 0,
                    'Projects'         => []
                ];
            }

            // Add project data to unique pair
            $aggregatedPairs[$pairKey]['TotalOverlapDays'] += $days;
            $aggregatedPairs[$pairKey]['Projects'][] = [
                'ProjectID'    => $projectId,
                'OverlapDays'  => $days,
                'OverlapStart' => $record['OverlapStart'],
                'OverlapEnd'   => $record['OverlapEnd']
            ];
        }

        // Sort pairs desc based on TotalOverlapDays
        $uniquePairs = array_values($aggregatedPairs);
        usort($uniquePairs, function ($a, $b) {
            return $b['TotalOverlapDays'] <=> $a['TotalOverlapDays'];
        });

        return $uniquePairs;
    }

    // Helpers
    protected function sendApiResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode([
            'data' => $data,
            'status' => $status = 200?'success':'error'
        ]);
        exit;
    }
}
