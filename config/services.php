<?php
return [
    // ... config lain

    'book_api' => [
        'key' => env('BOOK_API_KEY'),
        'url' => env('BOOK_API_URL', 'https://openlibrary.org'),
        'provider' => env('BOOK_API_PROVIDER', 'openlibrary'),
    ],
];