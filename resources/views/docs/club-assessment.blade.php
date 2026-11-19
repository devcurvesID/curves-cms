@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Penilaian Klub (CA)</strong></div>
    <div class="content-style">
        <p>Penilaian Club dapat di akses melalui menu Dokumen. Masuklah ke menu Penilaian Klub melalui tombol Penilaian Klub (CA).</p>
        <img src="{{asset('images/assets/documentation/ca/docs ca-0.png')}}">
        <p>Pada menu ini akan diperlihatkan pengaturan-pengaturan mengenai club.</p>
        <img src="{{asset('images/assets/documentation/ca/docs ca-1.png')}}">
        <p>Nilai-nilai pada ahlaman ini dapat diganti jika dirasa tidak sesuai. Simpanlah perubahan data yang dilakukan dengan menggunakan tombol Ajukan pada sisi bawah halaman</p>
    </div>
</div>

@endsection