<script>
$(function () {
    const modal = $('#renewal-ezypay-modal');
    const message = $('#renewal-ezypay-message');
    const pay = $('#renewal-ezypay-pay');
    const token = $('#renewal-token');
    let busy = false;
    let ready = false;
    let completed = false;
    let renewalKey;
    let memberId;
    let membershipId;
    $('#membership_type').closest('tr').insertAfter($('#payment_method').closest('tr'));

    function selectedEzypay() {
        return $('#payment_method option:selected').text().trim().toUpperCase() === 'EZYPAY';
    }
    function amount(name) {
        return Number(rupiah_format_destroyer($('input[name="' + name + '"]').val() || '0'));
    }
    function paymentMode() {
        const self = $('#renewal-self').prop('checked') || !token.val();
        $('#renewal-subscription-row').prop('hidden', self);
        pay.text(self ? 'Buat tautan pembayaran' : 'Proses pembayaran');
    }
    function setBusy(value) {
        busy = value;
        modal.find('button, input, select').prop('disabled', value);
        pay.prop('disabled', value || !ready || completed);
    }
    function errorText(xhr) {
        const result = xhr.responseJSON || {};
        return result.message || 'Gagal menghubungi server. Muat ulang data dan periksa status pembayaran.';
    }
    function summary() {
        const fields = {
            'Anggota': $('#selected-member option:selected').text(),
            'Tipe keanggotaan': $('#membership_type option:selected').text(),
            'Total': amount('total').toLocaleString('id-ID'),
            'Diskon': amount('discount').toLocaleString('id-ID'),
            'Bayar': amount('pay').toLocaleString('id-ID'),
            'Sisa tagihan untuk cicilan': (amount('total') - amount('pay')).toLocaleString('id-ID')
        };
        const list = $('#renewal-summary').empty();
        Object.entries(fields).forEach(function ([label, value]) {
            list.append($('<dt>').text(label), $('<dd>').text(value));
        });
    }
    function prepare() {
        if (busy || completed) return;
        ready = false;
        setBusy(true);
        message.text('Menyiapkan customer, plan, dan akun EZYPAY…');
        $.ajax({
            url: @json(route('ezypay.renewal.options')), method: 'POST', dataType: 'json',
            data: {
                _token: @json(csrf_token()), member_id: memberId, membership_type_id: membershipId,
                sf_amount: $('#membership_type option:selected').attr('sf'),
                mf_amount: $('#membership_type option:selected').attr('mf'),
                name: $('#renewal-name').val(), email: $('#renewal-email').val(),
                phone: $('#renewal-phone').val(), cellphone: $('#renewal-cellphone').val()
            }
        }).done(function (result) {
            renewalKey = result.renewal_key;
            $('#renewal-plan').text('Plan ' + result.plan.name + ' · MEMBERSHIP-RENEWAL');
            const previous = token.val();
            token.empty().append($('<option>').val('').text('Pembayaran Mandiri'));
            result.methods.forEach(function (option) {
                token.append($('<option>').val(option.token).text(option.label + (option.primary ? ' (Utama)' : '')));
            });
            if (previous && token.find('option').toArray().some(option => option.value === previous)) token.val(previous);
            ready = true;
            message.text(result.warning || (result.methods.length ? '' : 'Belum ada akun pembayaran. Tambahkan kartu atau gunakan Pembayaran Mandiri.'));
        }).fail(function (xhr) {
            message.text(errorText(xhr));
        }).always(function () {
            setBusy(false);
            $('#renewal-add-card').prop('disabled', !ready);
            paymentMode();
        });
    }
    window.openRenewalEzypay = function () {
        if (completed || busy) return;
        if (!$('#selected-member').val() || !$('#membership_type').val()
            || !Number.isFinite(amount('pay')) || amount('pay') <= 0 || amount('pay') > amount('total')
            || !$('input[name="fa_date"]').val() || !$('#fa_coach').val()) {
            alert('Lengkapi anggota, tanggal, coach, tipe keanggotaan, dan nominal Bayar.');
            return;
        }
        memberId = $('#selected-member').val();
        $('#renewal-card-form').attr('action', @json(url('cf-lite/payment/bill-list')) + '/' + encodeURIComponent(memberId) + '/ezypay-card');
        membershipId = $('#membership_type').val();
        ready = false;
        $('#renewal-name, #renewal-email, #renewal-phone, #renewal-cellphone').val('');
        summary();
        modal.modal('show');
        setBusy(true);
        message.text('Memuat kontak anggota…');
        $.getJSON(@json(url('cf-lite/cashier/renewal/ezypay-contact')) + '/' + encodeURIComponent(memberId))
            .done(function (contact) {
                $('#renewal-name').val(contact.name);
                $('#renewal-email').val(contact.email);
                $('#renewal-phone').val(contact.phone || contact.cellphone);
                $('#renewal-cellphone').val(contact.cellphone || contact.phone);
                setBusy(false);
                prepare();
            }).fail(function (xhr) {
                setBusy(false);
                message.text(errorText(xhr));
            });
    };
    function bankVisibility() {
        $('#membership_type').closest('tr').toggle(Boolean($('#payment_method').val()));
        $('#bank').closest('tr').toggle(!selectedEzypay());
        if (selectedEzypay()) $('#bank').val('').trigger('change');
    }
    $('#payment_method').on('change', bankVisibility);
    bankVisibility();
    $('#renewal-prepare').on('click', prepare);
    $('#renewal-self, #renewal-token').on('change', paymentMode);
    $('#renewal-add-card').on('click', function () {
        $('#renewal-card-form').prop('hidden', !$('#renewal-card-form').prop('hidden'));
    });
    $('#renewal-card-form').on('submit', function (event) {
        event.preventDefault();
        if (busy || completed) return;
        const form = this;
        const data = $(form).serializeArray();
        data.push({name: '_token', value: @json(csrf_token())});
        setBusy(true);
        message.text('Menyimpan kartu…');
        $.ajax({
            url: $(form).attr('action'),
            method: 'POST', dataType: 'json', data: data
        }).done(function () {
            form.reset();
            $(form).prop('hidden', true);
            setBusy(false);
            prepare();
        }).fail(function (xhr) {
            setBusy(false);
            message.text(errorText(xhr));
        }).always(function () {
            $(form).find('[name="accountNumber"]').val('');
        });
    });
    modal.on('hide.bs.modal', function (event) {
        if (busy) event.preventDefault();
    }).on('hidden.bs.modal', function () {
        $('#renewal-card-form')[0].reset();
        $('#renewal-card-form').prop('hidden', true);
    });
    pay.on('click', function () {
        if (busy || !ready || completed) return;
        setBusy(true);
        message.text('Memproses renewal EZYPAY…');
        const data = {
            _token: @json(csrf_token()), clientPrint: '', id: 1, club_id: $('input[name="club_id"]').val(),
            login_user: $('input[name="login_user"]').val(), fa_coach: $('#fa_coach').val(),
            fa_date: $('input[name="fa_date"]').val(), selectedmember: memberId,
            membership_type: membershipId, payment_method: $('#payment_method').val(),
            sf_amount: amount('sf_amount'), mf_amount: amount('mf_amount'), discount: amount('discount'),
            total: amount('total'), pay: amount('pay'), bank: '', renewal_key: renewalKey,
            self_payment: $('#renewal-self').prop('checked') || !token.val() ? 1 : 0,
            subscription: $('#renewal-subscription').prop('checked') ? 1 : 0,
            paymentMethodToken: token.val(), printerName: 'XP-80', useDefaultPrinter: 'undefined'
        };
        function resultLinks(result) {
            if (result.checkoutUrl) {
                $('#renewal-checkout-url').val(result.checkoutUrl);
                $('#renewal-checkout-open').attr('href', result.checkoutUrl);
                $('#renewal-checkout').prop('hidden', false);
            }
            if (result.installment_url) window.location.assign(result.installment_url);
        }
        $.ajax({url: @json(route('ezypay.renewal.pay')), method: 'POST', dataType: 'json', data: data})
            .done(function (result) {
                completed = true;
                message.text(result.checkoutUrl ? 'Tautan pembayaran siap diberikan kepada anggota.' : 'Pengajuan pembayaran berhasil disimpan.');
                if (result.pdf) {
                    $('#receipt').val(result.pdf);
                    $('#receiptID').val(result.receiptID);
                    $('#print-btn, #send_receipt').show();
                }
                $('#pay-btn').prop('disabled', true).text('Pengajuan tersimpan');
                resultLinks(result);
            }).fail(function (xhr) {
                const result = xhr.responseJSON || {};
                completed = xhr.status !== 422 && result.retry_allowed !== true;
                message.text(errorText(xhr));
                resultLinks(result);
            }).always(function () {
                setBusy(false);
            });
    });
});
</script>
