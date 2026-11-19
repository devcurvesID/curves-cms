@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Laporan Sumber FA</strong></div>
    <div class="content-style">
        <p>Untuk melihat laporan mengenai penjualan oleh sumber FA click lah tombol Laporan Sumber FA pada menu Laporan</p>
        <img src="{{asset('images/assets/documentation/report/docs fa-source-report-0.png')}}">
        <p>Pada laporan FA seperti pada gambar diatas, header tabel Tidak terjual dapat diclick untuk menampilkan detail rekap alasan tidak terjualnya FA.</p>
        <img src="{{asset('images/assets/documentation/report/docs fa-source-report-1.png')}}">
        <p></p>
    </div>

</div>



@endsection