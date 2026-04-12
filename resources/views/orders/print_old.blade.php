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

        .header-right > p {
            margin-bottom: 0px;
        }

        
        .table {
            width: 100%; /* Lebar tabel penuh */
            margin: 0; /* Hilangkan margin atas/bawah */
            border-collapse: collapse; /* Gabungkan border */
            font-size: 9px; /* Perkecil font */
            
        }
        
        .table th, .table td {
            padding: 4px!important; /* Kurangi padding atas dan bawah */
            line-height: 1; /* Kurangi tinggi baris */
            text-align: left;
        }
        
        .table-responsive {
            margin: 0; /* Hapus margin atas dan bawah */
        }
        

        hr {
            border: 0;
            border-top: 1px solid black;
            margin: 0px 0;
        }

        .content {
            padding: 5px; /* Kurangi padding */
        }
        
        .header-center {
            flex: 1;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .header-center b {
            font-size: 20px;
            font-weight: bold;
        }
        
        /* Print-specific styles */
        @media print {
            @page {
                size: A4 potrait;
                margin: 0mm; /* Kurangi margin */
            }

            body {
                width: 210mm; /* Width of A5 landscape */
                height: 148mm; /* Height of A5 landscape */
                overflow: hidden; /* Prevent scrolling */
            }

            .content {
                box-sizing: border-box;
                height: 100%;
                max-height: 100%; /* Prevent content overflow */
                padding: 2mm; /* Ensure padding is uniform */
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
                <img src="{{ asset($config->caminho_img_login) }}" alt="Logo">
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

        <div class="row invoice-info" style="font-size: 14px;">
            <div class="col-sm-4 invoice-col">
                <strong>Detail Pelanggan</strong>
                <address style="margin-bottom: 0px;">
                    Nama: <strong>{{ $order->customer->name }}</strong><br>
                    Telepon: {{ $order->customer->phone_number }}<br>
                    Alamat: {{ $order->customer->address }}<br>
                    Kota: {{ $order->customer->city->name ?? '-' }}<br>
                </address>
            </div>
            <!-- /.col -->
            <!--<div class="col-sm-4 invoice-col">-->
            <!--    <b>Status Reparasi</b><br>-->
            <!--    <address>-->
            <!--        {{-- Status: {{ $order->status }}<br> --}}-->
            <!--        Diambil Oleh: {{ $order->status == 'DIAMBIL' ? $order->picked_by : '-' }}<br>-->
            <!--        Estimasi: {{ \Carbon\Carbon::createFromFormat('Y-m-d', $order->estimate_take_item)->format('d-m-Y') }}<br><b>SORE</b>-->
            <!--        @if($order->picked_at)-->
            <!--            Tanggal Diambil: <strong>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->picked_at)->format('d-m-Y') }}</strong><br><b>SORE</b>-->
            <!--        @endif-->
            <!--    </address>-->
            <!--</div>-->

            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>Detail Order</b><br>
                Cabang: {{ $order->site->name }}<br>
                Tanggal Transaksi: {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$order->created_at)->format('d-m-Y H:i:s') }}<br>
                @if(!is_null($order->creator))
                Diterima Oleh: {{ $order->creator->name }}<br>
                @endif
                <div class="header-center" style="text-align: left;">
                <b style="font-size: 30px;">{{ $order->number_ticket }}</b>
            </div>
            </div>
            <div class="col-sm-4 invoice-col">
                <div class="header-right" style="text-align: center; font-size: 20px;">
                Estimasi: {{ \Carbon\Carbon::createFromFormat('Y-m-d', $order->estimate_take_item)->format('d-m-Y') }}
                <br><div style="text-align: center"><b>SELESAI SORE</b></div>
            </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        <div class="header-section">
            
        </div>
        <!-- Items Table -->
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered" cellpadding="0">
                <thead>
                <tr>
                    <th class="text-center" style="font-size: 14px;width: 5%;">No.</th>
                    <!--<th style="font-size: 11px;width: 25%;">Jenis Service</th>-->
                    <th style="font-size: 14px;width: 50%;">Keterangan</th>
                    <th class="text-right" style="font-size: 14px;width: 20%;">Subtotal</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->orderItems as $orderItem)
                    <tr>
                        <td class="text-center" style="font-size: 14px;"><strong>{{ $loop->iteration }}</strong></td>
                        <!--<td style="font-size: 11px;">{{ $products->find($orderItem->product_id)->name }}</td>-->
                        <td style="font-size: 14px;">{{ $orderItem->note }}</td>
                        <td class="text-right" style="font-size: 14px;">Rp{{ number_format($orderItem->bruto, null, ",", ".") }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="row">
            <div class="col-xs-6">
                <p class="lead" style="margin-bottom: 0px;">Metode Pembayaran:</p>
                <p style="font-size: 11px;">
                    <strong>
                        @if($order->status == 'DIAMBIL')
                            {{ $order->payment->paymentMethod->name }} - {{ $order->payment->paymentMerchant->name }}
                        @else
                            -
                        @endif
                    </strong>
                </p>
                <p style="font-size: 11px;"><b>Catatan:</b> {{ $order->note ?? '-' }}</p>
                <p style="font-size: 11px;"><b>Disclaimer:</b> {{ $config->disclaimer }}</p>
                <p style="margin-top: 10px;font-size: 12px;">
                    <b>For:</b> {{ ucwords(request()->get('type')) }}
                </p>
            </div>
            <div class="col-xs-6">
                <div class="table-responsive">
                    <table class="table table-total">
                        <tr>
                            <th style="font-size: 12px;">Total:</th>
                            <td class="text-right" style="font-size: 12px;" >Rp{{ number_format($order->orderItems->sum('bruto'), null, ",", ".") }}</td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px;">DP:</th>
                            <td class="text-right" style="font-size: 12px;" >Rp{{ number_format($order->uang_muka, null, ",", ".") }}</td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px;">Discount:</th>
                            <td class="text-right" style="font-size: 12px;" >Rp{{ number_format($order->discount, null, ",", ".") }}</td>
                        </tr>
                        <tr>
                            <th style="font-size: 12px;">Sisa Pembayaran:</th>
                            <td class="text-right" style="font-size: 12px;">Rp{{ number_format((($order->orderItems->sum('bruto') - $order->uang_muka) - $order->discount), null, ",", ".") }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
