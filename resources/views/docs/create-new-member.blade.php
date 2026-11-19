@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Menambah Anggota</strong></div>
    <div class="content-style">
        <p>Untuk menambahkan anggota baru pada program ini pertama-tama masuk pada menu calon anggota sehingga muncul tampilan seperti pada gambar dibawah</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-0.png')}}">
        <p>Jika calon anggota yang akan dimasukan mengetahui Curves melalui referensi dari teman yang sudah bergabung click tombol yang berlabelkan “Referensi Anggota”</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-1.png')}}">
        <p>
            Masukan nama dan noor telepon dari calon anggota yang akan dimasukan lalu tekan tombol “Pilih Referensi Anggota” untuk mencari nama anggota yang menjadi refferensi calon anggota baru. Setelah memasukan data secara lengkap anda perlu menympan data tersebut dengan menggunakan tombol “Simpan ” pada sisi kanan bawah program.
            Jika calon anggota yang mendaftar bukan melalui member yang sudah ada sebelum nya maka pada menu calon anggota tekan tombol “Bukan Referensi Anggota” sehingga muncul tampilan
        </p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-2.png')}}">
        <p>
            Click tombol bertulisan “Pilih Sumber” lalu pilih sumber yang sesuai yang membuat calon member mengetahui tentang Curves setelah memasukan data member kedalam kolom yang sesuai tekan tombol “Simpan” untuk menyimpan data calon member.
            Setelah data lead tersimpan, pada menu “Calon Member” akan muncul data tersebut.
        </p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-3.png')}}">
        <p>
            Agar lead tersebut dapat diajukan untuk melakukan FA maka check list lah check box kontak yang ada pada sisi kanan data lead tersebut setelah men check list check box tersebut maka tanggal janji temu serta waktu janji temu dan memasukan jumlah panggilan tak terjawab pada kolom “Tidak Ada Jawaban”.
            Jika data-data tersebut sudah diisi maka anda dapat menekan tombol “Ajukan” yang terletak pada kanan bawah halaman.
            Jika sudah di ajukan dan melakukan first appoinment kembalilah ke menu utama dan masuklah ke menu “Janji Temu” .
        </p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-4.png')}}">
        <p>Pada menu ini anda akan mengolah data lead yang diajukan setelah melakukan FA</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-5.png')}}">
        <p>Pilih lah tanggal yang ada FA yang terjadwal. Anda dapat mengganti tampilan jadwal dengan skala per bulan atau perhari dengan menggunakan tombol yang ada pada kanan atas tabel jadwal.</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-6.png')}}">
        <p>
            Selain itu anda dapat melihat jadwal pada minggu/bulan sebelum nya atau melihat jadwal pada minggu/bulan depan dengan menggunakan tombol kiri kanan pada kiri atas tabel.
            Setelah menemukan jadwal  FA yang dituju click lah tanggal tersebut sehingga anda dapat melihat data FA nya.
        </p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-7.png')}}">
        <p>
            Jika sudah melakukan FA dengan lead, checklist lah checkbox pada kolom “Hadir” setelah check box tersebut di checklist maka checkbox pada kolom “Jual” dapat di checklist, checklist lah checkbox tersebut jika lead akan bergabung menjadi member setelah melaakukan FA lalu tekan tombol “Simpan”.
            Jika yang dichecklist hanya kolom hadir saja maka artinya lead tersebut menolak untuk menjadi member.
            Jika hal tersebut terjadi maka akan ada data yang ahrus dilengkapi dipaling bawah halaman.
        </p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-8.png')}}">
        <p>
            click tombol “Lengkapi Data FA” untuk melengkapi data dari lead yang tidak jadi bergabung menjadi member.
            Tombol disebelah tombol “Lengkapi Data FA” adalah tombol untuk mengembalikan lead ke kondisi sebelum melakukan FA, tombol ini berguna jika terjadi salah input atau ternyata lead berubah pikiran dan ingin menjadi member.
            Jika lead berniat menjadi member maka tampilan akan sedikit berbeda.
        </p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-9.png')}}">
        <p>Jika lead berniat menjadi member maka data yang harus dilengkapi adalah pembayaran.</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-10.png')}}">
        <p>Pilih lah salah 1 tipe keanggotaan yang akan di ambil oleh member. Setelah memilih salah 1 dari tipe kenggotaan yang di pilih maka akan muncul menu pembayaran sesuai dengan harga yang sudah ditentukan sebelumnya.</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-11.png')}}">
        <p>Setelah disesuaikan dengan pembayaran tekanlah tombol “Bayar”. Dan akan memunculkan form ini pada browser</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-12.png')}}">
        <p>Jika sudah sesuai tekanlah tombol “Proses”.</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-13.png')}}">
        <p>
            Detail pembayaran akan muncul setelahnya.
            Detail ini dapat diprint langsung dengan menggunakan tombol biru di sisi atas kanan halaman.
            Setelah pembayaran selesai data lengkap member perlu dilengkapi kembalilah ke menu utama lewat menu sebelah kiri halaman.
            Dari menu utama masuklah ke menu “Janji Temu”.
            Pada bagian bawah halaman akan muncul tampilan sepeprti gambar dibawah
        </p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-14.png')}}">
        <p>Click tombol “Lengkapi Data Anggota” untuk masuk ke detail anggota.</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-15.png')}}">
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-16.png')}}">
        <p>Jika data sudah dilengkapi tekanlah tombol “Simpan” maka proses merubah lead menjadi member selesai.</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-17.png')}}">
        <p>Hasilnya dapat dilihat dengan masuknya lead yang baru dibuat ke daftar anggota.</p>
        <p>Jika ada lead yang ingin menjadi member dengan cara walk in maka masuk lah ke menu Anggota Baru dari menu utama lalu tekan lah tombol bulat berwarna ungu pada kanan atas halaman.</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-18.png')}}">
        <p>Dengan menekan tombol tersebut akan menampilkan form walk in seperti  pada gambar di bawah.</p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-19.png')}}">
        <p>
            Jika lead tertarik menjadi anggota tekan lah tombol “+ Anggota” jika tidak tertarik menjadi member tekan lah tombol “+ Tidak Terjual” jika lead tertarik menjadi member sekaligus ingin melakukan pembayaran maka tekanlah tombol “Pembayaran”, tombol itu akan membuat member dan langsung masuk kedalam menu pembayaran member tersebut.
            Proses yang lain nya akan sama seperti proses membuat member melalui FA.
        </p>
        <p>Dengan adanya member yang sduah terdaftar maka ada cara lain untuk mendaftarkan lead yaitu dengan masuk kedalam menu “Daftar Anggota” lewat menu utama. </p>
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-20.png')}}">
        <img src="{{asset('images/assets/documentation/add-new-member/docs add-new-member-21.png')}}">
        <p>Setelah masuk tekanlah tombol paling kanan di kolom “Lain-lain” pada table anggota untuk masuk ke halaman “Tambah Calon Anggota”.</p>
    </div>

</div>

@endsection