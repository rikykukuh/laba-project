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

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Order</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Edit Order">
                    <i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form action="{{ route('orders.update', $order->id) }}" method="post">
                {{ csrf_field() }}
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name', $order->name) }}" autofocus>
                            @if($errors->has('name'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('client_id') ? 'has-error' : '' }}">
                            <label for="client_id">Client</label>
                            <select name="client_id" id="client_id" class="form-control" data-placeholder="Choose Client" required>
                                <option disabled> -- Choose Client -- </option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $client->id === $order->client_id ? 'selected' : '' }}> {{ $client->name }} </option>
                                @endforeach
                            </select>
                            @if($errors->has('client_id'))
                                <span class="help-block">
                                             <strong>{{ $errors->first('client_id') }}</strong>
                                         </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('total') ? 'has-error' : '' }}">
                            <label for="total">Total</label>
                            <input type="number" name="total" id="total" class="form-control" placeholder="Total" required value="{{ old('total', $order->total) }}" >
                            @if($errors->has('total'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('total') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('uang_muka') ? 'has-error' : '' }}">
                            <label for="uang_muka">Uang Muka</label>
                            <input type="number" name="uang_muka" id="uang_muka" class="form-control" placeholder="Uang Muka" required value="{{ old('uang_muka', $order->uang_muka) }}">
                            @if($errors->has('uang_muka'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('uang_muka') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" data-placeholder="Choose Status" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $loop->index }}" {{ $loop->index === (int)$order->status ? 'selected' : ''  }}> {{ $status }} </option>
                                @endforeach
                            </select>
                            @if($errors->has('status'))
                                <span class="help-block">
                                             <strong>{{ $errors->first('status') }}</strong>
                                         </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('payment') ? 'has-error' : '' }}">
                            <label for="payment">Payment</label>
                            <input type="number" name="payment" id="payment" class="form-control" placeholder="Payment" required value="{{ old('payment', $order->payment) }}" >
                            @if($errors->has('payment'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('payment') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('number_ticket') ? 'has-error' : '' }}">
                            <label for="number_ticket">No Tiket</label>
                            <input type="text" name="number_ticket" id="number_ticket" class="form-control" placeholder="No Tiket" required value="{{ old('number_ticket', $order->number_ticket) }}" >
                            @if($errors->has('number_ticket'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('number_ticket') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('due_date') ? 'has-error' : '' }}">
                            <label for="due_date">Jatuh Tempo</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" placeholder="Jatuh Tempo" required value="{{ old('due_date', $order->due_date) }}" >
                            @if($errors->has('due_date'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('due_date') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('sisa_pembayaran') ? 'has-error' : '' }}">
                            <label for="sisa_pembayaran">Sisa Pembayaran</label>
                            <input type="number" name="sisa_pembayaran" id="sisa_pembayaran" class="form-control" placeholder="Sisa Pembayaran" required value="{{ old('sisa_pembayaran', $order->sisa_pembayaran) }}" >
                            @if($errors->has('sisa_pembayaran'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('sisa_pembayaran') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-default pull-left" style="margin-right: 15px;">Back to Detail Order</a>
                        <button type="submit" class="btn btn-warning pull-right"><i class="fa fa-fw fa-save"></i> Update</button>
                        <a href="{{ route('orders.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Back to Orders</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
