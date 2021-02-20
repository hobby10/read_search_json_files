<?php

return [
    'data_files_folder' => 'data_files',
    'search_with_keys' => ['currency', 'statusCode', 'balanceMax', 'balanceMin'],
    'status_code' => [
        'authorised' => [1, 100],
        'decline' => [2, 200],
        'refunded' => [3, 300],
    ],
    'providers_searchable_keys' => [
        'DataProviderX' => [
            'currency', 'statusCode'
        ],
        'DataProviderY' => [
            'currency', 'statusCode', 'balanceMax', 'balanceMin'
        ],
    ],
];
