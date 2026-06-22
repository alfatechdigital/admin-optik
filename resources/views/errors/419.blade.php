@extends('errors.layout')

@section('title', 'Sesi Berakhir')
@section('code', 'Error 419')
@section('icon')
    <i class="bi bi-clock-history"></i>
@endsection
@section('message')
    Sesi Anda telah berakhir karena tidak ada aktivitas dalam waktu yang lama. Silakan segarkan halaman dan coba lagi.
@endsection
