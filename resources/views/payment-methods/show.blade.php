@extends('layouts.AdminLTE.index')

@section('icon_page', 'exchange')

@section('title', 'Payment Method')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('payment-methods.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Add
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-6 col-md-offset-3">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Payment Method</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Name</strong>
                <p>{{ $payment_method->name }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Created At</strong>
                <p>{{ $payment_method->created_at }}</p>
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('payment-methods.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Back to Payment Method</a>
                <a href="{{ route('payment-methods.edit', $payment_method->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Payment Method</a>
                <form onsubmit="return confirm('Do you really want to submit the form DELETE?');" action="{{ route('payment-methods.destroy', $payment_method->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Delete Payment Method</button>
                </form>
            </div>

        </div>

    </div>

@endsection
