@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pembatalan keanggotaan</strong></div>
    <div class="content-style">
        <p>
            Keanggotaan member dapat dibatalkan pada bagian ini akan di contohkan cara untuk membatalkan keanggotaan member pada dummy member.
            Masuk lah ke menu Pembatalan Anggota dari menu utama.
        </p>
        <img src="{{asset('images/assets/documentation/member-cancellation/docs member-cancellation-0.png')}}">
        <p>Setelah itu akan tampil tampilan seperti pada contoh gambar dibawah</p>
        <img src="{{asset('images/assets/documentation/member-cancellation/docs member-cancellation-1.png')}}">
        <p>Tekanlah tombol Pengajuan Pembatalan Anggota untuk melakukan proses pembatalan keanggotaan</p>
        <img src="{{asset('images/assets/documentation/member-cancellation/docs member-cancellation-2.png')}}">
        <p>
            pada tampilan ini tidak semua member ditampilkan, disini hanya ditampilkan member yang bergabung pada 3 hari terakhir saja.
            Jika ingin memproses member yang sudah bergabung lebih dari 3 hari gunakanlah fitur serach dengan memasukan kata kunci yangsesuai.
            Jika member yang dicari memang ada maka member tersebut akan muncul pada tabel.
        </p>
        <img src="{{asset('images/assets/documentation/member-cancellation/docs member-cancellation-3.png')}}">
        <p>Member yang memulai keanggotaan nya kurang dari 3 hari dari waktu pengajuan pembatalan dapat langsung dibatalkan.</p>
        <img src="{{asset('images/assets/documentation/member-cancellation/docs member-cancellation-4.png')}}">
        <p>
            Pada contoh gambar di atas member belum menjadi member selama 3 hari karena tanggal dimulainya keanggotaan adalah 8 Juni 2018 (yang digunakan bukan tanggal bergabung) dan pengajuan tersebut dibuat pada tanggal 6 Juni 2018 (masih 2 hari sejak tanggal mulainya keanggotaan).
            Jika sudah sesuai buatlah pengajuan pendaftaran melalui tombol yang sudah di sediakan.
        </p>
        <img src="{{asset('images/assets/documentation/member-cancellation/docs member-cancellation-5.png')}}">
        <p>
            Sebelum memprosess pembatalan akan ditampilkan detail tentang pembatalan keanggotaan tersebut beerta detail pembayaran yang perlu dicheck.
            Setelah melakukan pengisian dan pemeriksaan pada halaman ini proses lah pembatalan keanggotaan tersebut.
        </p>
        <p>
            Jika member membatalkan keanggotaan nya dalam waktu 3 hari sejak masuk menjadi anggota biaya yang member sudah member bayar untuk menjadi anggota akan dikembalikan.
            Proses pengembalian adana akan seperti pada gambar dibawah.
        </p>
        <img src="{{asset('images/assets/documentation/member-cancellation/docs member-cancellation-6.png')}}">
        <p>
            Dana yang dikembalikan dapat berbentuk uang ataupun voucher.
            Jika pengembalian dana sudah selesai maka tekanlah tombol Simpan dan Kembalikan Dana Sekarang untuk melaksanakan proses tersebut.
        </p>
        <p>
            Jika member yang akan dibatalkan sudah menjadi anggota lebih dari 3 hari maka pembatalan akan memerlukan konfirmasi dan memerlukan waktu 1 bulan untuk melakukan pembatalan.
            Pengajuan pembatalan ini dapat dibatalkan jika member berubah pikiran.
            Untuk membatalkan proses pembatalan keanggotaan masuklah ke menu Daftar Pembatalan Keanggotaan dari menu Pembatalan Anggota.
        </p>
        <img src="{{asset('images/assets/documentation/member-cancellation/docs member-cancellation-7.png')}}">
        <p>
            Setelah masuk ke menu Daftar Pembatalan Anggota, proses pembatalan yang menunggu proses konfirmasi akan ada pada tabel.
            Untuk melihat detail nya tekanlah tombol Keterangan pada sisi kanan tabel.
        </p>
        <img src="{{asset('images/assets/documentation/member-cancellation/docs member-cancellation-8.png')}}">
        <p>Setelah itu akan muncul halaman yang berisi detail pembatalan keanggotaan.</p>
        <img src="{{asset('images/assets/documentation/member-cancellation/docs member-cancellation-9.png')}}">
        <p>Jika pembatalan member ingin dibatalkan tekanlah tombol Batalkan pada sisi bawah halaman.</p>

    </div>


</div>


@endsection