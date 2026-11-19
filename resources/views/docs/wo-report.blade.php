@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Laporan Latihan Anggota</strong></div>
    <div class="content-style">
        <p>Untuk melihat laporan mengenai latihan anggota clicklah tombol Laporan Latihan Anggota pada menu Laporan.</p>
        <img src="{{asset('images/assets/documentation/report/docs wo-report-0.png')}}">
        <p>
            Pada menu ini kana ditampilkan daftar anggota dan jumlah latihan yang masing-masing anggota sudah lakukan.
            Untuk melihat detail dari latihan yang dilakukan oleh spesifik anggota clicklah nama anggota tersebut.
            program akan menampilkan tanggal dan jam anggota terebut melakukan latihan.
        </p>
        <img src="{{asset('images/assets/documentation/report/docs wo-report-1.png')}}">
        <p>Jika anda ingin melihat laporan mengenai banyak nya latihan yang dilakukan oleh member pada waktu terterntu clicklah tombol bergambar jam pada sisi kanan atas halaman Daftar Latihan.</p>
        <img src="{{asset('images/assets/documentation/report/docs wo-report-2.png')}}">
        <p>
            Pada halaman ini akan ditampilkan total latihan yang dilakukan oleh anggota dan waktu latihan yang dilakukan.
            Jika anda ingin melihat total latihan pada beberapa tanggal tertentu anda dapat mengclick tanggal apda calendar yang tersedia pada bagian atas atas halaman.
            setelah di click tanggal akan terlihat menjadi warna merah itu pertanda jika tanggal tersebut sudah dipilih untuk menampilkan total latihan pada tabel.
            Tanggal yang dipilih dapat lebih dari 1 tanggal.
            Setelah semua tanggal yang inginditampilkan sudah dipilih  click tombol cari pada bagian bawah calendar maka program akan menampilkan total latihan pada tanggal tersebut.
        </p>
        <p>Pada menu Laporan Latihan anda dapat melihat laporan untuk flag latihan anggota dengan mengclick tombol bersimbol bendera.</p>
        <img src="{{asset('images/assets/documentation/report/docs wo-report-3.png')}}">
        <p>Pada halaman ini akan ditampilkan daftar anggota dan flag latihan yangs udah merekan lakukan.</p>
        <p>Pada sisi kanan tombol untuk melihat laporan mengenai flag anggota pada menu Laporan Latihan ada tombol untuk melihat rangkuman mengenai jumlah latihan.</p>
        <img src="{{asset('images/assets/documentation/report/docs wo-report-4.png')}}">
        <p>Halaman ini menaampilkan jumlah latihan yang dilakukan dan persentase latihan dari jumlah anggota, dan persentase latihan yang dilakukan per mingggu.</p>

    </div>
</div>

@endsection