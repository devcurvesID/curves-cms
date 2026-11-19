@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Laporan Penjualan Staff</strong></div>
    <div class="content-style">
        <p>Untuk melihat daftar penjualan staff masuklah ke Menu Laporan Penjualan Staff untuk menampilkan daftar staff dan penjualan yang dilakukan.</p>
        <img src="{{asset('images/assets/documentation/report/docs staff-selling-report-0.png')}}">
        <p>Laporan Penjualan Staff ini dapat di filter dengan menggunakan tombol Cari pada bagian atas tabel, setelah memasukan filter yang sesuai</p>
    </div>

</div>



@endsection