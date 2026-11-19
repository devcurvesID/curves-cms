@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Invoice Product</strong></div>
    <div class="content-style">
        <p>
            Fitur Invoice Produk ini adalah fitur yang digunakan untuk menangani retur produk jika ada member yang ingin melakukan retur (pengembalian produk).
            Clicklah tombol Invoice Produk pada menu Dokumen untuk masuk padaa fitur Invoice Produk ini.
        </p>
        <img src="{{asset('images/assets/documentation/invoice-product/docs invoice-product-0.png')}}">
        <p>Pada menu ini click lah lagi tombol Invoice Produk untuk proses pereturan produk. Pada menu ini akan diperlihatkan pembelian produk yang pernah dilakukan</p>
        <img src="{{asset('images/assets/documentation/invoice-product/docs invoice-product-1.png')}}">
        <p>Carilah pembelian yang dilakukan untuk barang yang akan diretur. Click nomor Pembayaran pembelian untuk menampilkan detail pebayaran.</p>
        <img src="{{asset('images/assets/documentation/invoice-product/docs invoice-product-2.png')}}">
        <p>Jika pembelian yang dicari sudah ditemukan, pada detail pembelian produk tersebut clicklah tombol Pengembalian Produk.</p>
        <img src="{{asset('images/assets/documentation/invoice-product/docs invoice-product-3.png')}}">
        <p>
            Isi detail pengembalian produk, jika tipe pengembalian produk akan digantikan dengan voucher, masukanlah kode voucher, lalu masukan tanggal berlaku voucher.
            Pilih produk yang akan dikembalikan dengan menchecklist checkbox pada sisi kiri tabel pada baris produk yang akan dikembalikan.
            Lalu masukan jumlah barang yang akan dikembalikan.
        </p>
        <img src="{{asset('images/assets/documentation/invoice-product/docs invoice-product-4.png')}}">
        <p>Jika pengembalian berupa tunai, maka kode voucher dan masa berlaku voucher tidak perlu di isi lagi.</p>
        <p>Click tombol ajukan untuk mengajukan pengembalian produk ini.</p>
        <img src="{{asset('images/assets/documentation/invoice-product/docs invoice-product-5.png')}}">
        <p>Pada menu sebelumnya clicklah tombol Inventaris Pengembalian Barang untuk melakukan pengembalian produk pada inventory.</p>
        <img src="{{asset('images/assets/documentation/invoice-product/docs invoice-product-6.png')}}">
        <p>Pada menu ini akan ditampilkan semua proses retur pada club. Clicklah nomor pengembalian untuk menampilkan detail pengembalian. </p>
        <img src="{{asset('images/assets/documentation/invoice-product/docs invoice-product-7.png')}}">
        <p>Check checkbox pada kolom ubah lalu masukan  jumlah produk pada kolom Tambah Jumlah sesuai dengan jumlah produk yang diterima, lalu click Ajukan untuk melakukan perubahan inventory.</p>

    </div>
</div>


@endsection