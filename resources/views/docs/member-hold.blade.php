@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Penangguhan Keanggotaan</strong></div>
    <div class="content-style">
        <p>
            Keanggotaan dari anggota yang aktif dapat ditangguhkan untuk sementara waktu dengan alasan tertentu.
            Untuk melakukan penangguhan terhadap keanggotaan yang ada masuklah kedalam menu Penangguhan Keanggotaan dari menu utama.
        </p>
        <img src="{{asset('images/assets/documentation/member-hold/docs member-hold-0.png')}}">
        <p>Lalu tekanlah tombol Permohonan Baru Penangguhan Keanggotaan</p>
        <img src="{{asset('images/assets/documentation/member-hold/docs member-hold-1.png')}}">
        <img src="{{asset('images/assets/documentation/member-hold/docs member-hold-2.png')}}">
        <p>
            Pada halaman ini pertama-tama carilah terlebih dahulu nama dari anggota yang keanggotaan nya akan di tangguhkan pada textbox pencarian aggota yang terletak pada sisi kanan halaman,
            setelah itu nama anggota tersebut akan secara otomatis tersimpan pada form sisi kiri yang perlu diisi.
            Setelah melengkapi form tersebut ajukanlah penangguhan tersebut.
        </p>
        <img src="{{asset('images/assets/documentation/member-hold/docs member-hold-3.png')}}">
        <p>Pada halaman sebelumnya akan terlihat dibawah tombol Daftar Permohonan Penangguhan Keanggotaan ada 1 anggota yang perlu diproses.</p>
        <img src="{{asset('images/assets/documentation/member-hold/docs member-hold-4.png')}}">
        <p>
            Pada halaman ini akan ditampilkan daftar dari anggota yang melakukan pengajuan untuk penangguhan keanggotaan.
            Checklist setiap anggota yang akan disetujui lalu click tombol Setujui Permohonan.
        </p>
        <img src="{{asset('images/assets/documentation/member-hold/docs member-hold-5.png')}}">
        <p>
            Setiap anggota yang keanggotaan nya ditangguhkan akan masuk pada daftar Penangguhan Keanggotaan yang dapat di akses dengan mengclick tombol Keanggotaan Ditangguhkan pada halaman sebelumnya.
        </p>
        <img src="{{asset('images/assets/documentation/member-hold/docs member-hold-6.png')}}">
        <p>Jika penangguhan ingin disudahi maka click lah tombol Penangguhan Selesai Hari Ini pada sisi kanan tabel.</p>

    </div>

</div>

@endsection