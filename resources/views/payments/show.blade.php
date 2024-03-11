@extends('layouts.AdminLTE.index')

@section('icon_page', 'exchange')

@section('title', 'Cities')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('cities.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Add
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-6 col-md-offset-3">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Payment</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Name</strong>
                <p>{{ $payment->name }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Created At</strong>
                <p>{{ $payment->created_at }}</p>
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('cities.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Back to Cities</a>
                <a href="{{ route('cities.edit', $payment->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Payment</a>
                <form onsubmit="return confirm('Do you really want to submit the form DELETE?');" action="{{ route('cities.destroy', $payment->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Delete Payment</button>
                </form>
            </div>

        </div>

    </div>

@endsection
