@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
    <div>
        <div class="title-style"><strong>Hak Akses Menu</strong></div>
        <div class="content-style">
            <p>
                Pada halaman ini hak akses karyawan dapat diubah.
                Untuk melakukan pengaturan hak akses untuk karyawan clicklah tombol Hak Akses Menu pada menu Pengaturan
            </p>
            <img src="{{asset('images/assets/documentation/privilege-access/docs privilege-access-0.png')}}">
            <p>Pilihlah club tempat karyawan yang ingin diubah hak akases nya bekerja</p>
            <img src="{{asset('images/assets/documentation/privilege-access/docs privilege-access-1.png')}}">
            <p>Pilih karyawan yang akan diubah hak aksesnya.</p>
            <img src="{{asset('images/assets/documentation/privilege-access/docs privilege-access-2.png')}}">
            <p>Clicklah toogle on dan off untuk mengganti hak akses karyawan. Jika pengaturan sudah sesuai dengan yang diinginkan simpan perubahan tersebut.</p>

        </div>
    </div>
@endsection