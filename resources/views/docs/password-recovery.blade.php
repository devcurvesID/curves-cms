@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pengembalian Password</strong></div>
    <div class="content-style">
        <p>jika seandainya anda lupa password anda dapat men click “Forgot Your Password?”. </p>
        <img src="{{asset('images/assets/documentation/password-recovery/docs password-recovery-0.png')}}">
        <p>
            Halaman ini akan terbuka jika tombol “Forgot Your Password?” di click, mesukan email yang didaftarkan pada textbox “E-Mail Address” dan tekan button “Send Password Reset Link”, anda akan menerima email yang berisi paswword baru anda.
        </p>

    </div>

</div>

@endsection