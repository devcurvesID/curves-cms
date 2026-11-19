@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pengembalian Dana</strong></div>
    <div class="content-style">
        <p>
            Pengembalian dana adalah fitur yang digunakan saat proses pembatalan anggota.
            Untuk masuk pada fitur ini secara manual clicklah tombol Pengembalian Dana pada menu utama.
        </p>
        <img src="{{asset('images/assets/documentation/refund/docs refund-0.png')}}">
        <img src="{{asset('images/assets/documentation/refund/docs refund-1.png')}}">
        <p>Pada halaman ini akan ditampilkan daftar dari proses pengembalian dana yang belum tuntas. Untuk melanjutkan proses pengembalian dana clicklah nomor pembayaran .</p>
        <img src="{{asset('images/assets/documentation/refund/docs refund-2.png')}}">
        <img src="{{asset('images/assets/documentation/refund/docs refund-3.png')}}">
        <p>Proses berikutnya akan mirip seperti saat pembatalan anggota.</p>


    </div>
</div>

@endsection