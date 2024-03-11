@extends('layouts.AdminLTE.index')

@section('icon_page', 'shopping-cart')

@section('title', 'Orders')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('orders.create') }}" class="link_menu_page">
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
									<th>E-mail</th>
									<th class="text-center">Status</th>
									<th class="text-center">Created</th>
									<th class="text-center">Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach($orders as $order)
									@if($order->id)
										<td>{{$order->name}}</td>
										<td>{{$order->user_id()->name}}</td>
										<td>{{$order->status}}</td>
										<td>{{$order->created}}</td>
										<td class="text-center">
											 <a class="btn btn-default  btn-xs" href="{{ route('orders.show', $order->id) }}" title="See {{ $order->name }}"><i class="fa fa-eye">   </i></a>
											 <a class="btn btn-primary  btn-xs" href="{{ route('orders.show', $order->id) }}" title="Change Password {{ $order->name }}"><i class="fa fa-key"></i></a>
											 <a class="btn btn-warning  btn-xs" href="{{ route('orders.edit', $order->id) }}" title="Edit {{ $order->name }}"><i class="fa fa-pencil"></i></a>
											 <a class="btn btn-danger  btn-xs" href="#" title="Delete {{ $order->name}}" data-toggle="modal" data-target="#modal-delete-{{ $order->id }}"><i class="fa fa-trash"></i></a>
										</td>
									@endif
								@endforeach

							</tbody>
							<tfoot>
								<tr>
									<th>Name</th>
									<th>E-mail</th>
									<th class="text-center">Status</th>
									<th class="text-center">Created</th>
									<th class="text-center">Actions</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection
