@extends('layouts.AdminLTE.index')

@section('icon_page', 'building')

@section('title', 'Cities')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('payment-merchants.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Add
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-8 col-md-offset-2">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Payment Merchant</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Name</strong>
                <p>{{ $payment_merchant->name }}</p>
                <hr>
                <strong><i class="fa fa-user-circle-o margin-r-5"></i> Payment Method Name</strong>
                <p>{{ $payment_merchant->paymentMethod->name }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Created At</strong>
                <p>{{ $payment_merchant->created_at }}</p>
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('payment-merchants.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Back to Payment Merchant</a>
                <a href="{{ route('payment-merchants.edit', $payment_merchant->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Payment Merchant</a>
                <form onsubmit="return confirm('Do you really want to submit the form DELETE?');" action="{{ route('payment-merchants.destroy', $payment_merchant->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Delete Payment Merchant</button>
                </form>
            </div>

        </div>

    </div>

@endsection
