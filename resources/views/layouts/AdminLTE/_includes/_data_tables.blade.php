@section('layout_css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables.net/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables.net-bs/css/buttons.dataTables.min.css') }}">
@endsection

@section('layout_js')
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/dataTables.buttons.min.js') }}"></script>
    {{-- <script src="{{ asset('plugins/datatables.net-bs/js/buttons.flash.min.js') }}"></script> --}}
    <script src="{{ asset('plugins/datatables.net-bs/js/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/buttons.colVis.min.js') }}"></script>
    {{-- <script> --}}
    {{-- 	$(function (){ --}}
    {{-- 		var table = $('#tabelapadrao').DataTable({ --}}
    {{-- 			"order": [[ 0, "desc" ]], --}}
    {{-- 			responsive: true, --}}
    {{-- 			"language": { --}}
    {{-- 				"sEmptyTable": "Nenhum registro encontrado", --}}
    {{-- 				"sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros", --}}
    {{-- 				"sInfoEmpty": "Mostrando 0 até 0 de 0 registros", --}}
    {{-- 				"sInfoFiltered": "(Filtrados de _MAX_ registros)", --}}
    {{-- 				"sInfoPostFix": "", --}}
    {{-- 				"sInfoThousands": ".", --}}
    {{-- 				"sLengthMenu": "_MENU_ Por Página", --}}
    {{-- 				"sLoadingRecords": "Carregando...", --}}
    {{-- 				"sProcessing": "Processando...", --}}
    {{-- 				"sZeroRecords": "Nenhum registro encontrado", --}}
    {{-- 				"sSearch": "Pesquisar", --}}
    {{-- 				"oPaginate": { --}}
    {{-- 					"sNext": "Próximo", --}}
    {{-- 					"sPrevious": "Anterior", --}}
    {{-- 					"sFirst": "Primeiro", --}}
    {{-- 					"sLast": "Último" --}}
    {{-- 				}, --}}
    {{-- 				"oAria": { --}}
    {{-- 					"sSortAscending": ": Ordenar colunas de forma ascendente", --}}
    {{-- 					"sSortDescending": ": Ordenar colunas de forma descendente" --}}
    {{-- 				} --}}
    {{-- 			}, --}}
    {{-- 			responsive: true, --}}

    {{--        		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]], --}}

    {{--             //"dom": 'Bfrtip', --}}
    {{--             //"buttons": ['pageLength', 'copy', 'excel', 'pdf', 'colvis',], --}}
    {{-- 		}); --}}
    {{-- 	}); --}}
    {{-- </script> --}}
    @yield('in_data_table')
@endsection
