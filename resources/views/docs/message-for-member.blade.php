@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pesan untuk Anggota</strong></div>
    <div class="content-style">
        <p>
            Melalui program ini anda dapat memberikan pesan kepada anggota jika dirasa perlu.
            Cara untuk menulis pesan adalah dengan masuk ke menu Pesan dari menu Utama dengan meng click tombol Pesan yang terletak dibagian bawah kanan menu utama.
        </p>
        <img src="{{asset('images/assets/documentation/message-for-member/docs message-for-member-0.png')}}">
        <p>Lalu clicklah tombol Pesan Untuk Anggota pada halaman tersebut</p>
        <img src="{{asset('images/assets/documentation/message-for-member/docs message-for-member-1.png')}}">
        <img src="{{asset('images/assets/documentation/message-for-member/docs message-for-member-2.png')}}">
        <p>
            Pada menu ini akan ditampilkan daftar member serta pesan terhadap masing-masing member.
            Jika ada pesan pesan tersebut akan ditampilkan pada kolom Pesan, jika tidak ada maka isi kolom tersebut akan kosong.
            Anda dapat menambahkan pesan pada member yang belum diberi pesan dengan menggunakan tombol Tambahkan Pesan pada sisi kanan tabel.
        </p>
        <img src="{{asset('images/assets/documentation/message-for-member/docs message-for-member-3.png')}}">
        <p>
            Isi form baru yang muncul seperti pada gambar diatas untuk menambhakan pesan pada member.
            Pilihlah tanggal pesan tersebut berlaku.
            Jika pesan yang akan ditulis sudah ditentukan templatenya pilih lah template tersebut pada pilihan Pesan Standar, jika belum ada tulislah pesan tersebut secara manual pada textbox Pesan.
        </p>
        <p>
            Jika pada member sudah ada sebelumnya dan ingin diganti, clicklah nama member yang pesan nya hendak diganti agar menampilkan kembali form Pesan dan gantilah yang dirasa perlu diganti.
        </p>
        <p>
            Fitur filter tersedia pada atas tabel yang berguna untuk melakukan penyaringan data jika data terlalu banyak.
            Pilihlah kategori pesan yang ingin dicari lalu clicklah tombol Terapkan Filter untuk menerapkan Filter tersebut.
        </p>

    </div>
</div>
@endsection