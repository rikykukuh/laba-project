<?php

namespace App\Http\Controllers\Order;

use App\DataTables\OrdersDataTable;
use App\Http\Controllers\Controller;

use App\Models\Client;
use App\Models\ItemType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemPhoto;
use App\Models\Payment;
use App\Models\PaymentMerchant;
use App\Models\PaymentMethod;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        return $dataTable->render('orders.index');
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
    //                 return $row->client->name;
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
        $clients = Client::all();
        $statuses = $this->statuses;

        $item_types = ItemType::all();
        $payment_methods = PaymentMethod::all();
        $payment_merchants = PaymentMerchant::all();
        $sites = Site::all();

        return view('orders.create', compact('statuses', 'clients', 'item_types', 'payment_methods', 'payment_merchants', 'sites'));
    }

    public function store(Request $request)
    {
        // if ($request->ajax()) {
        //     return response()->json($request->post());
        // }

        $items = $request->items;
        $item_images = $request->item_images;
        // dump(count($item_type));
        // dd($request->post());

        $dp = $request->dp;
        $kekurangan = $request->kekurangan;
        $total = $request->total;
        $client_id = $request->customer_id;
        $site_id = $request->site_id;

        $order = Order::create([
            'total' => $total,
            'uang_muka' => $dp,
            'status' => 'DIPROSES',
            'number_ticket' => Str::uuid(),
            'sisa_pembayaran' => $kekurangan,
            'client_id' => $client_id,
            'site_id' => $site_id,

            // 'name' => $random_string,
            // 'payment' => rand(1000, 100000),
            // 'due_date' => now(),
            // 'picked_by' => $request->picked_by,
            // 'picked_at' => date('Y-m-d H:i:s', strtotime($request->picked_at)),
        ]);

        for ($i = 0; $i < count($items); $i++) {
            $gambar = $items[$i]['gambar'];

            $order_item = OrderItem::create([
                'order_id' => $order->id,
                'item_type_id' => $items[$i]['type'],
                'total' => $items[$i]['biaya'],
                'note' => $items[$i]['keterangan'],
            ]);

            for ($j = 0; $j < count($gambar); $j++) {
                $randomFilename = Str::uuid()->toString();
                $base64Image = $gambar[$j];
                $split = explode(',', substr($base64Image, 5), 2);
                $mime = $split[0];
                $mime_split_without_base64 = explode(';', $mime, 2);
                $mime_split = explode('/', $mime_split_without_base64[0], 2);
                $extension = $mime_split[1];

                $filePathPreview = "previews/$randomFilename.$extension";
                $filePathThumbnail = "thumbnails/$randomFilename.$extension";

                $image = explode('base64,',$base64Image);
                $image = end($image);
                $image = str_replace(' ', '+', $image);

                Storage::disk('public')->put($filePathThumbnail,base64_decode($image));
                Storage::disk('public')->put($filePathPreview,base64_decode($image));

                // Storage::disk('public')->put('public/'.$filePathThumbnail, $base64Image);
                // Storage::disk('public')->put('public/'.$filePathPreview, $base64Image);

                OrderItemPhoto::create([
                    'order_item_id' => $order_item->id,
                    'thumbnail_url' => $filePathThumbnail,
                    'preview_url' => $filePathPreview,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json($order);
        }

        return redirect()->route('orders.index')->with('success', 'Sukses! Order ' . $order->name . ' berhasil dibuat!');
    }

    public function show($id)
    {
        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
        // dd($order);
        $clients = Client::all();
        $statuses = $this->statuses;
        // dd($statuses);

        $payment_methods = PaymentMethod::all();
        $payment_merchants = PaymentMerchant::all();
        $item_types = ItemType::all('id', 'name');

        $sites = Site::all();

        return view('orders.show', compact('order', 'clients', 'statuses', 'payment_methods', 'payment_merchants', 'item_types', 'sites'));
    }

    public function edit($id)
    {
        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
        $clients = Client::all();
        $statuses = $this->statuses;

        return view('orders.edit', compact('order', 'statuses', 'clients'));
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
        $client_id = $request->customer_id;
        $picked_by = $request->picked_by;
        $picked_at = $request->picked_at;
        $site_id = $request->site_id;

        $data = collect([
            // 'uang_muka' => $request->uang_muka,
            // 'due_date' => $request->due_date,
            'total' => $total,
            'status' => $status,
            'sisa_pembayaran' => $sisa_pembayaran,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'picked_by' => $picked_by,
            'picked_at' => date('Y-m-d H:i:s', strtotime($picked_at)),
        ]);

        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);

        if (!is_null($picked_by)) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'value' => (int) $total,
                'payment_method_id' => (int) $request->payment_method,
                'payment_merchant_id' => (int) $request->payment_merchant,
            ]);

            $data->put('payment_id', $payment->id);
        } else {
            $data->forget(['status', 'sisa_pembayaran', 'picked_by', 'picked_at']);
        }

        $order->update($data->all());

        for ($i = 0; $i < count($items); $i++) {
            $order_item = OrderItem::findOrFail($items[$i]['id']);

            $order_item->update([
                // 'order_id' => (int) $id,
                'item_type_id' => (int) $items[$i]['type'],
                'total' => $items[$i]['biaya'],
                'note' => $items[$i]['keterangan'],
            ]);

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

                $filePathPreview = "previews/$randomFilename.$extension";
                $filePathThumbnail = "thumbnails/$randomFilename.$extension";

                $image = explode('base64,',$base64Image);
                $image = end($image);
                $image = str_replace(' ', '+', $image);

                Storage::disk('public')->put($filePathThumbnail,base64_decode($image));
                Storage::disk('public')->put($filePathPreview,base64_decode($image));

                // Storage::disk('public')->put('public/'.$filePathThumbnail, $base64Image);
                // Storage::disk('public')->put('public/'.$filePathPreview, $base64Image);

                OrderItemPhoto::create([
                    'order_item_id' => $order_item->id,
                    'thumbnail_url' => $filePathThumbnail,
                    'preview_url' => $filePathPreview,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json($order);
        }

        return redirect()->route('orders.index')->with('success', 'Sukses Order ' . $order->name . ' berhasil diedit!');
    }

    public function destroy($id)
    {
        $order = Order::with('orderItems.orderItemPhotos')->findOrFail($id);
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Sukses! Order ' . $order->name . ' berhasil dihapus!');
    }

    public function getPaymentMerchants(Request $request) {
        $paymentMethod = $request->query('payment_method');
        $paymentMerchants = PaymentMerchant::where('payment_method_id', $paymentMethod)->get();
        return response()->json($paymentMerchants);
    }
}
