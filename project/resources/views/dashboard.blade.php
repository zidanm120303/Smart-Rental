@extends('layouts.app')

@section('content')
<section class="sr-card p-6">
    <h1 class="text-2xl font-bold text-slate-950">Dasbor</h1>
    <p class="mt-2 text-sm text-slate-500">Gunakan menu utama untuk membuka ringkasan operasional Smart Rental Pro.</p>
    <a href="{{ route('dashboard') }}" class="sr-button-primary mt-5 inline-flex">Buka Dasbor Utama</a>
</section>
@endsection
