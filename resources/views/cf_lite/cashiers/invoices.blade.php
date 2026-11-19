@extends('cf_lite.layouts.staff-template')

@section('other-style')
<style>
    .invoice-directory { padding: 20px 15px; }
    .invoice-directory h2 { margin-top: 0; }
    .invoice-directory .invoice-filters { background: #f7f5fa; border: 1px solid #e5dfeb; border-radius: 6px; padding: 18px; margin: 22px 0; }
    .invoice-directory .table td { vertical-align: middle; }
    .invoice-directory .empty-state { padding: 35px; text-align: center; color: #666; }
    #member-invoices-modal .modal-content { border-radius: 12px; }
    #member-invoices-modal .modal-header { padding: 20px 24px; border-bottom: 1px solid #e9e2ee; }
    #member-invoices-modal .modal-title { font-size: 23px; font-weight: 700; color: #352644; }
    #member-invoices-modal .modal-header p { margin: 4px 0 0; }
    #member-invoices-modal .modal-body { max-height: 65vh; overflow-y: auto; padding: 20px 24px; background: #f8f6fa; }
    #member-invoices-modal .invoice-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-bottom: 14px; }
    #member-invoices-modal .invoice-stat { background: #fff; border: 1px solid #e5ddec; border-radius: 8px; padding: 10px 14px; }
    #member-invoices-modal .invoice-stat strong { display: block; font-size: 22px; line-height: 1.3; color: #352644; }
    #member-invoices-modal .invoice-toolbar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 14px; }
    #member-invoices-modal .invoice-toolbar .form-control { width: auto; }
    #member-invoices-modal .invoice-card { background: #fff; border: 1px solid #e5ddec; border-radius: 8px; padding: 16px; margin-bottom: 12px; overflow-wrap: anywhere; }
    #member-invoices-modal .invoice-card-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    #member-invoices-modal .invoice-card-header h5 { margin: 0 0 6px; font-size: 16px; font-weight: 700; }
    #member-invoices-modal .invoice-amount { margin: 0; font-size: 21px; font-weight: 700; color: #352644; text-align: right; }
    #member-invoices-modal .invoice-amount small { display: block; font-size: 12px; font-weight: normal; color: #625b69; }
    #member-invoices-modal .invoice-fields { display: grid; grid-template-columns: repeat(auto-fit, minmax(135px, 1fr)); gap: 8px 16px; margin: 15px 0; }
    #member-invoices-modal dt { color: #625b69; font-size: 12px; font-weight: normal; }
    #member-invoices-modal dd { margin: 3px 0 0; font-weight: 600; }
    #member-invoices-modal details { border-top: 1px solid #eee8f1; padding-top: 10px; }
    #member-invoices-modal summary { cursor: pointer; color: #563b73; font-weight: 600; }
    #member-invoices-modal .invoice-items { margin-bottom: 10px; }
    #member-invoices-modal .invoice-pay-link { margin-top: 12px; }
    @media (max-width: 480px) {
        #member-invoices-modal .modal-body, #member-invoices-modal .modal-header { padding: 14px; }
        #member-invoices-modal .invoice-card-header { flex-direction: column; }
        #member-invoices-modal .invoice-amount { text-align: left; }
        #member-invoices-modal .invoice-stat { padding: 8px; }
    }
</style>
@endsection

@section('content')
<div class="invoice-directory">
    <h2><span class="fa fa-file-text-o" aria-hidden="true"></span> Invoices</h2>
    <p class="text-muted">Lihat invoice member dan tautan Pembayaran Mandiri. Cari berdasarkan nama atau pilih klub.</p>
    @include('cf_lite.includes.error-message')
    <form method="GET" action="{{ route('ezypay.invoices.index') }}" class="invoice-filters">
        <div class="row">
            <div class="col-sm-5 form-group"><label for="invoice-search">Nama member</label>
                <input id="invoice-search" type="search" name="q" class="form-control" value="{{ $search }}" maxlength="255" placeholder="Cari nama member…"></div>
            <div class="col-sm-4 form-group"><label for="invoice-club">Klub</label>
                <select id="invoice-club" name="club_id" class="form-control"><option value="">Semua klub</option>
                    @foreach($clubs as $club)<option value="{{ $club->id }}" {{ (string) $clubId === (string) $club->id ? 'selected' : '' }}>{{ $club->club_name }}</option>@endforeach
                </select></div>
            <div class="col-sm-3 form-group"><label class="hidden-xs" style="display:block">&nbsp;</label>
                <button type="submit" class="btn btn-primary">Cari</button>
                <a href="{{ route('ezypay.invoices.index') }}" class="btn btn-default">Reset</a></div>
        </div>
    </form>
    <section class="panel panel-default" aria-labelledby="pending-checkouts-title">
        <div class="panel-heading">
            <h3 class="panel-title" id="pending-checkouts-title">Pembayaran Mandiri tanpa ID invoice</h3>
        </div>
        <div class="panel-body">
            <p class="text-muted"><strong>{{ number_format($pendingCheckouts->total(), 0, ',', '.') }}</strong> checkout memiliki tautan dan belum memiliki ID invoice di CMS.</p>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead><tr><th>Nama member</th><th>Klub</th><th>Nomor pembayaran</th><th>Tanggal percobaan</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($pendingCheckouts as $checkout)
                            @php($validUrl = filter_var($checkout->checkout_url, FILTER_VALIDATE_URL) && parse_url($checkout->checkout_url, PHP_URL_SCHEME) === 'https')
                            <tr>
                                <td>{{ $checkout->member_name ?: '—' }}</td>
                                <td>{{ $checkout->club_name ?: '—' }}</td>
                                <td>{{ $checkout->payment_number ?: '—' }}</td>
                                <td>{{ $checkout->created_at ? \Carbon\Carbon::parse($checkout->created_at)->format('d/m/Y H:i') : '—' }}</td>
                                <td>
                                    @if($validUrl)
                                        <a href="{{ $checkout->checkout_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-default btn-sm">Buka Tautan Pembayaran</a>
                                    @else
                                        <span class="text-muted">Tautan tidak tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty-state">Tidak ada checkout tanpa ID invoice yang sesuai dengan pencarian dan filter klub.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $pendingCheckouts->links() }}
        </div>
    </section>
    <h3>Daftar Member EZYPAY</h3>
    <p><strong>{{ number_format($members->total(), 0, ',', '.') }}</strong> member ditemukan.</p>
    <div class="table-responsive"><table class="table table-striped table-bordered">
        <thead><tr><th>No.</th><th>Nama member</th><th>Email</th><th>Klub</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($members as $member)
                <tr><td>{{ $members->firstItem() + $loop->index }}</td><td>{{ $member->name }}</td><td>{{ $member->email ?: '—' }}</td>
                    <td>{{ optional($member->user_club)->club_name ?: '—' }}</td>
                    <td><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#member-invoices-modal"
                        data-invoices-url="{{ route('ezypay.invoices.show', ['member' => $member->id]) }}" data-member-name="{{ $member->name }}">Lihat Invoices</button></td></tr>
            @empty
                <tr><td colspan="5" class="empty-state">Tidak ada member EZYPAY yang sesuai dengan pencarian dan filter klub.</td></tr>
            @endforelse
        </tbody>
    </table></div>
    {{ $members->links() }}
</div>

<div class="modal fade" id="member-invoices-modal" tabindex="-1" role="dialog" aria-labelledby="member-invoices-title">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
                <h4 class="modal-title" id="member-invoices-title">Invoices EZYPAY</h4>
                <p class="text-muted">Member: <strong id="member-invoices-name"></strong></p>
            </div>
            <div class="modal-body">
                <div id="member-invoices-message" role="status" aria-live="polite"></div>
                <div id="member-invoices-summary" style="display:none">
                    <div class="invoice-stats" aria-label="Ringkasan invoice yang dimuat">
                        <div class="invoice-stat">Dimuat<strong id="invoice-count-loaded">0</strong></div>
                        <div class="invoice-stat">Lunas<strong id="invoice-count-paid">0</strong></div>
                        <div class="invoice-stat">Belum dibayar<strong id="invoice-count-unpaid">0</strong></div>
                    </div>
                    <div class="invoice-toolbar">
                        <label for="invoice-status-filter" class="sr-only">Filter status invoice</label>
                        <select id="invoice-status-filter" class="form-control input-sm">
                            <option value="">Semua status</option>
                            <option value="PAID">Lunas</option>
                            <option value="UNPAID">Belum dibayar</option>
                            <option value="other">Status lainnya</option>
                        </select>
                        <span id="invoice-list-count" class="text-muted" role="status" aria-live="polite"></span>
                    </div>
                </div>
                <div id="member-invoices-results"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="member-invoices-retry" class="btn btn-default" style="display:none">Coba Lagi</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('other-script')
<script>
    $(function () {
        var modal = $('#member-invoices-modal');
        var message = $('#member-invoices-message');
        var results = $('#member-invoices-results');
        var summary = $('#member-invoices-summary');
        var filter = $('#invoice-status-filter');
        var retry = $('#member-invoices-retry');
        var url = null;
        var pending = null;
        var invoices = [];
        var total = 0;
        var statuses = {
            PAID: ['Lunas', 'success'], UNPAID: ['Belum dibayar', 'warning'],
            PENDING: ['Menunggu pembayaran', 'info'], FAILED: ['Gagal', 'danger'],
            CANCELLED: ['Dibatalkan', 'default'], REFUNDED: ['Dikembalikan', 'default']
        };

        function money(amount) {
            if (!amount || amount.value == null || !Number.isFinite(Number(amount.value))) return '—';
            var currency = amount.currency === 'IDR' ? 'Rp' : (amount.currency || '');
            return (currency + ' ' + Number(amount.value).toLocaleString('id-ID')).trim();
        }

        function date(value) {
            var parts = typeof value === 'string' && /^(\d{4})-(\d{2})-(\d{2})/.exec(value);
            return parts ? new Date(Date.UTC(+parts[1], +parts[2] - 1, +parts[3]))
                .toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric', timeZone: 'UTC'}) : '—';
        }

        function renderFields(values) {
            var fields = $('<dl>').addClass('invoice-fields');
            values.forEach(function (field) {
                var column = $('<div>');
                $('<dt>').text(field[0]).appendTo(column);
                $('<dd>').text(field[1] || '—').appendTo(column);
                fields.append(column);
            });
            return fields;
        }

        function renderInvoice(invoice) {
            var card = $('<section>').addClass('invoice-card');
            var status = statuses[invoice.status] || [invoice.status || 'Tidak diketahui', 'default'];
            var heading = $('<div>').addClass('invoice-card-header').appendTo(card);
            var identity = $('<div>').appendTo(heading);
            $('<h5>').text(invoice.documentNumber || invoice.externalInvoiceId || 'Invoice EZYPAY').appendTo(identity);
            $('<span>').addClass('label label-' + status[1]).text(status[0]).appendTo(identity);
            $('<p>').addClass('invoice-amount').append($('<small>').text('Total invoice'), document.createTextNode(money(invoice.amount))).appendTo(heading);

            card.append(renderFields([
                ['Jatuh tempo', date(invoice.dueDate)], ['Jadwal pembayaran', date(invoice.scheduledPaymentDate)],
                ['Metode pembayaran', invoice.paymentMethod]
            ]));

            var details = $('<details>').append($('<summary>').text('Rincian invoice'));
            details.append(renderFields([
                ['Referensi', invoice.externalInvoiceId], ['Tanggal invoice', date(invoice.date)],
                ['Diskon', money(invoice.discount)], ['Pajak', money(invoice.tax)]
            ]));
            if (invoice.memo) {
                $('<p>').append($('<strong>').text('Memo: '), document.createTextNode(invoice.memo)).appendTo(details);
            }
            if (invoice.items.length) {
                var table = $('<table>').addClass('table table-condensed invoice-items');
                var headers = $('<tr>').append($('<th>').text('Item'), $('<th>').addClass('text-right').text('Nominal'));
                table.append($('<thead>').append(headers));
                var items = $('<tbody>');
                invoice.items.forEach(function (item) {
                    $('<tr>').append($('<td>').text(item.description || 'Item invoice'),
                        $('<td>').addClass('text-right').text(money(item.amount))).appendTo(items);
                });
                details.append(table.append(items));
            }
            card.append(details);
            if (invoice.payNowUrl && invoice.status === 'UNPAID') {
                $('<a>').addClass('btn btn-default btn-sm invoice-pay-link').attr({
                    href: invoice.payNowUrl, target: '_blank', rel: 'noopener noreferrer'
                }).text('Buka tautan pembayaran').appendTo(card);
            }
            results.append(card);
        }

        function renderResults() {
            var status = filter.val();
            var shown = invoices.filter(function (invoice) {
                if (!status) return true;
                if (status === 'other') return invoice.status !== 'PAID' && invoice.status !== 'UNPAID';
                return invoice.status === status;
            });
            results.empty();
            $('#invoice-list-count').text(shown.length + ' ditampilkan dari ' + invoices.length + ' yang dimuat'
                + (total > invoices.length ? ' (' + total + ' total invoice)' : ''));
            if (!shown.length) {
                $('<p>').addClass('alert alert-info').text('Tidak ada invoice dengan status ini.').appendTo(results);
                return;
            }
            shown.forEach(renderInvoice);
        }

        function loadInvoices() {
            if (!url) return;
            if (pending) pending.abort();
            results.empty();
            summary.hide();
            filter.val('');
            retry.hide();
            message.attr('class', 'alert alert-info').text('Memuat invoice EZYPAY…').show();
            pending = $.ajax({url: url, dataType: 'json'})
                .done(function (data) {
                    invoices = data.invoices;
                    total = data.total;
                    if (!invoices.length) {
                        message.text('Belum ada invoice EZYPAY untuk member ini.');
                        return;
                    }
                    $('#invoice-count-loaded').text(invoices.length);
                    $('#invoice-count-paid').text(invoices.filter(function (invoice) { return invoice.status === 'PAID'; }).length);
                    $('#invoice-count-unpaid').text(invoices.filter(function (invoice) { return invoice.status === 'UNPAID'; }).length);
                    message.hide();
                    summary.show();
                    renderResults();
                })
                .fail(function (xhr, status) {
                    if (status === 'abort') return;
                    message.attr('class', 'alert alert-danger').text((xhr.responseJSON || {}).message || 'Gagal memuat invoice EZYPAY.');
                    retry.show();
                })
                .always(function () { pending = null; });
        }

        modal.on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            url = button.attr('data-invoices-url');
            $('#member-invoices-name').text(button.attr('data-member-name') || '');
            loadInvoices();
        }).on('hide.bs.modal', function () {
            if (pending) pending.abort();
            url = null;
        });
        retry.on('click', loadInvoices);
        filter.on('change', renderResults);
    });
</script>
@endsection
