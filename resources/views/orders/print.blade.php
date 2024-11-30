@extends('layouts.print')

@section('title', 'ORDER-' . Str::padLeft($order->id, 4, '0'))

@section('style')
    <style>
        /* General styles */
        body {
            font-size: 10px; /* Perkecil ukuran font */
            line-height: 1.2; /* Kurangi jarak antar baris */
            margin: 0;
            padding: 0;
        }

        .header-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px; /* Kurangi jarak */
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .header-left img {
            width: 50px; /* Perkecil ukuran logo */
            height: auto;
            margin-right: 5px; /* Kurangi margin logo */
        }

        .header-left .header-details {
            line-height: 1.2;
            font-size: 10px; /* Sesuaikan ukuran font */
        }

        .header-right {
            text-align: right;
            font-size: 10px; /* Sesuaikan ukuran font */
        }

        .table {
            width: 100%;
            margin: 0 auto 5px;
            border-collapse: collapse;
            font-size: 9px; /* Perkecil ukuran font tabel */
        }

        .table th,
        .table td {
            padding: 3px; /* Kurangi padding */
            text-align: left;
        }

        .table-total th,
        .table-total td {
            border: none;
            padding: 3px; /* Kurangi padding */
            text-align: left;
        }

        hr {
            border: 0;
            border-top: 1px solid black;
            margin: 5px 0;
        }

        .content {
            padding: 5px; /* Kurangi padding */
        }

        /* Print-specific styles */
        @media print {
            @page {
                size: A5 landscape;
                margin: 5mm; /* Kurangi margin */
            }

            body {
                width: 210mm; /* Width of A5 landscape */
                height: 148.5mm; /* Height of A5 landscape */
                overflow: hidden; /* Prevent scrolling */
            }

            .content {
                box-sizing: border-box;
                height: 100%;
                max-height: 100%; /* Prevent content overflow */
                padding: 5mm; /* Ensure padding is uniform */
            }

            .table th,
            .table td {
                font-size: 8px; /* Sesuaikan font tabel untuk print */
            }

            .header-section {
                font-size: 8px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-left">
                <img src="https://pancalaba.rikykukuhsetiawan.com/img/config/luggage.png" alt="Logo">
                <div class="header-details">
                    <strong>PANCALABA</strong><br>
                    Jl. Boulevard TB 2/11 Kelapa Gading<br>
                    Jakarta Utara, 14240<br>
                    Telp: 0812-1904-4164
                </div>
            </div>
            <div class="header-right">
                <p><strong>Buka:</strong> Senin - Jumat 09:00 - 17:00</p>
                <p><strong>Sabtu:</strong> 09:00 - 16:00</p>
                <p><strong>Tutup:</strong> Minggu / Hari Raya</p>
            </div>
        </div>
        <hr>

        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                <strong>Detail Pelanggan</strong>
                <address>
                    Nama: <strong>{{ $order->customer->name }}</strong><br>
                    Telepon: {{ $order->customer->phone_number }}<br>
                    Alamat: {{ $order->customer->address }}<br>
                    Kota: {{ $order->customer->city->name ?? '-' }}<br>
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>Status Reparasi</b><br>
                <address>
                    {{-- Status: {{ $order->status }}<br> --}}
                    Diambil Oleh: {{ $order->status == 'DIAMBIL' ? $order->picked_by : '-' }}<br>
                    Estimasi: {{ $order->estimate_take_item }}<br>
                    @if($order->picked_at)
                        Tanggal Diambil: <strong>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->picked_at)->format('Y-m-d') }}</strong><br>
                    @endif
                </address>
            </div>

            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>Detail Order</b><br>
                <b>No:</b> {{ $order->number_ticket }}<br>
                <b>Cabang:</b> {{ $order->site->name }}<br>
                <b>Tanggal Transaksi:</b> {{ $order->created_at }}<br>
                @if(!is_null($order->creator)))
                <b>Diterima Oleh:</b> {{ $order->creator->name }}<br>
                @endif
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Items Table -->
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">No.</th>
                    <th style="width: 25%;">Jenis Service</th>
                    <th style="width: 50%;">Keterangan</th>
                    <th class="text-right" style="width: 20%;">Subtotal</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->orderItems as $orderItem)
                    <tr>
                        <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                        <td>{{ $products->find($orderItem->product_id)->name }}</td>
                        <td>{{ $orderItem->note }}</td>
                        <td class="text-right">Rp{{ number_format($orderItem->bruto, null, ",", ".") }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="row">
            <div class="col-xs-6">
                <p class="lead">Metode Pembayaran:</p>
                <p>
                    <strong>
                        @if($order->status == 'DIAMBIL')
                            {{ $order->payment->paymentMethod->name }} - {{ $order->payment->paymentMerchant->name }}
                        @else
                            -
                        @endif
                    </strong>
                </p>
                <p><b>Catatan:</b> {{ $order->note ?? '-' }}</p>
                <p><b>Disclaimer:</b> {{ $config->disclaimer }}</p>
            </div>
            <div class="col-xs-6">
                <div class="table-responsive">
                    <table class="table table-total">
                        <tr>
                            <th>Total:</th>
                            <td class="text-right">Rp{{ number_format($order->orderItems->sum('bruto'), null, ",", ".") }}</td>
                        </tr>
                        <tr>
                            <th>DP:</th>
                            <td class="text-right">Rp{{ number_format($order->uang_muka, null, ",", ".") }}</td>
                        </tr>
                        <tr>
                            <th>Discount:</th>
                            <td class="text-right">Rp{{ number_format($order->discount, null, ",", ".") }}</td>
                        </tr>
                        <tr>
                            <th>Sisa Pembayaran:</th>
                            <td class="text-right">Rp{{ number_format((($order->orderItems->sum('bruto') - $order->uang_muka) - $order->discount), null, ",", ".") }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
