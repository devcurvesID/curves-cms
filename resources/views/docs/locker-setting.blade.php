@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pengaturan Loker</strong></div>
    <div class="content-style">
        <p>Pengaturan untuk loker ada pada menu loker dari menu Pengaturan. Loker dapat diinputkan dengan masuk ke menu Pengaturan Loker.</p>
        <img src="{{asset('images/assets/documentation/locker-setting/docs locker-setting-0.png')}}">
        <p>Untuk memasukan loker baru click tombol + pada halaman tersebut.</p>
        <img src="{{asset('images/assets/documentation/locker-setting/docs locker-setting-1.png')}}">
        <img src="{{asset('images/assets/documentation/locker-setting/docs locker-setting-2.png')}}">
        <p>
            Baris atas adalah nomor awal loker dan baris bawah adalah nomor akhir loker.
            Loker yang terbentuk oleh contoh input pada gambar diatas akan memiliki 15 loker 5 loker A yang terbentuk dari range 1 sampai 5 , 5 loker B yang terbentuk dari range 1 sampai 5, 5 loker C yang terbentuk dari range 1 sampai 5.
            Range 1 sampai 5 dalam contoh ini karena input pada nomor akhir adalah 5.
        </p>
        <img src="{{asset('images/assets/documentation/locker-setting/docs locker-setting-3.png')}}">
        <img src="{{asset('images/assets/documentation/locker-setting/docs locker-setting-4.png')}}">
        <p>
            Jika loker kurang dan ingin ditambahkan masukanlah nomor awal dengan nomor index baru yang akan ditambah (pada contoh nomor awal diisi 6 karena index terakhir loker saat itu adalah 5) pada contoh ini akan terbentuk 5 loker A yang baru (dari 6- 10), 5 loker B yang baru (dari 6- 10) dan 5 loker C yang baru (dari 6- 10).
        </p>
        <img src="{{asset('images/assets/documentation/locker-setting/docs locker-setting-5.png')}}">
        <p>
            Untuk melakukan pengaturan penyewaan loker, click tombol Atur Paket Loker pada menu sebelum nya.
            Click loker tertentu jika loker tersebut ingin dihapus dari program ini.
        </p>
        <img src="{{asset('images/assets/documentation/locker-setting/docs locker-setting-6.png')}}">
        <p>
            Pada halaman ini akan diperlihatkan paket penyewaan loker yang ada.
            Anda dapat menambah paket penyewaan loker yang baru dengan men click Tambah Loker Baru.
        </p>
        <img src="{{asset('images/assets/documentation/locker-setting/docs locker-setting-7.png')}}">
        <p>
            Inputkan data dari penyewaan loker yang akan dibuat.
            Jika sudah click lah tombol Simpan untuk menyimpan data paket penyewaan tersebut.
        </p>
        <p>
            Jika ada member yangs edang menyewa loker dan tanggal penyewaan nya ingin dirubah, pada Menu Loker clicklah tombol Atur Penyewaan Loker.
            Pada halaman tersebut akan ditampilkan daftar loker yang sedang disewa member.
        </p>
        <img src="{{asset('images/assets/documentation/locker-setting/docs locker-setting-8.png')}}">
        <p>Untuk melakukan perubahan data clicklah loker yang akan diubah. </p>
        <img src="{{asset('images/assets/documentation/locker-setting/docs locker-setting-9.png')}}">
        <p>Ubah lah tanggal sesuai dengan apa yang diinginkan, lalu click tombol Ubah untuk menympan perubahan.</p>

    </div>

</div>


@endsection