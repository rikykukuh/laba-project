@extends('layouts.AdminLTE.index')

@section('icon_page', 'exchange')

@section('title', 'Payments')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('payments.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Add
        </a>
    </li>

@endsection


@section('content')

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Payment</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Edit Payment">
                    <i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form action="{{ route('payments.update', $payment->id) }}" method="post">
                {{ csrf_field() }}
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name', $payment->name) }}" autofocus>
                            @if($errors->has('name'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-default pull-left" style="margin-right: 15px;">Back to Detail Payment</a>
                        <button type="submit" class="btn btn-warning pull-right"><i class="fa fa-fw fa-save"></i> Update</button>
                        <a href="{{ route('payments.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Back to Payments</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection