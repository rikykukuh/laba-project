<?php

namespace App\DataTables;

use App\Models\Order;
use Exception;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class OrderProductsDataTable extends DataTable
{
    public $model, $total_bruto, $total_discount, $total_netto, $total_vat, $total_total;

    public function __construct()
    {
        $this->model = Order::query();

        $totals = $this->model->where('transaction_type', '=', 1)->selectRaw('
        SUM(bruto) as total_bruto,
        SUM(discount) as total_discount,
        SUM(netto) as total_netto,
        SUM(vat) as total_vat,
        SUM(total) as total_total
    ')->first();

        $this->total_bruto = $totals->total_bruto;
        $this->total_discount = $totals->total_discount;
        $this->total_netto = $totals->total_netto;
        $this->total_vat = $totals->total_vat;
        $this->total_total = $totals->total_total;
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     * @throws Exception
     */
    public function dataTable($query): DataTableAbstract
    {
        // $segment = request()->segment(1) == 'laporan';
        $columns = ['created_at', 'total'];
        $datatables = datatables()->of($query)
            ->addIndexColumn()
            ->filter(function ($query) {
                if (request()->has('id')) {
                    $query->where('id', 'like', "%" . request('id') . "%");
                }

                if (request()->has('name')) {
                    $query->where('name', 'like', "%" . request('name') . "%");
                }

                if (request()->has('site_id')) {
                    if(request()->get('site_id') != 'ALL') {
                        $query->where('site_id', 'like', "%" . request('site_id') . "%");
                    }
                }

                if (request()->has('date_start') || request()->has('date_end')) {
                    // $query->whereBetween('created_at', [Carbon::parse(request('date_start'))->format('Y-m-d'), Carbon::parse(request('date_end'))->format('Y-m-d')]);
                    $query->where('created_at', '>=', Carbon::parse(request('date_start'))->startOfDay()->format('Y-m-d'))
                        ->where('created_at', '<', Carbon::parse(request('date_end'))->addDay(1)->format('Y-m-d'));
                }
            }, true)
            ->addColumn('created_at', function($row) {
                return Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->toDateTimeString();
            })->addColumn('netto', function($row) {
                return 'Rp. '.number_format($row->netto, 2, ",", ".");
            });

        // if ($segment) {
            $datatables->addColumn('bruto', function($row) {
                return 'Rp. '.number_format($row->bruto, 2, ",", ".");
            });

            $datatables->addColumn('discount', function($row) {
                return $row->discount > 100 ? 'Rp. '.number_format($row->discount, 2, ",", ".") : "$row->discount%";
            });

            $datatables->addColumn('vat', function($row) {
                return 'Rp. '.number_format($row->vat, 2, ",", ".");
            });

            $datatables->addColumn('total', function($row) {
                return 'Rp. '.number_format($row->total, 2, ",", ".");
            });

            // $datatables->addColumn('picked_by', function($row) {
            //     return $row->picked_by ?? '-';
            // });
            //
            // $datatables->addColumn('picked_at', function($row) {
            //     return $row->picked_at ?? '-';
            // });
        // }

        // if (!$segment) {
            $datatables->addColumn('action', function ($order) {
                $btn = '<a href="'.route('order-products.print', $order->id).'" target="_blank" class="btn bg-navy btn-sm" title="Cetak '.$order->name.'" style="margin-right: 15px;">
                    <i class="fa fa-fw fa-print"></i>
                </a>';
                $btn .= '<a class="btn btn-primary btn-sm" style="margin-right:15px;" href="'.route('order-products.show', $order->id).'" title="Detail '.$order->name.'"><i class="fa fa-eye"></i></a>';
                $btn .= '<form onsubmit="return confirm(\'Apakah Anda benar-benar ingin MENGHAPUS?\');" action="'.route('order-products.destroy', $order->id).'" method="post" style="display: inline-block">';
                $btn .= csrf_field();
                $btn .= method_field('DELETE');
                $btn .= '<button class="btn btn-danger btn-sm" type="submit" title="Delete '.$order->name.'" data-toggle="modal" data-target="#modal-delete-'.$order->id.'"><i class="fa fa-trash"></i></button>';
                $btn .= '</form>';
                return $btn;
            });

            $columns[] = 'action';
        // }

        return $datatables->orderColumns(['id', 'created_at'], '-:column $1')
            ->rawColumns($columns);

        // $data = Order::with('orderItems.orderItemPhotos')->select('*');
        // return Datatables::of($data)
        //     ->addIndexColumn()
        //     ->addColumn('name', function($row) {
        //         return $row->customer->name;
        //     })->addColumn('total', function($row) {
        //         return 'Rp. '.number_format($row->total, 2, ",", ".");
        //     })
        //     ->addColumn('created_at', function($row) {
        //         return Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->toDateTimeString();
        //     })->addColumn('action', function($row) {
        //         $btn = '<a class="btn btn-default btn-sm" href="'.route('order-products.show', $row->id).'" title="Detail '.$row->name.'"><i class="fa fa-eye"></i></a>';
        //         $btn .= '<form onsubmit="return confirm(\'Apakah Anda benar-benar ingin MENGHAPUS?\');" action="'.route('order-products.destroy', $row->id).'" method="post" style="display: inline-block">';
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model->newQuery()->where('transaction_type', '=', 1)->with(['customer', 'site', 'orderItems.orderItemPhotos'])->select('*');
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
            ->orderBy(7, 'asc')
            ->parameters([
                'dom'          => 'Bfrtip',
                'buttons'      => [
                    'reload',
                    'excel',
                    'csv',
                    ['className' => 'buttons-printer', 'extend' => 'print', "text" => '<i class="fa fa-file-pdf-o"></i> PDF'],
                ],
                'footerCallback' => 'function (row, data, start, end, display) {
                        function formatCurrencyToNumber(currency) {
                            // Menghapus simbol Rp.
                            let number = currency.replace("Rp. ", "");

                            // Mengganti titik pemisah ribuan dengan string kosong
                            number = number.replace(/\./g, "");

                            // Mengganti koma pemisah desimal dengan titik
                            number = number.replace(",", ".");

                            // Mengubah menjadi angka desimal
                            return parseFloat(number);
                        }
                        let api = this.api();
                        setTimeout(function() {
                            let intVal = function (i) {
                                return typeof i === "string" ? formatCurrencyToNumber(i) : typeof i === "number" ? i : 0;
                            };

                            let totalBruto = intVal('.$this->total_bruto.');
                            let totalDiscount = intVal('.$this->total_discount.');
                            let totalNetto = intVal('.$this->total_netto.');
                            let totalVat = intVal('.$this->total_vat.');
                            let totalTotal = intVal('.$this->total_total.');

                            $("#total_bruto").html("Rp. " + totalBruto.toLocaleString("id-ID", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $("#total_discount").html("Rp. " + totalDiscount.toLocaleString("id-ID", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $("#total_netto").html("Rp. " + totalNetto.toLocaleString("id-ID", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $("#total_vat").html("Rp. " + totalVat.toLocaleString("id-ID", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $("#total_total").html("Rp. " + totalTotal.toLocaleString("id-ID", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }, 1000);
                }',
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        // $segment = request()->segment(1) == 'laporan';
        $columns = [
            Column::make('customer.name')->title('Nama Pelanggan')->addClass( 'text-center' ),
            Column::make('customer.phone_number')->title('Nomor Telepon')->addClass( 'text-center' ),
            Column::make('customer.address')->title('Alamat')->addClass( 'text-center' ),
            Column::make('site.name')->title('Cabang')->addClass( 'text-center' ),
            Column::make('number_ticket')->title('ID Pesanan')->addClass( 'text-center' ),
        ];

        // if ($segment) {
            $columns[] = Column::make('bruto')->exportFormat('0.00')->addClass( 'text-center' );
            $columns[] = Column::make('discount')->addClass( 'text-center' );
            $columns[] = Column::make('netto')->exportFormat('0.00')->addClass( 'text-center' );
            $columns[] = Column::make('vat')->exportFormat('0.00')->addClass( 'text-center' );
            $columns[] = Column::make('total')->exportFormat('0.00')->addClass( 'text-center' );
            // $columns[] = Column::make('picked_by')->title('Diambil')->addClass( 'text-center' );
            // $columns[] = Column::make('picked_at')->title('Tanggal Diambil')->addClass( 'text-center' );
        // } else {
        //     $columns[] = Column::make('netto')->exportFormat('0.00')->addClass( 'text-center' );
        // }

        $columns[] = Column::make('status')->addClass( 'text-center' );
        $columns[] = Column::make('created_at')->title('Tanggal Dibuat')->addClass( 'text-center' );

        // if (!$segment) {
            $columns[] = Column::computed( 'action' )->addClass( 'text-center' )
                ->exportable( FALSE )
                ->printable( FALSE );
        // }

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'OrderProduct-' . date('YmdHis');
    }
}
