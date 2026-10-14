<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Food;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FoodDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_and_all_resource_lists_are_available(): void
    {
        $this->get(route('dashboard'))->assertOk()->assertSee('Operations dashboard');

        foreach (['customers', 'restaurants', 'foods', 'orders', 'deliveries'] as $resource) {
            $this->get(route($resource.'.index'))->assertOk();
        }

        $this->assertDatabaseCount('migrations', 5);
        $this->assertTrue(Schema::hasTable('customers'));
        $this->assertTrue(Schema::hasTable('restaurants'));
        $this->assertTrue(Schema::hasTable('foods'));
        $this->assertTrue(Schema::hasTable('orders'));
        $this->assertTrue(Schema::hasTable('deliveries'));
    }

    public function test_create_edit_and_show_screens_render_for_every_resource(): void
    {
        $customer = Customer::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $food = Food::factory()->for($restaurant)->create();
        $order = Order::factory()->create(['customer_id' => $customer->id, 'food_id' => $food->id]);
        $delivery = Delivery::factory()->for($order)->create();

        foreach ([
            'customers' => $customer,
            'restaurants' => $restaurant,
            'foods' => $food,
            'orders' => $order,
            'deliveries' => $delivery,
        ] as $resource => $record) {
            $this->get(route($resource.'.create'))->assertOk();
            $this->get(route($resource.'.show', $record))->assertOk();
            $this->get(route($resource.'.edit', $record))->assertOk();
        }
    }

    public function test_order_total_is_calculated_and_delivery_is_unique_per_order(): void
    {
        $customer = Customer::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $food = Food::factory()->for($restaurant)->create(['price' => 12.50]);

        $this->post(route('orders.store'), [
            'customer_id' => $customer->id,
            'food_id' => $food->id,
            'quantity' => 3,
            'status' => 'pending',
        ])->assertRedirect(route('orders.index'))->assertSessionHas('success');

        $order = Order::firstOrFail();
        $this->assertSame('37.50', $order->total);
        $this->assertSame($customer->id, $order->customer->id);
        $this->assertSame($restaurant->id, $order->food->restaurant->id);
        $this->assertSame(1, $restaurant->orders()->count());

        $delivery = [
            'order_id' => $order->id,
            'courier_name' => 'Jamie Rivera',
            'status' => 'assigned',
        ];
        $this->post(route('deliveries.store'), $delivery)->assertRedirect(route('deliveries.index'));
        $this->assertDatabaseHas('deliveries', ['order_id' => $order->id, 'courier_name' => 'Jamie Rivera']);

        $this->from(route('deliveries.create'))
            ->post(route('deliveries.store'), $delivery)
            ->assertSessionHasErrors('order_id');

        $this->put(route('deliveries.update', Delivery::firstOrFail()), [
            'order_id' => $order->id,
            'courier_name' => 'Jamie Rivera',
            'status' => 'in_transit',
        ])->assertRedirect(route('deliveries.index'));
        $this->assertDatabaseHas('deliveries', ['order_id' => $order->id, 'status' => 'in_transit']);
    }

    public function test_invalid_references_and_deleting_referenced_customers_are_rejected(): void
    {
        $customer = Customer::factory()->create();
        $food = Food::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id, 'food_id' => $food->id]);

        $this->from(route('orders.create'))
            ->post(route('orders.store'), [
                'customer_id' => 99999,
                'food_id' => $food->id,
                'quantity' => 1,
                'status' => 'pending',
            ])
            ->assertSessionHasErrors('customer_id');

        $this->delete(route('customers.destroy', $customer))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_deleting_an_order_also_deletes_its_delivery(): void
    {
        $order = Order::factory()->create();
        $delivery = Delivery::factory()->for($order)->create();
        $this->assertEquals((float) $order->food->price * $order->quantity, (float) $order->total);

        $this->delete(route('orders.destroy', $order))
            ->assertRedirect(route('orders.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('deliveries', ['id' => $delivery->id]);
    }
}
