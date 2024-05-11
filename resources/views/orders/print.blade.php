@extends('layouts.print')

@section('title', 'Laba | Order Detail')

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
                size: landscape;
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
                 Nama: <strong>{{ $order->client->name }}</strong><br>
                 Telepon: {{ $order->client->phone_number }}<br>
                 Alamat: {{ $order->client->address }}<br>
                 Kota: {{ $order->client->city->name }}<br>
             </address>
         </div>
         <!-- /.col -->
         @if($order->status == 'DIPROSES')
              <div class="col-sm-4 invoice-col">&nbsp;</div>
         @else
             <div class="col-sm-4 invoice-col">
                 <b>Order Status</b><br>
                 <address>
                     Status: {{ $order->status }}<br>
                     Diambil Oleh: {{ $order->picked_by }}<br>
                     Waktu Pengambilan: {{ $order->picked_at }}<br>
                 </address>
             </div>
         @endif

         <!-- /.col -->
         <div class="col-sm-4 invoice-col">
             <b>Detail Order</b><br>
             <br>
             <b>ID Pesanan:</b> {{ Str::padLeft($order->id, 4, '0') }}<br>
             <b>Nomor Tiket:</b> {{ $order->number_ticket }}<br>
             <b>Cabang:</b> {{ $order->site->name }}<br>
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
                         <th>Jenis Barang</th>
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
                         <td>{{ $item_types->find($orderItem->item_type_id)->name }}</td>
                         <td>{{ $orderItem->note }}</td>
                         <td class="text-right">Rp{{ number_format($orderItem->total,null,",",".") }}</td>
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
                         <th class="text-right">Rp{{ number_format($order->orderItems->sum('total'),null,",",".") }}</th>
                     </tr>
                 </table>
             </div>
         </div>
         <!-- /.col -->
     </div>
     <!-- /.row -->
@endsection
