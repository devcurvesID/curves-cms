<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="separator-menu text-center"><strong>FITUR</strong></div>
    <!--
    <div><a>Calon Anggota</a></div>
    <div><a>Anggota Baru</a></div>
    <div><a>Data Anggota</a></div>
    -->
    <!--
    <div class="panel panel-default">
        <div class="panel-heading" role="tab">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseMemberExtention" aria-expanded="false" aria-controls="collapseMemberExtention">
                    Perpanjang Keanggotaan
                </a>
            </h4>
        </div>
        <div id="collapseMemberExtention" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div><a>Permintaan Perpanjangan Keanggotaan</a></div>
                <div><a>Daftar Aktivasi Perpanjangan Keanggotaan</a></div>
                <div><a>Pembatalan Perpanjangan Keanggotaan</a></div>
                <div><a>Pengajuan Pengubahan Keanggotaan</a></div>
            </div>
        </div>
    </div>
    -->
    <!-- <div><a>Penangguhan Keanggotaan</a></div> -->
    <!--
    <div class="panel panel-default">
        <div class="panel-heading" role="tab">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseMemberCancellation" aria-expanded="false" aria-controls="collapseMemberCancellation">
                    Pembatalan Anggota
                </a>
            </h4>
        </div>
        <div id="collapseMemberCancellation" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div><a>Pengajuan Pembatalan Keanggotaan</a></div>
                <div><a>Daftar Pembatalan Keanggotaan</a></div>
                <div><a>Anggota Tidak Aktif</a></div>
            </div>
        </div>
    </div>
    -->
    <!-- <div><a>Hutang</a></div> -->
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/product')}}">Produk</a></h4></div></div>
    <!-- <div><a>Pembayaran</a></div> -->

    <div class="panel panel-default">
        <div class="panel-heading" role="tab">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseReport" aria-expanded="false" aria-controls="collapseReport">
                    Laporan
                </a>
            </h4>
        </div>
        <div id = "collapseReport" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div><a href="{{url('docs/traffic-control-report')}}">Pengendalian Lalu Lintas Penjualan</a></div>
                <div><a href="{{url('docs/fa-source-report')}}">Laporan Sumber FA</a></div>
                <div><a href="{{url('docs/membership-type-report')}}">Laporan Tipe Keanggotaan</a></div>
                <div><a href="{{url('docs/birthday-report')}}">Laporan Ulang Tahun</a></div>
                <div><a href="{{url('docs/member-status-report')}}">Laporan Status Anggota</a></div>
                <div><a href="{{url('docs/shirt-size-report')}}">Laporan Ukuran Baju</a></div>
                <div><a href="{{url('docs/product-report')}}">Laporan Produk</a></div>
                <div><a href="{{url('docs/staff-selling-report')}}">Laporan Penjualan Staff</a></div>
                <div><a href="{{url('docs/fa-staff-report')}}">Laporan FA Staff</a></div>
                <div><a href="{{url('docs/wm-difference-report')}}">Laporan Selisih WM</a></div>
                <div><a href="{{url('docs/top-losers-report')}}">Top Losers</a></div>
                <div><a href="{{url('docs/wo-report')}}">Laporan Latihan Anggota</a></div>
                <div><a href="{{url('docs/challenge-report')}}">Laporan Tantangan</a></div>
                <div><a href="{{url('docs/reward-report')}}">Laporan Penghargaan</a></div>
                <!--<div><a href="{{url('docs/member-cancellation-report')}}">Laporan pembatalan Anggota</a></div>-->
                <div><a href="{{url('docs/appointment-report')}}">Laporan Janji Temu</a></div>
                <div><a href="{{url('docs/member-hold-report')}}">Laporan Keanggotaan Ditangguhkan</a></div>
                <div><a href="{{url('docs/absent-member-report')}}">Laporan Anggota Tidak Hadir</a></div>
                <!--<div><a href="{{url('docs/not-wo-member')}}">Laporan Anggota yang Belum WO</a></div>-->
            </div>
        </div>
    </div>
    <div class="panel panel-default"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/financial-management')}}">Manajemen Uang</a></h4></div></div>

    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/refund')}}">Pengemblian Dana</a></h4></div></div>

    <!-- <div><a>Penimbangan & Pengukuran</a></div> -->
    <div class="panel panel-default">
        <div class="panel-heading" role="tab">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseMemberObservation" aria-expanded="true" aria-controls="collapseMemberObsevation">
                    Pemantauan Anggota
                </a>
            </h4>
        </div>
        <div id="collapseMemberObservation" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div><a href="{{url('docs/workout-member-monitoring')}}">Pemantauan Latihan Anggota</a></div>
                <div><a href="{{url('docs/birthday-member-monitoring')}}">Pemantauan Ulang Tahun Anggota</a></div>
            </div>
        </div>
    </div>
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/locker-rental')}}">Penyewaan Loker</a></h4></div></div>

    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/product-request')}}">Permintaan Produk</a></h4></div></div>
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/community-partner')}}">Community Partner</a></h4></div></div>
    <!-- <div><a>Daftar Hadir Calon Anggota</a></div> -->

    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/other-setting')}}">Pengaturan Lain</a></h4></div></div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseProgramSetting" aria-expanded="true" aria-controls="collapseProgramSetting">
                    Pengaturan Aplikasi
                </a>
            </h4>
        </div>
        <div id="collapseProgramSetting" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div><a href="{{url('docs/club')}}">Klub</a></div>
                <div><a href="{{url('docs/staff')}}">Staff</a></div>
                <div><a href="{{url('docs/product-management')}}">Pengaturan Produk</a></div>
                <div><a href="{{url('docs/voucher')}}">Voucher</a></div>
                <div><a href="{{url('docs/tax-type')}}">Tipe Pajak</a></div>
                <div><a href="{{url('docs/privilege-access')}}">Hak Akses Menu</a></div>
                <div><a href="{{url('docs/functional-role')}}">Peran Fungsional</a></div>
                <div><a href="{{url('docs/attribute')}}">Attribut</a></div>
                <div><a href="{{url('docs/standard-message')}}">Pesan Standar</a></div>
                <div><a href="{{url('docs/challenge')}}">Tantangan</a></div>
                <div><a href="{{url('docs/mutate-staff')}}">Mutasi Karyawan</a></div>
                <div><a href="{{url('docs/global-activity')}}">Kegiatan Global</a></div>
                <div><a href="{{url('docs/global-message')}}">Pesan Global</a></div>
                <div><a href="{{url('docs/membership-type')}}">Tipe Keanggotaan</a></div>
                <div><a href="{{url('docs/locker-setting')}}">Loker</a></div>
                <div><a href="{{url('docs/company')}}">Perusahaan</a></div>
                <div><a href="{{url('docs/certification')}}">Sertifikasi</a></div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseDocument" aria-expanded="true" aria-controls="collapseDocument">
                    Dokumen
                </a>
            </h4>
        </div>
        <div id="collapseDocument" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div><a href="{{url('docs/staff-report')}}">Laporan Staff</a></div>
                <div><a href="{{url('docs/invoice-product')}}">Invoice Produk</a></div>
                <div><a href="{{url('docs/club-assessment')}}">Penilaian Club</a></div>
                <div><a href="{{url('docs/projection-sheet')}}">Lembar Proyeksi</a></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" role="tab">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseMessages" aria-expanded="true" aria-controls="collapseMessages">
                    Pesan
                </a>
            </h4>
        </div>
        <div id="collapseMessages" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div><a href="{{url('docs/message-for-member')}}">Pesan Untuk Anggota</a></div>
                <div><a href="{{url('docs/wa')}}">Whatsapp</a></div>
                <div><a href="{{url('docs/sms')}}">SMS</a></div>
            </div>
        </div>
    </div>
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/travel-pass')}}">Kartu Perjalanan</a></h4></div></div>
    <div class="panel-default panel panel-last"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/login-manual')}}">Login Manual</a></h4></div></div>


    <div class="separator-menu text-center"><strong>PROSES</strong></div>

    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/create-new-member')}}">Menambah Anggota Baru</a></h4></div></div>
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/extend-membership')}}">Memperpanjang Keanggotaan</a></h4></div></div>
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/membership-cancellation')}}">Membatalkan Keanggotaan</a></h4></div></div>
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/change-club')}}">Pindah Klub</a></h4></div></div>
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/payment')}}">Pembayaran</a></h4></div></div>
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/weight-and-measurement')}}">Weight and Measurement</a></h4></div></div>
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title"><a href="{{url('docs/login')}}">Login</a></h4></div></div>
    <div class="panel-default panel"><div class="panel-heading-btn"><h4 class="panel-title panel-last"><a href="{{url('docs/password-recovery')}}">Mengembalikan password</a></h4></div></div>



</div>

<script>

    $(document).ready(function() {
      $('.collapse.in').prev('.panel-heading').addClass('active');
      $('#accordion, #bs-collapse')
        .on('show.bs.collapse', function(a) {
          $(a.target).prev('.panel-heading').addClass('active');
        })
        .on('hide.bs.collapse', function(a) {
          $(a.target).prev('.panel-heading').removeClass('active');
        });
    });


</script>

<style>
    div.separator-menu{
        margin-top: 10px;
        padding-top: 10px;
        padding-bottom: 10px;
        background-color: #9B59B6;
        font-size: 20px;
        border-top-right-radius: 20px;
        border-top-left-radius: 20px;
    }

    body {
      margin: 0;
      font-family: 'Roboto';
      font-size: 14px;
      background: #DABFF0;
    }

    h3 {
      color: #fff;
      font-size: 24px;
      text-align: center;
      margin-top: 30px;
      padding-bottom: 30px;
      border-bottom: 1px solid #eee;
      margin-bottom: 30px;
      font-weight: 300;
    }

    img {
        max-width: 100%;
        padding-top: 10px;
        padding-bottom: 20px;
    }

    div[class*='col-'] {
      padding: 0 30px;
    }

    .wrap {
      box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 3px 1px -2px rgba(0, 0, 0, 0.2), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
      border-radius: 4px;
    }

    a:focus,
    a:hover,
    a:active {
      outline: 0;
      text-decoration: none;
    }

    .panel {
      border-width: 0 0 1px 0;
      border-style: solid;
      border-color: #fff;
      background: none;
      box-shadow: none;
    }

    .panel:last-child {
      border-bottom: none;
    }

    .panel-group > .panel:first-child .panel-heading {
      border-radius: 4px 4px 0 0;
    }

    .panel-group .panel {
      border-radius: 0;
    }

    .panel-group .panel + .panel {
      margin-top: 0;
    }

    .panel-heading {
      background-color: #ffa500;
      border-radius: 0;
      border: none;
      color: #fff;
      padding: 0;
    }

    .panel-last {
        border-bottom-right-radius: 20px;
        border-bottom-left-radius: 20px;
    }

    .panel-heading-btn {
      background-color: #ffa500;
      border-radius: 0;
      border: none;
      color: #fff;
      padding: 0;
    }

    .panel-title a {
      display: block;
      color: #000000;
      padding: 15px;
      position: relative;
      font-size: 16px;
      font-weight: 400;
      background-color: #ffa500;
    }

    .panel-body {
      background: #fff;
      text-align: left;
    }

    .panel:last-child .panel-body {
      border-radius: 0 0 4px 4px;
    }

    .panel:last-child .panel-heading {
      border-radius: 0 0 4px 4px;
      transition: border-radius 0.3s linear 0.2s;
    }

    .panel:last-child .panel-heading.active {
      border-radius: 0;
      transition: border-radius linear 0s;
    }
    /* #bs-collapse icon scale option */

    .panel-heading a:before {
      content: '\e146';
      position: absolute;
      font-family: 'Material Icons';
      right: 5px;
      top: 10px;
      font-size: 24px;
      transition: all 0.5s;
      transform: scale(1);
    }

    .panel-heading.active a:before {
      content: ' ';
      transition: all 0.5s;
      transform: scale(0);
    }

    #bs-collapse .panel-heading a:after {
      content: ' ';
      font-size: 24px;
      position: absolute;
      font-family: 'Material Icons';
      right: 5px;
      top: 10px;
      transform: scale(0);
      transition: all 0.5s;
    }

    #bs-collapse .panel-heading.active a:after {
      content: '\e909';
      transform: scale(1);
      transition: all 0.5s;
    }
    /* #accordion rotate icon option */

    #accordion .panel-heading a:before {
      content: '\e316';
      font-size: 24px;
      position: absolute;
      font-family: 'Material Icons';
      right: 5px;
      top: 10px;
      transform: rotate(180deg);
      transition: all 0.5s;
    }

    #accordion .panel-heading.active a:before {
      transform: rotate(0deg);
      transition: all 0.5s;
    }


</style>