@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Laporan Ukuran Baju</strong></div>
    <div class="content-style">
        <p>
            Untuk melihat ukuran-ukuran baju member masuklah ke menu Ukuran Baju.
            Pada menu tersebut akan ditampilkan daftar ukuran baju jumlah member dan staff yang menggunakannya.
        </p>
        <img src="{{asset('images/assets/documentation/report/docs shirt-size-report-0.png')}}">
        <p>
            Jika ingin melihat daftar nama yang menggunakan ukuran baju tertentu clicklah ukuran baju pada kolom Ukuran, maka akanditampilkan daftar nama yang menggunakan baju tersebut.
        </p>
        <img src="{{asset('images/assets/documentation/report/docs shirt-size-report-1.png')}}">
    </div>

</div>

@endsection