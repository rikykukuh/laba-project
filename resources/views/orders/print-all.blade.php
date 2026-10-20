@extends('layouts.print')

@section('title', 'ORDER-' . Str::padLeft($order->id, 4, '0') . ' - Customer, Cashier, Reparation')

@section('style')
    <style>
            @page {
                size: A4 potrait;
                margin: 0mm;
            }
        /* General styles */
        body {
            font-size: 10px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .header-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .header-left img {
            width: 50px;
            height: auto;
            margin-right: 5px;
        }

        .header-left .header-details {
            line-height: 1.2;
            font-size: 10px;
        }

        .header-right {
            text-align: right;
            font-size: 10px;
        }

        .header-right > p {
            margin-bottom: 0px;
        }

        
        .table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
            font-size: 9px;
            
        }
        
        .table th, .table td {
            padding: 4px!important;
            line-height: 1;
            text-align: left;
        }
        
        .table-responsive {
            margin: 0;
        }
        

        hr {
            border: 0;
            border-top: 1px solid black;
            margin: 0px 0;
        }

        .content {
            padding: 5px;
            page-break-after: always;
            page-break-inside: avoid;
        }

        .content:last-of-type {
            page-break-after: auto;
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
                size: A4 portrait;
                margin: 0mm;
            }

            body {
                width: 210mm;
                height: auto;
                overflow: visible;
            }

            .content {
                box-sizing: border-box;
                width: 210mm;
                height: 297mm;
                padding: 5mm;
                page-break-after: always;
                page-break-inside: avoid;
            }

            .content:last-of-type {
                page-break-after: auto;
            }

            .table th,
            .table td {
                font-size: 8px;
            }

            .header-section {
                font-size: 8px;
            }
            
        }
    </style>
@endsection

@section('content')
    @include('orders._print_invoice', ['type' => 'customer'])
    @include('orders._print_invoice', ['type' => 'cashier'])
    @include('orders._print_invoice', ['type' => 'reparation'])
@endsection
