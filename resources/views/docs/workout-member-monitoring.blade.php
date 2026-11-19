@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pemantauan Latihan Anggota</strong></div>
    <div class="content-style">
        <p>Untuk menyimpan pemantauan dalam latihan anggota click lah tombol Pemantauan Latihan Anggota pada menu Pemantauan.</p>
        <img src="{{asset('images/assets/documentation/workout-member-monitoring/docs workout-member-monitoring-0.png')}}">
        <img src="{{asset('images/assets/documentation/workout-member-monitoring/docs workout-member-monitoring-1.png')}}">
        <p>
            Pada halaman ini akan ditampilkan member-member yang aktif serta jumlah latihan yang pernah dilakukan dan juga jumlah panggilan dan pesan yang sudah pernah dikirim.
            Untuk menambah panggilan yang dilakukan dan pesan yang dikirim, inputkanlah data tersebut pada kolom Panggilan dan Pesan yang berada pada sisi kanan halaman.
            Setelah diinputkan sesuai dengan kemauan clicklah tombol submit untuk setiap member yang di inputkan.
            Sistem akan menampilkan data terbaru setelahnya.
        </p>

    </div>
</div>
@endsection