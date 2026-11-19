@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Voucher</strong></div>
    <div class="content-style">
        <p>Untuk melakukan pengaturan terhadapat voucher yang di keluarkan oleh club dapat dilakukan dengan masuk kedalam menu Voucher dari menu Pengaturan</p>
        <img src="{{asset('images/assets/documentation/voucher/docs voucher-0.png')}}">
        <p>Pada menu voucher akan ditampilkan tipe voucher yang ada, banyak nya voucher untuk setiap tipenya ditampilkan dibawah tombol masing-masing tipe voucher.</p>
        <img src="{{asset('images/assets/documentation/voucher/docs voucher-1.png')}}">
        <p>Dalam contoh ini akan dicontohkan untuk pengaturan voucher pada voucher hadiah.</p>
        <img src="{{asset('images/assets/documentation/voucher/docs voucher-2.png')}}">
        <p>Jika masuk ke salah 1 tipe voucher akan ditampilkan voucher-voucher pada tipe tersebut. untuk menambah voucher baru click lah tombol +Tambah Voucher pada sisi kanan atas tabel.</p>
        <img src="{{asset('images/assets/documentation/voucher/docs voucher-3.png')}}">
        <p>
            Pilih lah tipe voucher yang akan ditambahkan, lalu masukan tanggal berlaku voucher.
            Masukan nilai potongan voucher yang sesuai.
            Jika baris pada tabel kurang dapat ditambahkan dengan menggunakan tombol + di samping kanan tabel.
        </p>
        <img src="{{asset('images/assets/documentation/voucher/docs voucher-4.png')}}">
        <p>
            Voucher yang sudah dibuat masih dapat di ubah atau diperbaiki jika ada kesalahan dengan mencheck list checkbox pada sisi kiri tabel lalu click tombol Ubah.
            Jika voucher telah dibagikan kepada anggota atau pihak penerima lain nya checklistlah voucher tersebut lalu click tombol Keluarkan Voucher untuk mengubah status voucher tersebut menjadi Out (yang berarti sudah didistribusikan).
            Status voucher dapat dilihat pada kolom Status.
        </p>
        <img src="{{asset('images/assets/documentation/voucher/docs voucher-5.png')}}">
        <p>
            Voucher yang kadaluarsa akan memiliki status kadaluarsa seperti contoh pada gambar diatas.
        </p>

    </div>

</div>

@endsection