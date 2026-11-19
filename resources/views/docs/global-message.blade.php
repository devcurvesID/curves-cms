@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div >
    <div class="title-style"><strong>Pesan Global</strong></div>
    <div class="content-style">
        <p>Pesan global untuk member dapat di set pada Menu Pesan Global melalui menu Pengaturan</p>
        <img src="{{asset('images/assets/documentation/global-message/docs global-message-0.png')}}">
        <p>
            Pada menu ini akan ditampilkan daftar Pesan Global yang ada.
            Untuk menambahkan pesan global yang baru gunakan tombol Tambah Pesan Global Baru.
            Untuk menghapus gunakan tombol delete pada baris yang akan dihapus.
            Untuk mengedit pesan click pesan tersebut.
        </p>
        <img src="{{asset('images/assets/documentation/global-message/docs global-message-1.png')}}">
        <p>
            Saat membuat pesan global yang baru akan muncul tampilan seperti gambar diatas.
            Click tombol klub untuk menampilkan club yang ada, lalu checklist checkbox club akan menerima pesan global ini.
            jika input sudah selesai simpan dengan menggunakan tombol Simpan.
        </p>

    </div>
</div>

@endsection