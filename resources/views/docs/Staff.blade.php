@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Staff</strong></div>
    <div class="content-style">
        <p>
            Untuk melakukan pengaturan staf anda dapat mengaksesnya melalui pengaturan aplikasi setelah itu masuk kedalam menu staf.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-0.png')}}">
        <img src="{{asset('images/assets/documentation/staff/docs staff-1.png')}}">
        <p>
            Setelah masuk kedalam menu staf anda dapat memilih klub dimana anda akan melakukan perubahan/penambahan/penghapusan staf.
            Setelah masuk kedalam salah 1 klub maka anda dapat melihat list staf yang hadir di dalam nya.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-2.png')}}">
        <p>
            Berikut adalah tampilan jika staf didalam klub yang di pilih masih kosong.
            Sesuai dengan keterangan di atas tombol bersimbol “+” adalah tombol untuk menambah staf yang akan masuk.
            Setelah masuk kedalam menu untuk menambah staf masukanlah data-data mengenai staf ke textbox yang sesuai.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-3.png')}}">
        <p>
            Gambar diatas adalah informasi mengenai apa yang harus di isi kedalam textbox-textbox tersebut.
            Foto untuk staf dapat di click untuk melakukan upload foto staf.
            Setelah semua textbox di isi dengan informasi yang sesuai, anda dapat menyimpan nya dengan tombol “simpan” yang terletak di sisi kanan bawah halaman tersebut.
            Setelah berhasil disimpan anda perlu melengkapi data anggota secara lengkap
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-4.png')}}">
        <img src="{{asset('images/assets/documentation/staff/docs staff-5.png')}}">
        <p>
            Gambar di atas adalah gambar untuk halaman kelengkapan data staf dan beberapa keterangan untuk membantu pengisian yang diperlukan.
            Setelah melakukan pengisian kelengkapan staf maka penambahan staf sudah selelasi hasilnya dapat terlihat pada menu sebelumnya
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-6.png')}}">
        <p>
            Halaman yang awalnya tidak ada member sekarang sudah ada member yang aktif.
            Didalam detail staf masih ada data yang bisa di lengkapi atau diperbaharui lewat tombol yang terletak pada kanan atas halaman.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-7.png')}}">
        <p>
            Menambah catatan adalah fitur yang dapat digunakan untuk menambahkan catatan untuk staf yang bersangkutan.
            Catatan untuk pegawai bisa di buat beberapa catatan bila dirasa perlu.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-8.png')}}">
        <p>
            Gambar di atas adalah gambar halaman saat akan menambah catatan untuk pegawai.
            Jika catatan sudah di tambahkan maka catatan akan muncul pada halaman itu.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-9.png')}}">
        <p>
            Sebagai contoh gambar di atas adalah contoh dari halaman yang sudah memiliki catatan.
            Tanggal yang terletak pada sebelah kiri dari catatan adalah tanggal dibuatnya catatan tersebut, dan disebelah kanan dari catatan terdapat tombol bersimbolkan tempat smpah yang dapat digunakan untuk menghapus catatan.
            Jika catatan telah buat pada tampilan menu “staf” akan ditmpilkan catatan tersebut.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-10.png')}}">
        <p>
            Gambar diatas adalah contoh gambar untuk staf yang sudah diberi catatan pada tampilan menu “staf”.
            Fitur ganti pasword dapat digunakan untuk mengganti password staf.
            Informasi yang diperlukan adalah password sekarang dan password yang akan menggantikanya.
            Fitur sertifikasi staf dapat di gunakan untuk memasukan sertifikasi yang telah diikuti staf.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-11.png')}}">
        <p>
            Jika staf ingin di nonatktifkan dapat dilakukan dengan cara mengclick tombol “Nonaktifkan Karyawan” pada sisi kiri bawah dari halaman Staf.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-12.png')}}">
        <p>
            Jika tombol tersebut di click maka akan muncul  halaman seperti  gambar di bawah
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-13.png')}}">
        <p>
            Form ini berguna untuk mendatailkan alasan, tipe, dan tanggal pe nonactivan.
            Jika nonactivasi berupa cuti maka tanggal akhir cuti perlu dimasukan.
            Tetapi jika pengunduran diri cukup hanya tanggal peng unduran dirinya saja.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-14.png')}}">
        <p>
            Untuk melihat daftar staf yang sedang tidak aktif anda dapat melihatnya dengan meng click tombol pada kanan atas dari halaman staf.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-15.png')}}">
        <p>
            Gambar diatas adalah gambar halaman saat memasuki halaman daftar karyawan yang tidak aktif.
            Daftar tersebut dibagi 2, bagian atas adalah karyawan yang tidak aktif karena sedang cuti dan bagian bawah adalah karyawan yang tidak aktifkarena mengundurkan diri.
            Untuk setiap staf yang mengambil cuti mereka akan kembali aktif bila tombol “Cuti Telah Selesai” di click dan mereka akan memiliki peran yang sama saat karyawan telah aktif kembali.
            Jika karyawan mengundurkan diri, karyawan tetap bisa di aktifkan kembali bila diperlukan.
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-16.png')}}">
        <p>
            Dengan menggunakan tombol “Aktifkan Staff kembali” yang terletak sebelah kanan.
            Jika tombol tersebut ditekan maka anda harus mengisi kembali data yang akan diperlukan oleh staf pada form yang muncul seperti pada gambar diabawah
        </p>
        <img src="{{asset('images/assets/documentation/staff/docs staff-17.png')}}">
        <p>
            Jika form tersebut sudah dilengkapi aktifkan staf kembali dengan tombol “Aktifkan” yang terletak pada kanan bawah form tersebut.
        </p>

    </div>

</div>

@endsection