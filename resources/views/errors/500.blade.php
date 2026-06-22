@extends('errors.layout')

@section('title', 'Kesalahan Server')
@section('code', 'Error 500')
@section('icon')
    <i class="bi bi-exclamation-triangle"></i>
@endsection
@section('message')
    Terjadi kesalahan pada server kami. Tim teknis kami telah diberitahu. Silakan coba lagi beberapa saat lagi atau hubungi administrator jika masalah berlanjut.
@endsection
