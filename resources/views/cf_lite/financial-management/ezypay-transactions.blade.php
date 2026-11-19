@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <style>
        .transaction-summary {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            padding: 16px;
            margin-bottom: 16px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        .transaction-amount { font-size: 24px; font-weight: 700; }
        .transaction-section { margin-top: 16px; }
        .transaction-section dl {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px 20px;
            margin-bottom: 0;
        }
        .transaction-section dd { overflow-wrap: anywhere; }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <p>
            <a href="{{ url('cf-lite/financial-management') }}" class="btn btn-default">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                Pengelolaan Keuangan
            </a>
        </p>
        <h2 class="page-title">Laporan Transaksi Ezypay</h2>

        <form method="GET" action="{{ route('ezypay.transactions.index') }}" class="form-inline">
            <div class="form-group">
                <label for="limit">Jumlah per halaman API</label>
                <input id="limit" name="limit" type="number" min="1" value="{{ old('limit', $limit) }}" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-default">Terapkan</button>
            @error('limit')
                <span class="text-danger" role="alert">{{ $message }}</span>
            @enderror
        </form>
        <br>

        @if($loadFailed)
            <div class="alert alert-warning" role="alert">
                Gagal memuat transaksi Ezypay.
                <a href="{{ route('ezypay.transactions.index', ['limit' => $limit, 'cursor' => $cursor]) }}">Coba lagi</a>
            </div>
        @elseif($transactions->isEmpty())
            <div class="alert alert-info" role="status">Tidak ada transaksi anggota klub ini pada halaman ini.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Waktu transaksi</th>
                            <th scope="col">Nomor transaksi</th>
                            <th scope="col">Anggota</th>
                            <th scope="col">Nomor dokumen</th>
                            <th scope="col">Jenis</th>
                            <th scope="col">Metode</th>
                            <th scope="col" class="text-right">Jumlah</th>
                            <th scope="col">Status</th>
                            <th scope="col">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            @php($amount = data_get($transaction, 'amount.value'))
                            <tr>
                                <td>{{ str_replace('T', ' ', substr($transaction['createdOn'] ?? '—', 0, 19)) }}</td>
                                <td>{{ $transaction['number'] ?? '—' }}</td>
                                <td>{{ $transaction['member_name'] ?? data_get($transaction, 'sender.name', '—') }}</td>
                                <td>{{ data_get($transaction, 'document.number') ?? '—' }}</td>
                                <td>{{ $transaction['type'] ?? '—' }}</td>
                                <td>{{ $transaction['paymentMethodType'] ?? '—' }}</td>
                                <td class="text-right">
                                    @if(is_numeric($amount))
                                        {{ data_get($transaction, 'amount.currency') }}
                                        {{ number_format((float) $amount, (float) $amount == (int) $amount ? 0 : 2, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $transaction['status'] ?? '—' }}</td>
                                <td>
                                    @if(!empty($transaction['id']))
                                        <button type="button" class="btn btn-default btn-sm"
                                            data-toggle="modal" data-target="#ezypay-transaction-modal"
                                            data-url="{{ route('ezypay.transactions.show', ['transactionId' => $transaction['id']]) }}">
                                            Lihat Detail
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($cursor > 0)
            <a class="btn btn-default" href="{{ route('ezypay.transactions.index', ['limit' => $limit]) }}">Halaman pertama</a>
        @endif
        @if($nextCursor !== null)
            <a class="btn btn-primary" href="{{ route('ezypay.transactions.index', ['limit' => $limit, 'cursor' => $nextCursor]) }}">Halaman berikutnya</a>
        @endif
    </div>

    <div class="modal fade" id="ezypay-transaction-modal" tabindex="-1" role="dialog" aria-labelledby="ezypay-transaction-title">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
                    <h4 class="modal-title" id="ezypay-transaction-title">Detail Transaksi Ezypay</h4>
                </div>
                <div class="modal-body">
                    <p data-detail-loading role="status">Memuat detail transaksi…</p>
                    <p data-detail-error class="alert alert-warning" role="alert" hidden></p>
                    <div data-detail-content hidden>
                        <div class="transaction-summary">
                            <div>
                                <span class="text-muted">Jumlah transaksi</span>
                                <div class="transaction-amount" data-detail-amount></div>
                            </div>
                            <div>
                                <div class="label" data-detail-status></div>
                                <div data-detail-time></div>
                            </div>
                        </div>
                        <div data-detail-sections></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script>
        $(function () {
            const modal = $('#ezypay-transaction-modal');
            const loading = modal.find('[data-detail-loading]');
            const error = modal.find('[data-detail-error]');
            const content = modal.find('[data-detail-content]');
            const sections = modal.find('[data-detail-sections]');
            const statusLabel = modal.find('[data-detail-status]');
            const statuses = new Map([
                ['SUCCESS', ['Berhasil', 'label-success']],
                ['FAILED', ['Gagal', 'label-danger']],
                ['PENDING', ['Menunggu', 'label-warning']]
            ]);
            const groups = [
                ['Pembayaran', ['Nomor transaksi', 'Anggota', 'Penerima', 'Jenis transaksi', 'Metode pembayaran', 'Kartu']],
                ['Dokumen dan referensi', ['Nomor dokumen', 'Jenis dokumen', 'ID transaksi', 'Sumber transaksi', 'Kanal']],
                ['Informasi kegagalan', ['Waktu gagal', 'Alasan gagal', 'Kode respons penyedia', 'Pesan penyedia']]
            ];
            let request;

            function formatTime(value) {
                return typeof value === 'string' ? value.replace('T', ' ').replace(/\.\d+(?=Z|[+-]\d{2}:?\d{2}$|$)/, '') : '—';
            }

            modal.on('show.bs.modal', function (event) {
                loading.prop('hidden', false);
                error.prop('hidden', true);
                content.prop('hidden', true);
                sections.empty();

                request = $.getJSON($(event.relatedTarget).data('url'))
                    .done(function (detail) {
                        const [label, style] = statuses.get(detail.Status) || [detail.Status || 'Status belum tersedia', 'label-default'];
                        statusLabel.attr('class', 'label ' + style).text(label);
                        modal.find('[data-detail-amount]').text(detail.Jumlah || '—');
                        modal.find('[data-detail-time]').text(formatTime(detail['Waktu transaksi']));

                        groups.forEach(function ([title, labels]) {
                            const fields = $('<dl>');
                            labels.forEach(function (field) {
                                const value = detail[field];
                                if (value == null || value === '') return;
                                fields.append($('<div>').append(
                                    $('<dt>').text(field),
                                    $('<dd>').text(field === 'Waktu gagal' ? formatTime(value) : value)
                                ));
                            });
                            if (!fields.children().length) return;
                            sections.append($('<section>').addClass('transaction-section').append($('<h5>').text(title), fields));
                        });
                        content.prop('hidden', false);
                    })
                    .fail(function (response, status) {
                        if (status === 'abort') return;
                        error.text(response.responseJSON?.message || 'Gagal memuat detail transaksi Ezypay.')
                            .prop('hidden', false);
                    })
                    .always(function () {
                        loading.prop('hidden', true);
                        request = null;
                    });
            });

            modal.on('hide.bs.modal', function () {
                if (request) request.abort();
            });
        });
    </script>
@endsection
