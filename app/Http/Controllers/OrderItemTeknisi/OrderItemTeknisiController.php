<?php


namespace App\Http\Controllers\OrderItemTeknisi;

use App\Http\Controllers\Controller;
use App\Models\OrderItemTeknisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderItemTeknisiExport;
use App\Exports\SummaryTeknisiExport;
use App\Exports\TechnicianImportTemplateExport;
use App\Exports\OrderItemQcExport;
use App\Exports\SummaryQcExport;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


class OrderItemTeknisiController extends Controller
{

    public function index(Request $request)
    {
        $query = OrderItemTeknisi::with(['user', 'orderItem', 'orderItem.order.orderItems:id,order_id']);

        if ($request->search) {
            $query->where('order_item_id', 'like', '%' . $request->search . '%')
                ->orWhereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $data = $query->latest()->paginate(10);

        $summaryQuery = OrderItemTeknisi::select(
                    'order_item_teknisi.user_id',
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'masuk' THEN 1 ELSE 0 END) as masuk"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'proses' THEN 1 ELSE 0 END) as proses"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'selesai' THEN 1 ELSE 0 END) as selesai"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'gudang a' THEN 1 ELSE 0 END) as gudang_a"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'gudang b' THEN 1 ELSE 0 END) as gudang_b"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'gudang c' THEN 1 ELSE 0 END) as gudang_c"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'cancel' THEN 1 ELSE 0 END) as cancel"),
                    DB::raw("SUM(CASE WHEN order_items.state IS NULL OR TRIM(order_items.state) = '' THEN 1 ELSE 0 END) as belum_ada_state")
                    )
                    ->join('order_items', 'order_items.id', '=', 'order_item_teknisi.order_item_id');

        if ($request->start_date && $request->end_date) {
            $summaryQuery->whereBetween('order_item_teknisi.created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }
        $summary = $summaryQuery
                    ->with('user')
                    ->groupBy('order_item_teknisi.user_id')
                    ->get();

        $qcQuery = OrderItem::with(['qc:id,name', 'order.orderItems:id,order_id'])
            ->whereNotNull('qc_id');

        if ($request->search) {
            $search = $request->search;
            $qcQuery->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('qc', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('order', function ($query) use ($search) {
                        $query->where('number_ticket', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->start_date && $request->end_date) {
            $qcQuery->whereBetween('updated_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $qcData = $qcQuery->latest('updated_at')->paginate(10, ['*'], 'qc_page');

        $qcSummaryQuery = OrderItem::select(
                'qc_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'masuk' THEN 1 ELSE 0 END) as masuk"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'proses' THEN 1 ELSE 0 END) as proses"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'selesai' THEN 1 ELSE 0 END) as selesai"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'gudang a' THEN 1 ELSE 0 END) as gudang_a"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'gudang b' THEN 1 ELSE 0 END) as gudang_b"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'gudang c' THEN 1 ELSE 0 END) as gudang_c"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'cancel' THEN 1 ELSE 0 END) as cancel"),
                DB::raw("SUM(CASE WHEN state IS NULL OR TRIM(state) = '' THEN 1 ELSE 0 END) as belum_ada_state")
            )
            ->whereNotNull('qc_id');

        if ($request->start_date && $request->end_date) {
            $qcSummaryQuery->whereBetween('updated_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $qcSummary = $qcSummaryQuery->with('qc')->groupBy('qc_id')->get();


        $technicians = $this->eligibleTechnicians()
            ->orderBy('name')
            ->get(['users.id', 'users.name']);

        return view('OrderItemTeknisi.index', compact('data', 'summary', 'qcData', 'qcSummary', 'technicians'));

    }

    public function searchOrders(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('q'));

        $orders = Order::query()
            ->with('customer:id,name')
            ->withCount('orderItems')
            ->where('transaction_type', 0)
            ->whereHas('orderItems')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('number_ticket', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest('id')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $orders->map(function (Order $order) {
                $customerName = optional($order->customer)->name;

                return [
                    'id' => $order->id,
                    'text' => ($order->number_ticket ?: 'Tanpa No. Bon')
                        . ($customerName ? ' - ' . $customerName : '')
                        . ' (' . $order->order_items_count . ' item)',
                ];
            })->values(),
        ]);
    }

    public function orderItems(Order $order): JsonResponse
    {
        abort_unless((int) $order->transaction_type === 0, 404);

        $order->load(['orderItems.product:id,name', 'orderItems.teknisis:id']);
        $isPickedUp = strtoupper((string) $order->status) === 'DIAMBIL';

        $items = $order->orderItems->values()->map(function (OrderItem $item, int $index) use ($isPickedUp, $order) {
            $technicianIds = $this->assignedTechnicianIds($item);
            $productName = optional($item->product)->name ?: 'Item service';
            $description = trim((string) $item->note);
            $sequenceLabel = OrderItem::sequenceLabel($index + 1);
            $importCode = $order->itemImportCode($index + 1);

            return [
                'id' => $item->id,
                'sequence' => $sequenceLabel,
                'import_code' => $importCode,
                'text' => $sequenceLabel . ' [' . $importCode . '] - ' . $productName
                    . ($description !== '' ? ' | ' . $description : '')
                    . ' (Slot ' . $technicianIds->count() . '/3)',
                'state' => $isPickedUp ? 'selesai' : ($item->state ?: ''),
                'technician_ids' => $technicianIds->values(),
                'technician_count' => $technicianIds->count(),
                'is_full' => $technicianIds->count() >= 3,
                'qc_id' => $item->qc_id ? (int) $item->qc_id : null,
            ];
        })->values();

        return response()->json([
            'order_picked_up' => $isPickedUp,
            'all_slots_full' => $items->isNotEmpty() && $items->every(function ($item) {
                return $item['is_full'];
            }),
            'items' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'order_id' => 'required|integer|exists:orders,id',
            'order_item_id' => 'required|integer|exists:order_items,id',
            'state' => 'required|in:masuk,proses,selesai,gudang A,gudang B,gudang C,cancel',
            'is_qc' => 'nullable|boolean',
        ]);

        if (!$this->eligibleTechnicians()->whereKey($validated['user_id'])->exists()) {
            return response()->json([
                'message' => 'User yang dipilih tidak terdaftar sebagai teknisi atau QC.',
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($validated) {
                $item = OrderItem::where('order_id', $validated['order_id'])
                    ->lockForUpdate()
                    ->findOrFail($validated['order_item_id']);
                $item->load(['order:id,status,transaction_type', 'teknisis:id']);

                if ((int) optional($item->order)->transaction_type !== 0) {
                    return ['error' => 'Bon yang dipilih bukan transaksi reparasi.'];
                }

                $userId = (int) $validated['user_id'];
                $isQc = (bool) ($validated['is_qc'] ?? false);
                $isPickedUp = strtoupper((string) optional($item->order)->status) === 'DIAMBIL';

                if ($isQc) {
                    if ($item->qc_id && (int) $item->qc_id !== $userId) {
                        return ['error' => 'QC pada item service ini sudah terisi.'];
                    }

                    $item->update([
                        'qc_id' => $userId,
                        'state' => $isPickedUp ? 'selesai' : ($validated['state'] ?: null),
                    ]);

                    return ['item' => $item, 'is_qc' => true];
                }

                $technicianIds = $this->assignedTechnicianIds($item);
                $alreadyInPivot = $item->teknisis->pluck('id')->map(function ($id) {
                    return (int) $id;
                })->contains($userId);

                if ($alreadyInPivot) {
                    return ['error' => 'Teknisi tersebut sudah ditugaskan pada item service ini.'];
                }

                if (!$technicianIds->contains($userId) && $technicianIds->count() >= 3) {
                    return ['error' => 'Slot teknisi pada item service ini sudah terpenuhi (3/3).'];
                }

                $now = now();
                DB::table('order_item_teknisi')->updateOrInsert(
                    ['order_item_id' => $item->id, 'user_id' => $userId],
                    ['created_at' => $now, 'updated_at' => $now]
                );

                $technicianIds = $technicianIds->push($userId)->unique()->values();
                $item->update([
                    'teknisi1_id' => $technicianIds->get(0),
                    'teknisi2_id' => $technicianIds->get(1),
                    'teknisi3_id' => $technicianIds->get(2),
                    'state' => $isPickedUp ? 'selesai' : ($validated['state'] ?: null),
                ]);

                return ['item' => $item, 'is_qc' => false];
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Item service tidak ditemukan pada bon yang dipilih.',
            ], 422);
        }

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 422);
        }

        return response()->json([
            'message' => $result['is_qc']
                ? 'QC berhasil ditugaskan pada item service.'
                : 'Teknisi berhasil ditugaskan pada item service.',
            'state' => $result['item']->state,
        ]);
    }

    public function export(Request $request)
    {
        if ($request->input('type') === 'qc') {
            $query = OrderItem::with(['qc:id,name', 'order.orderItems:id,order_id'])->whereNotNull('qc_id');
            if ($request->search) {
                $search = $request->search;
                $query->where(function ($query) use ($search) {
                    $query->whereHas('qc', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('order', function ($query) use ($search) {
                        $query->where('number_ticket', 'like', '%' . $search . '%');
                    });
                });
            }
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('updated_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            return Excel::download(new OrderItemQcExport($query->get()), 'list_qc.xlsx');
        }

        $query = OrderItemTeknisi::with(['user', 'orderItem.order.orderItems:id,order_id']);

        if ($request->search) {
            $query->where('order_item_id', 'like', '%' . $request->search . '%')
                ->orWhereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $data = $query->get();

        return Excel::download(new OrderItemTeknisiExport($data), 'list_teknisi.xlsx');
    }

    public function exportSummary(Request $request)
    {
        if ($request->input('type') === 'qc') {
            $query = OrderItem::select(
                    'qc_id',
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'masuk' THEN 1 ELSE 0 END) as masuk"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'proses' THEN 1 ELSE 0 END) as proses"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'selesai' THEN 1 ELSE 0 END) as selesai"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'gudang a' THEN 1 ELSE 0 END) as gudang_a"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'gudang b' THEN 1 ELSE 0 END) as gudang_b"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'gudang c' THEN 1 ELSE 0 END) as gudang_c"),
                    DB::raw("SUM(CASE WHEN LOWER(TRIM(state)) = 'cancel' THEN 1 ELSE 0 END) as cancel"),
                    DB::raw("SUM(CASE WHEN state IS NULL OR TRIM(state) = '' THEN 1 ELSE 0 END) as belum_ada_state")
                )->whereNotNull('qc_id');
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('updated_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            return Excel::download(
                new SummaryQcExport($query->with('qc')->groupBy('qc_id')->get()),
                'summary_qc.xlsx'
            );
        }

        $query = OrderItemTeknisi::select(
                'order_item_teknisi.user_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'masuk' THEN 1 ELSE 0 END) as masuk"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'proses' THEN 1 ELSE 0 END) as proses"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'selesai' THEN 1 ELSE 0 END) as selesai"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'gudang a' THEN 1 ELSE 0 END) as gudang_a"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'gudang b' THEN 1 ELSE 0 END) as gudang_b"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'gudang c' THEN 1 ELSE 0 END) as gudang_c"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(order_items.state)) = 'cancel' THEN 1 ELSE 0 END) as cancel"),
                DB::raw("SUM(CASE WHEN order_items.state IS NULL OR TRIM(order_items.state) = '' THEN 1 ELSE 0 END) as belum_ada_state")
            )
            ->join('order_items', 'order_items.id', '=', 'order_item_teknisi.order_item_id');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_item_teknisi.created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $summary = $query->with('user')
            ->groupBy('order_item_teknisi.user_id')
            ->get();

        return Excel::download(new SummaryTeknisiExport($summary), 'summary_teknisi.xlsx');
    }

    public function downloadImportTemplate()
    {
        $technicians = $this->eligibleTechnicians()->orderBy('name')->get(['users.id', 'users.name']);

        return Excel::download(
            new TechnicianImportTemplateExport($technicians),
            'template_import_teknisi.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $rows = Excel::toCollection(null, $request->file('file'))->first();
        } catch (\Throwable $exception) {
            return back()->withErrors(['file' => 'File Excel tidak dapat dibaca. Pastikan memakai template yang tersedia.']);
        }

        if (!$rows || $rows->isEmpty()) {
            return back()->withErrors(['file' => 'File Excel tidak memiliki data.']);
        }

        $expectedHeaders = ['kode-bon-item', 'nama-teknisi', 'tanggal-dikerjakan', 'tanggal-selesai', 'qc'];
        $actualHeaders = collect($rows->first())->take(5)->map(function ($header) {
            return Str::slug(trim((string) $header));
        })->values()->all();

        if ($actualHeaders !== $expectedHeaders) {
            return back()->withErrors([
                'file' => 'Header Excel tidak sesuai. Silakan download dan gunakan template terbaru.',
            ]);
        }

        $techniciansByName = $this->eligibleTechnicians()
            ->get(['users.id', 'users.name'])
            ->groupBy(function ($user) {
                return mb_strtolower(trim($user->name), 'UTF-8');
            });

        $parsedRows = [];
        $errors = [];

        foreach ($rows->slice(1)->values() as $index => $row) {
            $excelRow = $index + 2;
            $values = collect($row)->pad(5, null)->take(5)->values();
            $itemCode = $this->normalizeItemImportCode($values[0]);
            $technicianName = trim((string) $values[1]);
            $startedAt = $this->parseExcelDate($values[2]);
            $finishedAt = $values[3] === null || (is_string($values[3]) && trim($values[3]) === '')
                ? now()->startOfDay()
                : $this->parseExcelDate($values[3]);
            $qcFlag = mb_strtolower(trim((string) $values[4]), 'UTF-8');
            $isQc = $qcFlag === 'ya';

            if ($technicianName === '' && $itemCode === '' && !$startedAt) {
                continue;
            }

            $technicianMatches = $techniciansByName->get(mb_strtolower($technicianName, 'UTF-8'), collect());

            if ($technicianName === '') {
                $errors[] = "Baris {$excelRow}: Teknisi wajib diisi.";
            } elseif ($technicianMatches->isEmpty()) {
                $errors[] = "Baris {$excelRow}: Teknisi atas nama '{$technicianName}' belum terdaftar di sistem sebagai teknisi atau QC.";
            } elseif ($technicianMatches->count() > 1) {
                $errors[] = "Baris {$excelRow}: Nama teknisi '{$technicianName}' duplikat di data user.";
            }

            $codeParts = $this->parseItemImportCode($itemCode);
            if (!$codeParts) {
                $errors[] = "Baris {$excelRow}: Kode Bon Item tidak valid. Contoh penulisan: B12345A.";
            }

            if (!$startedAt) {
                $errors[] = "Baris {$excelRow}: Tanggal Dikerjakan tidak valid.";
            }

            if (!$finishedAt) {
                $errors[] = "Baris {$excelRow}: Tanggal Selesai tidak valid.";
            }

            if ($qcFlag !== '' && !$isQc) {
                $errors[] = "Baris {$excelRow}: Flag QC harus dikosongkan atau dipilih Ya.";
            }

            if ($technicianMatches->count() === 1 && $codeParts && $startedAt && $finishedAt) {
                if ($finishedAt->lt($startedAt)) {
                    $errors[] = "Baris {$excelRow}: Tanggal Selesai tidak boleh sebelum Tanggal Dikerjakan.";
                }

                $parsedRows[] = [
                    'excel_row' => $excelRow,
                    'user_id' => $technicianMatches->first()->id,
                    'item_code' => $itemCode,
                    'ticket_code' => $codeParts['ticket_code'],
                    'item_sequence' => $codeParts['item_sequence'],
                    'item_position' => $codeParts['item_position'],
                    'started_at' => $startedAt,
                    'finished_at' => $finishedAt,
                    'is_qc' => $isQc,
                ];
            }
        }

        if (empty($parsedRows) && empty($errors)) {
            return back()->withErrors(['file' => 'Tidak ada baris data yang dapat diimport.']);
        }

        $ticketCodes = collect($parsedRows)->pluck('ticket_code')->unique()->values();
        $orders = Order::with(['orderItems.teknisis:id'])
            ->where('transaction_type', 0)
            ->whereIn(
                DB::raw("UPPER(REPLACE(REPLACE(number_ticket, '-', ''), ' ', ''))"),
                $ticketCodes
            )
            ->get()
            ->groupBy(function (Order $order) {
                return $this->normalizeTicketCode($order->number_ticket);
            });

        $items = collect();

        foreach ($parsedRows as $rowIndex => $parsedRow) {
            $orderMatches = $orders->get($parsedRow['ticket_code'], collect());

            if ($orderMatches->isEmpty()) {
                $errors[] = "Baris {$parsedRow['excel_row']}: Bon {$parsedRow['ticket_code']} tidak ditemukan.";
                continue;
            }

            if ($orderMatches->count() > 1) {
                $errors[] = "Baris {$parsedRow['excel_row']}: Bon {$parsedRow['ticket_code']} ditemukan lebih dari satu kali.";
                continue;
            }

            /** @var Order $order */
            $order = $orderMatches->first();
            $item = $order->orderItems->values()->get($parsedRow['item_position'] - 1);

            if (!$item) {
                $errors[] = "Baris {$parsedRow['excel_row']}: Item {$parsedRow['item_sequence']} tidak ditemukan pada bon {$order->number_ticket}.";
                continue;
            }

            $parsedRows[$rowIndex]['order_item_id'] = $item->id;
            $parsedRows[$rowIndex]['ticket_number'] = $order->number_ticket;
            $items->put($item->id, $item);
        }

        $resolvedRows = collect($parsedRows)->filter(function ($row) {
            return isset($row['order_item_id']);
        });

        foreach ($resolvedRows->groupBy('order_item_id') as $itemId => $itemRows) {
            $item = $items->get($itemId);
            if (!$item) {
                continue;
            }

            $technicianRows = $itemRows->where('is_qc', false);
            $technicianIds = collect([$item->teknisi1_id, $item->teknisi2_id, $item->teknisi3_id])
                ->merge($item->teknisis->pluck('id'))
                ->merge($technicianRows->pluck('user_id'))
                ->filter()
                ->unique();

            if ($technicianIds->count() > 3) {
                $itemCode = $itemRows->first()['item_code'];
                $errors[] = "Kode Bon Item {$itemCode} sudah memiliki 3 teknisi yang menangani.";
            }

            $qcIds = $itemRows->where('is_qc', true)->pluck('user_id')
                ->push($item->qc_id)->filter()->unique();
            if ($qcIds->count() > 1) {
                $errors[] = "Kode Bon Item {$itemRows->first()['item_code']} sudah memiliki QC yang berbeda.";
            }
        }

        if (!empty($errors)) {
            return back()->withInput()->with('import_errors', array_values(array_unique($errors)));
        }

        $parsedRows = $resolvedRows->values()->all();

        DB::transaction(function () use ($parsedRows, $items) {
            foreach ($parsedRows as $parsedRow) {
                if ($parsedRow['is_qc']) {
                    continue;
                }

                DB::table('order_item_teknisi')->updateOrInsert(
                    [
                        'order_item_id' => $parsedRow['order_item_id'],
                        'user_id' => $parsedRow['user_id'],
                    ],
                    [
                        'created_at' => $parsedRow['started_at'],
                        'updated_at' => $parsedRow['finished_at'],
                    ]
                );
            }

            foreach (collect($parsedRows)->groupBy('order_item_id') as $itemId => $itemRows) {
                $item = $items->get($itemId);
                $qcId = $itemRows->where('is_qc', true)->pluck('user_id')->first();
                $technicianRows = $itemRows->where('is_qc', false);
                $technicianIds = collect([$item->teknisi1_id, $item->teknisi2_id, $item->teknisi3_id])
                    ->merge($item->teknisis->pluck('id'))
                    ->merge($technicianRows->pluck('user_id'))
                    ->filter()
                    ->unique()
                    ->values();

                $item->update([
                    'teknisi1_id' => $technicianIds->get(0),
                    'teknisi2_id' => $technicianIds->get(1),
                    'teknisi3_id' => $technicianIds->get(2),
                    'qc_id' => $qcId ?: $item->qc_id,
                ]);
            }
        });

        return back()->with(
            'success',
            count($parsedRows) . ' penugasan teknisi berhasil diimport.'
        );
    }

    private function eligibleTechnicians()
    {
        return User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['teknisi', 'qc_user']);
        });
    }

    private function assignedTechnicianIds(OrderItem $item)
    {
        return collect([$item->teknisi1_id, $item->teknisi2_id, $item->teknisi3_id])
            ->merge($item->relationLoaded('teknisis') ? $item->teknisis->pluck('id') : [])
            ->filter()
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values();
    }

    private function normalizeText($value)
    {
        if (is_float($value) && floor($value) === $value) {
            return (string) (int) $value;
        }

        return trim((string) $value);
    }

    private function normalizeTicketCode($value): string
    {
        return Order::normalizeTicketCode($this->normalizeText($value));
    }

    private function normalizeItemImportCode($value): string
    {
        return $this->normalizeTicketCode($value);
    }

    private function parseItemImportCode(string $itemCode): ?array
    {
        if (!preg_match('/^(.+\d)([A-Z]+)$/', $itemCode, $matches)) {
            return null;
        }

        $position = OrderItem::sequencePosition($matches[2]);

        if (!$position) {
            return null;
        }

        return [
            'ticket_code' => $matches[1],
            'item_sequence' => $matches[2],
            'item_position' => $position,
        ];
    }

    private function parseExcelDate($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->startOfDay();
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->startOfDay();
            } catch (\Throwable $exception) {
                return null;
            }
        }

        $value = trim((string) $value);
        foreach (['d-m-Y', 'd/m/Y', 'Y-m-d'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date && $date->format($format) === $value) {
                    return $date->startOfDay();
                }
            } catch (\Throwable $exception) {
                // Coba format tanggal berikutnya.
            }
        }

        return null;
    }

}
