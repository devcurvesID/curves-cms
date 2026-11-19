@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Laporan Janji Temu</strong></div>
    <div class="content-style">
        <p>
            Daftar Janji Temu untuk menampilkan daftar FA yang pernah dilakukan dan langkah yang terkait pada penjualan FA.
            jika tahap dalam FA terpenuhi akan ditampilkan dengen simbol checklist dan tanggal terpenuhinya langkah tersebut dalam warna hijau pada tabel.
            Jika tidak terpenuhi maka akan ditampilakan kotak merah dengan simbol silang.
        </p>
        <img src="{{asset('images/assets/documentation/report/docs appointment-report-0.png')}}">

    </div>
</div>

@endsection