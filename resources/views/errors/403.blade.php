@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', 'Error 403')
@section('icon')
    <i class="bi bi-shield-lock"></i>
@endsection
@section('message')
    Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
@endsection
