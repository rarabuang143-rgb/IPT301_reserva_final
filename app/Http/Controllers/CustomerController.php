<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Full name', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['name' => 'address', 'label' => 'Delivery address', 'type' => 'textarea'],
        ];
    }

    public function index()
    {
        return view('resources.index', [
            'title' => 'Customers', 'routeName' => 'customers', 'records' => Customer::latest()->paginate(10),
            'columns' => ['name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'address' => 'Address'],
        ]);
    }

    public function create()
    {
        return view('resources.form', ['title' => 'Customer', 'routeName' => 'customers', 'record' => new Customer, 'fields' => $this->fields()]);
    }

    public function store(Request $request)
    {
        Customer::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:2000'],
        ]));

        return to_route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->loadCount('orders');

        return view('resources.show', [
            'title' => 'Customer', 'routeName' => 'customers', 'record' => $customer,
            'details' => ['Full name' => $customer->name, 'Email' => $customer->email, 'Phone' => $customer->phone, 'Delivery address' => $customer->address, 'Orders' => $customer->orders_count],
        ]);
    }

    public function edit(Customer $customer)
    {
        return view('resources.form', ['title' => 'Customer', 'routeName' => 'customers', 'record' => $customer, 'fields' => $this->fields()]);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email,'.$customer->id],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:2000'],
        ]));

        return to_route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists()) {
            return back()->with('error', 'This customer has orders and cannot be deleted.');
        }

        $customer->delete();

        return to_route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
