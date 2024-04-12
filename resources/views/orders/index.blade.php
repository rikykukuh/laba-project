@extends('layouts.AdminLTE.index')

@section('icon_page', 'shopping-basket')

@section('title', 'Pesanan')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('orders.create') }}" class="link_menu_page">
			<i class="fa fa-plus"></i> Tambar Pesanan
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
									<th>Nama Pelanggan</th>
									{{-- <th>Note</th> --}}
									{{-- <th class="text-center">Total</th> --}}
									<th class="text-center">Status</th>
									<th class="text-center">Tanggal Dibuat</th>
									<th class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody>
								@foreach($orders as $order)
									@if($order->id)
										<tr>
                                            <td>{{$order->client->name}}</td>
                                            {{-- <td>{{$order->orderItems->first()->note}}</td> --}}
                                            {{-- <td class="text-center">{{$order->orderItems->first()->total}}</td> --}}
                                            <td class="text-center">
{{--                                                @if($order->status == 0)--}}
{{--                                                    @php--}}
{{--                                                        echo '<b>New</b>';--}}
{{--                                                    @endphp--}}
{{--                                                @endif--}}
{{--                                                @if($order->status == 1)--}}
{{--                                                    @php--}}
{{--                                                        echo '<b>Ready</b>';--}}
{{--                                                    @endphp--}}
{{--                                                @endif--}}
{{--                                                @if($order->status == 2)--}}
{{--                                                    @php--}}
{{--                                                        echo '<b>Paid</b>';--}}
{{--                                                    @endphp--}}
{{--                                                @endif--}}
{{--                                                @if($order->status == 3)--}}
{{--                                                    @php--}}
{{--                                                        echo '<b>Picked Up</b>';--}}
{{--                                                    @endphp--}}
{{--                                                @endif--}}
                                                {{ $order->status }}
                                            </td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($order->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-default  btn-xs" href="{{ route('orders.show', $order->id) }}" title="Detail {{ $order->name }}"><i class="fa fa-eye">   </i></a>
                                                {{-- <a class="btn btn-warning  btn-xs" href="{{ route('orders.edit', $order->id) }}" title="Edit {{ $order->name }}"><i class="fa fa-pencil"></i></a> --}}
                                                <form onsubmit="return confirm(''Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('orders.destroy', $order->id) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $order->name}}" data-toggle="modal" data-target="#modal-delete-{{ $order->id }}"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
									@endif
								@endforeach

							</tbody>
							<tfoot>
								<tr>
                                    <th>Nama Pelanggan</th>
                                    {{-- <th>Note</th> --}}
									{{-- <th class="text-center">Total</th> --}}
									<th class="text-center">Status</th>
									<th class="text-center">Tanggal Dibuat</th>
									<th class="text-center">Aksi</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
        @if ($orders->hasPages())
            <div class="box-footer with-border">
                {{ $orders->links() }}
            </div>
        @endif
	</div>

@endsection
