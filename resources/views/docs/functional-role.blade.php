@extends('docs.layouts.documentation-template')

@section('title', 'dokumentasi')

@section('content')
    <div>
        <div class="title-style"><strong>Peran Fungsional</strong></div>
        <div class="content-style">
            <p>
                Menu Peran Fungsional dapat diaksess melalui menu Pengaturan
            </p>
            <img src="{{asset('images/assets/documentation/functional-role/docs Functional-role-0.png')}}">
            <img src="{{asset('images/assets/documentation/functional-role/docs Functional-role-1.png')}}">
            <p>
                Peran Fungsional dapat ditambahkan dengan menggunakan tombol Tambah Peran Fungsional Baru.
                Untuk memperbaharui ataupun menhapus peran fungsional dapat menggunakan tombol Perbaharui ataupun tombol Hapus pada kolom Aksi.
            </p>

        </div>




    </div>


@endsection