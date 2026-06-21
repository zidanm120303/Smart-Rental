<?php

return [
    'tax_rate' => env('RENTAL_TAX_RATE', 0.11),
    'deposit_rate' => env('RENTAL_DEPOSIT_RATE', 0.30),
    'minimum_rental_hours' => env('RENTAL_MINIMUM_HOURS', 24),
    'currency' => env('RENTAL_CURRENCY', 'IDR'),
    'late_fee_per_day' => env('RENTAL_LATE_FEE_PER_DAY', 50000),
];
