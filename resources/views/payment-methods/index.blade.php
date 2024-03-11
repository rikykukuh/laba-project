@extends('layouts.AdminLTE.index')

@section('icon_page', 'exchange')

@section('title', 'Payment Methods')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('payment-methods.create') }}" class="link_menu_page">
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
									<th class="text-center">Created At</th>
									<th class="text-center">Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach($payment_methods as $payment_method)
									@if($payment_method->id)
										<tr>
                                            <td>{{$payment_method->name}}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($payment_method->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-default  btn-xs" href="{{ route('payment-methods.show', $payment_method->id) }}" title="See {{ $payment_method->name }}"><i class="fa fa-eye">   </i></a>
                                                <a class="btn btn-warning  btn-xs" href="{{ route('payment-methods.edit', $payment_method->id) }}" title="Edit {{ $payment_method->name }}"><i class="fa fa-pencil"></i></a>
                                                <form onsubmit="return confirm('Do you really want to submit the form DELETE?');" action="{{ route('payment-methods.destroy', $payment_method->id) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $payment_method->name}}" data-toggle="modal" data-target="#modal-delete-{{ $payment_method->id }}"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
									@endif
								@endforeach

							</tbody>
							<tfoot>
								<tr>
									<th>Name</th>
									<th class="text-center">Created At</th>
									<th class="text-center">Actions</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
        @if ($payment_methods->hasPages())
        <div class="box-footer with-border">
            {{ $payment_methods->links() }}
        </div>
        @endif
	</div>

@endsection
