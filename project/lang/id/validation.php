<?php

return [
    'accepted' => ':attribute harus diterima.',
    'after' => ':attribute harus berupa tanggal setelah :date.',
    'before' => ':attribute harus berupa tanggal sebelum :date.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':attribute harus berupa tanggal yang valid.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'integer' => ':attribute harus berupa angka bulat.',
    'max' => [
        'string' => ':attribute maksimal :max karakter.',
    ],
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
        'array' => ':attribute minimal berisi :min item.',
    ],
    'numeric' => ':attribute harus berupa angka.',
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah digunakan.',
    'attributes' => [
        'name' => 'nama',
        'email' => 'email',
        'password' => 'password',
        'customer_id' => 'customer',
        'asset_ids' => 'aset',
        'pickup_at' => 'jadwal pickup',
        'return_at' => 'jadwal kembali',
        'delivery_method' => 'metode pengiriman',
    ],
];
