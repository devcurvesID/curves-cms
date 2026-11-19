@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Login Manual</strong></div>
    <div class="content-style">
        <p>
            Absent wo member dapat dimasukan secara manual dari program ini.
            untuk melakukan login secara manual click tombol Login Manua pada menu utama.
        </p>
        <img src="{{asset('images/assets/documentation/login-manual/docs login-manual-0.png')}}">
        <p>Carilah member yang akan dilogin kan secara manual.</p>
        <img src="{{asset('images/assets/documentation/login-manual/docs login-manual-1.png')}}">
        <img src="{{asset('images/assets/documentation/login-manual/docs login-manual-2.png')}}">
        <p>
            Pilih lah tanggal yang sesuai.
            Tanggal yang dipilih bisa lebih dari 1 tanggal, jika tanggal sudah dipilih akan ditandai menjadi warna merah seperti pada contoh gambar diatas.
            Tanggal yang tercatat bahwa member melakukan wo pada tanggal tersebut akan ditandai dengan adanya label pada tanggal tersebut.
            Label akan bertulisan nama club tempat member melakukan login pada tanggal tersebut. pada halaman ini data login member juga dapat dihilangkan dengan cara mengclick tanggal memeber melakukan wo dan ditandai dengan warna hijau.
            Jika penambahan atau penghapusan jadwal login sudah sesuai dan ingin disimpan, click lah tombol Simpan pada sisi bawah calendar.
        </p>
        <p>Login manual ini juga dapat dapat diakses melalui menu pada side bar</p>
        <img src="{{asset('images/assets/documentation/login-manual/docs login-manual-3.png')}}">
        <p>
            ika login manual diakses melalui sidebar akan muncul halaman lain.
            Pilih lah Latihan Anggota pada halaman tersebut untuk melakukan login manual pada member.
        </p>
        <img src="{{asset('images/assets/documentation/login-manual/docs login-manual-4.png')}}">
        <p>
            Proses berikutnya adalah memilih member yang akan dilakukan login secara manual.
            Proses tersebut dan kelanjutan nya akan sama seperti pembahasan sebelumnya.
        </p>
    </div>
</div>
@endsection