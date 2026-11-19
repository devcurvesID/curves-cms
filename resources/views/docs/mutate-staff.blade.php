@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Mutasi Karyawan</strong></div>
    <div class="content-style">
        <p>Untuk memindahkan karyawan yang bekerja ke club lain masuklah ke menu Mutasi Karyawan dari menu Pengaturan.</p>
        <img src="{{asset('images/assets/documentation/mutate-staff/docs mutate-staff-0.png')}}">
        <p>Lalu pilihlah club yang dituju</p>
        <img src="{{asset('images/assets/documentation/mutate-staff/docs mutate-staff-0.png')}}">
        <p>
            Pastikan club asal dan club tujuan sama, lalu pastikan juga jabatan karyawan di club tujuan.
            Jika sudah sesuai simpan perubahan dengan tombol Simpan pada sisi kiri bawah halaman.
        </p>


    </div>


</div>


@endsection