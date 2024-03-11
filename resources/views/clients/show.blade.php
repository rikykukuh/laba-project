@extends('layouts.AdminLTE.index')

@section('icon_page', 'users')

@section('title', 'Clients')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('clients.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Add
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-6 col-md-offset-3">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Client</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-book margin-r-5"></i> Name</strong>
                <p>{{ $client->name }}</p>
                <hr>
                <strong><i class="fa fa-home margin-r-5"></i> Address</strong>
                <p>{{ $client->address }}</p>
                <hr>
                <strong><i class="fa fa-phone margin-r-5"></i> Phone Number</strong>
                <p>{{ $client->phone_number }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Created At</strong>
                <p>{{ $client->created_at }}</p>
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('clients.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Back to Clients</a>
                <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Client</a>
                <form onsubmit="return confirm('Do you really want to submit the form DELETE?');" action="{{ route('clients.destroy', $client->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Delete Client</button>
                </form>
            </div>

        </div>

    </div>

@endsection
