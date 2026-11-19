@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Perusahaan</strong></div>
    <div class="content-style">
        <p>Pengaturan untuk perusahaan yang berkerja sama dengan Curves dapat di akses pada menu Pengaturan, clicklah tombol Perusahaan.</p>
        <img src="{{asset('images/assets/documentation/company/docs company-0.png')}}">
        <p>
            Pada menu tersebut akan ditampilkan daftar Perusahaan yang berkerja sama dengan Curves.
            Untuk melakukan penambahan perusahaan yang bekerja sama click Tambah Perusahaan Baru, dan masukan data-data yangs sesuai.
            Untuk melakukan perubahan data perusahaan click lah nama perusahaan pada tabel tersebut.
        </p>
    </div>


</div>



@endsection