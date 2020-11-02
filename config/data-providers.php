<?php

return [
    'DataProviderX' => [
        'enabled'   => true,
        'database_path' => '/json/DataProviderX.json',
        'mappings'  => [
            'amount'    => 'parentAmount',
            'currency'  => 'Currency',
            'email'     => 'parentEmail',
            'status'    => 'statusCode',
            'codes'     => [
                'authorized'    => 1,
                'declined'      => 2,
                'refunded'      => 3
            ],
            'date'      => 'registrationDate',
            'unique_id' => 'parentIdentification'
        ]
    ],
    'DataProviderY' => [
        'enabled'   => true,
        'database_path' => '/json/DataProviderY.json',
        'mappings' => [
            'amount'    => 'balance',
            'currency'  => 'currency',
            'email'     => 'email',
            'status'    => 'status',
            'codes'     => [
                'authorized'    => 100,
                'declined'      => 200,
                'refunded'      => 300
            ],
            'date'      => 'created_at',
            'unique_id' => 'id'
        ]
    ]
];
