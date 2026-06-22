@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', 'Error 404')
@section('icon')
    <i class="bi bi-search"></i>
@endsection
@section('message')
    Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan. Pastikan alamat URL sudah benar.
@endsection
