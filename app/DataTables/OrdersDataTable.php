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
    public $model, $total_bruto, $total_discount, $total_netto, $total_vat, $total_total, $total_dp;

    public function __construct()
    {
        $this->model = Order::query(); // Inisialisasi model tanpa kondisi query

        // Hitung total untuk footer
        $totals = Order::where('transaction_type', '=', 0)->selectRaw('
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
        $columns = ['name', 'created_at', 'total'];
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

                if (request()->has('status') && request()->get('status') != 'ALL') {
                    $query->where('status', 'like', "%" . request('status') . "%");
                }

                if (request()->has('date_start') || request()->has('date_end')) {
                    $query->whereBetween('created_at', [
                        Carbon::parse(request('date_start'))->startOfDay(),
                        Carbon::parse(request('date_end'))->endOfDay()
                    ]);
                }
            }, false)
            ->addColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->toDateTimeString();
            })
            ->addColumn('name', function ($row) {
                $customer = $row->customer;
                return $customer->name . " (" . $customer->phone_number . ")";
            })
            ->addColumn('netto', function ($row) {
                return 'Rp. ' . number_format($row->netto, 2, ",", ".");
            })
            ->addColumn('total', function ($row) {
                return 'Rp. ' . number_format($row->total, 2, ",", ".");
            })
            ->addColumn('product', function ($row) {
                return $row->orderItems->map(function ($orderItem) {
                    return $orderItem->products->map(function ($product) {
                        return $product->name;
                    })->implode(', ');
                })->implode('<br>');
            })
            ->addColumn('uang_muka', function ($row) {
                return 'Rp. ' . number_format($row->uang_muka, 2, ",", ".");
            })
            ->addColumn('action', function ($order) {
                $btn = '<a href="' . route('orders.print', $order->id) . '" target="_blank" class="btn bg-navy btn-sm" title="Cetak ' . $order->customer->name . '" style="margin-right: 15px;">
                    <i class="fa fa-fw fa-print"></i>
                </a>';
                $btn .= '<a class="btn btn-primary btn-sm" style="margin-right:15px;" href="' . route('orders.show', $order->id) . '" title="Detail ' . $order->customer->name . '"><i class="fa fa-eye"></i></a>';
                $btn .= '<button class="btn btn-danger btn-sm btn-delete" title="Delete ' . $order->customer->name . '" data-toggle="modal" data-target="#modal-delete" data-order-id="' . $order->id . '" data-order-name="' . $order->customer->name . '"><i class="fa fa-trash"></i></button>';
                return $btn;
            });

        $columns[] = 'product';
        $columns[] = 'action';

        return $datatables->orderColumns(['created_at'], '-:column $1')
            ->rawColumns($columns);
    }

    /**
     * Get query source of dataTable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        // Terapkan kondisi query di sini
        return $this->model
            ->with(['customer', 'site', 'orderItems.orderItemPhotos', 'orderItems.products'])
            ->where('transaction_type', '=', 0)
            ->select('orders.*');
    }

    /**
     * Get html builder.
     *
     * @return Builder
     */
    public function html(): \Yajra\DataTables\Html\Builder
    {
        return $this->builder()
            ->setTableId('table-service')
            ->addTableClass('table-striped table-bordered table-hover')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'asc')
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => [
                    'reload',
                    'excel',
                    'csv',
                    ['className' => 'buttons-printer', 'extend' => 'print', 'text' => '<i class="fa fa-file-pdf-o"></i> PDF'],
                ],
                'footerCallback' => 'function (row, data, start, end, display) {
                    function formatCurrencyToNumber(currency) {
                        let number = currency.replace("Rp. ", "").replace(/\./g, "").replace(",", ".");
                        return parseFloat(number);
                    }
                    let api = this.api();
                    setTimeout(function () {
                        let intVal = function (i) {
                            return typeof i === "string" ? formatCurrencyToNumber(i) : typeof i === "number" ? i : 0;
                        };

                        let totalBruto = intVal(' . $this->total_bruto . ');
                        let totalDiscount = intVal(' . $this->total_discount . ');
                        let totalNetto = intVal(' . $this->total_netto . ');
                        let totalVat = intVal(' . $this->total_vat . ');
                        let totalTotal = intVal(' . $this->total_total . ');
                        let totalDp = intVal(' . $this->total_dp . ');

                        $("#total_bruto").html("Rp. " + totalBruto.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $("#total_discount").html("Rp. " + totalDiscount.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $("#total_netto").html("Rp. " + totalNetto.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $("#total_vat").html("Rp. " + totalVat.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $("#total_total").html("Rp. " + totalTotal.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $("#total_dp").html("Rp. " + totalDp.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
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
        $columns = [
            Column::make('created_at')->title('Tanggal Dibuat')->addClass('text-center'),
            Column::make('name')->title('Nama Pelanggan')->addClass('text-center'),
            Column::make('product')->title('Reparasi')->addClass('text-center'),
            Column::make('number_ticket')->title('ID Pesanan')->addClass('text-center'),
            Column::make('total')->title('Total')->exportFormat('0.00')->addClass('text-center'),
            Column::make('uang_muka')->title('DP')->exportFormat('0.00')->addClass('text-center'),
            Column::make('status')->addClass('text-center'),
            Column::computed('action')->addClass('text-center')
                ->exportable(false)
                ->printable(false),
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
        return 'Orders-' . date('YmdHis');
    }
}
