@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
    <div >
        <div class="title-style"><strong>Sertifikasi</strong></div>
        <div class="content-style">
            <p>Untuk melakukan pengaturan Sertifikasi masuklah ke menu Sertifikasi melalui menu Pengaturan Aplikasi</p>
            <img src="{{asset('images/assets/documentation/certification/docs certification-0.png')}}">
            <p>
                Tombol Tambah Sertifikasi pada sisi kanan atas adalah tombol untuk menambah sertifikasi.
                Pada sisi kanan terdapat tombol Delete untuk menghapus sertifikasi.
                Jika sertifikasi memerlukan perubahan cliclklah nama Sertifikasi yang akan diubah pada kolom nama sertifikasi.
            </p>

        </div>



    </div>



@endsection