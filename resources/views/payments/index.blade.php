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
								@foreach($payments as $payment)
									@if($payment->id)
										<tr>
                                            <td>{{$payment->name}}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($payment->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-default  btn-xs" href="{{ route('payments.show', $payment->id) }}" title="See {{ $payment->name }}"><i class="fa fa-eye">   </i></a>
                                                <a class="btn btn-warning  btn-xs" href="{{ route('payments.edit', $payment->id) }}" title="Edit {{ $payment->name }}"><i class="fa fa-pencil"></i></a>
                                                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('ayments.destroy', $payment->id) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $payment->name}}" data-toggle="modal" data-target="#modal-delete-{{ $payment->id }}"><i class="fa fa-trash"></i></button>
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
        @if ($payments->hasPages())
        <div class="box-footer with-border">
            {{ $payments->links() }}
        </div>
        @endif
	</div>

@endsection
