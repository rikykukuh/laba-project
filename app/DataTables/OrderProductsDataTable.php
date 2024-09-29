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
    public $model, $total_bruto, $total_discount, $total_netto, $total_vat, $total_total, $total_dp;

    public function __construct()
    {
        $this->model = Order::query(); // Inisialisasi model tanpa kondisi query

        // Hitung total untuk footer
        $totals = Order::where('transaction_type', '=', 1)->selectRaw('
            SUM(bruto) as total_bruto,
            SUM(discount) as total_discount,
            SUM(netto) as total_netto,
            SUM(vat) as total_vat,
            SUM(total) as total_total,
            SUM(uang_muka) as total_dp
        ')->first();

        $this->total_bruto = $totals->total_bruto;
        $this->total_discount = $totals->total_discount;
        $this->total_netto = $totals->total_netto;
        $this->total_vat = $totals->total_vat;
        $this->total_total = $totals->total_total;
        $this->total_dp = $totals->total_dp;
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
                // Global Search
                if (request()->has('search') && request()->get('search')['value'] != '') {
                    $search = request()->get('search')['value'];
                    $query->where('number_ticket', 'like', '%' . $search . '%');
                    $query->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")->orWhere('phone_number', 'like', "%{$search}%");
                    });
                }

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
            }, false)
            ->addColumn('created_at', function($row) {
                return Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->toDateTimeString();
            })->addColumn('netto', function($row) {
                return 'Rp. '.number_format($row->netto, 2, ",", ".");
            });

        // if ($segment) {
            $datatables->addColumn('name', function($row) {
                $customer = $row->customer;
                return $customer->name . " (" . $customer->phone_number . ")";
            });

            $datatables->addColumn('product', function ($row) {
                $products = $row->orderItems->map(function ($orderItem) {
                    return $orderItem->products->map(function ($product) {
                        return $product->name;
                    })->implode(', ');
                });

                $table = '<table class="table table-xs table-striped table-bordered" style="margin:5px auto;">';
                // $table .= '<thead><tr><th>Product</th></tr></thead>';
                $table .= '<tbody>';
                foreach ($products as $product) {
                    $table .= '<tr><td>' . $product . '</td></tr>';
                }
                $table .= '</tbody>';
                $table .= '</table>';

                return $table;
            });

            // $datatables->addColumn('bruto', function($row) {
            //     return 'Rp. '.number_format($row->bruto, 2, ",", ".");
            // });
            //
            // $datatables->addColumn('discount', function($row) {
            //     return $row->discount > 100 ? 'Rp. '.number_format($row->discount, 2, ",", ".") : "$row->discount%";
            // });
            //
            // $datatables->addColumn('vat', function($row) {
            //     return 'Rp. '.number_format($row->vat, 2, ",", ".");
            // });

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
                $btn = '<a href="'.route('order-products.print', $order->id).'" target="_blank" class="btn bg-navy btn-sm" title="Cetak '.$order->customer->name.'" style="margin-right: 15px;">
                    <i class="fa fa-fw fa-print"></i>
                </a>';
                $btn .= '<a class="btn btn-primary btn-sm" style="margin-right:15px;" href="'.route('order-products.show', $order->id).'" title="Detail '.$order->customer->name.'"><i class="fa fa-eye"></i></a>';
                $btn .= '<button class="btn btn-danger btn-sm btn-delete" title="Delete '.$order->customer->name.'" data-toggle="modal" data-target="#modal-delete" data-order-id="'.$order->id.'" data-order-name="'.$order->customer->name.'"><i class="fa fa-trash"></i></button>';
                return $btn;
            });

            $columns[] = 'product';
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
        return $this->model->newQuery()->where('transaction_type', '=', 1)->with(['customer', 'site', 'orderItems.orderItemPhotos', 'orderItems.products'])->select('*');
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
            ->orderBy(0, 'asc')
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
            Column::make('created_at')->title('Tanggal Dibuat')->addClass( 'text-center' ),
            Column::make('name')->title('Nama Pelanggan')->addClass( 'text-center' ),
            Column::make('product')->title('Products')->addClass( 'text-center' ),
            Column::make('total')->title('Total')->exportFormat('0.00')->addClass( 'text-center' ),
            // Column::make('uang_muka')->title('DP')->exportFormat('0.00')->addClass( 'text-center' ),
            // Column::make('status')->addClass( 'text-center' ),
            Column::computed( 'action' )->addClass( 'text-center' )
                ->exportable( FALSE )
                ->printable( FALSE ),
        ];

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
