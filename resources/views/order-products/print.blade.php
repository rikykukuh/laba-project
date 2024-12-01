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
                size: A4 portrait;
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

     <!-- baris judul -->
     <div class="row">
         <div class="col-xs-12">
             {{-- <h2 class="page-header"> --}}
             {{--     <i class="fa fa-calendar-check-o"></i> Tanggal Dicetak: <strong>{{ date('d-m-Y') }}</strong> --}}
             {{-- </h2> --}}
         </div>
         <!-- /.col -->
     </div>
     <!-- baris informasi -->
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
                 <b>Status Pesanan</b><br>
                 <address>
                     Status: {{ $order->status }}<br>
                     @if($order->status == 'DIAMBIL')
                         Diambil Oleh: {{ $order->picked_by }}<br>
                         Waktu Pengambilan: {{ $order->picked_at }}<br>
                     @endif
                     Tanggal Dicetak: <strong>{{ date('d-m-Y') }}</strong>
                 </address>
             </div>

         <!-- /.col -->
         <div class="col-sm-4 invoice-col">
             <b>Detail Order</b><br>
             <br>
             <b>No:</b> {{ $order->number_ticket }}<br>
             <b>Cabang:</b> {{ $order->site->name }}<br>
             <b>Tanggal Transaksi:</b> {{ $order->created_at }}<br>
         </div>
         <!-- /.col -->
     </div>
     <!-- /.row -->

     <!-- Baris tabel -->
     <div class="row">
         <div class="col-xs-12 table-responsive">
             <table class="table table-sm table-striped table-bordered">
                 <thead>
                     <tr>
                         <th class="text-center" style="width: 5%;">No.</th>
                         <th>Jenis Produk</th>
                         <th>Keterangan</th>
                         <th>Qty</th>
                         <th class="text-right">Subtotal</th>
                     </tr>
                 </thead>
                 <tbody>
                 @foreach($order->orderItems as $orderItem)
                     <tr>
                         <td class="text-center" style="width: 5%;">
                             <strong>{{ $loop->iteration }}</strong>
                         </td>
                         <td>{{ $products->find($orderItem->product_id)->name }}</td>
                         <td>{{ $orderItem->note }}</td>
                         <td>{{ number_format($orderItem->quantity,null,",",".") }}</td>
                         <td class="text-right">Rp{{ number_format($orderItem->bruto,null,",",".") }}</td>
                     </tr>
                 @endforeach
                 </tbody>
             </table>
         </div>
         <!-- /.col -->
     </div>
     <!-- /.row -->

     <div class="row">
         <!-- kolom metode pembayaran -->
         <div class="col-xs-6">
             <p class="lead">Metode Pembayaran:</p>
             <h4>
                 <strong>
                     @if($order->status == 'DIAMBIL')
                         {{ $order->payment->paymentMethod->name }} - {{ $order->payment->paymentMerchant->name == '-' ? null : $order->payment->paymentMerchant->name }}
                     @else
                         -
                     @endif
                 </strong>
                 <p style="margin-top: 20px;font-size: 12px;">
                     <b>Catatan:</b> {{ $order->note ?? '-' }}
                 </p>
                 <p style="margin-top: 20px;font-size: 12px;">
                     <b>For:</b> {{ ucwords(request()->get('type')) }}
                 </p>
             </h4>
         </div>
         <!-- /.col -->
         <div class="col-xs-6">
{{--             <p class="lead">Waktu Jatuh Tempo: {{ $order->due_date ?? '-' }}</p>--}}

             <div class="table-responsive">
                 <table class="table table-total">
                   {{-- <tr> --}}
                   {{--     <th style="width:50%">Subtotal:</th> --}}
                   {{--     <td>Rp250.300</td> --}}
                   {{-- </tr> --}}
                   {{-- <tr> --}}
                   {{--     <th>Pajak (9,3%)</th> --}}
                   {{--     <td>Rp10.340</td> --}}
                   {{-- </tr> --}}
                   {{-- <tr> --}}
                   {{--     <th>Pengiriman:</th> --}}
                   {{--     <td>Rp5.800</td> --}}
                   {{-- </tr> --}}
                     <tr>
                         <th>Total:</th>
                         <th class="text-right">Rp{{ number_format($order->orderItems->sum('netto'),null,",",".") }}</th>
                     </tr>
                 </table>
             </div>
         </div>
         <!-- /.col -->
     </div>
     <!-- /.row -->
@endsection
