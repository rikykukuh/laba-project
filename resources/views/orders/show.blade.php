@extends('layouts.AdminLTE.index')

@section('icon_page', 'shopping-basket')

@section('title', 'Orders')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('orders.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Add
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-6 col-md-offset-3">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Order</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-book margin-r-5"></i> Name</strong>
                <p>{{ $order->name }}</p>
                <hr>
                <strong><i class="fa fa-user-circle-o margin-r-5"></i> Client Name</strong>
                <p>{{ $order->client->name }}</p>
                <hr>
                <strong><i class="fa fa-file-text-o margin-r-5"></i> Status</strong>
                <p>{{ $statuses[$order->status] }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Status</strong>
                <p>{{ $order->due_date }}</p>
                <hr>
                <img src="{{ $order }}" alt="">
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('orders.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Back to Orders</a>
                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Order</a>
                <form onsubmit="return confirm('Do you really want to submit the form DELETE?');" action="{{ route('orders.destroy', $order->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Delete Order</button>
                </form>
            </div>

        </div>

    </div>

@endsection
