<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    private function fields(?int $currentOrderId = null): array
    {
        $orders = Order::with(['customer', 'food'])
            ->whereDoesntHave('delivery')
            ->when($currentOrderId, fn ($query) => $query->orWhere('id', $currentOrderId))
            ->orderByDesc('id')
            ->get();

        return [
            ['name' => 'order_id', 'label' => 'Order', 'type' => 'select', 'options' => $orders->mapWithKeys(fn (Order $order) => [$order->id => '#'.$order->id.' — '.$order->customer->name.' / '.$order->food->name])],
            ['name' => 'courier_name', 'label' => 'Courier name', 'type' => 'text'],
            ['name' => 'status', 'label' => 'Delivery status', 'type' => 'select', 'options' => array_combine(Delivery::STATUSES, array_map(fn ($status) => str_replace('_', ' ', ucfirst($status)), Delivery::STATUSES))],
            ['name' => 'dispatched_at', 'label' => 'Dispatched at', 'type' => 'datetime-local', 'required' => false],
            ['name' => 'delivered_at', 'label' => 'Delivered at', 'type' => 'datetime-local', 'required' => false],
        ];
    }

    public function index()
    {
        return view('resources.index', [
            'title' => 'Deliveries', 'routeName' => 'deliveries',
            'records' => Delivery::with(['order.customer', 'order.food'])->latest()->paginate(10),
            'columns' => ['order_id' => 'Order #', 'order.customer.name' => 'Customer', 'courier_name' => 'Courier', 'status' => 'Status', 'dispatched_at' => 'Dispatched'],
        ]);
    }

    public function create()
    {
        return view('resources.form', ['title' => 'Delivery', 'routeName' => 'deliveries', 'record' => new Delivery(['status' => 'assigned']), 'fields' => $this->fields()]);
    }

    public function store(Request $request)
    {
        Delivery::create($request->validate([
            'order_id' => ['required', 'exists:orders,id', 'unique:deliveries,order_id'],
            'courier_name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:'.implode(',', Delivery::STATUSES)],
            'dispatched_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date', 'after_or_equal:dispatched_at'],
        ]));

        return to_route('deliveries.index')->with('success', 'Delivery created successfully.');
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['order.customer', 'order.food.restaurant']);

        return view('resources.show', [
            'title' => 'Delivery', 'routeName' => 'deliveries', 'record' => $delivery,
            'details' => ['Order #' => $delivery->order_id, 'Customer' => $delivery->order->customer->name, 'Restaurant' => $delivery->order->food->restaurant->name, 'Courier' => $delivery->courier_name, 'Status' => str_replace('_', ' ', ucfirst($delivery->status)), 'Dispatched at' => $delivery->dispatched_at?->format('M j, Y g:i A') ?? '—', 'Delivered at' => $delivery->delivered_at?->format('M j, Y g:i A') ?? '—'],
        ]);
    }

    public function edit(Delivery $delivery)
    {
        return view('resources.form', ['title' => 'Delivery', 'routeName' => 'deliveries', 'record' => $delivery, 'fields' => $this->fields($delivery->order_id)]);
    }

    public function update(Request $request, Delivery $delivery)
    {
        $delivery->update($request->validate([
            'order_id' => ['required', 'exists:orders,id', 'unique:deliveries,order_id,'.$delivery->id],
            'courier_name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:'.implode(',', Delivery::STATUSES)],
            'dispatched_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date', 'after_or_equal:dispatched_at'],
        ]));

        return to_route('deliveries.index')->with('success', 'Delivery updated successfully.');
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return to_route('deliveries.index')->with('success', 'Delivery deleted successfully.');
    }
}
