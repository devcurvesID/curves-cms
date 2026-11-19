@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Permintaan Produk</strong></div>
    <div class="content-style">
        <p>
            Fitur permintaan produk adalah fitur untuk melakukan permintaan penambahan stok produk dari club lain.
            Untuk masuk pada fitur ini click lah tombol Permintaan Produk pada menu utama.
        </p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-0.png')}}">
        <p>Click tombol Permintaan Produk untuk melakukan permintaan penambahan stok produk pada klub-klub lain.</p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-1.png')}}">
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-2.png')}}">
        <p>
            Jika belum ada permintaan tabel daftar permintaan akan kosong.
            Untuk melakukan permintaan produk click lah tombol Permintaan Produk Baru.
        </p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-3.png')}}">
        <p>
            Buat permintaan produk dengan me click tombol Tambah ke Daftar Permintaan pada produk yang ingin ditambahkan dengan jumlah yang sesuai.
            Produk yang diminta akan dicatat terlebih dahulu pada daftar permintaan.
            Untuk mengirim permintaan click tombol Daftar Permintaan.
        </p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-4.png')}}">
        <p>
            Pada halaman ini akan ditampilkan detail daftar permintaan. Permintaan masih dapat diubah jika dirasa tidak sesuai.
            Jika permintaan sudah sesuai dan ingin dikirim, clicklah tombol Kirim Permintaan Produk.
        </p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-5.png')}}">
        <p>
            Permintaan yang dibuat akan terlihat pada halaman Permintaan Produk.
            Untuk melihat detail dari permintaan tersebut clicklah permintaan pada kolom Nomor Permintaan Produk.
            Setelah dikirim maka klub-klub lain dapat melihat permintaan tersebut.
        </p>
        <p>
            pada contoh ini akan dicontohkan cara untuk menerima permintaan dan pengiriman produk pada klub yang meminta.
            Pada klub lain masuklah ke Daftar Permintaan untuk melihat permintaan yang baru saja dibuat pada contoh sebelumnya.
        </p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-6.png')}}">
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-7.png')}}">
        <p>
            Pada halaman ini akan ditampilkan daftar permintaan produk lain.
            Jika anda ingin mengirimkan produk dari klub anda ke klub yang meminta, clicklah nomor permintaan.
        </p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-8.png')}}">
        <p>Detail permintaan produk akan ditampilkan pada halaman ini. jika permintaan akan diterima clicklah tombol Ajukan.</p>
        <p>
            Sampai tahap ini hanyalah sampai menerima permintaan, belum melakukan pengiriman produk yang diminta.
            Jika produk akan dikirim dan dilakukan pendataan clicklah tombol Permintaan Diterima pada menu sebelumnnya.
        </p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-9.png')}}">
        <p>Pada halaman tersebut akan ditampilkan daftar permintaan yang akan dikelola. Clicklah nomor permintaan untuk melakukan pendataan produk yang akan dikirim pada klub yang meminta.</p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-10.png')}}">
        <p>
            Pada tabel checklist lah checkbox pada kolom ubahuntuk melakukan perubahan data pada barang yang akan dikirim.
            Jumlah barang yang ada pada klub anda akan terlihat pada kolom stok.
            Masukanlah jumlah barang yang akan dikirim, jika sudah sesuai clicklah tombol Kirim.
        </p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-11.png')}}">
        <p>Jika sudah dikirim maka status permintaan akan berubah menjadi Sending pada kolom Status.</p>
        <p>Kembali pada klub yang melakukan permintaan, jika permintaan yg dikirim ada yg menerima dan mengirimkan produk yang diminta seperti pada contoh diatas, masuklah ke Produk Diterima</p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-12.png')}}">
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-13.png')}}">
        <p>Clicklah nomor permintaan produk setelah menerima pengiriman.</p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-14.png')}}">
        <p>Masukan pada kolom Jumlah yang Diterima jumlah barang yang diterima. Click tombol Ajukan jika sudah selesai.</p>
        <img src="{{asset('images/assets/documentation/product-request/docs product-request-15.png')}}">
        <p>Status permintaan akan berubah menjadi Finished yang berarti sudah selesai.</p>
        <p>Semua perubahan jumlah stok produk akan diubah secara otomatis oleh program baik pada sisi pengirim ataupun penerima.</p>

    </div>


</div>

@endsection