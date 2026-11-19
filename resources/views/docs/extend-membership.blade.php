@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Perpanjang Membership</strong></div>
    <div class="content-style">
        <p>Anggota yang sudah tidak aktif dapat dilihat detailnya pada menu Pembatalan Anggota</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-0.png')}}">
        <p>Pada menu ini akan ditampilkan daftar anggota yang sudah tidak aktif.</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-1.png')}}">
        <p>Setiap anggota yang tidak aktif dapat diaktifkan kembali dengan menggunakan tombol pada sisi kanan tabel.</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-2.png')}}">
        <p>Setelah itu anda akan masuk ke halaman pilih tipe keanggotaan seperti pada saat membuat member baru dan akan memiliki langkah yang sama dengan membuat member baru untuk menyelesaikan pengaktifan member.</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-3.png')}}">
        <p>Setelah proses pemilihan tipe keanggotann selesai  perlu ada konfirmasi untuk melakukan pengaktifan anggota tersebut sebelum anggota mulai aktif kembali. Masuklah ke menu Perpanjang Anggota dari menu utama.</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-4.png')}}">
        <p>Setelah itu masuklah ke daftar aktivasi perpanjang Keanggotaan</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-5.png')}}">
        <p>Pada menu ini kan diperlihatkan daftar anggota yang perlu diaktivasi, untuk mengaktivasi anggota-anggota tersebut tekanlah tombol Aktifkan pada sisi kiri bawah tabel</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-6.png')}}">
        <p>Setelah diaktifkan maka anggota-anggota tersebut akan menjadi member aktif, sehingga mereka hilang dari tabel tersebut.</p>
        <p>Jika member yang aktif ingin memperpanjang keanggotaan nya masuklah ke menu Perpanjang Keanggotaan dari menu utama, lalu masuk ke tekanlah tombol Permintaan Perpanjang Keanggotaan.</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-7.png')}}">
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-8.png')}}">
        <p>Pada menu ini akan ditampilan anggota-anggota yang keanggotaan nya dapat diperpanjang saat itu. Tekanlah tombol pada sisi kanan kolom pada baris anggota yang ingin diperpanjang.</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-9.png')}}">
        <p>Pilihlah tipe keanggotaan yang akan diperpanjang sesuai permintaan member, setelah itu cara pembayaran akan sama dengan saat pembuatan member seuai dengan ke tipe anggotaan yang dipilih.</p>
        <p>Tipe keanggotaan member dapat diubah jika member merasa tidak sesuai, masuklah ke Menu Perpanjang Keanggotaan, pada bagian bawah halaman tekanlah tombol Pengajuan Ubah Keanggotaan. </p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-10.png')}}">
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-11.png')}}">
        <p>
            Pada halaman ini akan ditampilkan daftar anggota yang masih aktif.
            Jika member sudah pernah melakukan perpanjangan keangotaan mereka sebelum nya maka mereka perlu untuk membatalkan perpanjangan tersebut terlebih dahulu sebelum dapat melakukan perubahan tipe keanggotaan nya.
            hal ini akan ditandai dengan tombol berwarna merah yang di lock pada kolom paling kanan tabel.
            Untuk member yang dapat diganti keanggotaan nya, ditandai dengan tombol berwana hijau.
        </p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-12.png')}}">
        <p>untuk membatalkan perpanjangan anggota tekanlah tombol Pembatalan Perpanjangan Anggota pada menu sebelumnya.</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-13.png')}}">
        <p>Pada menu ini akan diperlihatkan daftar anggota yang sudah melakukan perpanjangan keanggotaan. Untuk membatalkan perpanjangan tersebut tekanlah tombol merah pada sisi kanan tabel.</p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-14.png')}}">
        <p>Proseslah pengembalian dana tersebut, lalu kembali pada menu Ubah Keanggotaan jika sudah selesai. </p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-15.png')}}">
        <p>
            Member yang sebelumnya tidak dapat diubah keanggotaan nya karena ada permintaan perpanjangan keanggotaan kini sudah dapat diubah keanggotaan nya.
            Untuk menyelesaikan proses perubahan tipe keanggotaan tekanlah tombol hijau pada sisi kanan tabel,
        </p>
        <img src="{{asset('images/assets/documentation/extend-membership/docs extend-membership-16.png')}}">
        <p>Lalu pilihlah tipe keanggotaan yang sesuai dengan keinginan member, dan lengkapi pembayaran sesuai dengan keanggotaan yang dipilih. </p>
    </div>


</div>


@endsection