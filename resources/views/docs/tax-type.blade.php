@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><header>Tipe Pajak</header></div>
    <div class="content-style">
        <p>Pengaturan pajak dapat disetting pada menu Pengaturan</p>
        <img src="{{asset('images/assets/documentation/tax-type/docs tax-type-0.png')}}">
        <p>Click tombol Tipe Pajak</p>
        <img src="{{asset('images/assets/documentation/tax-type/docs tax-type-1.png')}}">
        <p>
            Pada menu ini akan ditampilkan daftar pajak yang ada.
            Untuk menambah tipe pajak baru click tombol Tambah Jenis Pajak Baru,
        </p>
        <img src="{{asset('images/assets/documentation/tax-type/docs tax-type-2.png')}}">
        <p>
            Lalu masukan nama tipe pajak baru, click Simpan untuk melakukan penyimpanan.
            Anda dapat menonaktifkan pajak dengan mengclick tombol Nonaktifkan.
            Jika pajak sedang dalam kondisi tidak aktif tombol tersebut akan menjadi tombol Aktifkan yang berfungsi untuk mengaktifkan pajak kembali.
        </p>
        <img src="{{asset('images/assets/documentation/tax-type/docs tax-type-3.png')}}">
    </div>
</div>



@endsection