@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
<div>
    <div class="title-style"><strong>WhatsApp</strong></div>
    <div class="content-style">
        <p>Jika ingin menyampaikan pesan melalui Whatsapp masuklah ke menu Whatsapp Center</p>
        <img src="{{asset('images/assets/documentation/wa/docs wa-0.png')}}">
        <img src="{{asset('images/assets/documentation/wa/docs wa-1.png')}}">
        <p>
            Pilih lah siapa yang akan menerima pesan yang dikirimkan lewt program ini dengan memilih dulu category penerima yang terletak pada sisi kiri atas halaman, jika ditujukan pada sumua member pilihlah pilihan Pesan Bebas.
            Lalu pilih siapa yang akan meenrima pesan tersebut dengan menchecklist checkbox member yang muncul dibawah setelah memilih kategori member.
            Anda dapat menchecklist checkbox bertulisan Pilih Semua untuk menchecklist semau member yang muncul pad tabel tersebut.
            Tulislah pesan yang akan dikirimkan pada member pada textbox besar yang tersedia, tetapi jika template sudah terssedia gunakanlah template tersebut pada selectbox di atas textbox besar.
            Jika sudah selesai kirimlah pesan tersebut dengan menggunakan tombol Kirim Pesan pada sisi bawah halaman.
        </p>
    </div>

</div>


@endsection