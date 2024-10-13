<?php

namespace App\Http\Controllers\OrderProduct;

use App\DataTables\OrderProductsDataTable;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemPhoto;
use App\Models\Payment;
use App\Models\PaymentMerchant;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderProductController extends Controller
{
    private $statuses;
    public function __construct()
    {
        $this->statuses = ['New', 'Process', 'Picked Up'];
    }

    public function index(OrderProductsDataTable $dataTable)
    {
        $sites = Site::all();
        return $dataTable->render('order-products.index', compact('sites'));
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
    //                 $btn = '<a class="btn btn-default btn-xs" href="'.route('order-products.show', $row->id).'" title="Detail '.$row->name.'"><i class="fa fa-eye"></i></a>';
    //                 $btn .= '<form onsubmit="return confirm(\'Apakah Anda benar-benar ingin MENGHAPUS?\');" action="'.route('order-products.destroy', $row->id).'" method="post" style="display: inline-block">';
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
    //     return view('order-products.index');
    // }

    public function create()
    {
        $customers = Customer::all();
        $statuses = $this->statuses;

        $products = Product::where('type', '=', 0)->get();
        $payment_methods = PaymentMethod::all();
        $payment_merchants = PaymentMerchant::all();
        $sites = Site::all();
        $cities = City::all();

        return view('order-products.create', compact('statuses', 'customers', 'products', 'payment_methods', 'payment_merchants', 'sites', 'cities'));
    }

    public function store(Request $request)
    {
        // if ($request->ajax()) {
        //     return response()->json($request->post());
        // }

        $items = $request->items;
        $item_images = $request->item_images;
        // dump(count($product));
        // dd($request->post());

        $dp = $request->dp;
        $kekurangan = $request->kekurangan;
        $total = $request->total;
        $customer_id = $request->customer_id;
        $site_id = $request->site_id;
        $bruto = $request->bruto;
        $discount = $request->discount;
        $netto = $request->netto;
        // $vat = (int) $request->vat;
        $vat = calculate_included_vat($netto, 11);

        $order = Order::create([
            'total' => $netto - $vat,
            'uang_muka' => $dp,
            'status' => 'DIBAYAR',
            'sisa_pembayaran' => $kekurangan,
            'customer_id' => $customer_id,
            'transaction_type' => 1,
            'site_id' => $site_id,
            'bruto' => $bruto,
            'discount' => $discount,
            'netto' => $netto,
            'vat' => $vat,

            // 'name' => $random_string,
            // 'payment' => rand(1000, 100000),
            // 'due_date' => now(),
            // 'picked_by' => $request->picked_by,
            // 'picked_at' => date('Y-m-d H:i:s', strtotime($request->picked_at)),
        ]);

        $ticket_format = sprintf('%06d', $order->id);
        $code_site = Site::findOrFail($site_id);
        $number_ticket = $code_site->code.'-'. $ticket_format;
        $order->update(['number_ticket' => $number_ticket]);

        for ($i = 0; $i < count($items); $i++) {
            $jumlah = (int) $items[$i]['jumlah'];
            $kuantitas = (int) $items[$i]['kuantitas'];
            $discount_item = (int) $items[$i]['discount_item'];

            if($discount_item > 100) {
                $total_discount_item = (int) $items[$i]['discount_item'];
            } else {
                $total_discount_item = ($jumlah * $kuantitas) * $discount_item / 100;
            }

            $amount = ($jumlah - $total_discount_item) * $kuantitas;
            $vatItem = calculate_included_vat($amount, 11);

            $vatAmount = calculate_included_vat($jumlah, 11);
            $order_item = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $items[$i]['type'],
                'note' => $items[$i]['keterangan'],
                'transaction_type' => 1,
                'bruto' => $jumlah,
                'discount' => $discount_item,
                'quantity' => $kuantitas,
                'netto' => $amount,
                'vat' => $vatItem,
                'total' => $amount - $vatItem,
            ]);
        }

        Payment::create([
            'order_id' => $order->id,
            'payment_type' => 1,
            'value' => (int) $netto - $vat,
            'payment_method_id' => (int) $request->payment_method,
            'payment_merchant_id' => (int) $request->payment_merchant,
        ]);

        if ($request->ajax()) {
            return response()->json($order);
        }

        return redirect()->route('order-products.index')->with('success', 'Sukses! Pesanan ' . $order->name . ' berhasil dibuat!');
    }

    // public function store(Request $request)
    // {
    //     // $validated = $request->validate([
    //     //     'items' => 'required|array',
    //     //     'customer_id' => 'required|integer|exists:customers,id',
    //     //     'site_id' => 'required|integer|exists:sites,id',
    //     //     'dp' => 'required|numeric',
    //     //     'kekurangan' => 'required|numeric',
    //     //     'total' => 'required|numeric',
    //     //     'bruto' => 'required|numeric',
    //     //     'discount' => 'required|numeric',
    //     //     'netto' => 'required|numeric',
    //     //     'payment_method' => 'required|integer|exists:payment_methods,id',
    //     //     'payment_merchant' => 'required|integer|exists:payment_merchants,id',
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
    //         $order = $this->createOrder($orderData);
    //         $this->createOrderItems($order->id, $items);
    //         $this->createPayment($order->id, $orderData['netto'], $orderData['vat'], $request->payment_method, $request->payment_merchant);
    //
    //         $this->updateOrderNumber($order, $orderData['site_id']);
    //
    //         DB::commit();
    //
    //         Log::channel('orders')->info('Order created successfully.', [
    //             'date' => now()->toDateTimeString(),
    //             'type' => 'success',
    //             'message' => 'Order created successfully.',
    //             'context' => $order->toArray(),
    //             'type_order' => 'penjualan',
    //         ]);
    //
    //         if ($request->ajax()) {
    //             return response()->json($order);
    //         }
    //
    //         return redirect()->route('order-products.index')->with('success', 'Sukses! Pesanan berhasil dibuat!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //
    //         Log::channel('orders')->error('Error creating order', [
    //             'date' => now()->toDateTimeString(),
    //             'type' => 'error',
    //             'message' => 'Error creating order.',
    //             'context' => [
    //                 'error' => $e->getMessage(),
    //                 'order_data' => $orderData,
    //                 'items' => $items
    //             ],
    //             'type_order' => 'penjualan',
    //         ]);
    //
    //         return response()->json(['message' => 'Error creating order.', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function show($id)
    {
        $order = Order::where('transaction_type', 1)->with(['orderItems.orderItemPhotos' => function ($query) {
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

        return view('order-products.show', compact('order', 'customers', 'statuses', 'payment_methods', 'payment_merchants', 'products', 'sites'));
    }

    public function edit($id)
    {
        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
        $customers = Customer::all();
        $statuses = $this->statuses;

        return view('order-products.edit', compact('order', 'statuses', 'customers'));
    }

    public function update(Request $request, $id)
    {
        // if ($request->ajax()) {
        //     return response()->json($request->post());
        // }

        $items = $request->items;
        $total = $request->total;
        $status = $request->status;
        $sisa_pembayaran = $request->sisa_pembayaran;
        $customer_id = $request->customer_id;
        $picked_by = $request->picked_by;
        $picked_at = $request->picked_at;
        $site_id = $request->site_id;
        $bruto = $request->bruto;
        $discount = $request->discount;
        $netto = $request->netto;
        // $vat = (int) $request->vat;
        $vat = calculate_included_vat($netto, 11);

        $data = collect([
            // 'uang_muka' => $request->uang_muka,
            // 'due_date' => $request->due_date,
            'total' => $netto - $vat,
            'bruto' => $bruto,
            'discount' => $discount,
            'netto' => $netto,
            'vat' => $vat,
            'status' => $status,
            'sisa_pembayaran' => $sisa_pembayaran,
            'customer_id' => $customer_id,
            'site_id' => $site_id,
            'picked_by' => $picked_by,
            'picked_at' => date('Y-m-d H:i:s', strtotime($picked_at)),
        ]);

        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);

        if (!is_null($picked_by)) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_type' => 1,
                'value' => (int) $netto - $vat,
                'payment_method_id' => (int) $request->payment_method,
                'payment_merchant_id' => (int) $request->payment_merchant,
            ]);

            $data->put('payment_id', $payment->id);
        } else {
            $data->forget(['status', 'sisa_pembayaran', 'picked_by', 'picked_at']);
        }

        $order->update($data->all());

        for ($i = 0; $i < count($items); $i++) {
            $jumlah = (int) $items[$i]['harga'];
            $kuantitas = (int) $items[$i]['kuantitas'];
            $discount_item = (int) $items[$i]['discount_item'];

            if($discount_item > 100) {
                $total_discount_item = (int) $items[$i]['discount_item'];
            } else {
                $total_discount_item = ($jumlah * $kuantitas) * $discount_item / 100;
            }

            $amount = ($jumlah - $total_discount_item) * $kuantitas;
            $vatItem = calculate_included_vat($amount, 11);

            $order_item = OrderItem::findOrFail($items[$i]['id']);

            $order_item->update([
                // 'order_id' => (int) $id,
                'product_id' => (int) $items[$i]['type'],
                'bruto' => $jumlah,
                'discount' => $discount_item,
                'quantity' => $kuantitas,
                'netto' => $amount,
                'vat' => $vatItem,
                'total' => $amount - $vatItem,
                'note' => $items[$i]['keterangan'],
            ]);
        }

        if ($request->ajax()) {
            return response()->json($order);
        }

        return redirect()->route('order-products.index')->with('success', 'Sukses Pesanan ' . $order->name . ' berhasil diedit!');
    }

    // public function update(Request $request, $id)
    // {
    //     // $validated = $request->validate([
    //     //     'items' => 'required|array',
    //     //     'customer_id' => 'required|integer|exists:customers,id',
    //     //     'site_id' => 'required|integer|exists:sites,id',
    //     //     'total' => 'required|numeric',
    //     //     'status' => 'required|string',
    //     //     'sisa_pembayaran' => 'nullable|numeric',
    //     //     'picked_by' => 'nullable|string',
    //     //     'picked_at' => 'nullable|date',
    //     //     'bruto' => 'required|numeric',
    //     //     'discount' => 'required|numeric',
    //     //     'netto' => 'required|numeric',
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
    //             $payment = $this->createPayment($order->id, $orderData['netto'], $orderData['vat'], $request->payment_method, $request->payment_merchant);
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
    //         return redirect()->route('order-products.index')->with('success', 'Sukses Pesanan berhasil diedit!');
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

    public function destroy($id)
    {
        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
        $order->delete();
        return redirect()->route('order-products.index')->with('success', 'Pesanan penjualan ' . $order->customer->name . ' berhasil dihapus!');
    }


    public function getPaymentMerchants(Request $request) {
        $paymentMethod = $request->query('payment_method');
        $paymentMerchants = PaymentMerchant::where('payment_method_id', $paymentMethod)->get();
        return response()->json($paymentMerchants);
    }

    public function orderPrint($id)
    {
        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
        // dd($order);
        $customers = Customer::all();
        $statuses = $this->statuses;
        // dd($statuses);

        $payment_methods = PaymentMethod::all();
        $payment_merchants = PaymentMerchant::all();
        $products = Product::all('id', 'name');

        $sites = Site::all();

        return view('order-products.print', compact('order', 'customers', 'statuses', 'payment_methods', 'payment_merchants', 'products', 'sites'));
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

    private function prepareOrderData($request)
    {
        $netto = $request->netto;
        $vat = calculate_included_vat($netto, 11);

        return [
            'total' => $netto - $vat,
            'uang_muka' => $request->dp,
            'status' => 'DIBAYAR',
            'sisa_pembayaran' => $request->kekurangan,
            'customer_id' => $request->customer_id,
            'transaction_type' => 1,
            'site_id' => $request->site_id,
            'bruto' => $request->bruto,
            'discount' => $request->discount,
            'netto' => $netto,
            'vat' => $vat,
        ];
    }

    private function createOrder($data)
    {
        return Order::create($data);
    }

    private function createOrderItems($orderId, $items)
    {
        foreach ($items as $item) {
            $amount = $this->calculateAmount($item);
            $vatItem = calculate_included_vat($amount, 11);

            OrderItem::create([
                'order_id' => $orderId,
                'product_id' => $item['type'],
                'note' => $item['keterangan'],
                'transaction_type' => 1,
                'bruto' => $item['jumlah'],
                'discount' => $item['discount_item'],
                'quantity' => $item['kuantitas'],
                'netto' => $amount,
                'vat' => $vatItem,
                'total' => $amount - $vatItem,
            ]);
        }
    }

    private function updateOrderItems($items)
    {
        foreach ($items as $item) {
            $amount = $this->calculateAmount($item);
            $vatItem = calculate_included_vat($amount, 11);

            $orderItem = OrderItem::findOrFail($item['id']);
            $orderItem->update([
                'product_id' => $item['type'],
                'note' => $item['keterangan'],
                'transaction_type' => 1,
                'bruto' => $item['harga'],
                'discount' => $item['discount_item'],
                'quantity' => $item['kuantitas'],
                'netto' => $amount,
                'vat' => $vatItem,
                'total' => $amount - $vatItem,
            ]);
        }
    }

    private function createPayment($orderId, $netto, $vat, $paymentMethod, $paymentMerchant)
    {
        Payment::create([
            'order_id' => $orderId,
            'payment_type' => 1,
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

    private function calculateAmount($item)
    {
        $jumlah = (int) $item['jumlah'];
        $kuantitas = (int) $item['kuantitas'];
        $discountItem = (int) $item['discount_item'];

        if ($discountItem > 100) {
            $totalDiscountItem = (int) $item['discount_item'];
        } else {
            $totalDiscountItem = ($jumlah * $kuantitas) * $discountItem / 100;
        }

        return ($jumlah - $totalDiscountItem) * $kuantitas;
    }
}
