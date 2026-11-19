@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pesan Standar</strong></div>
    <div class="content-style">
        <p>Pengaturan untuk Pessan Standar pada fitur pesan dapat di akses melalui menu Pengaturan</p>
        <img src="{{asset('images/assets/documentation/standard-message/docs standard-message-0.png')}}">
        <img src="{{asset('images/assets/documentation/standard-message/docs standard-message-1.png')}}">
        <p>
            Pesan Standar dapat ditambahkan melalui tombol Tambah Pesan Standar.
            Tuliskan lah pesan yang akan ditampilkan pada textbox yang tersedia.
            Pesan standar yang sudah ditbuat dapat di edit ataupun di delete jika perlu.
        </p>

    </div>
</div>

@endsection