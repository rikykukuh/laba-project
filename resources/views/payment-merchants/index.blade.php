@extends('layouts.AdminLTE.index')

@section('icon_page', 'building')

@section('title', 'Payment Merchants')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('payment-merchants.create') }}" class="link_menu_page">
			<i class="fa fa-plus"></i> Add
		</a>
	</li>

@endsection


@section('content')

    <div class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table id="tabelapadrao" class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th>Name</th>
									<th>Payment Method Name</th>
									<th class="text-center">Created At</th>
									<th class="text-center">Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach($payment_merchants as $payment_merchant)
									@if($payment_merchant->id)
										<tr>
                                            <td>{{$payment_merchant->name}}</td>
                                            <td>{{$payment_merchant->paymentMethod->name}}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($payment_merchant->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-default  btn-xs" href="{{ route('payment-merchants.show', $payment_merchant->id) }}" title="See {{ $payment_merchant->name }}"><i class="fa fa-eye">   </i></a>
                                                <a class="btn btn-warning  btn-xs" href="{{ route('payment-merchants.edit', $payment_merchant->id) }}" title="Edit {{ $payment_merchant->name }}"><i class="fa fa-pencil"></i></a>
                                                <form onsubmit="return confirm('Do you really want to submit the form DELETE?');" action="{{ route('payment-merchants.destroy', $payment_merchant->id) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $payment_merchant->name}}" data-toggle="modal" data-target="#modal-delete-{{ $payment_merchant->id }}"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
									@endif
								@endforeach

							</tbody>
							<tfoot>
								<tr>
									<th>Name</th>
									<th>Payment Method Name</th>
									<th class="text-center">Created At</th>
									<th class="text-center">Actions</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
        @if ($payment_merchants->hasPages())
        <div class="box-footer with-border">
            {{ $payment_merchants->links() }}
        </div>
        @endif
	</div>

@endsection
