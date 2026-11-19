@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Laporan Ulang Tahun</strong></div>
    <div class="content-style">
        <p>
            Pada menu Laporan clicklah tombol Laporan Ulang Tahun untuk mleihat daftar ulangtahun anggota dan member.
            Pada menu ini daftar tanggal lahir member atau staff dapat dipantau.
            Untuk mengganti data yang ditampilkan (antara member dan staff) tekanlah tombol ungu pada bagian atas tabel.
        </p>
        <img src="{{asset('images/assets/documentation/report/docs birthday-report-0.png')}}">


    </div>

</div>


@endsection