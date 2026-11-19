@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pindah Club</strong></div>
    <div class="content-style">
        <p>
            Jika member meminta dipindah ke klub lain (Curves di tempat yang berbeda) hal tersebut dapat dilakukan.
            Caranya adalah dengan masuk kedealam menu Daftar Anggota, lalu tekanlah tombol yang dilingkari merah pada gambar di bawah.
        </p>
        <img src="{{asset('images/assets/documentation/change-club/docs change-club-0.png')}}">
        <p>Setelah itu akan tampil halaman dengan tampilan seperti gambar di bawah</p>
        <img src="{{asset('images/assets/documentation/change-club/docs change-club-1.png')}}">
        <p>Setelah mengisi halaman tersebut sesuai dengan kebutuhan, simpanlah perubahan untuk memindahkan member tersebut.</p>
        <p>Maka pada Daftar Anggota Club tersebut muncul member yang baru saja dipindahkan.</p>
        <img src="{{asset('images/assets/documentation/change-club/docs change-club-2.png')}}">
        <p>Selain data pribadi member, data-data mengenai pembayaran dan tagihan pun ikut dipindahkan ke klub member yang baru.</p>

    </div>


</div>


@endsection