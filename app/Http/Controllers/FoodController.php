<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    private function fields(): array
    {
        return [
            ['name' => 'restaurant_id', 'label' => 'Restaurant', 'type' => 'select', 'options' => Restaurant::orderBy('name')->pluck('name', 'id')],
            ['name' => 'name', 'label' => 'Food / menu item', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'required' => false],
            ['name' => 'price', 'label' => 'Price', 'type' => 'number', 'step' => '0.01', 'min' => '0.01'],
            ['name' => 'available', 'label' => 'Available to order', 'type' => 'checkbox'],
        ];
    }

    public function index()
    {
        return view('resources.index', [
            'title' => 'Food & menu items', 'routeName' => 'foods',
            'records' => Food::with('restaurant')->latest()->paginate(10),
            'columns' => ['name' => 'Food / item', 'restaurant.name' => 'Restaurant', 'price' => 'Price', 'available' => 'Available'],
        ]);
    }

    public function create()
    {
        return view('resources.form', ['title' => 'Food / menu item', 'routeName' => 'foods', 'record' => new Food(['available' => true]), 'fields' => $this->fields()]);
    }

    public function store(Request $request)
    {
        Food::create($request->validate([
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'available' => ['sometimes', 'boolean'],
        ]) + ['available' => $request->boolean('available')]);

        return to_route('foods.index')->with('success', 'Menu item created successfully.');
    }

    public function show(Food $food)
    {
        $food->load('restaurant')->loadCount('orders');

        return view('resources.show', [
            'title' => 'Food / menu item', 'routeName' => 'foods', 'record' => $food,
            'details' => ['Food / item' => $food->name, 'Restaurant' => $food->restaurant->name, 'Description' => $food->description ?: '—', 'Price' => '$'.number_format((float) $food->price, 2), 'Available' => $food->available ? 'Yes' : 'No', 'Orders' => $food->orders_count],
        ]);
    }

    public function edit(Food $food)
    {
        return view('resources.form', ['title' => 'Food / menu item', 'routeName' => 'foods', 'record' => $food, 'fields' => $this->fields()]);
    }

    public function update(Request $request, Food $food)
    {
        $food->update($request->validate([
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'available' => ['sometimes', 'boolean'],
        ]) + ['available' => $request->boolean('available')]);

        return to_route('foods.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(Food $food)
    {
        if ($food->orders()->exists()) {
            return back()->with('error', 'This menu item is used by existing orders and cannot be deleted.');
        }

        $food->delete();

        return to_route('foods.index')->with('success', 'Menu item deleted successfully.');
    }
}
