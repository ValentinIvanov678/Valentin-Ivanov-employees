<?php
$config = [
    'columnNames' => [
        'EmpID', 
        'ProjectID',
        'DateFrom',
        'DateTo'
        ],
    'csvFolder' => __DIR__.'/assets/csv',
    'filesWithUIDs' => [
        '36c2f027-669d-4a91-aa63-b00e72de3012' => [
            'filename' => 'employee_projects_iso8601_date.csv',
            'format' => 'ISO 8601 Date (YYYY-MM-DD)'
        ],
        '9c68b824-3fac-4136-9d2a-c76604a44794' => [
            'filename' => 'employee_projects_iso8601_extended.csv',
            'format' => 'ISO 8601 Extended (YYYY-MM-DDTHH:MM:SS)'
        ],
        '74cad0f8-22f9-4061-9dc1-83676a2349e2' => [
            'filename' => 'employee_projects_iso8601_basic.csv',
            'format' => 'ISO 8601 Basic (YYYYMMDDTHHMMSS)'
        ],
        '151d75f5-6588-412f-b709-7fac938c6d55' => [
            'filename' => 'employee_projects_sql_datetime.csv',
            'format' => 'SQL Standard DATETIME (YYYY-MM-DD HH:MM:SS)'
        ],
        'b0996d78-d11a-4471-8dcb-5fca2804be25' => [
            'filename' => 'employee_projects_rfc2822.csv',
            'format' => 'RFC 2822 (Day, DD Mon YYYY HH:MM:SS GMT)'
        ],
        '632ce571-b6e4-4038-b763-5d1eba048993' => [
            'filename' => 'employee_projects_us_datetime.csv',
            'format' => 'US Standard 12-Hour (MM/DD/YYYY HH:MM:SS AM/PM)'
        ],
        'c09271ce-1987-4993-b3b1-2767c8d8db45' => [
            'filename' => 'employee_projects_eu_datetime.csv',
            'format' => 'European Standard 24-Hour (DD/MM/YYYY HH:MM:SS)'
        ],
        '846144c2-612c-4086-a4c1-496d451b15f1' => [
            'filename' => 'employee_projects_unix_timestamp.csv',
            'format' => 'Unix Epoch Timestamp (Integer Seconds)'
        ],
        '5b359b70-cdf7-455f-b6ac-be4fb03af273' => [
            'filename' => 'employee_projects_mixed_timestamps.csv',
            'format' => 'A mix of all Date Formats in one CSV'
        ]
    ],
];
