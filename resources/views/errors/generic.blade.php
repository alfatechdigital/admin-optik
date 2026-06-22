@extends('errors.layout')

@php
    $messages = [
        400 => 'Permintaan tidak valid. Silakan periksa kembali data yang Anda masukkan.',
        401 => 'Akses ditolak. Anda harus masuk (login) terlebih dahulu untuk mengakses halaman ini.',
        402 => 'Diperlukan pembayaran. Silakan selesaikan administrasi untuk melanjutkan.',
        405 => 'Metode akses tidak diizinkan. Sistem tidak mendukung cara Anda membuka halaman ini.',
        406 => 'Permintaan tidak dapat diterima oleh server karena format yang tidak sesuai.',
        409 => 'Terjadi konflik data. Data mungkin sedang digunakan atau sudah ada di sistem.',
        410 => 'Halaman atau data ini sudah tidak tersedia lagi (telah dihapus permanen).',
        412 => 'Prasyarat akses gagal. Ada kondisi yang belum terpenuhi untuk melanjutkan.',
        413 => 'Ukuran data terlalu besar. Pastikan file yang Anda unggah tidak melebihi batas.',
        414 => 'Alamat URL terlalu panjang untuk diproses oleh server kami.',
        415 => 'Tipe media tidak didukung. Silakan gunakan format file yang diizinkan.',
        416 => 'Jangkauan data yang diminta tidak tersedia di server.',
        417 => 'Ekspektasi server gagal. Permintaan Anda tidak dapat dipenuhi saat ini.',
        418 => 'Saya adalah teko kopi (I\'m a teapot). Permintaan ini tidak dapat diproses secara logis.',
        419 => 'Sesi Anda telah berakhir karena terlalu lama tidak ada aktivitas. Silakan segarkan halaman.',
        420 => 'Harap tenang (Enhance Your Calm). Anda mengirim terlalu banyak permintaan dalam waktu singkat.',
        421 => 'Permintaan salah sasaran. Server tidak dapat memberikan respon yang tepat.',
        423 => 'Data sedang dikunci. Silakan coba beberapa saat lagi.',
        424 => 'Kegagalan dependensi. Proses gagal karena ada tahapan sebelumnya yang tidak berhasil.',
        425 => 'Terlalu dini. Server menolak memproses permintaan yang dikirim terlalu cepat.',
        426 => 'Diperlukan pembaruan protokol. Silakan perbarui aplikasi atau browser Anda.',
    ];

    $icons = [
        401 => 'bi bi-shield-lock',
        405 => 'bi bi-slash-circle',
        413 => 'bi bi-file-earmark-arrow-up',
        418 => 'bi bi-cup-hot',
        419 => 'bi bi-clock-history',
        420 => 'bi bi-hourglass-split',
        423 => 'bi bi-lock',
    ];

    $title = $messages[$code] ?? 'Kesalahan Permintaan';
    $icon = $icons[$code] ?? 'bi bi-exclamation-octagon';
@endphp

@section('title', 'Terjadi Kesalahan')
@section('code', 'Error ' . $code)
@section('icon')
    <i class="{{ $icon }}"></i>
@endsection
@section('message')
    {{ $messages[$code] ?? 'Maaf, terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi.' }}
@endsection
