<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Print Posisi Barang</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            font-size: 11px;
        }

        .print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 9999;
        }

        .print-controls button {
            padding: 8px 16px;
            background: #3c8dbc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-controls button:hover {
            background: #367fa9;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .filter-info {
            margin-bottom: 10px;
            font-size: 10px;
        }

        .filter-info table {
            width: 100%;
        }

        .filter-info td {
            padding: 2px 5px;
        }

        .filter-info .label {
            font-weight: bold;
            width: 120px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: left;
            vertical-align: middle;
        }

        table.data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        table.data-table td.text-center {
            text-align: center;
        }

        .item-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .no-photo {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            border: 1px solid #ddd;
            color: #999;
            font-size: 9px;
            text-align: center;
        }

        .label-state {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
        }

        .state-masuk { background-color: #00c0ef; }
        .state-proses { background-color: #f39c12; }
        .state-selesai { background-color: #00a65a; }
        .state-gudang { background-color: #3c8dbc; }
        .state-cancel { background-color: #dd4b39; }
        .state-default { background-color: #999; }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        @media print {
            .print-controls {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button onclick="window.print()">
            <i class="fa fa-print"></i> Print / Save as PDF
        </button>
    </div>

    <div class="header">
        <h2>DAFTAR POSISI BARANG</h2>
        <p>Dicetak pada: {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s') }}</p>
    </div>

    <div class="filter-info">
        <table>
            <tr>
                <td class="label">Cabang:</td>
                <td>{{ $siteName }}</td>
                <td class="label">Status:</td>
                <td>{{ $statusLabel }}</td>
            </tr>
            <tr>
                <td class="label">State Barang:</td>
                <td>{{ $stateLabel }}</td>
                <td class="label">Tanggal Estimasi:</td>
                <td>
                    @if ($date_start && $date_end)
                        {{ \Carbon\Carbon::parse($date_start)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($date_end)->format('d-m-Y') }}
                    @else
                        Semua Tanggal
                    @endif
                </td>
            </tr>
            @if ($search)
            <tr>
                <td class="label">Pencarian:</td>
                <td colspan="3">{{ $search }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Total Barang:</td>
                <td colspan="3"><strong>{{ $items->count() }} barang</strong></td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Tanggal Transaksi</th>
                <th>Tanggal Estimasi</th>
                <th>No Bon</th>
                <th>ID Barang</th>
                <th>Keterangan</th>
                <th>State Barang</th>
                <th style="width: 80px;">Foto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                @php
                    $photo = $item->orderItemPhotos->first();
                    $stateClass = 'state-default';
                    $stateLower = strtolower($item->state ?? '');
                    if (strpos($stateLower, 'masuk') !== false) $stateClass = 'state-masuk';
                    elseif (strpos($stateLower, 'proses') !== false) $stateClass = 'state-proses';
                    elseif (strpos($stateLower, 'selesai') !== false) $stateClass = 'state-selesai';
                    elseif (strpos($stateLower, 'gudang') !== false) $stateClass = 'state-gudang';
                    elseif (strpos($stateLower, 'cancel') !== false) $stateClass = 'state-cancel';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ optional($item->order)->created_at ? \Carbon\Carbon::parse($item->order->created_at)->timezone('Asia/Jakarta')->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-center">
                        {{ optional($item->order)->estimate_take_item ? \Carbon\Carbon::parse($item->order->estimate_take_item)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-center">{{ optional($item->order)->number_ticket ?? '-' }}</td>
                    <td class="text-center"><strong>{{ $item->id }}</strong></td>
                    <td>{{ $item->note ?: '-' }}</td>
                    <td class="text-center">
                        <span class="label-state {{ $stateClass }}">
                            {{ $item->state ? ucwords($item->state) : 'Belum Ada State' }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if ($photo)
                            @php
                                $photoUrl = asset('storage/' . ltrim($photo->thumbnail_url ?: $photo->preview_url, '/'));
                            @endphp
                            <img src="{{ $photoUrl }}" alt="Foto {{ $item->id }}" class="item-photo">
                        @else
                            <div class="no-photo">Tidak ada foto</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 30px;">
                        Data barang tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total: {{ $items->count() }} barang</p>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
