@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Penimbangan & Pengukuran</strong></div>
    <div class="content-style">
        <p>
            Weight and measurement adalah pengukuran untuk berat dan ukuran badan anggota Curves.
            Untuk memasukan data weight and meaurement anggota masuk lah ke menu “Daftar anggota” dari menu utama.
        </p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-0.png')}}">
        <p>Setelah masuk ke menu tersebut tekanlah tombol paling kiri di kolom “Lain-lain”. Masukanlah data-data yang sesuai dengan pengukuran pada member.</p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-1.png')}}">
        <p>
            Sistem akan menjadwalkan weight and measurement member secar otomatis tetapi jika jadwal tersebut berhalangan dan ingin diubah user dapat mengubahnya dengan menchecklist terlebih dahulu checkbox di atas label “Tanggal” setelah semuanya textbox yang di perlukan di isi data tersebut dapat disimpan dengan menggunakan tombol “Simpan”.
        </p>
        <p>
            Selain memasukan data weight and measurement, user juga perlu memasukan data target user dalam weight and measurement nya.
            untuk memasukan data tersebut kemablilah ke menu sebelumnya dan masuk lah ke menu “Tambah Target Penimbangan & Pengukuran”.
            Masukan lah data sesuai dengan target member.
        </p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-2.png')}}">
        <p>
            Setiap weight and measurement member akan disimpan kedalam history weight and measurement masing-masing member.
            History dari weight and measurement setiap member dapat dilihat melalui tombol
        </p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-3.png')}}">
        <p>
            Selain weight and measurement data target member juga memliki history.
            Untuk melihat history dari target member tekanlah tombol yang dilingkari merah seperti gambar dibawah pada baris yang ingin dilihat
        </p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-4.png')}}">
        <img src="{{asset('images/assets/documentation/wm/docs wm-5.png')}}">
        <p>
            Riwayat penimbangan dan pengukuran dapat dihapus dengan menggunakan tombol merah pada sisi kanan tabel.
            Kolom tengah pada tabel dapat de click untuk menjadi link untuk melihat detail weight and measurement.
        </p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-6.png')}}">
        <p>Pada menu “Riwayat Target Penimbangan & Pengukuran”, detail setiap target dapat diliht melalui link yang tersedia pada kolom “Tanggal Penimbangan&Pengukuran”</p>
        <p>Ada cara lain untuk melakukan pengaturan weight and measurement yaitu dengan masuk ke menu “Penimbangan & Pengukuran” melalui menu utama </p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-7.png')}}">
        <p>Pada menu ini akan ditampilkan jadwal untuk weight and measurement semua anggota pada calendar yang ditampilkan pad halaman tersebut.</p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-8.png')}}">
        <p>Untuk menambahkan jadwal pengukuran member dapat dilakukan dengan meng click tombol pada kolom “Jadwal pengukuran”.</p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-9.png')}}">
        <p>Pada menu ini anda dapat menginput tanggal jadwal tanggal member melakukan weight and measurement lagi dengan mengganti kolom tanggal  jika sudah sesuai simpanlah penjadwalan tersebut.</p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-10.png')}}">
        <p>Jika jadwal yang sduah ditetapkan ingin diubah, ubah lah tanggal nya lalu checklist lah checkbox yang ada pad kolom kiri lalu tekanlah tombol “Perbaharui”.</p>
        <p>Jika jadwal weight and measurement sudah di set maka pada kolom jadwal pengukuran akan berubah menjadi </p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-11.png')}}">
        <p>Tanggal weight and measurement member tetap dapat diganti setelahnya karena tombol bergambar kalendar tersebut merupakan link untuk masuk kedalam menu “Jadwal Penimbangan & Pengukuran”.</p>
        <img src="{{asset('images/assets/documentation/wm/docs wm-12.png')}}">
        <p>Jika ada jadwal weight and measurement maka pada calendar pada menu “Penimbangan & Pengukuran” akan tampak seperti pada gambar diatas.</p>
        <p>Pada menu ini anda dapat langsung menambahkan weight and measurement yang baru pada member, caraya adalah dengan menggunakan tombol bersimbol “+” pada  kolom “Tambah” pada tabel dibawah kalendar. </p>
    </div>


</div>


@endsection