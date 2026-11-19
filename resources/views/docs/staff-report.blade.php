@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Laporan Staff</strong></div>
    <div class="content-style">
        <p>Untuk melihat laporan Staff click tombol Laporan Staff pada menu Dokumen.</p>
        <img src="{{asset('images/assets/documentation/staff-report/docs staff-report-0.png')}}">
        <p>
            Pada halaman ini akan ditampilkan daftar anggota yang ada.
            Untuk melihat laporan tentang member clicklah nama member pada kolom Nama Lengkap
        </p>
        <img src="{{asset('images/assets/documentation/staff-report/docs staff-report-1.png')}}">
        <p>Jika data dokumen menganai member belum diisi, tampilan akan tampak seperti pada gambar dibawah.</p>
        <img src="{{asset('images/assets/documentation/staff-report/docs staff-report-2.png')}}">
        <p>Pada bagian laporan, laporan baru dapat ditambahkan dengan menggunakan tombol Tambah Laporan Baru.</p>
        <img src="{{asset('images/assets/documentation/staff-report/docs staff-report-3.png')}}">
        <p>Pada menu penambahan Laporan Baru masukan data yang sesuai lalu click tombol Simpan untuk menympan lporan tersebut.</p>
    </div>
</div>

@endsection