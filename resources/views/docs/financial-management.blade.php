@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Manajemen Keuangan</strong></div>
    <div class="content-style">
        <p>Manajemen uang adalah fitur untuk melakukan pengaturan keuangan pada club. Click tombol Manajemen Uang dari menu utama.</p>
        <img src="{{asset('images/assets/documentation/financial-management/docs financial-management-0.png')}}">
        <p>Clicklah Deposito Anggota untuk menampilkan keuangan pada club.</p>
        <img src="{{asset('images/assets/documentation/financial-management/docs financial-management-1.png')}}">
        <p>Pada halaman ini akan ditampilkan daftar tagihan/pengembalian dana yang belum selesai dan juga daftar koreksi saldo yang belum selesai.</p>
        <img src="{{asset('images/assets/documentation/financial-management/docs financial-management-2.png')}}">
        <p>Jika perlu menambahkan koreksi saldo pada member, clicklah nama member yang akan ditambahkan koreksi saldo.</p>
        <img src="{{asset('images/assets/documentation/financial-management/docs financial-management-3.png')}}">
        <p>Click tombol +Koreksi berwarna kuning pada sisi kanan atas.</p>
        <img src="{{asset('images/assets/documentation/financial-management/docs financial-management-4.png')}}">
        <p>Pilih dan inputkan data mengenai koreksi saldo yang akan dibuat.</p>
        <p>Pada menu Deposito Anggota clicklah nomor pembayaran pada tabel daftar koreksi saldo untuk melihat detail pembayaran dan untuk melunaskan pembayaran tersebut.</p>
        <img src="{{asset('images/assets/documentation/financial-management/docs financial-management-5.png')}}">
        <img src="{{asset('images/assets/documentation/financial-management/docs financial-management-6.png')}}">
        <p>Pembayaran koreksi saldo akan memiliki tampilan berbeda sesuai dengan tipe koreksi pembayaran yang dilakukan oleh member.</p>
        <img src="{{asset('images/assets/documentation/financial-management/docs financial-management-7.png')}}">
        <p>Laporan Penjualan Harian adalah fitur untuk menampilkan data penjualan club pada tanggal yang dipilih.</p>

    </div>

</div>

@endsection