@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div >
    <div class="title-style"><strong>Pemantauan Ulang Tahun Anggota</strong></div>
    <div class="content-style">
        <p>Program ini dapat menyimpan pencatatan contact dari staff pada member yang dilakukan pada ulang tahun member. Pada menu Pemantauan clicklah tombol Pemantauan Ulang Tahun Anggota untuk melakukan pencatatan.</p>
        <img src="{{asset('images/assets/documentation/birthday-member-monitoring/docs birthday-member-monitoring-0.png')}}">
        <img src="{{asset('images/assets/documentation/birthday-member-monitoring/docs birthday-member-monitoring-1.png')}}">
        <p>
            Pada tabel ini akan diperlihatkan daftar anggota, ulang tahun anggota dan jumlah panggilan ataupun pesan yang sudah dikirimkan pada member.
            Sama seperti pada Pemantauan Latihan untuk melakukan input jumlah kontak dan pesan yang baru dikirimkan isilah pada kolom Panggilan dan kolom Pesan pada sisi kanan tabel lalu clicklah tombol Submit jika sudah selesai.
        </p>
    </div>


</div>

@endsection