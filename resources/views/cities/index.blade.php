@extends('layouts.AdminLTE.index')

@section('icon_page', 'map-marker')

@section('title', 'Cities')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('cities.create') }}" class="link_menu_page">
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
								@foreach($cities as $city)
									@if($city->id)
										<tr>
                                            <td>{{$city->name}}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($city->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-default  btn-xs" href="{{ route('cities.show', $city->id) }}" title="See {{ $city->name }}"><i class="fa fa-eye">   </i></a>
                                                <a class="btn btn-warning  btn-xs" href="{{ route('cities.edit', $city->id) }}" title="Edit {{ $city->name }}"><i class="fa fa-pencil"></i></a>
                                                <form onsubmit="return confirm('Do you really want to submit the form DELETE?');" action="{{ route('cities.destroy', $city->id) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $city->name}}" data-toggle="modal" data-target="#modal-delete-{{ $city->id }}"><i class="fa fa-trash"></i></button>
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
        @if ($cities->hasPages())
        <div class="box-footer with-border">
            {{ $cities->links() }}
        </div>
        @endif
	</div>

@endsection
