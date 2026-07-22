<?php

namespace App\Http\Controllers\Order;

use App\DataTables\OrdersDataTable;
use App\Http\Controllers\Controller;


use App\Models\City;
use App\Models\Config;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemPhoto;
use App\Models\Payment;
use App\Models\PaymentMerchant;
use App\Models\PaymentMethod;
use App\Models\Site;
use App\Models\User;
use App\Models\WhatsAppMessageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    private $statuses;
    public function __construct()
    {
        $this->statuses = ['New', 'Process', 'Picked Up'];
    }

    public function index(OrdersDataTable $dataTable)
    {
        $sites = Site::all();
        return $dataTable->render('orders.index', compact('sites'));
    }

    public function itemPositions(Request $request)
    {
        $selectedState = $request->get('state', 'ALL');
        $search = trim((string) $request->get('search', ''));

        $states = OrderItem::whereHas('order', function ($query) {
                $query->where('transaction_type', 0);
            })
            ->whereNotNull('state')
            ->where('state', '<>', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state');

        $hasItemsWithoutState = OrderItem::whereHas('order', function ($query) {
                $query->where('transaction_type', 0);
            })
            ->where(function ($query) {
                $query->whereNull('state')->orWhere('state', '');
            })
            ->exists();

        $allowedStates = $states->concat(['BELUM_ADA_STATE', 'ALL']);
        if (!$allowedStates->contains($selectedState)) {
            $selectedState = 'ALL';
        }

        $query = OrderItem::query()
            ->select('order_items.*')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.transaction_type', 0)
            ->whereNull('orders.deleted_at')
            ->with([
                'order:id,number_ticket,created_at',
                'orderItemPhotos:id,order_item_id,thumbnail_url,preview_url',
            ]);

        if ($selectedState === 'BELUM_ADA_STATE') {
            $query->where(function ($query) {
                $query->whereNull('order_items.state')->orWhere('order_items.state', '');
            });
        } elseif ($selectedState !== 'ALL') {
            $query->where('order_items.state', $selectedState);
        }

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('orders.number_ticket', 'like', '%' . $search . '%')
                    ->orWhere('order_items.id', 'like', '%' . $search . '%')
                    ->orWhere('order_items.note', 'like', '%' . $search . '%');
            });
        }

        $items = $query
            ->orderByDesc('orders.created_at')
            ->orderByDesc('order_items.id')
            ->paginate(20)
            ->appends($request->query());

        return view('orders.item-positions', compact(
            'items',
            'states',
            'hasItemsWithoutState',
            'selectedState',
            'search'
        ));
    }

    public function report(OrdersDataTable $dataTable)
    {
        $sites = Site::all();
        $start_date = now('Asia/Jakarta')->startOfDay();    
        $end_date   = now('Asia/Jakarta')->endOfDay();
    
        // isi request date_start dan date_end jika kosong
        request()->merge([
            'date_start' => request('date_start', $start_date->format('Y-m-d H:i:s')),
            'date_end'   => request('date_end', $end_date->format('Y-m-d H:i:s')),
        ]);
    
        return $dataTable->render('orders.index', compact('sites', 'start_date', 'end_date'));
    }

    public function selesaiBesok(OrdersDataTable $dataTable){
        $sites = Site::all();
        $start_date = now('Asia/Jakarta')->startOfDay();    
        $end_date   = now('Asia/Jakarta')->endOfDay();

        $is_ready_tomorrow = request('is_ready_tomorrow', 'False');
        // dd(request('is_ready_tomorrow', 'False'));
        // isi request date_start dan date_end jika kosong
        request()->merge([
            'date_start' => request('date_start', $start_date->format('Y-m-d H:i:s')),
            'date_end'   => request('date_end', $end_date->format('Y-m-d H:i:s')),
            'is_ready_tomorrow' => True
        ]);

        // dd(request());
    
        return $dataTable->render('orders.index', compact('sites', 'start_date', 'end_date', 'is_ready_tomorrow'));
    }

    public function create()
    {
        $config = Config::find(1);
        $customers = Customer::all();
        $statuses = $this->statuses;

        $products = Product::where('type', '=', 1)->get();
        $payment_methods = PaymentMethod::all();
        $payment_merchants = PaymentMerchant::all();
        $sites = Site::all();
        $cities = City::all();
        $users = User::whereHas('roles', fn($q) => 
                    $q->whereIn('name', ['driver'])
                )->get();

        return view('orders.create', compact('statuses', 'customers', 'products', 'payment_methods', 'payment_merchants', 'sites', 'cities', 'config', 'users'));
    }

    public function store(Request $request)
    {
        // if ($request->ajax()) {
        //     return response()->json($request->post());
        // }

        $config = Config::find(1);
        $items = $request->items;
        $item_images = $request->item_images;
        // dump(count($product));
        // dd($items);

        
        $amount_payment = $request->amount_payment;
        $split_amount_payment = $request->split_amount_payment;
        $dp = $request->amount_payment + $request->split_amount_payment;
        $kekurangan = $request->total - $dp;
        $total = $request->total;
        $discount = $request->discount;
        $netto = $total - $discount;
        $customer_id = $request->customer_id;
        $site_id = $request->site_id;
        $note = $request->note;
        $is_delivery = $request->is_delivery;
        $address = $request->address;
        $link_map_address = $request->link_map_address;
        $driver_id = $request->driver_id;
        $estimate_take_item = Carbon::parse($request->estimate_take_item);

        // selalu 1 hari sebelum
        $estimate_service_done = $estimate_take_item->copy()->subDay();

        $vat = calculate_included_vat($netto, $config->vat);

        $order = Order::create([
            'bruto' => $total,
            'discount' => $discount,
            'netto' => $netto,
            'vat' => $vat,
            'total' => $netto - $vat,
            'uang_muka' => $dp,
            'status' => 'DIPROSES',
            'sisa_pembayaran' => $kekurangan,
            'customer_id' => $customer_id,
            'created_by' => auth()->user()->id,
            'transaction_type' => 0,
            'site_id' => $site_id,
            'estimate_service_done' => $estimate_service_done,
            'estimate_take_item' => $estimate_take_item,
            'note' => $note,
            'address'=> $address,
            'is_delivery'=> $is_delivery,
            'link_map_address'=> $link_map_address,
            'driver_id'=> $driver_id

            // 'name' => $random_string,
            // 'payment' => rand(1000, 100000),
            // 'due_date' => now(),
            // 'picked_by' => $request->picked_by,
            // 'picked_at' => date('Y-m-d H:i:s', strtotime($request->picked_at)),
        ]);

        // dd($order);

        $ticket_format = sprintf('%06d', $order->id);
        $code_site = Site::findOrFail($site_id);
        $number_ticket = $code_site->code.'-'. $ticket_format;
        $order->update(['number_ticket' => $number_ticket]);

        for ($i = 0; $i < count($items); $i++) {
            $totalItem = $items[$i]['biaya'];
            $discountItem = $items[$i]['discount_item'];

            if($discountItem > 100) {
                $total_discount_item = (int) $items[$i]['discount_item'];
            } else {
                $total_discount_item = $totalItem * ($discountItem / 100);
            }

            $nettoItem = $totalItem - $total_discount_item;
            $vatItem = calculate_included_vat($nettoItem, $config->vat);

            $order_item = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $items[$i]['type'],
                'bruto' => $totalItem,
                'discount' => $discountItem,
                'netto' => $nettoItem,
                'vat' => $vatItem,
                'total' => $nettoItem - $vatItem,
                'note' => $items[$i]['keterangan'],
                'transaction_type' => 0,
                'state' => 'masuk'
            ]);

            if(isset($items[$i]['gambar'])) {
                $gambar = $items[$i]['gambar'];
                for ($j = 0; $j < count($gambar); $j++) {
                    $randomFilename = Str::uuid()->toString();
                    $base64Image = $gambar[$j];
                    $split = explode(',', substr($base64Image, 5), 2);
                    $mime = $split[0];
                    $mime_split_without_base64 = explode(';', $mime, 2);
                    $mime_split = explode('/', $mime_split_without_base64[0], 2);
                    $extension = $mime_split[1];

                    $today = now(); // atau \Carbon\Carbon::now();
                    $tahun = $today->format('Y');        // 2025
                    $bulan = $today->format('m');        // 05
                    $tanggal = $today->format('d');      // 14
                    
                    $folder = "thumbnails/{$tahun}/{$bulan}/{$tanggal}";
                    $filePathThumbnail = "{$folder}/$randomFilename.$extension";

                    // Extract and decode the image
                    $image = explode('base64,', $base64Image);
                    $image = end($image);
                    $image = str_replace(' ', '+', $image);

                    $decodedImage = base64_decode($image);

                    // Resize image to ensure it is below or equal to 200KB
                    $resizedImage = Image::make($decodedImage)
                        ->resize(800, null, function ($constraint) { // Adjust the width, maintain aspect ratio
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode($extension, 75); // Reduce quality to ensure size is under 200KB

                    // Save resized image to storage
                    Storage::disk('public')->put($filePathThumbnail, $resizedImage);
                    // Storage::disk('public')->put($filePathPreview, $resizedImage);
                    

                    // Save paths to database
                    OrderItemPhoto::create([
                        'order_item_id' => $order_item->id,
                        'thumbnail_url' => $filePathThumbnail,
                        'preview_url' => $filePathThumbnail,
                    ]);
                }
            }
        }

        $now = Carbon::now();
        $threshold = $now->subSeconds(5); // batas waktu 5 detik terakhir
        
        if ($kekurangan == 0) {
            $payment_type = 1; //lunas
        }else{
            $payment_type = 0; //dp
        }
        
        $existing = Payment::where('order_id', $order->id)
            ->where('payment_type', $payment_type)
            ->where('value', (int) $amount_payment)
            ->where('payment_method_id', (int) $request->payment_method)
            ->where('payment_merchant_id', (int) $request->payment_merchant)
            ->where('created_at', '>=', $threshold)
            ->first();
        
        if (!$existing) {
            $create_payment_id = Payment::create([
                'order_id' => $order->id,
                'payment_type' => $payment_type,
                'value' => (int) $amount_payment,
                'payment_method_id' => (int) $request->payment_method,
                'payment_merchant_id' => (int) $request->payment_merchant,
            ]);
        }
        if(!$create_payment_id){
            return response()->json(['message' => 'Error creating order Mohon buat ulang ordernya yaa', 'error' => $e->getMessage()], 500);
        }

        if (($split_amount_payment != 0) && $request->split_payment)
        {
            $existing = Payment::where('order_id', $order->id)
            ->where('payment_type', $payment_type)
            ->where('value', (int) $split_amount_payment)
            ->where('payment_method_id', (int) $request->split_payment_method)
            ->where('payment_merchant_id', (int) $request->split_payment_merchant)
            ->where('created_at', '>=', $threshold)
            ->first();
        
            if (!$existing) {
                $create_split_payment_id = Payment::create([
                    'order_id' => $order->id,
                    'payment_type' => $payment_type,
                    'value' => (int) $split_amount_payment,
                    'payment_method_id' => (int) $request->split_payment_method,
                    'payment_merchant_id' => (int) $request->split_payment_merchant,
                ]);
            }

            if(!$create_split_payment_id){
                return response()->json(['message' => 'Error creating order Mohon buat ulang ordernya yaa', 'error' => $e->getMessage()], 500);
            }
        }

        
        
        $order->update(['payment_id' => $create_payment_id->id]);

        if ($request->ajax()) {
            return response()->json($order);
        }

        return redirect()->route('orders.index')->with('success', 'Sukses! Pesanan ' . $order->name . ' berhasil dibuat!');
    }

    public function show($id)
    {
        $config = Config::find(1);
        $order = Order::where('transaction_type', 0)->with(['orderItems.orderItemPhotos' => function ($query) {
            $query->whereNull('deleted_at');
        }])->findOrFail($id);
        // dd($order);
        $customers = Customer::all();
        $statuses = $this->statuses;
        // dd($statuses);
        $first_payment = Payment::where('order_id', $id)
            ->where('payment_type', 0)
            ->orderBy('id', 'asc')
            ->get();
        // $payments = Payment::where('order_id', $id)
        //     ->where('payment_type', 0)
        //     ->orderBy('id', 'asc')
        //     ->get();
        $payments = Payment::where('order_id', $id)
            // ->where('payment_type', 0)
            ->orderBy('id', 'asc')
            ->get();
        
        $payment1 = $payments->get(0);
        $payment2 = $payments->get(1);
        $second_payment = Payment::where('order_id', $id)->where('payment_type', 1)
        ->orderBy('id', 'desc')
        ->first();
        // dd($payment1,$payment2,$first_payment,$second_payment);
        $payment_methods = PaymentMethod::all();
        $payment_merchants = PaymentMerchant::all();
        $products = Product::all('id', 'name');
        $users_teknisi = User::whereHas('roles', fn($q) => 
                    $q->whereIn('name', ['teknisi'])
                )->get();
        $users_driver = User::whereHas('roles', fn($q) => 
                    $q->whereIn('name', ['driver'])
                )->get();
        $users_qc = User::all();
        $users = User::all();



        $sites = Site::all();

        $bonWhatsappUrl = URL::signedRoute('orders.print.shared', [
            'id' => $order->id,
            'type' => 'customer',
        ]);
        $whatsappMessage = $this->buildWhatsAppBonMessage($order, $bonWhatsappUrl);

        return view('orders.show', compact('order', 'customers', 'statuses', 'payment_methods', 'payment_merchants', 'users_driver',
        'products', 'sites', 'config', 'first_payment', 'second_payment', 'payment1', 'payment2', 'users', 'users_teknisi', 'users_qc',
        'bonWhatsappUrl', 'whatsappMessage'));
    }

    public function sendWhatsAppBon(Order $order)
    {
        $order->loadMissing('customer');
        $phoneNumber = preg_replace('/\D+/', '', (string) optional($order->customer)->phone_number);

        if (!$order->customer || !$phoneNumber) {
            return back()->with('error', 'Nomor WhatsApp pelanggan belum tersedia.');
        }

        if (strlen($phoneNumber) < 8 || strlen($phoneNumber) > 16) {
            return back()->with('error', 'Nomor WhatsApp pelanggan tidak valid.');
        }

        $token = config('services.fonnte.token');
        if (!$token) {
            return back()->with('error', 'Token Fonnte belum dikonfigurasi.');
        }

        $bonUrl = URL::signedRoute('orders.print.shared', [
            'id' => $order->id,
            'type' => 'customer',
        ]);
        $message = $this->buildWhatsAppBonMessage($order, $bonUrl);

        try {
            $response = Http::timeout(20)->get(config('services.fonnte.endpoint'), [
                'token' => $token,
                'target' => $phoneNumber,
                'message' => $message,
                'countryCode' => (string) config('services.fonnte.country_code', '62'),
                'typing' => 'true',
                'preview' => 'false',
            ]);

            $payload = $response->json();
            $isSuccess = $response->successful()
                && (bool) ($payload['status'] ?? $payload['Status'] ?? false);

            if (!$isSuccess) {
                $reason = $payload['reason'] ?? $payload['detail'] ?? 'Fonnte menolak pengiriman pesan.';
                $this->logWhatsAppMessage($order, $phoneNumber, $message, 'failed', $payload);
                Log::warning('Pengiriman bon WhatsApp gagal.', [
                    'order_id' => $order->id,
                    'status_code' => $response->status(),
                    'reason' => $reason,
                ]);

                return back()->with('error', 'Pesan WhatsApp gagal dikirim: ' . $reason);
            }

            $this->logWhatsAppMessage($order, $phoneNumber, $message, 'queued', $payload);
        } catch (\Throwable $exception) {
            $this->logWhatsAppMessage($order, $phoneNumber, $message, 'failed', [
                'reason' => 'connection_error',
            ]);
            Log::error('Koneksi ke Fonnte gagal.', [
                'order_id' => $order->id,
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Tidak dapat terhubung ke layanan WhatsApp. Silakan coba lagi.');
        }

        return back()->with('success', 'Link bon berhasil dikirim ke WhatsApp pelanggan.');
    }

    private function buildWhatsAppBonMessage(Order $order, $bonUrl)
    {
        $order->loadMissing(['customer', 'creator']);
        $template = config('whatsapp.order_message_template');

        return strtr($template, [
            '{nama_pelanggan}' => optional($order->customer)->name ?: 'Pelanggan',
            '{nama_kasir}' => optional($order->creator)->name ?: 'Kasir',
            '{no_bon}' => $order->number_ticket ?: '-',
            '{tanggal_transaksi}' => $order->created_at
                ? $order->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') . ' WIB'
                : '-',
            '{link_bon}' => $bonUrl,
        ]);
    }

    private function logWhatsAppMessage(Order $order, $target, $message, $status, array $payload)
    {
        try {
            $providerIds = $payload['id'] ?? null;

            WhatsAppMessageLog::create([
                'order_id' => $order->id,
                'sent_by' => auth()->id(),
                'target' => $target,
                'message' => $message,
                'status' => $status,
                'provider_message_id' => is_array($providerIds) ? implode(',', $providerIds) : $providerIds,
                'request_id' => $payload['requestid'] ?? null,
                'provider_response' => json_encode($payload),
            ]);
        } catch (\Throwable $exception) {
            Log::error('Gagal menyimpan riwayat pesan WhatsApp.', [
                'order_id' => $order->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function edit($id)
    {
        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
        $customers = Customer::all();
        $statuses = $this->statuses;

        return view('orders.edit', compact('order', 'statuses', 'customers'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // if ($request->ajax()) {
        //     return response()->json($request->post());
        // }

        $config = Config::find(1);
        $items = $request->items;
        $bruto = $request->bruto;
        $discount = $request->discount;
        $status = $request->status;
        $sisa_pembayaran = $request->sisa_pembayaran;
        $customer_id = $request->customer_id;
        $picked_by = $request->picked_by;
        $picked_at = $request->picked_at;
        $site_id = $request->site_id;
        $estimate_service_done = $request->estimate_service_done;
        $estimate_take_item =  $request->estimate_take_item;
        $note =  $request->note;
        $complain =  $request->complain;
        $uang_muka = $request->uang_muka;
        $first_payment_id = $request->first_payment_id;
        $payment_methods_first = $request->payment_method_first;
        $payment_merchants_first = $request->payment_merchant_first;
        $first_split_payment_id = $request->first_split_payment_id;
        $split_payment_methods_first = $request->split_payment_method_first;
        $split_payment_merchants_first = $request->split_payment_merchant_first;
        $nominal1 = $request->nominal1;
        $nominal2 = $request->nominal2;
        $address = $request->address;
        $driver_id = $request->driver_id;
        $is_delivery = $request->is_delivery;
        $link_map_address = $request->link_map_address;


        $nettoItem = $bruto - $discount;
        $vatItem = calculate_included_vat($nettoItem, $config->vat);

        $data = collect([
            'uang_muka' => $uang_muka,
            // 'due_date' => $request->due_date,
            'total' => $nettoItem - $vatItem,
            'vat' => $vatItem,
            'netto' => $nettoItem,
            'discount' => $discount,
            'bruto' => $bruto,
            'status' => $status,
            'sisa_pembayaran' => $sisa_pembayaran,
            'customer_id' => $customer_id,
            'site_id' => $site_id,
            'picked_by' => $picked_by,
            'note' => $note,
            'complain' => $complain,
            'picked_at' => date('Y-m-d H:i:s', strtotime($picked_at)),
            'estimate_service_done' => $estimate_service_done,
            'estimate_take_item' => $estimate_take_item,
            'is_delivery' => $is_delivery,
            'address' => $address,
            'link_map_address' => $link_map_address,
            'driver_id' => $driver_id
        ]);

        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);

        if (!empty($first_payment_id)) {
            Payment::where('id', '=', $first_payment_id)->update([
                'payment_method_id' => $payment_methods_first,
                'payment_merchant_id' => $payment_merchants_first,
                'value' => $nominal1
            ]);
        
            $updated_payment = Payment::find($first_payment_id);
        
            if ($updated_payment) {
                Log::info('Updated Payment Method ID: ' . $updated_payment->payment_method_id);
            } else {
                Log::warning("Payment with ID {$first_payment_id} not found.");
            }
        }

        if (!empty($first_split_payment_id)) {
            Payment::where('id', '=', $first_split_payment_id)->update([
                'payment_method_id' => $split_payment_methods_first,
                'payment_merchant_id' => $split_payment_merchants_first,
                'value' => $nominal2
            ]);
        
            $updated_payment = Payment::find($first_split_payment_id);
        
            if ($updated_payment) {
                Log::info('Updated Payment Method ID: ' . $updated_payment->split_payment_method_id);
            } else {
                Log::warning("Payment with ID {$first_payment_id} not found.");
            }
        }


        if (!is_null($picked_by)) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_type' => 1,
                'value' => (int) ($sisa_pembayaran),
                'payment_method_id' => (int) $request->payment_method,
                'payment_merchant_id' => (int) $request->payment_merchant,
            ]);

            $data->put('payment_id', $payment->id);
        } else {
            $data->forget(['status', 'sisa_pembayaran', 'picked_by', 'picked_at']);
        }

        $order->update($data->all());
        $isPickedUp = strtoupper((string) $order->status) === 'DIAMBIL';

        if ($isPickedUp) {
            $order->orderItems()->update(['state' => 'selesai']);
        }

        for ($i = 0; $i < count($items); $i++) {
            $order_item = OrderItem::findOrFail($items[$i]['id']);

            $totalItem = (int) $items[$i]['bruto'];
            $discountItem = (int) $items[$i]['discount'];
            $teknisi1 = $items[$i]['teknisi1_id'];
            $teknisi2 = $items[$i]['teknisi2_id'];
            $teknisi3 = $items[$i]['teknisi3_id'];
            $qc = $items[$i]['qc_id'];
            $state = $isPickedUp ? 'selesai' : $items[$i]['state'];

            if($discountItem > 100) {
                $total_discount_item = (int) $items[$i]['discount'];
            } else {
                $total_discount_item = (int) ($totalItem * ($discountItem / 100));
            }

            $nettoItem = $totalItem - $total_discount_item;
            $vatItem = calculate_included_vat($nettoItem, $config->vat);

            $order_item->update([
                // 'order_id' => (int) $id,
                'product_id' => (int) $items[$i]['type'],
                'bruto' => $totalItem,
                'discount' => $discountItem,
                'netto' => $nettoItem,
                'vat' => $vatItem,
                'total' => $nettoItem - $vatItem,
                'note' => $items[$i]['keterangan'],
                'teknisi1_id' => $teknisi1,
                'teknisi2_id' => $teknisi2,
                'teknisi3_id' => $teknisi3,
                'qc_id' => $qc,
                'state' => $state
            ]);

            $teknisiIds = collect([
                $teknisi1,
                $teknisi2,
                $teknisi3,
            ])
            ->filter()
            ->unique()
            ->values()
            ->toArray();

            $order_item->teknisis()->sync($teknisiIds);

            if(isset($items[$i]['gambar'])) {
                for ($k = 0; $k < count($items[$i]['gambar']); $k++) {
                    $gambar = $items[$i]['gambar'];
                    if (filter_var($gambar[$k], FILTER_VALIDATE_URL) !== false) {
                        continue;
                    }
                    $base64Image = $gambar[$k];
                    $randomFilename = Str::uuid()->toString();
                    $split = explode(',', substr($base64Image, 5), 2);
                    $mime = $split[0];
                    $mime_split_without_base64 = explode(';', $mime, 2);
                    $mime_split = explode('/', $mime_split_without_base64[0], 2);
                    $extension = $mime_split[1];

                    // $filePathPreview = "previews/$randomFilename.$extension";
                    $filePathThumbnail = "thumbnails/$randomFilename.$extension";

                    $image = explode('base64,',$base64Image);
                    $image = end($image);
                    $image = str_replace(' ', '+', $image);

                    Storage::disk('public')->put($filePathThumbnail,base64_decode($image));
                    // Storage::disk('public')->put($filePathPreview,base64_decode($image));

                    // Storage::disk('public')->put('public/'.$filePathThumbnail, $base64Image);
                    // Storage::disk('public')->put('public/'.$filePathPreview, $base64Image);

                    OrderItemPhoto::create([
                        'order_item_id' => $order_item->id,
                        'thumbnail_url' => $filePathThumbnail,
                        'preview_url' => $filePathThumbnail,
                    ]);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json($order);
        }

        return redirect()->route('orders.index')->with('success', 'Sukses Pesanan ' . $order->name . ' berhasil diedit!');
    }

    // public function update(Request $request, $id)
    // {
    //     // $validated = $request->validate([
    //     //     'items' => 'required|array',
    //     //     'bruto' => 'required|numeric',
    //     //     'discount' => 'required|numeric',
    //     //     'status' => 'required|string',
    //     //     'sisa_pembayaran' => 'nullable|numeric',
    //     //     'customer_id' => 'required|integer|exists:customers,id',
    //     //     'picked_by' => 'nullable|string',
    //     //     'picked_at' => 'nullable|date',
    //     //     'site_id' => 'required|integer|exists:sites,id',
    //     //     'estimate_service_done' => 'required|date',
    //     //     'estimate_take_item' => 'required|date',
    //     //     'payment_method' => 'nullable|integer|exists:payment_methods,id',
    //     //     'payment_merchant' => 'nullable|integer|exists:payment_merchants,id',
    //     // ]);
    //
    //     $orderData = $this->prepareOrderData($request);
    //     $items = $request->items;
    //
    //     Log::channel('orders')->info('Order created', [
    //         'date' => now()->toDateTimeString(),
    //         'type' => 'info',
    //         'message' => 'Process order.',
    //         'context' => $orderData,
    //         'type_order' => 'penjualan',
    //     ]);
    //
    //     DB::beginTransaction();
    //
    //     try {
    //         $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
    //
    //         if (!is_null($request->picked_by)) {
    //             $payment = $this->createPayment($order->id, $orderData['netto'], $orderData['vat'], $request->payment_type, $request->payment_method, $request->payment_merchant);
    //             $orderData['payment_id'] = $payment->id;
    //         } else {
    //             unset($orderData['status'], $orderData['sisa_pembayaran'], $orderData['picked_by'], $orderData['picked_at']);
    //         }
    //
    //         $order->update($orderData);
    //
    //         $this->updateOrderItems($items);
    //
    //         DB::commit();
    //
    //         Log::channel('orders')->info('Order updated successfully.', [
    //             'date' => now()->toDateTimeString(),
    //             'type' => 'success',
    //             'message' => 'Order updated successfully.',
    //             'context' => $order->toArray(),
    //             'type_order' => 'penjualan',
    //         ]);
    //
    //         if ($request->ajax()) {
    //             return response()->json($order);
    //         }
    //
    //         return redirect()->route('orders.index')->with('success', 'Sukses Pesanan berhasil diedit!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //
    //         Log::channel('orders')->error('Error updating order', [
    //             'date' => now()->toDateTimeString(),
    //             'type' => 'error',
    //             'message' => 'Error updating order.',
    //             'context' => [
    //                 'error' => $e->getMessage(),
    //                 'order_data' => $orderData,
    //                 'items' => $items
    //             ],
    //             'type_order' => 'penjualan',
    //         ]);
    //
    //         return response()->json(['message' => 'Error updating order', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function complain(Request $request, $id)
    {
        $request->validate([
            'complain' => 'required|min:3'
        ]);

        $order = Order::with('customer')->findOrFail($id);

        $order->update([
            'complain' => $request->complain,
        ]);

        return redirect()->back()->with(
            'success',
            'Complain untuk pesanan ' . $order->number_ticket . 
            ' (' . optional($order->customer)->name . ') berhasil disimpan!'
        );
    }


    public function complainList(Request $request)
    {
        // dd($data, $customers);
        $query = Order::with('customer')
            ->whereNotNull('complain');

        // 🔍 filter by customer
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        // 🔍 optional: filter search nama / no hp
        if ($request->search) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            });
        }

        // if ($request->start_date && $request->end_date) {
        //     $query->whereBetween('created_at', [...]);
        // }

        $data = $query->latest()->paginate(10);

        // dropdown customer
        $customers = \App\Models\Customer::pluck('name', 'id');

        return view('orders.index_complain', compact('data', 'customers'));
    }

    public function deliveryList(Request $request)
    {
        $query = Order::with(['customer', 'site', 'driver'])
            ->where('transaction_type', 0)
            ->where('is_delivery', true)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'DIAMBIL');
            });

        if ($request->search) {
            $query->where(function ($query) use ($request) {
                $query->where('number_ticket', 'like', '%' . $request->search . '%')
                    ->orWhere('address', 'like', '%' . $request->search . '%')
                    ->orWhereHas('customer', function ($customerQuery) use ($request) {
                        $customerQuery->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('phone_number', 'like', '%' . $request->search . '%');
                    });

                $searchNumber = ltrim(preg_replace('/\D/', '', $request->search), '0');
                if ($searchNumber !== '') {
                    $query->orWhere('id', (int) $searchNumber);
                }
            });
        }

        if ($request->site_id) {
            $query->where('site_id', $request->site_id);
        }

        $data = $query->latest()->paginate(15);
        $sites = Site::orderBy('name')->get();
        $listType = 'pending';

        return view('orders.index_delivery', compact('data', 'sites', 'listType'));
    }

    public function deliveryListSudahDiambil(Request $request)
    {
        $query = Order::with(['customer', 'site', 'driver'])
            ->where('transaction_type', 0)
            ->where('is_delivery', true)
            ->where('status', 'DIAMBIL');

        if ($request->search) {
            $query->where(function ($query) use ($request) {
                $query->where('number_ticket', 'like', '%' . $request->search . '%')
                    ->orWhere('address', 'like', '%' . $request->search . '%')
                    ->orWhereHas('customer', function ($customerQuery) use ($request) {
                        $customerQuery->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('phone_number', 'like', '%' . $request->search . '%');
                    });

                $searchNumber = ltrim(preg_replace('/\D/', '', $request->search), '0');
                if ($searchNumber !== '') {
                    $query->orWhere('id', (int) $searchNumber);
                }
            });
        }

        if ($request->site_id) {
            $query->where('site_id', $request->site_id);
        }

        $data = $query->latest()->paginate(15);
        $sites = Site::orderBy('name')->get();
        $listType = 'taken';

        return view('orders.index_delivery', compact('data', 'sites', 'listType'));
    }

    public function destroy($id)
    {
        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Pesanan reparasi ' . $order->customer->name . ' berhasil dihapus!');
    }


    public function getPaymentMerchants(Request $request) {
        $paymentMethod = $request->query('payment_method');
        $paymentMerchants = PaymentMerchant::where('payment_method_id', $paymentMethod)->get();
        return response()->json($paymentMerchants);
    }

    public function orderPrint($id)
    {
        $config = Config::find(1);
        $order = Order::with('orderItems.orderItemPhotos', 'creator')->findOrFail($id);
        // dd($order);
        $customers = Customer::all();
        $statuses = $this->statuses;
        // dd($statuses);

        $payment_methods = PaymentMethod::all();
        $payment_merchants = PaymentMerchant::all();
        $products = Product::all('id', 'name');

        $sites = Site::all();

        return view('orders.print', compact('order', 'customers', 'statuses', 'payment_methods', 'payment_merchants', 'products', 'sites', 'config'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyItemPhoto(Request $request): JsonResponse
    {
        $orderItemPhoto = OrderItemPhoto::where('thumbnail_url', '=', $request->thumbnail_url)->first();
        $orderItemPhoto->delete();
        return response()->json($orderItemPhoto);
    }

    public function updateItemState(Request $request, $id): JsonResponse
    {
        $state = $request->input('state');
        $allowedStates = [null, '', 'masuk', 'proses', 'selesai', 'gudang A', 'gudang B', 'gudang C', 'cancel'];

        if (!in_array($state, $allowedStates, true)) {
            return response()->json(['message' => 'State tidak valid.'], 422);
        }

        $orderItem = OrderItem::with('order:id,status')->findOrFail($id);
        $isPickedUp = strtoupper((string) optional($orderItem->order)->status) === 'DIAMBIL';
        $orderItem->update([
            'state' => $isPickedUp ? 'selesai' : ($state === '' ? null : $state),
        ]);

        return response()->json($orderItem->fresh());
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function setStatus(Request $request, $id): JsonResponse
    {
        if (strtoupper((string) $request->status) === 'LUNAS') {
            return response()->json(['message' => 'Gunakan proses pelunasan untuk mengubah status LUNAS.'], 422);
        }

        $order = DB::transaction(function () use ($request, $id) {
            $order = Order::findOrFail($id);
            $order->status = $request->status;
            $order->save();

            if (strtoupper((string) $order->status) === 'DIAMBIL') {
                $order->orderItems()->update(['state' => 'selesai']);
            }

            return $order;
        });

        return response()->json($order);
    }

    public function setLunas(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|integer|exists:payment_methods,id',
            'payment_merchant' => 'nullable|integer|exists:payment_merchants,id',
            'nominal_pelunasan' => 'required|numeric|min:0',
        ]);

        $order = Order::where('transaction_type', 0)->findOrFail($id);
        $nominalPelunasan = (int) $validated['nominal_pelunasan'];
        $sisaPembayaran = (int) $order->sisa_pembayaran;

        if ($order->status === 'LUNAS') {
            return response()->json(['message' => 'Reparasi sudah lunas.'], 422);
        }

        if ($nominalPelunasan !== $sisaPembayaran) {
            return response()->json([
                'message' => 'Nominal pelunasan harus sama dengan sisa pembayaran.',
                'sisa_pembayaran' => $sisaPembayaran,
            ], 422);
        }

        DB::beginTransaction();

        try {
            $payment = null;

            if ($nominalPelunasan > 0) {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'payment_type' => 1,
                    'value' => $nominalPelunasan,
                    'payment_method_id' => (int) $validated['payment_method'],
                    'payment_merchant_id' => isset($validated['payment_merchant'])
                        ? (int) $validated['payment_merchant']
                        : null,
                ]);
            }

            $order->update([
                'status' => 'LUNAS',
                'sisa_pembayaran' => 0,
                'uang_muka' => (int) $order->uang_muka + $nominalPelunasan,
                'payment_id' => $payment ? $payment->id : $order->payment_id,
            ]);

            DB::commit();

            return response()->json($order->fresh());
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error pelunasan order', [
                'order_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Pelunasan tidak berhasil disimpan.'], 500);
        }
    }

    private function prepareOrderData($request)
    {
        $config = Config::find(1);
        $netto = $request->total - $request->discount;
        $vat = calculate_included_vat($netto, $config->vat);

        return [
            'bruto' => $request->total,
            'discount' => $request->discount,
            'netto' => $netto,
            'vat' => $vat,
            'total' => $netto - $vat,
            'uang_muka' => $request->dp,
            'status' => 'DIPROSES',
            'sisa_pembayaran' => $request->kekurangan,
            'customer_id' => $request->customer_id,
            'transaction_type' => 0,
            'site_id' => $request->site_id,
            'estimate_service_done' => $request->estimate_service_done,
            'estimate_take_item' => $request->estimate_take_item,
        ];
    }

    private function createOrder($data)
    {
        return Order::create($data);
    }

    private function createOrderItems($orderId, $items)
    {
        $config = Config::find(1);
        foreach ($items as $index => $item) {
            $image = $item['gambar'];
            $totalItem = $item['biaya'];
            $discountItem = $item['discount_item'];
            $totalDiscountItem = $discountItem > 100 ? $discountItem : $totalItem * ($discountItem / 100);
            $nettoItem = $totalItem - $totalDiscountItem;
            $vatItem = calculate_included_vat($nettoItem, $config->vat);

            $orderItem = OrderItem::create([
                'order_id' => $orderId,
                'product_id' => $item['type'],
                'bruto' => $totalItem,
                'discount' => $discountItem,
                'netto' => $nettoItem,
                'vat' => $vatItem,
                'total' => $nettoItem - $vatItem,
                'note' => $item['keterangan'],
                'transaction_type' => 0,
            ]);

            if (isset($image)) {
                $this->saveOrderItemImages($orderItem->id, $image);
            }
        }
    }

    private function updateOrderItems($items)
    {
        $config = Config::find(1);
        foreach ($items as $item) {
            $totalItem = (int) $item['bruto'];
            $discountItem = (int) $item['discount'];

            $totalDiscountItem = $discountItem > 100 ? $discountItem : $totalItem * ($discountItem / 100);
            $nettoItem = $totalItem - $totalDiscountItem;
            $vatItem = calculate_included_vat($nettoItem, $config->vat);

            $orderItem = OrderItem::findOrFail($item['id']);
            $orderItem->update([
                'product_id' => $item['type'],
                'note' => $item['keterangan'],
                'transaction_type' => 0,
                'bruto' => $item['bruto'],
                'discount' => $item['discount'],
                'netto' => $nettoItem,
                'vat' => $vatItem,
                'total' => $nettoItem - $vatItem,
            ]);

            if (isset($item['gambar'])) {
                $this->updateOrderItemImages($orderItem->id, $item['gambar']);
            }
        }
    }

    private function saveOrderItemImages($orderItemId, $images)
    {
        foreach ($images as $image) {
            $randomFilename = Str::uuid()->toString();
            $split = explode(',', substr($image, 5), 2);
            $mime = $split[0];
            $mime_split_without_base64 = explode(';', $mime, 2);
            $mime_split = explode('/', $mime_split_without_base64[0], 2);
            $extension = $mime_split[1];

            // $filePathPreview = "previews/$randomFilename.$extension";
            $filePathThumbnail = "thumbnails/$randomFilename.$extension";

            $imageContent = base64_decode(explode('base64,', $image)[1]);

            Storage::disk('public')->put($filePathThumbnail, $imageContent);
            // Storage::disk('public')->put($filePathPreview, $imageContent);

            OrderItemPhoto::create([
                'order_item_id' => $orderItemId,
                'thumbnail_url' => $filePathThumbnail,
                'preview_url' => $filePathThumbnail,
            ]);
        }
    }

    private function updateOrderItemImages($orderItemId, $images)
    {
        foreach ($images as $image) {
            if (filter_var($image, FILTER_VALIDATE_URL) !== false) {
                continue;
            }

            $randomFilename = Str::uuid()->toString();
            $split = explode(',', substr($image, 5), 2);
            $mime = $split[0];
            $mime_split_without_base64 = explode(';', $mime, 2);
            $mime_split = explode('/', $mime_split_without_base64[0], 2);
            $extension = $mime_split[1];

            // $filePathPreview = "previews/$randomFilename.$extension";
            $filePathThumbnail = "thumbnails/$randomFilename.$extension";

            $imageContent = base64_decode(explode('base64,', $image)[1]);

            Storage::disk('public')->put($filePathThumbnail, $imageContent);
            // Storage::disk('public')->put($filePathPreview, $imageContent);

            OrderItemPhoto::create([
                'order_item_id' => $orderItemId,
                'thumbnail_url' => $filePathThumbnail,
                'preview_url' => $filePathThumbnail,
            ]);
        }
    }

    private function createPayment($orderId, $netto, $vat, $paymentType, $paymentMethod, $paymentMerchant)
    {
        return Payment::create([
            'order_id' => $orderId,
            'payment_type' => $paymentType,
            'value' => $netto,
            'payment_method_id' => $paymentMethod,
            'payment_merchant_id' => $paymentMerchant,
        ]);
    }

    private function updateOrderNumber($order, $siteId)
    {
        $ticketFormat = sprintf('%06d', $order->id);
        $site = Site::findOrFail($siteId);
        $numberTicket = $site->code . '-' . $ticketFormat;
        $order->update(['number_ticket' => $numberTicket]);
    }

    public function getComplainId($orderId)
    {
        $order = Order::findOrFail($orderId);

        return response()->json([
            'complain' => $order->complain ?? ''
        ]);

    }
}
