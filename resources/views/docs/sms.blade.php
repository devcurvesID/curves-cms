@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>SMS</strong></div>
    <div class="content-style">
        <p>Untuk membuat pesan yang dikirimkan melalui SMS masuklah ke menu SMS Center dari menu Pesan</p>
        <img src="{{asset('images/assets/documentation/sms/docs sms-0.png')}}">
        <img src="{{asset('images/assets/documentation/sms/docs sms-1.png')}}">
        <p>
            Sama seperti pada menu Whatsapp Center pilihlah kategori member yang akan dikirim, pilihlah member yang akan dikirim pada tabel dibawah, lalu tulislah pesan atau gunakan template pesan.
            Bedanya disini adalah program ini hanya akan mengexport pesan dan daftar anggota ke bentuk Microsoft Excel yang dapat digunakan untuk program SMS Gateway yang tersedia.
            Exportlah file tersebut ke directory yang akan digunakan untuk program SMS Gateway tersebut.
        </p>
    </div>


</div>


@endsection