@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
    <div>
        <div class="title-style"><strong>Login</strong></div>
        <div class="content-style">
            <p>Untuk memulai program ini buka link pada browser dan lakukan proses login untuk dapat melanjutkan penggunaan program</p>
            <img src="{{asset('images/assets/documentation/login/docs login-0.png')}}">
            <p>
                Masukan email yang sudah di daftarkan pada text box “Username/Email” yang sudah di sediakan lalu masukan password yang seuai pada textbox “Password” lalu tekan tombol “Login” untuk melanjutkan.
                Sebelum melakukan login anda dapat mencheklist “Remember Me” untuk membuat anda tetap dalam keadaan login walau anda menutup program ini.
            </p>
        </div>

    </div>

@endsection