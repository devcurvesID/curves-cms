@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div >
    <div class="title-style"><strong>Penyewaan Loker</strong></div>
    <div class="content-style">
        <p>Penyewaan loker dapat dicatat oleh program ini. Fitur Peyewaan loker dapat diakses dari menu utama. </p>
        <img src="{{asset('images/assets/documentation/locker-rental/docs locker-rental-0.png')}}">
        <img src="{{asset('images/assets/documentation/locker-rental/docs locker-rental-1.png')}}">
        <p>
            Pada halaman ini akan ditampilkan loker yang ada pada klub.
            Loker yang berwarna ungu menandakan loker tersebut sudah ada member yang menyewa.
            Nama penyewa dan tanggal masa sewa berakhir tertulis pada loker.
            Loker yang berwarna hijau belum ada penyewa dan dapat digunakan.
            Click loker untuk memasukan data penyewa.
        </p>
        <img src="{{asset('images/assets/documentation/locker-rental/docs locker-rental-2.png')}}">
        <p>Click tombol Beli untuk melanjutkan.</p>
        <img src="{{asset('images/assets/documentation/locker-rental/docs locker-rental-3.png')}}">
        <p>Pilih anggota, periksa pemesanan, jika sudah sesuai click tombol Simpan.</p>
        <img src="{{asset('images/assets/documentation/locker-rental/docs locker-rental-4.png')}}">
        <p>Pilih Metode Pembayaran, periksa pembayaran, masukan nilai uang pembayaran yang dibayar oleh member, lalu click tombol Bayar untuk menyelesaikan proses sewa loker tersebut.</p>
        <img src="{{asset('images/assets/documentation/locker-rental/docs locker-rental-5.png')}}">
        <p>Proses penyewaan selesai dan data sudah disimpan pada program. Program akan menampilkan halaman detail pembayaran. Click tombol kiri atas untuk print detail pembayaran jika diperlukan.</p>
        <p>Jika member ingin melakukan perpanjangan sewa loker, cari dan clicklah loker yang sedang disewa oleh member tersebut, lalu masukan durasi perpanjangan loker seperti pada saat penyewaan pertama.</p>


    </div>


</div>
@endsection