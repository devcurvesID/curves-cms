@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>Kegiatan Global</strong></div>
    <div class="content-style">
        <p>Untu melakukan pengaturan kegiatan global yang akan dilakukan masuklah ke Menu Pengaturan Aplikasi dan clicklah tombol Kegiatan Global.</p>
        <img src="{{asset('images/assets/documentation/global-activity/docs global-activity-0.png')}}">
        <p>Pilih club yang akan melakukan kegiatan global.</p>
        <img src="{{asset('images/assets/documentation/global-activity/docs global-activity-1.png')}}">
        <p>Pada halaman ini akan ditampilkan kalendar yang akan menampilkan kegiatan global jika ada.</p>
        <img src="{{asset('images/assets/documentation/global-activity/docs global-activity-2.png')}}">
        <p>
            Gambar diatas adalah contoh tampilan yang akan muncul jika ingin menambhakan kegiatan global yang baru.
            Jika kegiatan global yang inginditambahkan berlaku untuk club lain juga click tombol club untuk menampilakn club lain nya, lalu checklist checkbox pada club yang akan mengadakan kegiatan global tersebut juga.
            Pengambilan maksimal adalah jumlah member dapat mengikuti kegiatan global tersebut.
        </p>
        <img src="{{asset('images/assets/documentation/global-activity/docs global-activity-3.png')}}">
        <p>
            Jika ada kegiatan global tampilan aan tampak seperti contoh gambar diatas.
            Kegiatan global dapat diedit dengan mengclick judul kegiatan global tersebut.
        </p>


    </div>


</div>
@endsection