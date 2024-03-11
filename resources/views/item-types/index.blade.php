@extends('layouts.AdminLTE.index')

@section('icon_page', 'list-alt')

@section('title', 'Item Types')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('item-types.create') }}" class="link_menu_page">
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
								@foreach($item_types as $item_type)
									@if($item_type->id)
										<tr>
                                            <td>{{$item_type->name}}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($item_type->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-default  btn-xs" href="{{ route('item-types.show', $item_type->id) }}" title="See {{ $item_type->name }}"><i class="fa fa-eye">   </i></a>
                                                <a class="btn btn-warning  btn-xs" href="{{ route('item-types.edit', $item_type->id) }}" title="Edit {{ $item_type->name }}"><i class="fa fa-pencil"></i></a>
                                                <form onsubmit="return confirm('Do you really want to submit the form DELETE?');" action="{{ route('item-types.destroy', $item_type->id) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $item_type->name}}" data-toggle="modal" data-target="#modal-delete-{{ $item_type->id }}"><i class="fa fa-trash"></i></button>
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
        @if ($item_types->hasPages())
        <div class="box-footer with-border">
            {{ $item_types->links() }}
        </div>
        @endif
	</div>

@endsection