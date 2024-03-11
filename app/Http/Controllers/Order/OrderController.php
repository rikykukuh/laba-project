<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;

use App\Models\Client;
use App\Models\ItemType;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private $statuses;
    public function __construct()
    {
        $this->statuses = ['New', 'Ready', 'Paid', 'Picked Up'];
    }

    public function index()
    {
        $orders = Order::with('orderItems')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $clients = Client::all();
        $statuses = $this->statuses;

        $item_types = ItemType::all();
        $payment_methods = PaymentMethod::all();

        return view('orders.create', compact('statuses', 'clients', 'item_types', 'payment_methods'));
    }

    public function store(Request $request)
    {
        // dd($request->post());
        $random_string = Str::random(rand(2, 16));

        $order = Order::create([
            'name' => $random_string,
            'total' => rand(2, 16),
            'uang_muka' => $random_string,
            'status' => $request->status,
            'payment' => rand(1000, 100000),
            'number_ticket' => $random_string,
            'uang_muka' => rand(1000, 100000),
            'due_date' => now(),
            'sisa_pembayaran' => rand(1000, 100000),
            'picked_by' => $request->picked_by,
            'picked_at' => date('Y-m-d H:i:s', strtotime($request->picked_at)),
            'client_id' => $request->client_id,
        ]);

        $order_item = OrderItem::create([
            'order_id' => $order->id,
            'item_type_id' => $request->item_type_id,
            'note' => $request->note,
            'total' => $request->total,
        ]);

        return redirect()->route('orders.index')->with('success', 'Great! Order ' . $order->name . ' created successfully!');
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        $statuses = $this->statuses;

        return view('orders.show', compact('order', 'statuses'));
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);
        $clients = Client::all();
        $statuses = $this->statuses;

        return view('orders.edit', compact('order', 'statuses', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'name' => $request->name,
            'total' => $request->total,
            'uang_muka' => $request->uang_muka,
            'status' => $request->status,
            'payment' => $request->payment,
            'number_ticket' => $request->number_ticket,
            'uang_muka' => $request->uang_muka,
            'due_date' => $request->due_date,
            'sisa_pembayaran' => $request->sisa_pembayaran,
            'client_id' => $request->client_id,
        ]);
        return redirect()->route('orders.index')->with('success', 'Excellence! Order ' . $order->name . ' updated successfully!');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Well done! Order ' . $order->name . ' deleted successfully!');
    }
}
