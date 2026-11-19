@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Pengendalian Lalu Lintas Penjualan</strong></div>
    <div class="content-style">
        <p>
            Laporan Lalu Lintas Penjualan adalah fitur untuk menampilkan laporan mengenai penjualan club.
            Informasi yang ditampilkan akan mencakup sumber informasi member, staff yag melakukan FA, informasi mengenai keuangan, tipe keanggotaan, dan informasi relevan lain nya.
        </p>
        <img src="{{asset('images/assets/documentation/report/docs traffic-control-report-0.png')}}">
        <p>
            Laporan ini juga dilegkapi dengan fitur filter yang dapat digunakan untuk menyaring data yang akan ditampilkan.
        </p>
    </div>



</div>


@endsection