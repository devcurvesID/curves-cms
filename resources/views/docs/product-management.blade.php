@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pengaturan Produk</strong></div>
    <div class="content-style">
        <p>
            Pengaturan data mengenai produk yang dijual oleh club ada pada halaman Pengaturan Produk, untuk masuk kemanu tersebut pertama-tama masuklah ke menu Pengaturan melalui tombol Pengaturan Aplikasi dari menu utama
        </p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-0.png')}}">
        <p>Lalu masuk ke menu Pengaturan Produk</p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-1.png')}}">
        <p>Untuk melakukan perubahan, penambahan ataupun penghapusan tipe produk clicklah tombol Daftar Tipe Produk</p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-2.png')}}">
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-3.png')}}">
        <p>Pada halaman ini akan ditampilkan daftar tipe produk yang ada. Untuk menambah tipe produk baru gunakanlah tombol + pada sisi kanan atas tabel.</p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-4.png')}}">
        <p>
            Form pada gambar diatas akan muncul jika adna hendak menambah tipe produk yang baru.
            Masukan nama tipe produk yang akan ditambahkan lalu click tombol Tambah Tipe.
        </p>
        <p>
            Jika tipe produk tidak sesuai atau ingin diubah gunakan tombol Ubah yang berwarna kuning pada tabel pada kolom tipe produk yang sessuai.
        </p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-5.png')}}">
        <p>
            Sama seperti fitur untuk menambah tipe produk, form untuk mengubah tipe produk akan muncul.
            Clicklah tombol Ubah pada for tersebut untuk menyimpan perubahan yang ada.
            Jika tipe produk ingin dihapus hapuslah dengan menggunakan tombol Hapus berwarna merah pada tabel.
        </p>
        <p>
            Untuk melakukan pengaturan pada daftar produk yang ada click tombol Daftar produk pada menu Pengaturan Produk
        </p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-6.png')}}">
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-7.png')}}">
        <p>
            Pada menu ini akan ditampilkan semua daftar produk yang ada.
            Jika data terlalu banyak anda dapat menggunakan filter produk ataupun melakukan pencarian terhadapat produk spesifik pada sisis kiri atas.
            Untuk melakukan penambahan produk baru gunakan lah tombol + pada sisi kanan atas halaman.
        </p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-8.png')}}">
        <p>Masukan nama produk dan kategori produk lalu click tombol Simpan.</p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-9.png')}}">
        <p>
            Setelah di Simpan akan ditampilkan halaman Detail Produk untuk mensseting produk secara lebih detail.
            Masukan detail data barang lalu click tombol Simpan untuk menyimpan produk tersebut.
        </p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-10.png')}}">
        <p>Dibagian bawah dari halaman Detail produk disediakan juga fitur untuk memberikan diskon global (berlaku pada semua club yang ada) terhadap Produk tersebut.</p>
        <p>
            Pada sisi kiri dari tombol untuk menambah produk secara satuan, ada tombol untuk melakukan penambahan produk secara masal.
            Penmbahan produk secara masal ini hanya dapat digunakan untuk 1 tipe produk saja.
        </p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-11.png')}}">
        <p>
            Masukan nama produk-produk yang akan ditambahkan secara masal.
            Set tipe produk dengan menggunakan select box pada sisi kanan atas tabel.
            Jika jumlah baris pada tabel kurang, dapat ditambahkan dengan menggunakan tombol + pada sisi kanan atas.
            Simpan semua produk yang akan ditambahkan dengan tombol Simpan
        </p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-12.png')}}">
        <p>Untuk menambah stok dari produk pada club masuklah ke menu Persediaan Produk.</p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-13.png')}}">
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-14.png')}}">
        <p>
            Pada halaman ini akan ditampilan produk-produk yang masih tersedia pada club yang dipilih, jika persediaan tinggal sedikit pada sisi atas akan ditampilkan peringatan akan produk yang stoknya perlu ditambah.
            Tambah persediaan produk dengan tombol Tambah Persediaan Barang.
        </p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-15.png')}}">
        <p>
            Untuk menambah persediaan produk checklist lah checkbox yang ada pada sisi kiri tabel terlebih dahulu, lalu masukan jumlah stok yang ditambahkan.
            Lalu click tombol Ajukan pada sisi bawah halaman untuk menambahkan produk.
        </p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-16.png')}}">
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-17.png')}}">
        <p>
            Pada menu Persediaan Produk detail persediaan produk dapat dilihat dengan mengclick nama produk yang akan dilihat.
            Pada halaman ini anda dapat mengatur diskon untuk produk yang bersangkutan.
            Diskon pada halaman ini adalah diskon khusus padaa club tersebut dan tidak berlaku pada club lain.
        </p>
        <p>
            Jika terjadi kekeliruan dalam stok produk seperti salah input atau ada produk yang rusak, kekeliruan tersebut dapat ditangani dengan masuk ke Menu Perbaikan Stok Produk
        </p>
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-18.png')}}">
        <img src="{{asset('images/assets/documentation/product-management/docs product-management-19.png')}}">
        <p>
            Pada halaman ini kekeliruan yang terjadi atas stok produk dapat diperbaiki.
            Checklist lah checkbox pada kolom Ubah lalu perbaiki kesalahan jumlah stok produk yang ada.
            Jika sudah selesai simpan lah perubahan dengan tombol Ajukan.
        </p>




    </div>
</div>
@endsection