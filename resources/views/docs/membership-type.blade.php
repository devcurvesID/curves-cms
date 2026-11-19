@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Tipe Keanggotaan</strong></div>
    <div class="content-style">
        <p>Tipe keanggotaan untuk member dapat ditambah ataupun diperbaharui melalui menu Tipe Keanggotaan yang dapat diakses mlelaui Menu Pengaturan</p>
        <img src="{{asset('images/assets/documentation/membership-type/docs membership-type-0.png')}}">
        <p>
            Tipe keanggotaan dapat ditambah dengan menggunakan tombol Tambah Jenis Keanggotaan.
            Tipe keanggotaan yang sudah ada dapat di edit dengen mengclick nama tipe keanggotaan yang ingin diubah.
        </p>
        <img src="{{asset('images/assets/documentation/membership-type/docs membership-type-1.png')}}">
        <p>
            Gambar diatas adalah contoh tampilan untuk penambahan tipe anggota.
            Durasi yang ada memiliki satuan bulan.
            Jika sudah sesuai tekan tombol Simpan untuk menyimpan tipe keanggotaan.
        </p>
    </div>

</div>


@endsection