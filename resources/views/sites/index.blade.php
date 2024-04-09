@extends('layouts.AdminLTE.index')

@section('icon_page', 'list-ol')

@section('title', 'Sites')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('sites.create') }}" class="link_menu_page">
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
								@foreach($sites as $site)
									@if($site->id)
										<tr>
                                            <td>{{$site->name}}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($site->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-default  btn-xs" href="{{ route('sites.show', $site->id) }}" title="See {{ $site->name }}"><i class="fa fa-eye">   </i></a>
                                                <a class="btn btn-warning  btn-xs" href="{{ route('sites.edit', $site->id) }}" title="Edit {{ $site->name }}"><i class="fa fa-pencil"></i></a>
                                                <form onsubmit="return confirm('Do you really want to submit the form DELETE?');" action="{{ route('sites.destroy', $site->id) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $site->name}}" data-toggle="modal" data-target="#modal-delete-{{ $site->id }}"><i class="fa fa-trash"></i></button>
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
        @if ($sites->hasPages())
        <div class="box-footer with-border">
            {{ $sites->links() }}
        </div>
        @endif
	</div>

@endsection
