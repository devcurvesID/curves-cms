@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Produk</strong></div>
    <div class="content-style">
        <p>
            Pembelian produk yang dilakukan  perlu dicatat oleh program.
            Fitur pembelian produk pada program ini dapat diakses dengan meng click tombol Produk pada Menu Utama
        </p>
        <img src="{{asset('images/assets/documentation/product/docs product-0.png')}}">
        <img src="{{asset('images/assets/documentation/product/docs product-1.png')}}">
        <p>
            Gambar diatas adalah tampilan pada menu Produk.
            Secara default produk yang dijual akan difilter untuk anggota, tetapi dapat diubah untuk filter retail dengan mengganti radio button ke Retail setelah itu tekan tombol search  yang bersimbol kaca pembesar untuk mengubah filter menjadi Retail.
            Untuk melakukan pembelian pilihlah barang yang akan dibeli dengan jumlah yang sesuai lalu click Masukan Keranjang.
            Jika barang-barang yang akan dibeli sudah massuk keranjang click tombol Pemabayaran untuk melanjutkan ke langkah berikutnya
        </p>
        <img src="{{asset('images/assets/documentation/product/docs product-2.png')}}">
        <p>
            Gambar diatas adalah contoh Keranjang dengan pembelian yang sudah dilakukan.
            Pembelian dapat diubah jika dirasa kurang seuai dengan mengganti banyak dari barang ataupun dengan menghapus produk.
            Jika sudah sesuai click  tombol Bayar untuk melakukan transaksi.
        </p>
        <img src="{{asset('images/assets/documentation/product/docs product-3.png')}}">
        <p>
            Setelah itu akan ditampilkan detail pembayaran pada transaksi ini.
            Cari dan pilihlah nama pembeli yang melakukan transaksi ini, lalu pilih metode pembayaran.
            Pembeli dapat menggunakan voucher yang sudah dibuat dan dikeluarkan untuk mendapatkan potongan harga.
            Masukan kode voucher lalu click tombol periksa disampingnya untuk mencheck potongan harga voucher yang dapat didapatkan.
            Total harga secara otomatisa akan terubah sesuai dengan nilai voucher yang ada.
            Jika sudah sesuai click tombol Bayar untuk menyelesaikan transaksi.
            Setelah itu akan ditampilkan detail transaksi yang dapat diprint.
        </p>
        <p>
            Selain melakukan transaksi dengan tipe standar, transaksi yang terjadi mungkin juga dengan tipe penghargaan.
            Untuk mengganti dari tipe standar ke tipe penghargaan, clicklah toogle bertulisan Standar untuuk merubahnya menjadi Penghargaan.
        </p>
        <img src="{{asset('images/assets/documentation/product/docs product-4.png')}}">
        <p>
            Jika tipe sudah menjadi Penghargaan akan muncul input Tantangan masukan tantangan yang sudah diselesaikan.
            Dengan memilih tipe penghargaan maka harga barang akan mendapatkan potongan harga yang sesuai dengan penghargaan yang sudah di set sebelumnya.
        </p>

    </div>



</div>


@endsection