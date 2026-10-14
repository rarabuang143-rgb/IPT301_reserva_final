<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    private function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Restaurant name', 'type' => 'text'],
            ['name' => 'cuisine', 'label' => 'Cuisine', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'textarea'],
        ];
    }

    public function index()
    {
        return view('resources.index', [
            'title' => 'Restaurants', 'routeName' => 'restaurants', 'records' => Restaurant::withCount('foods')->latest()->paginate(10),
            'columns' => ['name' => 'Restaurant', 'cuisine' => 'Cuisine', 'phone' => 'Phone', 'foods_count' => 'Menu items'],
        ]);
    }

    public function create()
    {
        return view('resources.form', ['title' => 'Restaurant', 'routeName' => 'restaurants', 'record' => new Restaurant, 'fields' => $this->fields()]);
    }

    public function store(Request $request)
    {
        Restaurant::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cuisine' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:2000'],
        ]));

        return to_route('restaurants.index')->with('success', 'Restaurant created successfully.');
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->loadCount('foods');

        return view('resources.show', [
            'title' => 'Restaurant', 'routeName' => 'restaurants', 'record' => $restaurant,
            'details' => ['Restaurant name' => $restaurant->name, 'Cuisine' => $restaurant->cuisine, 'Phone' => $restaurant->phone, 'Address' => $restaurant->address, 'Menu items' => $restaurant->foods_count],
        ]);
    }

    public function edit(Restaurant $restaurant)
    {
        return view('resources.form', ['title' => 'Restaurant', 'routeName' => 'restaurants', 'record' => $restaurant, 'fields' => $this->fields()]);
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $restaurant->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cuisine' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:2000'],
        ]));

        return to_route('restaurants.index')->with('success', 'Restaurant updated successfully.');
    }

    public function destroy(Restaurant $restaurant)
    {
        if ($restaurant->foods()->exists()) {
            return back()->with('error', 'This restaurant has menu items and cannot be deleted.');
        }

        $restaurant->delete();

        return to_route('restaurants.index')->with('success', 'Restaurant deleted successfully.');
    }
}
