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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    // public function index()
    // {
    //     // $orders = Order::with('orderItems.orderItemPhotos')->paginate(10);
    //
    //     if (request()->ajax()) {
    //         $data = Order::with('orderItems.orderItemPhotos')->select('*');
    //         return Datatables::of($data)
    //             ->addIndexColumn()
    //             ->addColumn('name', function($row) {
    //                 return $row->customer->name;
    //             })->addColumn('total', function($row) {
    //                 return 'Rp. '.number_format($row->total, 2, ",", ".");
    //             })
    //             ->addColumn('created_at', function($row) {
    //                 return Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->toDateTimeString();
    //             })->addColumn('action', function($row) {
    //                 $btn = '<a class="btn btn-default btn-xs" href="'.route('orders.show', $row->id).'" title="Detail '.$row->name.'"><i class="fa fa-eye"></i></a>';
    //                 $btn .= '<form onsubmit="return confirm(\'Apakah Anda benar-benar ingin MENGHAPUS?\');" action="'.route('orders.destroy', $row->id).'" method="post" style="display: inline-block">';
    //                 $btn .= csrf_field();
    //                 $btn .= method_field('DELETE');
    //                 $btn .= '<button class="btn btn-danger btn-xs" type="submit" title="Delete '.$row->name.'" data-toggle="modal" data-target="#modal-delete-'.$row->id.'"><i class="fa fa-trash"></i></button>';
    //                 $btn .= '</form>';
    //                 return $btn;
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //     }
    //
    //     return view('orders.index');
    // }

    public function create()
    {
        $customers = Customer::all();
        $statuses = $this->statuses;

        $products = Product::where('type', '=', 1)->get();
        $payment_methods = PaymentMethod::all();
        $payment_merchants = PaymentMerchant::all();
        $sites = Site::all();
        $cities = City::all();

        return view('orders.create', compact('statuses', 'customers', 'products', 'payment_methods', 'payment_merchants', 'sites', 'cities'));
    }

    // public function store(Request $request)
    // {
    //     // if ($request->ajax()) {
    //     //     return response()->json($request->post());
    //     // }
    //
    //     $items = $request->items;
    //     $item_images = $request->item_images;
    //     // dump(count($product));
    //     // dd($request->post());
    //
    //     $dp = $request->dp;
    //     $kekurangan = $request->kekurangan;
    //     $total = $request->total;
    //     $discount = $request->discount;
    //     $netto = $total - $discount;
    //     $customer_id = $request->customer_id;
    //     $site_id = $request->site_id;
    //     $estimate_service_done = $request->estimate_service_done;
    //     $estimate_take_item =  $request->estimate_take_item;
    //
    //     $vat = calculate_included_vat($netto, 11);
    //
    //     $order = Order::create([
    //         'bruto' => $total,
    //         'discount' => $discount,
    //         'netto' => $netto,
    //         'vat' => $vat,
    //         'total' => $netto - $vat,
    //         'uang_muka' => $dp,
    //         'status' => 'DIPROSES',
    //         'sisa_pembayaran' => $kekurangan,
    //         'customer_id' => $customer_id,
    //         'transaction_type' => 0,
    //         'site_id' => $site_id,
    //         'estimate_service_done' => $estimate_service_done,
    //         'estimate_take_item' => $estimate_take_item,
    //
    //         // 'name' => $random_string,
    //         // 'payment' => rand(1000, 100000),
    //         // 'due_date' => now(),
    //         // 'picked_by' => $request->picked_by,
    //         // 'picked_at' => date('Y-m-d H:i:s', strtotime($request->picked_at)),
    //     ]);
    //
    //     $ticket_format = sprintf('%06d', $order->id);
    //     $code_site = Site::findOrFail($site_id);
    //     $number_ticket = $code_site->code.'-'. $ticket_format;
    //     $order->update(['number_ticket' => $number_ticket]);
    //
    //     for ($i = 0; $i < count($items); $i++) {
    //         $gambar = $items[$i]['gambar'];
    //         $totalItem = $items[$i]['biaya'];
    //         $discountItem = $items[$i]['discount_item'];
    //
    //         if($discountItem > 100) {
    //             $total_discount_item = (int) $items[$i]['discount_item'];
    //         } else {
    //             $total_discount_item = $totalItem * ($discountItem / 100);
    //         }
    //
    //         $nettoItem = $totalItem - $total_discount_item;
    //         $vatItem = calculate_included_vat($nettoItem, 11);
    //
    //         $order_item = OrderItem::create([
    //             'order_id' => $order->id,
    //             'product_id' => $items[$i]['type'],
    //             'bruto' => $totalItem,
    //             'discount' => $discountItem,
    //             'netto' => $nettoItem,
    //             'vat' => $vatItem,
    //             'total' => $nettoItem - $vatItem,
    //             'note' => $items[$i]['keterangan'],
    //             'transaction_type' => 0,
    //         ]);
    //
    //         for ($j = 0; $j < count($gambar); $j++) {
    //             $randomFilename = Str::uuid()->toString();
    //             $base64Image = $gambar[$j];
    //             $split = explode(',', substr($base64Image, 5), 2);
    //             $mime = $split[0];
    //             $mime_split_without_base64 = explode(';', $mime, 2);
    //             $mime_split = explode('/', $mime_split_without_base64[0], 2);
    //             $extension = $mime_split[1];
    //
    //             $filePathPreview = "previews/$randomFilename.$extension";
    //             $filePathThumbnail = "thumbnails/$randomFilename.$extension";
    //
    //             $image = explode('base64,',$base64Image);
    //             $image = end($image);
    //             $image = str_replace(' ', '+', $image);
    //
    //             Storage::disk('public')->put($filePathThumbnail,base64_decode($image));
    //             Storage::disk('public')->put($filePathPreview,base64_decode($image));
    //
    //             // Storage::disk('public')->put('public/'.$filePathThumbnail, $base64Image);
    //             // Storage::disk('public')->put('public/'.$filePathPreview, $base64Image);
    //
    //             OrderItemPhoto::create([
    //                 'order_item_id' => $order_item->id,
    //                 'thumbnail_url' => $filePathThumbnail,
    //                 'preview_url' => $filePathPreview,
    //             ]);
    //         }
    //     }
    //
    //     Payment::create([
    //         'order_id' => $order->id,
    //         'payment_type' => $request->payment_type,
    //         'value' => (int) $netto - $vat,
    //         'payment_method_id' => (int) $request->payment_method,
    //         'payment_merchant_id' => (int) $request->payment_merchant,
    //     ]);
    //
    //     if ($request->ajax()) {
    //         return response()->json($order);
    //     }
    //
    //     return redirect()->route('orders.index')->with('success', 'Sukses! Pesanan ' . $order->name . ' berhasil dibuat!');
    // }

    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'items' => 'required|array',
        //     'dp' => 'required|numeric',
        //     'kekurangan' => 'required|numeric',
        //     'total' => 'required|numeric',
        //     'discount' => 'required|numeric',
        //     'customer_id' => 'required|integer|exists:customers,id',
        //     'site_id' => 'required|integer|exists:sites,id',
        //     'estimate_service_done' => 'required|date',
        //     'estimate_take_item' => 'required|date',
        //     'payment_type' => 'required|integer',
        //     'payment_method' => 'required|integer|exists:payment_methods,id',
        //     'payment_merchant' => 'required|integer|exists:payment_merchants,id',
        // ]);

        $orderData = $this->prepareOrderData($request);
        $items = $request->items;

        Log::channel('orders')->info('Order created', [
            'date' => now()->toDateTimeString(),
            'type' => 'info',
            'message' => 'Process order.',
            'context' => $orderData,
            'type_order' => 'reparasi',
        ]);

        DB::beginTransaction();

        try {
            $order = $this->createOrder($orderData);
            $this->createOrderItems($order->id, $items);
            $this->createPayment($order->id, $orderData['netto'], $orderData['vat'], $request->payment_type, $request->payment_method, $request->payment_merchant);

            $this->updateOrderNumber($order, $orderData['site_id']);

            DB::commit();

            Log::channel('orders')->info('Order created successfully.', [
                'date' => now()->toDateTimeString(),
                'type' => 'success',
                'message' => 'Order created successfully.',
                'context' => $order->toArray(),
                'type_order' => 'reparasi',
            ]);

            if ($request->ajax()) {
                return response()->json($order);
            }

            return redirect()->route('orders.index')->with('success', 'Sukses! Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('orders')->error('Error creating order', [
                'date' => now()->toDateTimeString(),
                'type' => 'error',
                'message' => 'Error creating order.',
                'context' => [
                    'error' => $e->getMessage(),
                    'order_data' => $orderData,
                    'items' => $items
                ],
                'type_order' => 'reparasi',
            ]);

            return response()->json(['message' => 'Error creating order', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $order = Order::where('transaction_type', 0)->with(['orderItems.orderItemPhotos' => function ($query) {
            $query->whereNull('deleted_at');
        }])->findOrFail($id);
        // dd($order);
        $customers = Customer::all();
        $statuses = $this->statuses;
        // dd($statuses);

        $payment_methods = PaymentMethod::all();
        $payment_merchants = PaymentMerchant::all();
        $products = Product::all('id', 'name');

        $sites = Site::all();

        return view('orders.show', compact('order', 'customers', 'statuses', 'payment_methods', 'payment_merchants', 'products', 'sites'));
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
    // public function update(Request $request, $id)
    // {
    //     // if ($request->ajax()) {
    //     //     return response()->json($request->post());
    //     // }
    //
    //     $items = $request->items;
    //     $bruto = $request->bruto;
    //     $discount = $request->discount;
    //     $status = $request->status;
    //     $sisa_pembayaran = $request->sisa_pembayaran;
    //     $customer_id = $request->customer_id;
    //     $picked_by = $request->picked_by;
    //     $picked_at = $request->picked_at;
    //     $site_id = $request->site_id;
    //     $estimate_service_done = $request->estimate_service_done;
    //     $estimate_take_item =  $request->estimate_take_item;
    //
    //     $nettoItem = $bruto - $discount;
    //     $vatItem = calculate_included_vat($nettoItem, 11);
    //
    //     $data = collect([
    //         // 'uang_muka' => $request->uang_muka,
    //         // 'due_date' => $request->due_date,
    //         'total' => $nettoItem - $vatItem,
    //         'vat' => $vatItem,
    //         'netto' => $nettoItem,
    //         'discount' => $discount,
    //         'bruto' => $bruto,
    //         'status' => $status,
    //         'sisa_pembayaran' => $sisa_pembayaran,
    //         'customer_id' => $customer_id,
    //         'site_id' => $site_id,
    //         'picked_by' => $picked_by,
    //         'picked_at' => date('Y-m-d H:i:s', strtotime($picked_at)),
    //         'estimate_service_done' => $estimate_service_done,
    //         'estimate_take_item' => $estimate_take_item,
    //     ]);
    //
    //     $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
    //
    //     if (!is_null($picked_by)) {
    //         $payment = Payment::create([
    //             'order_id' => $order->id,
    //             'payment_type' => 1,
    //             'value' => (int) ($nettoItem - $vatItem),
    //             'payment_method_id' => (int) $request->payment_method,
    //             'payment_merchant_id' => (int) $request->payment_merchant,
    //         ]);
    //
    //         $data->put('payment_id', $payment->id);
    //     } else {
    //         $data->forget(['status', 'sisa_pembayaran', 'picked_by', 'picked_at']);
    //     }
    //
    //     $order->update($data->all());
    //
    //     for ($i = 0; $i < count($items); $i++) {
    //         $order_item = OrderItem::findOrFail($items[$i]['id']);
    //
    //         $totalItem = (int) $items[$i]['bruto'];
    //         $discountItem = (int) $items[$i]['discount'];
    //
    //         if($discountItem > 100) {
    //             $total_discount_item = (int) $items[$i]['discount'];
    //         } else {
    //             $total_discount_item = (int) ($totalItem * ($discountItem / 100));
    //         }
    //
    //         $nettoItem = $totalItem - $total_discount_item;
    //         $vatItem = calculate_included_vat($nettoItem, 11);
    //
    //         $order_item->update([
    //             // 'order_id' => (int) $id,
    //             'product_id' => (int) $items[$i]['type'],
    //             'bruto' => $totalItem,
    //             'discount' => $discountItem,
    //             'netto' => $nettoItem,
    //             'vat' => $vatItem,
    //             'total' => $nettoItem - $vatItem,
    //             'note' => $items[$i]['keterangan'],
    //         ]);
    //
    //         for ($k = 0; $k < count($items[$i]['gambar']); $k++) {
    //             $gambar = $items[$i]['gambar'];
    //             if (filter_var($gambar[$k], FILTER_VALIDATE_URL) !== false) {
    //                 continue;
    //             }
    //             $base64Image = $gambar[$k];
    //             $randomFilename = Str::uuid()->toString();
    //             $split = explode(',', substr($base64Image, 5), 2);
    //             $mime = $split[0];
    //             $mime_split_without_base64 = explode(';', $mime, 2);
    //             $mime_split = explode('/', $mime_split_without_base64[0], 2);
    //             $extension = $mime_split[1];
    //
    //             $filePathPreview = "previews/$randomFilename.$extension";
    //             $filePathThumbnail = "thumbnails/$randomFilename.$extension";
    //
    //             $image = explode('base64,',$base64Image);
    //             $image = end($image);
    //             $image = str_replace(' ', '+', $image);
    //
    //             Storage::disk('public')->put($filePathThumbnail,base64_decode($image));
    //             Storage::disk('public')->put($filePathPreview,base64_decode($image));
    //
    //             // Storage::disk('public')->put('public/'.$filePathThumbnail, $base64Image);
    //             // Storage::disk('public')->put('public/'.$filePathPreview, $base64Image);
    //
    //             OrderItemPhoto::create([
    //                 'order_item_id' => $order_item->id,
    //                 'thumbnail_url' => $filePathThumbnail,
    //                 'preview_url' => $filePathPreview,
    //             ]);
    //         }
    //     }
    //
    //     if ($request->ajax()) {
    //         return response()->json($order);
    //     }
    //
    //     return redirect()->route('orders.index')->with('success', 'Sukses Pesanan ' . $order->name . ' berhasil diedit!');
    // }

    public function update(Request $request, $id)
    {
        // $validated = $request->validate([
        //     'items' => 'required|array',
        //     'bruto' => 'required|numeric',
        //     'discount' => 'required|numeric',
        //     'status' => 'required|string',
        //     'sisa_pembayaran' => 'nullable|numeric',
        //     'customer_id' => 'required|integer|exists:customers,id',
        //     'picked_by' => 'nullable|string',
        //     'picked_at' => 'nullable|date',
        //     'site_id' => 'required|integer|exists:sites,id',
        //     'estimate_service_done' => 'required|date',
        //     'estimate_take_item' => 'required|date',
        //     'payment_method' => 'nullable|integer|exists:payment_methods,id',
        //     'payment_merchant' => 'nullable|integer|exists:payment_merchants,id',
        // ]);

        $orderData = $this->prepareOrderData($request);
        $items = $request->items;

        Log::channel('orders')->info('Order created', [
            'date' => now()->toDateTimeString(),
            'type' => 'info',
            'message' => 'Process order.',
            'context' => $orderData,
            'type_order' => 'penjualan',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);

            if (!is_null($request->picked_by)) {
                $payment = $this->createPayment($order->id, $orderData['netto'], $orderData['vat'], $request->payment_type, $request->payment_method, $request->payment_merchant);
                $orderData['payment_id'] = $payment->id;
            } else {
                unset($orderData['status'], $orderData['sisa_pembayaran'], $orderData['picked_by'], $orderData['picked_at']);
            }

            $order->update($orderData);

            $this->updateOrderItems($items);

            DB::commit();

            Log::channel('orders')->info('Order updated successfully.', [
                'date' => now()->toDateTimeString(),
                'type' => 'success',
                'message' => 'Order updated successfully.',
                'context' => $order->toArray(),
                'type_order' => 'penjualan',
            ]);

            if ($request->ajax()) {
                return response()->json($order);
            }

            return redirect()->route('orders.index')->with('success', 'Sukses Pesanan berhasil diedit!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('orders')->error('Error updating order', [
                'date' => now()->toDateTimeString(),
                'type' => 'error',
                'message' => 'Error updating order.',
                'context' => [
                    'error' => $e->getMessage(),
                    'order_data' => $orderData,
                    'items' => $items
                ],
                'type_order' => 'penjualan',
            ]);

            return response()->json(['message' => 'Error updating order', 'error' => $e->getMessage()], 500);
        }
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
        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
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

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function setStatus(Request $request, $id): JsonResponse
    {
        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();
        return response()->json($order);
    }

    private function prepareOrderData($request)
    {
        $netto = $request->total - $request->discount;
        $vat = calculate_included_vat($netto, 11);

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
        foreach ($items as $index => $item) {
            $image = $item['gambar'];
            $totalItem = $item['biaya'];
            $discountItem = $item['discount_item'];
            $totalDiscountItem = $discountItem > 100 ? $discountItem : $totalItem * ($discountItem / 100);
            $nettoItem = $totalItem - $totalDiscountItem;
            $vatItem = calculate_included_vat($nettoItem, 11);

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
        foreach ($items as $item) {
            $totalItem = (int) $item['bruto'];
            $discountItem = (int) $item['discount'];

            $totalDiscountItem = $discountItem > 100 ? $discountItem : $totalItem * ($discountItem / 100);
            $nettoItem = $totalItem - $totalDiscountItem;
            $vatItem = calculate_included_vat($nettoItem, 11);

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

            $filePathPreview = "previews/$randomFilename.$extension";
            $filePathThumbnail = "thumbnails/$randomFilename.$extension";

            $imageContent = base64_decode(explode('base64,', $image)[1]);

            Storage::disk('public')->put($filePathThumbnail, $imageContent);
            Storage::disk('public')->put($filePathPreview, $imageContent);

            OrderItemPhoto::create([
                'order_item_id' => $orderItemId,
                'thumbnail_url' => $filePathThumbnail,
                'preview_url' => $filePathPreview,
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

            $filePathPreview = "previews/$randomFilename.$extension";
            $filePathThumbnail = "thumbnails/$randomFilename.$extension";

            $imageContent = base64_decode(explode('base64,', $image)[1]);

            Storage::disk('public')->put($filePathThumbnail, $imageContent);
            Storage::disk('public')->put($filePathPreview, $imageContent);

            OrderItemPhoto::create([
                'order_item_id' => $orderItemId,
                'thumbnail_url' => $filePathThumbnail,
                'preview_url' => $filePathPreview,
            ]);
        }
    }

    private function createPayment($orderId, $netto, $vat, $paymentType, $paymentMethod, $paymentMerchant)
    {
        return Payment::create([
            'order_id' => $orderId,
            'payment_type' => $paymentType,
            'value' => $netto - $vat,
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
}
