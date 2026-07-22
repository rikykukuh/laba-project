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
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


class OrderItemTeknisiController extends Controller
{

    public function index(Request $request)
    {
        $query = OrderItemTeknisi::with(['user', 'orderItem', 'orderItem.order']);

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


        return view('OrderItemTeknisi.index', compact('data', 'summary'));

    }

    public function export(Request $request)
    {
        $query = OrderItemTeknisi::with(['user', 'orderItem.order']);

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

        $expectedHeaders = ['no-bon', 'id-barang', 'nama-teknisi', 'tanggal-dikerjakan', 'tanggal-selesai'];
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

        foreach ($rows->slice(1) as $index => $row) {
            $excelRow = $index + 2;
            $values = collect($row)->pad(5, null)->take(5)->values();
            $ticketNumber = trim($this->normalizeText($values[0]));
            $itemId = $this->normalizeInteger($values[1]);
            $technicianName = trim((string) $values[2]);
            $startedAt = $this->parseExcelDate($values[3]);
            $finishedAt = $values[4] === null || (is_string($values[4]) && trim($values[4]) === '')
                ? now()->startOfDay()
                : $this->parseExcelDate($values[4]);

            if ($technicianName === '' && !$itemId && $ticketNumber === '' && !$startedAt) {
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

            if (!$itemId) {
                $errors[] = "Baris {$excelRow}: ID Barang wajib berupa angka positif.";
            }

            if ($ticketNumber === '') {
                $errors[] = "Baris {$excelRow}: No Bon wajib diisi.";
            }

            if (!$startedAt) {
                $errors[] = "Baris {$excelRow}: Tanggal Dikerjakan tidak valid.";
            }

            if (!$finishedAt) {
                $errors[] = "Baris {$excelRow}: Tanggal Selesai tidak valid.";
            }

            if ($technicianMatches->count() === 1 && $itemId && $ticketNumber !== '' && $startedAt && $finishedAt) {
                if ($finishedAt->lt($startedAt)) {
                    $errors[] = "Baris {$excelRow}: Tanggal Selesai tidak boleh sebelum Tanggal Dikerjakan.";
                }

                $parsedRows[] = [
                    'excel_row' => $excelRow,
                    'user_id' => $technicianMatches->first()->id,
                    'order_item_id' => $itemId,
                    'ticket_number' => $ticketNumber,
                    'started_at' => $startedAt,
                    'finished_at' => $finishedAt,
                ];
            }
        }

        if (empty($parsedRows) && empty($errors)) {
            return back()->withErrors(['file' => 'Tidak ada baris data yang dapat diimport.']);
        }

        $items = OrderItem::with(['order:id,number_ticket', 'teknisis:id'])
            ->whereIn('id', collect($parsedRows)->pluck('order_item_id')->unique())
            ->get()
            ->keyBy('id');

        foreach ($parsedRows as $parsedRow) {
            $item = $items->get($parsedRow['order_item_id']);

            if (!$item) {
                $errors[] = "Baris {$parsedRow['excel_row']}: ID Barang {$parsedRow['order_item_id']} tidak ditemukan.";
                continue;
            }

            $actualTicket = $this->normalizeText(optional($item->order)->number_ticket);
            if ($actualTicket !== $parsedRow['ticket_number']) {
                $errors[] = "Baris {$parsedRow['excel_row']}: No Bon tidak sesuai dengan ID Barang {$parsedRow['order_item_id']}.";
            }
        }

        foreach (collect($parsedRows)->groupBy('order_item_id') as $itemId => $itemRows) {
            $item = $items->get($itemId);
            if (!$item) {
                continue;
            }

            $technicianIds = collect([$item->teknisi1_id, $item->teknisi2_id, $item->teknisi3_id])
                ->merge($item->teknisis->pluck('id'))
                ->merge($itemRows->pluck('user_id'))
                ->filter()
                ->unique();

            if ($technicianIds->count() > 3) {
                $ticketNumber = $this->normalizeText(optional($item->order)->number_ticket);
                $errors[] = "ID Barang {$itemId} dengan No Bon {$ticketNumber} sudah memiliki 3 teknisi yang menangani.";
            }
        }

        if (!empty($errors)) {
            return back()->withInput()->with('import_errors', array_values(array_unique($errors)));
        }

        DB::transaction(function () use ($parsedRows, $items) {
            foreach ($parsedRows as $parsedRow) {
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
                $technicianIds = collect([$item->teknisi1_id, $item->teknisi2_id, $item->teknisi3_id])
                    ->merge($item->teknisis->pluck('id'))
                    ->merge($itemRows->pluck('user_id'))
                    ->filter()
                    ->unique()
                    ->values();

                $item->update([
                    'teknisi1_id' => $technicianIds->get(0),
                    'teknisi2_id' => $technicianIds->get(1),
                    'teknisi3_id' => $technicianIds->get(2),
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

    private function normalizeInteger($value)
    {
        if (!is_numeric($value) || (int) $value <= 0 || (float) $value != (int) $value) {
            return null;
        }

        return (int) $value;
    }

    private function normalizeText($value)
    {
        if (is_float($value) && floor($value) === $value) {
            return (string) (int) $value;
        }

        return trim((string) $value);
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
