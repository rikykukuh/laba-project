@extends('layouts.AdminLTE.index')

@section('icon_page', 'plus')

@section('title', 'Add Payment Merchants')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('payment-merchants.index') }}" class="link_menu_page">
			<i class="fa fa-building"></i> Payment Merchants
		</a>
	</li>

@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Add Payment Merchant</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Form Payment">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
					 <form action="{{ route('payment-merchants.store') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="active" value="1">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <label for="name">Payment Merchant Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Payment Merchant Name" required value="{{ old('name') }}" autofocus>
                                    @if($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group {{ $errors->has('payment_method_id') ? 'has-error' : '' }}">
                                    <label for="payment_method_id">Client</label>
                                    <select name="payment_method_id" id="payment_method_id" class="form-control" data-placeholder="Choose Payment Method" required>
                                        <option disabled selected> -- Choose Payment Method -- </option>
                                        @foreach($payment_methods as $payment_method)
                                            <option value="{{ $payment_method->id }}"> {{ $payment_method->name }} </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('payment_method_id'))
                                        <span class="help-block">
                                             <strong>{{ $errors->first('payment_method_id') }}</strong>
                                         </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                               <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-fw fa-plus"></i> Add</button>
                                <a href="{{ route('payment-merchants.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Cancel</a>
                            </div>
                        </div>
                    </form>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('layout_js')

    <script>
        $(function(){
            $('.select2').select2({
                "language": {
                    "noResults": function(){
                        return "Nenhum registro encontrado.";
                    }
                }
            });
        });

    </script>

@endsection
