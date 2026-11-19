@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pembayaran</strong></div>
    <div class="content-style">
        <p>
            Selain paket pembayaran “Month to Month” dan “VIP” paket pembayaran lain nya memiliki aturan pembayaran yang berbeda antara pembayaran “Auto Debet” dan pembayaran “Pre-Paid”.
            Untuk “Auto Debet” pembayaran akan dilakukan perbulan sesuai dengan durasi ke anggotaan.
            Untuk “Pre-Paid”, pembayaran akan dilakukan dengan cicilan 2 bulan pertama.
        </p>
        <p>
            Dibawah ini akan dicontohkan pembayaran auto-debet yang dilakukan oleh dummy member yang bertipe keanggotaan “Reguler Auto Debet (12 Bulan)”.
        </p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-0.png')}}">
        <p>
            Pembayaran pertama dilakukan secara tidak full.
            Setelah proses pembayaran pertama selesai (walau tidak full) anda dapat meseting cicilan atau memenuhi pembayaran member pada menu “Anggota”.
        </p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-1.png')}}">
        <p>Jika pembayaran pertama tidak full, pembayaran tersebut dapat di lihat di menu daftar tagihan dari menu daftar anggota.</p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-2.png')}}">
        <p>
            Pembayaran yang tidak full akan terlihat pada tabel pad menu Daftar Tagihan tersebut.
            Untuk melihat detail pembayaran clicklah link pada kolom Tanggal jatuh Tempo.
            Untuk melunasi tagihan tersebut, tekanlah tombol Bayar Sekarang.
        </p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-3.png')}}">
        <p>Isi pembayaran sesuai dengan transaksi yang terjadi. Setelah itu tekan lah tombol Proses. Setelah selesai anda akan masuk ke halaman Pembayaran. </p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-4.png')}}">
        <p>Untuk kembali ke menu utama gunakanlah link yang ada pada sisi kiri halaman. lalu masuklah kembali ke menu Daftar Anggota dan tekanlah tombol untuk mengatur cicilan</p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-5.png')}}">
        <p>
            Pada menu ini akan ditampilkan cicilan yang perlu dibayar member.
            Cicilan yang perlu dibayar oleh member sudah diatur tanggalnya secara otomatis oleh sistem, tetapi jika tanggal tersebut kurang sesuai dengan keinginan tanggal tersebut dapat diubah.
            Jika sudah sesuai keinginan tekanlah tombol Simpan pada bagian bawah halaman.
            Setelah itu halaman Daftar Tagihan akan terbuka secara otomatis dan menampilkan daftar pembayaran yang perlu dilunasi oleh member
        </p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-6.png')}}">
        <p>Pembayaran dapat dilakukan paling cepat adalah 2 minggu sebelum tanggal jatuh tempo yang sudah di tetapkan.</p>
        <p>Proses pembayaran Pre-Pay berbeda dari pembayaran Auto-Debet. Dibawah ini akan di contohkan pembayaran  oleh contoh member yang mengambil tipe keanggotaan Reguler Pre-Paid (12 Bulan).</p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-7.png')}}">
        <p>Sama seperi pada contoh Auto-Debet pada contoh Pre-Paid ini juga pembayaran akan dilakukan secara tidak full.</p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-8.png')}}">
        <p>
            Pada menu Daftar Tagihan pembayaran belum dapat diselesaikan sebelum melakukan pengaturan cicilan.
            Tekanlah tombol Pengaturan Cicilan yang ada pada kolom paling kanan baris untuk melakukan pencicilan dan menyelesaikan pembayaran.
        </p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-9.png')}}">
        <p>
            Pada menu Pengaturan Cicilan program secara otomatis membagi cicilan menjadi 2 cicilan, cicilan pertama di bulan pertama dan cicilan ke dua di bulan kedua sejak waktu pendaftaran.
            Harga yang perlu dibayar pada setiap cicilan adalah harga sisa dari pembayaran pertama dibagi 2.
            Tetapi harga dan cicilan tersebut dapat diubah jika dirasa tidak sesuai.
            Saat harga diubah di bulan pertama atau ke dua maka harga yang perlu dibayar di bulan yang lain nya akan berubah menyesuaikan dengan harga sisanya.
            Jika harga dan tanggal sudah sesuai maka simpanlah setingan cicilan tersebut.
        </p>
        <p>Setelah cicilan disetting maka pada Daftar Tagihan akan muncul tampilan seperti pada gambar dibawah</p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-10.png')}}">
        <p>Jika member berniat membayar pemabayarn terlebih dahulu pembayaran terebut dapat di proses dengan menggunakan tombol Pembayaran Telah Diterima pada kolom paling kanan tabel. </p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-11.png')}}">
        <p>Ajukanlah pembayaran tersebut sesuai dengan transaksi pembayaran yang terjadi. Setelah selesai maka anda akan masuk kedalam halaman Detail Pembayaran.</p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-12.png')}}">
        <p>Setelah pembayaran tersebut selesai maka pembayaran tersebut akan hilang dari daftar Tagihan yang ada.</p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-13.png')}}">
        <p>Setiap pembayaran yang terjadi akan tersimpan pada Daftar Pembayaran, anda dapat melihat detail pembayaran dengan menekan link yang ada kolom Tanggal Jatuh Tempo. </p>
        <img src="{{asset('images/assets/documentation/payment/docs payment-14.png')}}">
        <img src="{{asset('images/assets/documentation/payment/docs payment-15.png')}}">
        <p>Detail pembayaran yang diperlihatkan akan sesuai dengan yang diinputkan pada saat pembayaran.</p>
    </div>

</div>

@endsection