<?php

return [
    'tax_rate' => env('RENTAL_TAX_RATE', 0.11),
    'deposit_rate' => env('RENTAL_DEPOSIT_RATE', 0.30),
    'minimum_days' => env('RENTAL_MINIMUM_DAYS', 1),
    'currency' => env('RENTAL_CURRENCY', 'IDR'),
];
