<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan Cicilan EZYPAY Wajib</title>
    <link href="{{ asset('bootstrap-3_3_7/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body { background: #f7f5fa; color: #333; padding: 30px 16px; }
        main { max-width: 940px; margin: auto; background: white; border: 1px solid #e3ddeb; border-radius: 8px; padding: 28px; }
        h1 { font-size: 25px; margin-top: 0; } .summary { background: #f7f5fa; border-radius: 6px; padding: 18px; margin: 20px 0; }
        dt { color: #666; font-weight: normal; margin-top: 10px; } dd { margin-top: 3px; font-weight: 600; }
        .balance { font-size: 24px; } td { min-width: 130px; } td:first-child { min-width: 50px; } input { min-width: 140px; }
        .actions { margin-top: 20px; } #allocation { margin: 18px 0; } .payment-link { overflow-wrap: anywhere; }
    </style>
</head>
<body>
<main>
    <h1>Pengaturan Cicilan EZYPAY</h1>
    <div class="alert alert-warning" role="alert">Proses pembayaran pertama sudah selesai. Jadwal cicilan wajib disimpan sebelum Anda dapat membuka menu lain. Jangan ulangi pembayaran pertama.</div>
    <div class="summary row">
        <div class="col-sm-7"><dl><dt>Anggota</dt><dd>{{ $member->name }}</dd><dt>Nomor pembayaran pertama</dt><dd>{{ $payment->payment_number }}</dd></dl></div>
        <div class="col-sm-5"><dl><dt>Sisa Tagihan yang harus dijadwalkan</dt><dd class="balance">Rp {{ number_format($purchase->installment_remaining, 0, ',', '.') }}</dd></dl></div>
    </div>
    @if($purchase->checkout_url)
        <div class="alert alert-info payment-link">Pembayaran Mandiri: berikan <a href="{{ $purchase->checkout_url }}" target="_blank" rel="noopener noreferrer">tautan checkout ini</a> kepada customer. Pembuatan tautan belum berarti pembayaran lunas.</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <section class="summary">
        <h2 style="font-size:18px">Metode Pembayaran EZYPAY</h2>
        <p id="methods-message" role="status" aria-live="polite">Memuat metode pembayaran customer...</p>
        <div id="installment-method-choice">
            <label for="installment-method">Metode pembayaran untuk semua cicilan</label>
            <select id="installment-method" class="form-control" name="paymentMethodToken" form="installment-form" required disabled><option value="">Pilih metode pembayaran</option></select>
        </div>
        <button type="button" id="add-customer-card" class="btn btn-primary" style="margin-top:10px" aria-controls="installment-card-form" aria-expanded="false">Tambah Kartu Customer</button>
        <button type="button" id="reload-methods" class="btn btn-default" style="margin-top:10px">Muat Ulang</button>
        <form id="installment-card-form" style="display:none;margin-top:20px" autocomplete="off">
            <h3 style="font-size:17px">Input Nomor Kartu Customer</h3>
            <p>Masukkan nomor kartu beserta data pemilik dan masa berlaku langsung di halaman ini, lalu klik Simpan Kartu.</p>
            <div id="card-message" role="alert"></div>
            <div class="form-group"><label for="card-number">Nomor kartu</label><input id="card-number" name="accountNumber" type="text" class="form-control" inputmode="numeric" pattern="[0-9]{12,19}" minlength="12" maxlength="19" placeholder="Masukkan nomor kartu tanpa spasi" aria-describedby="card-number-help" required><p id="card-number-help" class="help-block">Nomor kartu dikirim ke EZYPAY untuk didaftarkan sebagai metode pembayaran.</p></div>
            <div class="form-group"><label for="card-holder">Nama pemilik kartu</label><input id="card-holder" name="accountHolderName" class="form-control" maxlength="255" placeholder="Sesuai nama pada kartu" required></div>
            <div class="form-group"><label for="card-month">Bulan kedaluwarsa (MM)</label><input id="card-month" class="form-control" inputmode="numeric" pattern="0[1-9]|1[0-2]" maxlength="2" required></div>
            <div class="form-group"><label for="card-year">Tahun kedaluwarsa (YY)</label><input id="card-year" class="form-control" inputmode="numeric" pattern="[0-9]{2}" maxlength="2" required></div>
            <div class="checkbox"><label><input id="card-consent" type="checkbox" required> Pemilik kartu menyetujui kartu ini dimasukkan ke sistem.</label></div>
            <button id="save-card" type="submit" class="btn btn-primary">Simpan Kartu</button>
        </form>
    </section>
    @if(in_array($purchase->installment_sync_status, ['processing', 'needs_review']))
        <div class="alert alert-warning">Batch sedang diproses atau perlu diperiksa di EZYPAY. Pengiriman ulang diblokir untuk mencegah invoice ganda.</div>
    @endif
    <p>Atur nominal dan tanggal setiap cicilan. Anda dapat membuat lebih dari satu jadwal; totalnya harus sama dengan Sisa Tagihan.</p>
    <form method="POST" action="{{ route('ezypay.installments.store', ['purchase' => $purchase->id]) }}" id="installment-form">
        @csrf
        @php($rows = old('installments', [['amount' => $purchase->installment_remaining, 'date' => '']]))
        @php($rows = is_array($rows) ? $rows : [])
        <div class="table-responsive"><table class="table table-bordered">
            <thead><tr><th>Cicilan</th><th>Nominal (Rp)</th><th>Tanggal pembayaran</th><th>Aksi</th></tr></thead>
            <tbody id="installment-rows">
            @foreach($rows as $index => $row)
                <tr><td class="row-number"></td><td><input class="form-control amount" type="number" min="1" step="1" name="installments[{{ $index }}][amount]" value="{{ is_array($row) ? ($row['amount'] ?? '') : '' }}" aria-label="Nominal cicilan" required></td>
                    <td><input class="form-control date" type="date" name="installments[{{ $index }}][date]" min="{{ \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d') }}" value="{{ is_array($row) ? ($row['date'] ?? '') : '' }}" aria-label="Tanggal pembayaran cicilan" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove">Hapus</button></td></tr>
            @endforeach
            </tbody>
        </table></div>
        <button type="button" id="add-installment" class="btn btn-default">Tambah Cicilan</button>
        <div id="allocation" role="status" aria-live="polite"></div>
        <div class="form-group"><label for="installment-memo">Catatan cicilan (memo)</label><textarea id="installment-memo" name="memo" class="form-control" maxlength="2000" rows="3">{{ old('memo') }}</textarea></div>
        <div id="saving-indicator" class="alert alert-info" role="status" aria-live="polite" style="display:none">Menyimpan jadwal dan menyinkronkan invoice ke EZYPAY. Mohon tunggu dan jangan mengirim ulang.</div>
        <div class="actions"><button type="submit" class="btn btn-primary" id="save-installments" disabled>Simpan Jadwal dan Sync ke EZYPAY</button></div>
    </form>
</main>
<script>
(function() {
    var remaining = Number(@json($purchase->installment_remaining));
    var today = @json(\Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d'));
    var body = document.getElementById('installment-rows');
    var form = document.getElementById('installment-form');
    var locked = @json(in_array($purchase->installment_sync_status, ['processing', 'needs_review']));
    var methodSelect = document.getElementById('installment-method');
    var cardForm = document.getElementById('installment-card-form');
    var methodsMessage = document.getElementById('methods-message');
    var save = document.getElementById('save-installments');
    var methodsUrl = @json(route('ezypay.installments.methods', ['purchase' => $purchase->id]));
    var cardUrl = @json(route('ezypay.installments.card', ['purchase' => $purchase->id]));
    var previousToken = @json(old('paymentMethodToken', ''));
    function syncSaveButton() { save.disabled = locked || !methodSelect.value; }
    function loadMethods() {
        methodSelect.disabled = true; save.disabled = true;
        methodSelect.innerHTML = '<option value="">Pilih metode pembayaran</option>';
        cardForm.style.display = 'none';
        methodsMessage.textContent = 'Memuat metode pembayaran...';
        document.getElementById('reload-methods').disabled = true;
        fetch(methodsUrl, {headers:{Accept:'application/json'}, credentials:'same-origin'})
            .then(function(response) { return response.json().then(function(data) { if(!response.ok) throw new Error(data.message || 'Gagal memuat metode pembayaran.'); return data; }); })
            .then(function(data) {
                if(!Array.isArray(data.data)) throw new Error('Respons metode pembayaran tidak valid.');
                data.data.forEach(function(method) { var option = document.createElement('option'); option.value = method.token; option.textContent = method.label + (method.primary ? ' (Utama)' : ''); methodSelect.appendChild(option); });
                if(data.data.length) {
                    document.getElementById('installment-method-choice').hidden = false;
                    methodSelect.disabled = false;
                    methodSelect.value = previousToken;
                    methodsMessage.textContent = 'Pilih metode pembayaran yang akan digunakan untuk seluruh jadwal cicilan.';
                } else {
                    document.getElementById('installment-method-choice').hidden = true;
                    cardForm.style.display = 'block';
                    methodsMessage.textContent = 'Belum ada metode pembayaran. Silakan input nomor kartu pada form di bawah.';
                }
                syncSaveButton();
            }).catch(function(error) { methodsMessage.textContent = error.message; })
            .finally(function() { document.getElementById('reload-methods').disabled = false; });
    }
    methodSelect.addEventListener('change', syncSaveButton);
    document.getElementById('reload-methods').addEventListener('click', loadMethods);
    document.getElementById('add-customer-card').addEventListener('click', function() {
        cardForm.style.display = 'block';
        this.setAttribute('aria-expanded', 'true');
        document.getElementById('card-number').focus();
    });
    cardForm.addEventListener('submit', function(event) {
        event.preventDefault();
        var button = document.getElementById('save-card'); if(button.disabled) return;
        button.disabled = true; button.textContent = 'Menyimpan kartu...';
        document.getElementById('add-customer-card').disabled = true;
        var message = document.getElementById('card-message'); message.textContent = '';
        fetch(cardUrl, {method:'POST', credentials:'same-origin', headers:{Accept:'application/json', 'Content-Type':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
            body:JSON.stringify({payment_method_type:'card', accountHolderName:document.getElementById('card-holder').value,
                accountNumber:document.getElementById('card-number').value, expiryMonth:document.getElementById('card-month').value,
                expiryYear:document.getElementById('card-year').value, termAndConditionAgreed:document.getElementById('card-consent').checked})})
            .then(function(response) { return response.json().then(function(data) { if(!response.ok) throw new Error(data.message || 'Gagal menyimpan kartu.'); return data; }); })
            .then(function() { cardForm.reset(); loadMethods(); })
            .catch(function(error) { message.textContent = error.message; message.className = 'alert alert-danger'; })
            .finally(function() { document.getElementById('card-number').value = ''; document.getElementById('card-consent').checked = false; button.disabled = false; button.textContent = 'Simpan Kartu'; document.getElementById('add-customer-card').disabled = false; });
    });
    loadMethods();

    function total() { return Array.from(body.querySelectorAll('.amount')).reduce(function(sum, input) { return sum + Math.round((Number(input.value) || 0) * 100); }, 0); }
    function update() {
        Array.from(body.rows).forEach(function(row, index) {
            row.querySelector('.row-number').textContent = index + 1;
            row.querySelector('.amount').name = 'installments[' + index + '][amount]';
            row.querySelector('.date').name = 'installments[' + index + '][date]';
        });
        var difference = Math.round(remaining * 100) - total();
        var status = document.getElementById('allocation');
        status.className = difference === 0 ? 'text-success' : 'text-danger';
        status.textContent = 'Total cicilan: Rp ' + (total() / 100).toLocaleString('id-ID') + ' | Belum dialokasikan: Rp ' + (difference / 100).toLocaleString('id-ID');
        document.getElementById('add-installment').disabled = body.rows.length >= 120;
    }
    body.addEventListener('input', update);
    body.addEventListener('click', function(event) { if(event.target.classList.contains('remove')) { event.target.closest('tr').remove(); update(); } });
    document.getElementById('add-installment').addEventListener('click', function() {
        if(body.rows.length >= 120) return;
        var row = document.createElement('tr');
        row.innerHTML = '<td class="row-number"></td><td><input class="form-control amount" type="number" min="1" step="1" aria-label="Nominal cicilan" required></td><td><input class="form-control date" type="date" aria-label="Tanggal pembayaran cicilan" required></td><td><button type="button" class="btn btn-danger btn-sm remove">Hapus</button></td>';
        row.querySelector('.amount').value = Math.max(0, remaining - total() / 100);
        row.querySelector('.date').min = today;
        body.appendChild(row); update();
    });
    form.addEventListener('submit', function(event) {
        if (locked || !methodSelect.value) { event.preventDefault(); alert('Pilih metode pembayaran yang valid.'); return; }
        if (!body.rows.length || total() !== Math.round(remaining * 100)) {
            event.preventDefault(); alert('Total cicilan harus sama dengan Sisa Tagihan.'); return;
        }
        document.getElementById('save-installments').disabled = true;
        document.getElementById('save-installments').textContent = 'Menyimpan dan sync...';
        document.getElementById('saving-indicator').style.display = 'block';
        form.style.pointerEvents = 'none';
    });
    update();
})();
</script>
</body>
</html>
