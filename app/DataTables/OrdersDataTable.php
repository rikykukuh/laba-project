<?php

namespace App\DataTables;

use App\Models\Order;
use Exception;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class OrdersDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     * @throws Exception
     */
    public function dataTable($query): DataTableAbstract
    {
        return datatables()->of($query)
            ->addIndexColumn()
            ->filter(function ($query) {
                if (request()->has('id')) {
                    $query->where('id', 'like', "%" . request('id') . "%");
                }

                if (request()->has('name')) {
                    $query->where('name', 'like', "%" . request('name') . "%");
                }
            }, true)
            ->addColumn('created_at', function($row) {
                return Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->toDateTimeString();
            })->addColumn('total', function($row) {
                return 'Rp. '.number_format($row->total, 2, ",", ".");
            })
            ->addColumn('action', function ($order) {
                $btn = '<a class="btn btn-default btn-sm" style="margin-right:10px;" href="'.route('orders.show', $order->id).'" title="Detail '.$order->name.'"><i class="fa fa-eye"></i></a>';
                $btn .= '<form onsubmit="return confirm(\'Apakah Anda benar-benar ingin MENGHAPUS?\');" action="'.route('orders.destroy', $order->id).'" method="post" style="display: inline-block">';
                $btn .= csrf_field();
                $btn .= method_field('DELETE');
                $btn .= '<button class="btn btn-danger btn-sm" type="submit" title="Delete '.$order->name.'" data-toggle="modal" data-target="#modal-delete-'.$order->id.'"><i class="fa fa-trash"></i></button>';
                $btn .= '</form>';
                return $btn;
            })
            ->rawColumns(['created_at', 'total', 'action']);

        // $data = Order::with('orderItems.orderItemPhotos')->select('*');
        // return Datatables::of($data)
        //     ->addIndexColumn()
        //     ->addColumn('name', function($row) {
        //         return $row->client->name;
        //     })->addColumn('total', function($row) {
        //         return 'Rp. '.number_format($row->total, 2, ",", ".");
        //     })
        //     ->addColumn('created_at', function($row) {
        //         return Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->toDateTimeString();
        //     })->addColumn('action', function($row) {
        //         $btn = '<a class="btn btn-default btn-sm" href="'.route('orders.show', $row->id).'" title="Detail '.$row->name.'"><i class="fa fa-eye"></i></a>';
        //         $btn .= '<form onsubmit="return confirm(\'Apakah Anda benar-benar ingin MENGHAPUS?\');" action="'.route('orders.destroy', $row->id).'" method="post" style="display: inline-block">';
        //         $btn .= csrf_field();
        //         $btn .= method_field('DELETE');
        //         $btn .= '<button class="btn btn-danger btn-sm" type="submit" title="Delete '.$row->name.'" data-toggle="modal" data-target="#modal-delete-'.$row->id.'"><i class="fa fa-trash"></i></button>';
        //         $btn .= '</form>';
        //         return $btn;
        //     })
        //     ->rawColumns(['action'])
        //     ->make(true);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model): \Illuminate\Database\Eloquent\Builder
    {
        return $model->newQuery()->with(['client', 'orderItems.orderItemPhotos'])->select('*');
        // return Order::with('orderItems.orderItemPhotos')->select('*');
    }

    /**
     * Get html builder.
     *
     * @return Builder
     */
    public function html(): \Yajra\DataTables\Html\Builder
    {
        return $this->builder()
            ->setTableId('table-order')
            ->addTableClass('table-striped table-bordered table-hover')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->parameters([
                'dom'          => 'Bfrtip',
                'buttons'      => [
                    'reload',
                    'excel',
                    'csv',
                    ['className' => 'buttons-printer', 'extend' => 'print', "text" => '<i class="fa fa-file-pdf-o"></i> PDF'],
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            Column::make('client.name')->title('Nama Pelanggan')->addClass( 'text-center' ),
            Column::make('number_ticket')->title('ID Pesanan')->addClass( 'text-center' ),
            Column::make('total')->exportFormat('0.00')->addClass( 'text-center' ),
            Column::make('status')->addClass( 'text-center' ),
            Column::make('created_at')->title('Tanggal Dibuat')->addClass( 'text-center' ),
            Column::computed( 'action' )->addClass( 'text-center' )
                ->exportable( FALSE )
                ->printable( FALSE )
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Orders-' . date('YmdHis');
    }
}
