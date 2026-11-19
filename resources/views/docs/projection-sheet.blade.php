@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Lembar Proyeksi</strong></div>
    <div class="content-style">
        <p>Pada menu Laporan dapat ditampilkan laporan harian mengenai club </p>
        <img src="{{asset('images/assets/documentation/projection-sheet/docs projection-sheet-0.png')}}">
        <p>Clicklah tombol Lembar Proyeksi</p>
        <img src="{{asset('images/assets/documentation/projection-sheet/docs projection-sheet-1.png')}}">
        <p>Lalu masukan tanggal laporan club, lalu click tombol Ajukan untuk menampilkan laporan mengenai club pada tanggal tersebut.</p>
    </div>
</div>

@endsection