<div class="modal fade" id="renewal-ezypay-modal" tabindex="-1" role="dialog" aria-labelledby="renewal-ezypay-title">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" type="button" data-dismiss="modal" aria-label="Tutup">&times;</button>
                <h4 id="renewal-ezypay-title" class="modal-title">Pembayaran Renewal EZYPAY</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <h5>Kontak anggota</h5>
                        <div class="form-group"><label for="renewal-name">Nama</label><input id="renewal-name" class="form-control" required></div>
                        <div class="form-group"><label for="renewal-email">Email</label><input id="renewal-email" class="form-control" type="email" required></div>
                        <div class="form-group"><label for="renewal-phone">Telepon</label><input id="renewal-phone" class="form-control" type="tel" required></div>
                        <div class="form-group"><label for="renewal-cellphone">Ponsel</label><input id="renewal-cellphone" class="form-control" type="tel" required></div>
                        <button type="button" id="renewal-prepare" class="btn btn-default">Simpan kontak dan muat akun</button>
                    </div>
                    <div class="col-sm-6">
                        <h5>Ringkasan renewal</h5>
                        <dl id="renewal-summary"></dl>
                        <p id="renewal-plan"></p>
                        <div class="checkbox"><label><input type="checkbox" id="renewal-self"> Pembayaran Mandiri</label></div>
                        <div class="checkbox" id="renewal-subscription-row"><label><input type="checkbox" id="renewal-subscription"> Subscription</label></div>
                        <label for="renewal-token">Akun pembayaran</label>
                        <select id="renewal-token" class="form-control"><option value="">Pembayaran Mandiri</option></select>
                        <button type="button" id="renewal-add-card" class="btn btn-default btn-sm" disabled>Tambah Kartu</button>
                        <form id="renewal-card-form" method="POST" action="{{ url('cf-lite/payment/bill-list/0/ezypay-card') }}" hidden>
                            @csrf
                            <div class="form-group"><label>Nama pemilik kartu<input name="accountHolderName" class="form-control" maxlength="255" required></label></div>
                            <div class="form-group"><label>Nomor kartu<input name="accountNumber" class="form-control" inputmode="numeric" pattern="[0-9]{12,19}" maxlength="19" autocomplete="off" required></label></div>
                            <div class="form-group"><label>Bulan (MM)<input name="expiryMonth" class="form-control" pattern="0[1-9]|1[0-2]" maxlength="2" required></label></div>
                            <div class="form-group"><label>Tahun (YY)<input name="expiryYear" class="form-control" pattern="[0-9]{2}" maxlength="2" required></label></div>
                            <label><input type="checkbox" name="termAndConditionAgreed" value="1" required> Pemilik kartu menyetujui pendaftaran kartu.</label>
                            <button type="submit" class="btn btn-default">Simpan Kartu</button>
                        </form>
                    </div>
                </div>
                <p id="renewal-ezypay-message" role="status" aria-live="polite"></p>
                <div id="renewal-checkout" hidden>
                    <label for="renewal-checkout-url">Tautan pembayaran untuk customer</label>
                    <input id="renewal-checkout-url" class="form-control" readonly>
                    <a id="renewal-checkout-open" target="_blank" rel="noopener noreferrer">Buka tautan pembayaran</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="renewal-ezypay-pay" disabled>Proses pembayaran</button>
            </div>
        </div>
    </div>
</div>
