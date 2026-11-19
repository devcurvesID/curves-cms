@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Community Partner</strong></div>
    <div class="content-style">
        <p>Jika klub berniat melakukan partnership, atau ada perusahaan lain yang berniat melakukan partnership dengan club masuklah ke Community Partner dari menu utama.</p>
        <img src="{{asset('images/assets/documentation/community-partner/docs community-partner-0.png')}}">
        <p>Jika perusahaan yang berniat melakukan partnership tersebut belumpernah melakukan partnership dengan curves sebelumnya, atau perusahaan belum ada pada database program, masukanlah data perusahaan terlebih dahulu melalui Atur Comunity Partner.</p>
        <img src="{{asset('images/assets/documentation/community-partner/docs community-partner-1.png')}}">
        <p>Pada halaman ini program akan menampilkan daftar comunity partner yang ada. Gunakan tombol Tambah Community Partner Baru untuk menambahkan perusahaan partner baru.</p>
        <img src="{{asset('images/assets/documentation/community-partner/docs community-partner-2.png')}}">
        <p>Masukan data yang sesuai lalu Simpan</p>
        <img src="{{asset('images/assets/documentation/community-partner/docs community-partner-3.png')}}">
        <p>Untuk melakukan perubahan data clicklah nama peruahaan pada kolom Nama Community.</p>
        <p>Untuk menambahkan pesan pada member mengenai partnership yang akan berlangsung clicklah Pesan Comunity Partner pada menu sebelumnya.</p>
        <img src="{{asset('images/assets/documentation/community-partner/docs community-partner-4.png')}}">
        <p>
            Pada menu ini akan ditampilkan daftar pesan pada member mengenai partnership yang berlangsung.
            Untuk menambahkan pesan baru click tombol Tambah Pesan Cummunity Partner Baru.
            Pada halaman penambahan Pesan Community Partner Baru click tombol Community Partner untuk menampilkan Community Partner yang ada lalu click radio button pada Community Partner yang akan menjalankan partnership.
            Tekan tombol Simpan untuk menyimpan pesan tersebut.
            untuk melakukan perubahan pada Pesan Community Partner click pesan terssebut pada kolom Pesan.
        </p>

    </div>

</div>


@endsection