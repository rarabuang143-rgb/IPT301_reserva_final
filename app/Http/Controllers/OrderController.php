<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Food;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private function fields(?int $currentFoodId = null): array
    {
        $foods = Food::with('restaurant')
            ->where('available', true)
            ->when($currentFoodId, fn ($query) => $query->orWhere('id', $currentFoodId))
            ->orderBy('name')
            ->get();

        return [
            ['name' => 'customer_id', 'label' => 'Customer', 'type' => 'select', 'options' => Customer::orderBy('name')->pluck('name', 'id')],
            ['name' => 'food_id', 'label' => 'Food / menu item', 'type' => 'select', 'options' => $foods->mapWithKeys(fn (Food $food) => [$food->id => $food->name.' — '.$food->restaurant->name.' ($'.number_format((float) $food->price, 2).')'])],
            ['name' => 'quantity', 'label' => 'Quantity', 'type' => 'number', 'min' => '1', 'max' => '99'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => array_combine(Order::STATUSES, array_map(fn ($status) => str_replace('_', ' ', ucfirst($status)), Order::STATUSES))],
        ];
    }

    public function index()
    {
        return view('resources.index', [
            'title' => 'Orders', 'routeName' => 'orders',
            'records' => Order::with(['customer', 'food.restaurant'])->latest()->paginate(10),
            'columns' => ['id' => 'Order #', 'customer.name' => 'Customer', 'food.name' => 'Item', 'food.restaurant.name' => 'Restaurant', 'quantity' => 'Qty', 'total' => 'Total', 'status' => 'Status'],
        ]);
    }

    public function create()
    {
        return view('resources.form', ['title' => 'Order', 'routeName' => 'orders', 'record' => new Order(['quantity' => 1, 'status' => 'pending']), 'fields' => $this->fields()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'food_id' => ['required', Rule::exists('foods', 'id')->where('available', true)],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'status' => ['required', 'in:'.implode(',', Order::STATUSES)],
        ]);
        $food = Food::findOrFail($data['food_id']);
        $data['total'] = $food->price * $data['quantity'];
        Order::create($data);

        return to_route('orders.index')->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'food.restaurant', 'delivery']);

        return view('resources.show', [
            'title' => 'Order #'.$order->id, 'routeName' => 'orders', 'record' => $order,
            'details' => ['Order #' => $order->id, 'Customer' => $order->customer->name, 'Food / item' => $order->food->name, 'Restaurant' => $order->food->restaurant->name, 'Quantity' => $order->quantity, 'Total' => '$'.number_format((float) $order->total, 2), 'Status' => str_replace('_', ' ', ucfirst($order->status)), 'Delivery' => $order->delivery ? $order->delivery->status : 'Not assigned'],
        ]);
    }

    public function edit(Order $order)
    {
        return view('resources.form', ['title' => 'Order', 'routeName' => 'orders', 'record' => $order, 'fields' => $this->fields($order->food_id)]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'food_id' => ['required', Rule::exists('foods', 'id')->where(fn ($query) => $query->where('available', true)->orWhere('id', $order->food_id))],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'status' => ['required', 'in:'.implode(',', Order::STATUSES)],
        ]);
        $food = Food::findOrFail($data['food_id']);
        $data['total'] = $food->price * $data['quantity'];
        $order->update($data);

        return to_route('orders.index')->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return to_route('orders.index')->with('success', 'Order and its delivery record deleted successfully.');
    }
}
