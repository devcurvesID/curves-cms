@extends('docs.layouts.documentation-template')

@section('title', 'Dokumentasi')


@section('content')
    <div>
        <div class="title-style"><strong>Klub</strong></div>
        <div class="content-style">
            <p>
                menu ini dapat digunakan untu melakukan pengaturan data serta setingan klub yang ada seperti jam buka klub, pajak klub, dan tipe keanggotaan pada klub.
                selain itu penambahan klub juga dapat dilakukan pada menu ini.
            </p>
            <p>Untuk menambah klub baru click symbol “+”</p>
            <img src="{{asset('images/assets/documentation/club/docs club-2.png')}}">

            <p>
                <br/>Setelah masuk  akan muncul tampilan seperti.
                Pada halaman ini akan ditampilkan data-data mengenai klub<br/><br/>
            </p>
            <img src="{{asset('images/assets/documentation/club/docs club-3.png')}}">
            <img src="{{asset('images/assets/documentation/club/docs club-4.png')}}">
            <p>
                Lengkapi data untuk klub yang akan dibuat pada text box yang telah disediakan.
                Jika waktu buka klub masih dirasa kurang, waktu buka klub dapat ditambahkan dengan menekan tombol “+”pada sisi kanan
            </p>
            <img src="{{asset('images/assets/documentation/club/docs club-5.png')}}">
            <p>
                Jika tombol tersebut telah di click maka akan muncul baris baru untuk waktu buka klub.
            </p>
            <img src="{{asset('images/assets/documentation/club/docs club-6.png')}}">
            <p>Jika baris untuk jam buka klub ternyata perlu dihapus maka gunakan tombol hapus disamping kanan dari baris yang perlu dihapus.</p>
            <p>
                Biaya Jasa klub dapat disesuaikan dengan harga yang akan di tentukan untuk setiap baris nya.
                Baris biaya tersebut juga dapat dihapus jika dirasa tidak sesuai dengan menggunakan tombol “hapus ” di samping kanan baris atapun ditambahkan dengan menggunakan tombol “+” di atas tabel.
            </p>
            <img src="{{asset('images/assets/documentation/club/docs club-7.png')}}">
            <p>
                Pada colomn pajak terdapat check box yang menandakan apakah biaya tersebut akan dikenai pajak atau tidak jika biaya tersebut akan dikenai pajak maka checkbox tersebut perlu di checklist tapi jika tidak perlu akan dikenai pajak maka perlu di unchecklist.
            </p>
            <p>
                Jika semua textbox yang perlu disimpan sudah diisi dengan benar langkah terakhir dalam membuat klub tersebut adalah dengan menyimpan nya dengan men click tombol “Simpan Semua” pada sisi kanan bawah pada halaman tersebut.
            </p>
            <p>
                Untuk melakukan perubahan  atau pengeditan pada klub yang sudah di buat adalah dengan memilih klub yang akan diubah pada menu klub pada halaman sebelum nya.
                Setelah masuk anda dapat mengubah data didalam nya sesuai dengan keinginan anda.
                Untuk menyimpan perubahan data yang ingin disimpan anda perlu men click tombol berbentuk disket.
            </p>
            <img src="{{asset('images/assets/documentation/club/docs club-8.png')}}">
            <p>
                Selain dengan tombol berbentuk disket tersebut semua perubahan dapat disimpan sekaligus dengan menggunakan tombol “Simpan Semua” pada sisi paling bawah halaman.
            </p>
            <img src="{{asset('images/assets/documentation/club/docs club-9.png')}}">
            <p>
                Untuk membatalkan semua perubahan yang terjadi pada halaman dapat digunakan tombol “Batalkan” yang terleta sebelah kiri dari tombol “Simpan Semua”.
                Pada sisi kanan atas daripada halaman ini terdapat Tombol “Pajak”, tombol itu dapat digunakan untuk mengatur pajak yang ada.
            </p>
            <img src="{{asset('images/assets/documentation/club/docs club-10.png')}}">
            <img src="{{asset('images/assets/documentation/club/docs club-11.png')}}">
            <p>
                Jika tombol tersebut ditekan akan muncul halaman ini.
                Halaman ini berguna untuk melakukan perubahan data tentang pajak yang akan berlaku pada klub. Untuk melakukan perubahan data pajak pada pajak yang sudah ada pertama-tama check terlebih dahulu checkbox yang terleta pada sisi kiri pada baris yang akan di ubah, setelah dicheck maka data mulai dapat diubah.
                Setelah melakukan perubahan sesuai dengan yang dinginkan simpan perubahan tersebuut dengan men click tombol “Perbahaui Data Terpilih”.
            </p>
            <img src="{{asset('images/assets/documentation/club/docs club-12.png')}}">
            <p>
                Untuk melakukan penambahan data baru dapat dilakukan dengan cara menekan tombol bersimbol “+” pada sisi kanan halaman, setelah di click maka akan muncul form kecil untuk menambah pajak.
                Setelah melengkapi data yang ingin dimasukan tekan tombol “Simpan Konfigurasi Pajak” yang terletak dibawahnya untuk menympan pajak tersebut.
            </p>
            <img src="{{asset('images/assets/documentation/club/docs club-13.png')}}">
        </div>
    </div>



@endsection