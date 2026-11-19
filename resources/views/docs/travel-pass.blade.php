@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Kartu Perjalanan</strong></div>
    <div class="content-style">
        <p>
            Jika ada member yang akan pergi keluar negri dan pada negara yang dituju member tersebut sudah ada klub curves yang tersedia, member dapat melakukan workout pada klub diluar negri tersebut dengan cara membuat kartu perjalanan.
        </p>
        <p>Untuk melakukan pendataan akan permintaan kartu perjalanan click lah Kartu Perjalanan pada menu utama.</p>
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-0.png')}}">
        <p>Lalu click Penambahan Kartu Perjalanan Baru.</p>
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-1.png')}}">
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-2.png')}}">
        <p>
            Padahalam ini carilah member terlebih dahulu menggunakan fitur pencarian pada sisi kanan halaman.
            Setelah member yang akan dibuat kartu perjalanan nya ditemukan, clicklah member tersebut agar nama member terinput pada Formulir Permohonan.
            Masukan data-data yang sesuai lalu ajukan permintaan tersebut.
        </p>
        <p>Untuk melihat daftar memeber yang sedang memiliki kartu perjalanan, clicklah tombol Kartu Perjalanan Aktif pada menu sebelumnya.</p>
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-3.png')}}">
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-4.png')}}">
        <p>Nama anggota pada kolom Nama Anggota dapat di click untuk melakukan pengolahan data mengenai kartu perjalanan member termasuk latihan anggota selama anggota diluar negri.</p>
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-5.png')}}">
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-6.png')}}">
        <p>Masukanlah absen-absen wo member sesuai dengan yang member lakukan saat diluar negri (prosess ini sama seperti proses login manual).</p>
        <p>Untuk melihat daftar member yang kartu perjalanan nya telah berakhir clicklah tombol Kartu Perjalanan Berakhir pada menu sebelumnya.</p>
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-7.png')}}">
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-8.png')}}">
        <p>Click nama member pada kolom Nama Anggota untuk melihat detail kartu perjalanan.</p>
        <p>Jika ada member curves dari luar negri yang sedang data ke Indonesia, member tersebut melakukan wo juga pada club anda. </p>
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-9.png')}}">
        <p>
            Jika data visiting member belum ada atau sudah expired karena tanggal validnya sudah terlewat, clicklah tombol Tambah Visiting Member Baru untuk membuat data member tersebut.
            Setelah terisi dan disimpan data terebut akan muncul pada tabel yang menampilkan daftar visiting member.
        </p>
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-10.png')}}">
        <p>
            Jika data perlu diubah, clicklah nama member pada kolom Nama Anggota.
            Untuk melakukan pendataan wo pada visiting member click lah tombol Latihan.
        </p>
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-11.png')}}">
        <p>Pilihlah tanggal dimana visiting member melakukan wo (dapat dipilih lebih dari 1 tanggal), setelah itu tekan tombol Simpan</p>
        <img src="{{asset('images/assets/documentation/travel-pass/docs travel-pass-12.png')}}">
        <p>Untuk melihat visiting member yang sudah expired, dapat dilakukan dengan meng click tombol Expired Visiting Member pada menu sebelumnya.</p>
    </div>
</div>

@endsection