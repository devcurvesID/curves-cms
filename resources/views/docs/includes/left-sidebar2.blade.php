<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="separator-menu text-center"><strong>FITUR</strong></div>
    <!--
    <div><a>Calon Anggota</a></div>
    <div><a>Anggota Baru</a></div>
    <div><a>Data Anggota</a></div>
    -->
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
    <!-- <div><a>Penangguhan Keanggotaan</a></div> -->
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

    <!-- <div><a>Hutang</a></div> -->
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/product')}}">Produk</a></div></div>
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
                <div><a href="{{url('docs/shirt-size-product')}}">Laporan Ukuran Baju</a></div>
                <div><a href="{{url('docs/product-report')}}">Laporan Produk</a></div>
                <div><a href="{{url('docs/staff-selling-report')}}">Laporan Penjualan Staff</a></div>
                <div><a href="{{url('docs/fa-staff-report')}}">Laporan FA Staff</a></div>
                <div><a href="{{url('docs/wm-difference-report')}}">Laporan Selisih WM</a></div>
                <div><a href="{{url('docs/top-losers-report')}}">Top Losers</a></div>
                <div><a href="{{url('docs/wo-report')}}">Laporan Latihan Anggota</a></div>
                <div><a href="{{url('docs/challenge-report')}}">Laporan Tantangan</a></div>
                <div><a href="{{url('docs/reward-report')}}">Laporan Penghargaan</a></div>
                <div><a href="{{url('docs/member-cancellation-report')}}">Laporan pembatalan Anggota</a></div>
                <div><a href="{{url('docs/appointment-report')}}">Laporan Janji Temu</a></div>
                <div><a href="{{url('docs/member-hold-report')}}">Laporan Keanggotaan Ditangguhkan</a></div>
                <div><a href="{{url('docs/absent-member-report')}}">Laporan Anggota Tidak Hadir</a></div>
                <div><a href="{{url('docs/not-wo-member')}}">Laporan Anggota yang Belum WO</a></div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseMoneyManagement" aria-expanded="false" aria-controls="collapseMoneyManagement">
                    Manajemen Uang
                </a>
            </h4>
        </div>
        <div id = "collapseMoneyManagement" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div><a>Deposito Anggota</a></div>
                <div><a>Laporan Penjualan Harian</a></div>
            </div>
        </div>
    </div>

    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/refund')}}">Pengemblian Dana</a></div></div>

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
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/locker-rental')}}">Penyewaan Loker</a></div></div>

    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/product-request')}}">Permintaan Produk</a></div></div>
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/community-partner')}}">Community Partner</a></div></div>
    <!-- <div><a>Daftar Hadir Calon Anggota</a></div> -->

    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/other-setting')}}">Pengaturan Lain</a></div></div>
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
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/travel-pass')}}">Kartu Perjalanan</a></div></div>
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/login-manual')}}">Login Manual</a></div></div>


    <div class="separator-menu text-center"><strong>PROSES</strong></div>

    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/create-new-member')}}">Menambah Anggota Baru</a></div></div>
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/extend-membership')}}">Memperpanjang Keanggotaan</a></div></div>
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/membership-cancellation')}}">Membatalkan Keanggotaan</a></div></div>
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/change-club')}}">Pindah Klub</a></div></div>
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/payment')}}">Pembayaran</a></div></div>
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/weight-and-measurement')}}">Weight and Measurement</a></div></div>
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/login')}}">Login</a></div></div>
    <div class="panel-default panel"><div class="panel-heading"><a href="{{url('docs/password-recovery')}}">mengembalikan password</a></div></div>



</div>

<script>



</script>

<style>
    div.separator-menu{
        margin-top: 10px;
        margin-bottom: 10px;
        background-color: #9B59B6;
        font-size: 20px;

    }


</style>