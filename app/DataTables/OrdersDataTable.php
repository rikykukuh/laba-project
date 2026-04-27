<?php

namespace App\DataTables;

use App\Models\Order;
use Exception;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Auth;

class OrdersDataTable extends DataTable
{
    public $model, $total_bruto, $total_discount, $total_netto, $total_vat, $total_total, $total_dp;

    public function __construct()
    {
        $this->model = Order::query(); // Inisialisasi model tanpa kondisi query

        $status = request('status', 'ALL');
        $site_id = request('site_id', 'ALL');

        // Default date: kemarin jam 00:00:00 sampai hari ini jam 23:59:59
        $date_start = request('date_start')
            ? Carbon::parse(request('date_start'))->startOfDay()
            : now('Asia/Jakarta')->startOfDay();

        $date_end = request('date_end')
            ? Carbon::parse(request('date_end'))->endOfDay()
            : now('Asia/Jakarta')->endOfDay();

        // Hitung total untuk footer dengan kondisi filter
        $totalsQuery = Order::where('transaction_type', 0);

        if ($status !== 'ALL') {
            $totalsQuery->where('status', $status);
        }

        if ($site_id !== 'ALL') {
            $totalsQuery->where('site_id', $site_id);
        }

        $is_ready_tomorrow = request('is_ready_tomorrow', False);

        if ($is_ready_tomorrow) {
            $totalsQuery->whereDate('estimate_take_item', Carbon::tomorrow());
        }else{
            // Selalu pakai range tanggal (default atau dari request)
            $totalsQuery->whereBetween('created_at', [$date_start, $date_end]);
        }

        $totals = $totalsQuery
        ->whereNull('deleted_at')
        ->whereNotIn('status', ['CANCEL', 'GAGAL'])
        ->selectRaw('
            SUM(bruto) as total_bruto,
            SUM(discount) as total_discount,
            SUM(netto) as total_netto,
            SUM(vat) as total_vat,
            SUM(total) as total_total,
            SUM(uang_muka) as total_dp
        ')->first();

        $this->total_bruto = $totals->total_bruto ?? 0;
        $this->total_discount = $totals->total_discount ?? 0;
        $this->total_netto = $totals->total_netto ?? 0;
        $this->total_vat = $totals->total_vat ?? 0;
        $this->total_total = $totals->total_total ?? 0;
        $this->total_dp = $totals->total_dp ?? 0;
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
        
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->filter(function ($query) {
                $query->where('transaction_type', '=', 0)
                ->whereNull('deleted_at');;

                if (request()->has('search') && request()->get('search')['value'] != '') {
                    $search = request()->get('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('number_ticket', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($qc) use ($search) {
                            $qc->where('name', 'like', "%{$search}%")
                                ->orWhere('phone_number', 'like', "%{$search}%");
                        });
                    });
                }

                if (request()->has('status') && request('status') != 'ALL') {
                    $query->where('status', 'like', "%" . request('status') . "%");
                }

                $is_ready_tomorrow = request('is_ready_tomorrow', False);
                
                if ($is_ready_tomorrow) {
                    $ready_tomorrow = request('ready_tomorrow', False);
                    $ready_today = request('ready_today', False);

                    $query->where(function ($q) use ($ready_today, $ready_tomorrow) {

                        if ($ready_today) {
                            $q->orWhereDate('estimate_take_item', Carbon::today());
                        }

                        if ($ready_tomorrow) {
                            $q->orWhereDate('estimate_take_item', Carbon::tomorrow());
                        }

                        if (!$ready_today && !$ready_tomorrow){
                            $q->orWhereDate('estimate_take_item', Carbon::today());
                        }

                    });
                    
                }else{
                    if (request()->has('date_start') && request()->has('date_end') && request('date_start') && request('date_end')) {
                        // pakai tanggal dari request
                        $query->whereBetween('created_at', [
                            Carbon::parse(request('date_start'))->startOfDay(),
                            Carbon::parse(request('date_end'))->endOfDay()
                        ]);
                    }
                }
               
            }, false)
            ->addColumn('created_at', fn($row) => Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->toDateTimeString())
            ->addColumn('name', fn($row) => $row->customer->name . " (" . $row->customer->phone_number . ")")
            ->addColumn('product', function ($row) {
                return $row->orderItems->map(function ($orderItem) {
                    return $orderItem->products->map(fn($p) => $p->name)->implode(', ');
                })->implode('<br>');
            })
            
            // ->editColumn('bruto', fn($row) => $row->bruto) // export tetap angka
            // ->editColumn('discount', fn($row) => $row->discount)
            // ->editColumn('netto', fn($row) => $row->netto)
            // ->editColumn('uang_muka', fn($row) => $row->uang_muka)
            // ->addColumn('bruto_formatted', fn($row) => 'Rp. ' . number_format($row->bruto, 0, ',', '.'))
            // ->addColumn('discount_formatted', fn($row) => 'Rp. ' . number_format($row->discount, 0, ',', '.'))
            // ->addColumn('netto_formatted', fn($row) => 'Rp. ' . number_format($row->netto, 0, ',', '.'))
            // ->addColumn('uang_muka_formatted', fn($row) => 'Rp. ' . number_format($row->uang_muka, 0, ',', '.'))
            ->editColumn('estimate_take_item', fn($row) =>
                Carbon::parse($row->estimate_take_item)->format('d-M-Y')
            )
            ->editColumn('bruto', fn($row) =>
                in_array($row->status, ['CANCEL','GAGAL']) ? 0 : $row->bruto
            )

            ->editColumn('discount', fn($row) =>
                in_array($row->status, ['CANCEL','GAGAL']) ? 0 : $row->discount
            )

            ->editColumn('netto', fn($row) =>
                in_array($row->status, ['CANCEL','GAGAL']) ? 0 : $row->netto
            )

            ->editColumn('uang_muka', fn($row) =>
                in_array($row->status, ['CANCEL','GAGAL']) ? 0 : $row->uang_muka
            )

            ->addColumn('bruto_formatted', fn($row) =>
                'Rp. ' . number_format(in_array($row->status, ['CANCEL','GAGAL']) ? 0 : $row->bruto, 0, ',', '.')
            )

            ->addColumn('discount_formatted', fn($row) =>
                'Rp. ' . number_format(in_array($row->status, ['CANCEL','GAGAL']) ? 0 : $row->discount, 0, ',', '.')
            )

            ->addColumn('netto_formatted', fn($row) =>
                'Rp. ' . number_format(in_array($row->status, ['CANCEL','GAGAL']) ? 0 : $row->netto, 0, ',', '.')
            )

            ->addColumn('uang_muka_formatted', fn($row) =>
                'Rp. ' . number_format(in_array($row->status, ['CANCEL','GAGAL']) ? 0 : $row->uang_muka, 0, ',', '.')
            )
            ->addColumn('action', function ($order) {
                $btn = '<div class="btn-group btn-sm">
                            <button type="button" class="btn btn-sm bg-navy dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-fw fa-print"></i>
                            </button>
                            <ul class="dropdown-menu">';
                $btn .= '<li><a href="' . route('orders.print', $order->id) . '?type=customer" target="_blank">Customer</a></li>';
                $btn .= '<li><a href="' . route('orders.print', $order->id) . '?type=cashier" target="_blank">Cashier</a></li>';
                $btn .= '<li><a href="' . route('orders.print', $order->id) . '?type=reparation" target="_blank">Reparation</a></li>';
                $btn .= '</ul></div>';
                $btn .= '<a class="btn btn-primary btn-sm" style="margin:5px auto;" href="'.route('orders.show', $order->id).'"><i class="fa fa-eye"></i></a>';
                if (Auth::user()->can('root-dev')) {
                    $btn .= '<button class="btn btn-danger btn-sm btn-delete" data-toggle="modal" data-target="#modal-delete" data-order-id="'.$order->id.'"><i class="fa fa-trash"></i></button>';
                }
                $btn .= '<button class="btn btn-warning btn-sm"
                            data-toggle="modal"
                            data-target="#modal-complain"
                            data-id="'.$order->id.'"
                            data-name="'.$order->number_ticket.'"
                            data-customer="'.$order->customer->name.'">
                            <i class="fa fa-exclamation-circle"></i>
                        </button>';
                return $btn;
            })
            ->orderColumns(['created_at'], '-:column $1')
            ->rawColumns(['product', 'action']);
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

                        $("#total_total").html("Rp. " + totalBruto.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $("#total_discount").html("Rp. " + totalDiscount.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $("#total_netto").html("Rp. " + totalNetto.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $("#total_vat").html("Rp. " + totalVat.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        // $("#total_total").html("Rp. " + totalTotal.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
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

          
        if (request('is_ready_tomorrow', False)) {
            $create_or_estimate = Column::make('estimate_take_item')->title('Tanggal Diambil')->addClass('text-center');
        }else{
            $create_or_estimate = Column::make('created_at')->title('Tanggal Dibuat')->addClass('text-center');
        }
        $columns = [
            
            $create_or_estimate,
            Column::make('name')->title('Nama Pelanggan')->addClass('text-center'),
            // Column::make('product')->title('Reparasi')->addClass('text-center'),
            Column::make('number_ticket')->title('ID Pesanan')->addClass('text-center')->width(5),
            // Column::make('bruto')->title('Total')->exportFormat('0.00')->addClass( 'text-center' ),
            // Column::make('discount')->title('Discount')->exportFormat('0.00')->addClass( 'text-center' ),
            // Column::make('netto')->title('(Total - Discount)')->exportFormat('0.00')->addClass( 'text-center' ),
            // Column::make('uang_muka')->title('DP')->exportFormat('0.00')->addClass('text-center'),

            Column::computed('bruto_formatted')->title('Total')->addClass('text-center')->exportable(false),
            Column::computed('discount_formatted')->title('Discount')->addClass('text-center')->exportable(false),
            Column::computed('netto_formatted')->title('(Total - Discount)')->addClass('text-center')->exportable(false),
            Column::computed('uang_muka_formatted')->title('DP')->addClass('text-center')->exportable(false),

            // Kolom asli untuk export
            Column::make('bruto')->title('Total')->addClass('text-center')->exportFormat('0.00')->visible(false),
            Column::make('discount')->title('Discount')->addClass('text-center')->exportFormat('0.00')->visible(false),
            Column::make('netto')->title('(Total - Discount)')->addClass('text-center')->exportFormat('0.00')->visible(false),
            Column::make('uang_muka')->title('DP')->addClass('text-center')->exportFormat('0.00')->visible(false),

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
