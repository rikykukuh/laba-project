@extends('layouts.print')

@section('title', 'ORDER-' . Str::padLeft($order->id, 4, '0'))

@section('style')
    <style>
        .table-total tr:first-child, .table-total tr:last-child {
            border-top: 2px solid black;
        }
        .table-total tr:last-child {
            border-bottom: 2px solid black;
        }

        @media print {
            @page {
                size: A5 landscape;
            }
        }
    </style>
@endsection

@section('content')
     <!-- baris judul -->
     <div class="row">
         <div class="col-xs-12">
             <h2 class="page-header">
                 <i class="fa fa-calendar-check-o"></i> Tanggal Dicetak: <strong>{{ date('d-m-Y') }}</strong>
             </h2>
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
                 <b>Status Reparasi</b><br>
                 <address>
                     Status: {{ $order->status }}<br>
                     Diambil Oleh: {{ $order->status == 'DIAMBIL' ? $order->picked_by : '-' }}<br>
                     Estimasi Pengambilan: {{ $order->estimate_take_item }}<br>
                 </address>
             </div>

         <!-- /.col -->
         <div class="col-sm-4 invoice-col">
             <b>Detail Order</b><br>
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
             <table class="table table-striped">
                 <thead>
                     <tr>
                         <th class="text-center" style="width: 5%;">No.</th>
                         <th>Jenis Produk</th>
                         <th>Keterangan</th>
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
                         {{ $order->payment->paymentMethod->name }} - {{ $order->payment->paymentMerchant->name }}
                     @else
                         -
                     @endif
                 </strong>
                 <p style="margin-top: 20px;font-size: 12px;">
                    <b>Disclaimer:</b> {{ $config->disclaimer }}
                 </p>
             </h4>
         </div>
         <!-- /.col -->
         <div class="col-xs-6">
{{--             <p class="lead">Waktu Jatuh Tempo: {{ $order->due_date ?? '-' }}</p>--}}

             <div class="table-responsive">
                 <table class="table table-total">
                     <tr>
                         <th>Total:</th>
                         <td class="text-right">Rp{{ number_format($order->orderItems->sum('bruto'),null,",",".") }}</td>
                     </tr>
                    <tr>
                        <th>DP:</th>
                        <td class="text-right">Rp{{ number_format($order->uang_muka,null,",",".") }}</td>
                    </tr>
                     <tr>
                         <th>Discount:</th>
                         <td class="text-right">Rp{{ number_format($order->discount,null,",",".") }}</td>
                     </tr>
                    <tr>
                        <th>Sisa Pembayaran:</th>
                        <td class="text-right">Rp{{ number_format((($order->orderItems->sum('bruto') - $order->uang_muka) - $order->discount),null,",",".") }}</td>
                    </tr>
                     <tr></tr>
                 </table>
             </div>
         </div>
         <!-- /.col -->
     </div>
     <!-- /.row -->
@endsection
