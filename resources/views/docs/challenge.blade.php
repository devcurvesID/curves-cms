@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Tantangan</strong></div>
    <div class="content-style">
        <p>Tantangan untuk anggota dapat diatur pada menu Tantangn yang dapat di akses melalui Menu Pengaturan </p>
        <img src="{{asset('images/assets/documentation/challenge/docs challenge-0.png')}}">
        <p>Untuk melakukan penambahan Tantangan gunakan tombol Tambah tantangan baru pada sisi kanan atas tabel.</p>
        <img src="{{asset('images/assets/documentation/challenge/docs challenge-1.png')}}">
        <p>Masukan data-data sesuai dengan tantangan yang akan ditaambahkan.</p>
        <img src="{{asset('images/assets/documentation/challenge/docs challenge-2.png')}}">
        <p>Click nama tantangan jika ingin melakukan perubahan terhadapat tantang tersebut.</p>

    </div>



</div>


@endsection