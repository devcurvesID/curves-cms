@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pengaturan Lain</strong></div>
    <div class="content-style">
        <p>Pengaturan password untuk club login ada pada Pengaturan Lain.</p>
        <img src="{{asset('images/assets/documentation/other-setting/docs other-setting-0.png')}}">
        <p>Pilihlah klub yang passwordnya akan diatur</p>
        <img src="{{asset('images/assets/documentation/other-setting/docs other-setting-1.png')}}">
        <p>Masukan username dan password yang sesuai, lalu click toombol Perbaharui.</p>

    </div>

</div>
@endsection