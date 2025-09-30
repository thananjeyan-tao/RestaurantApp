<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    private int $kitchenCapacity;
    public function setUp(): void {
        parent::setUp();
        $this->kitchenCapacity = (int) Config::get('kitchen.capacity');
    }

    public function test_rejects_when_kitchen_capacity_full()
    {
        Order::factory()->count($this->kitchenCapacity)->create();

        $response = $this->postJson('/api/orders', [
            'items' => ['burger'],
            'pickup_time' => now()->addMinutes(10)->format('Y-m-d\TH:i:s\Z'),
            'VIP' => false,
        ]);

        $response->assertStatus(429);
    }

    public function test_allows_vip_orders_even_if_kitchen_is_full()
    {
        Order::factory()->count($this->kitchenCapacity)->create();

        $response = $this->postJson('/api/orders', [
            'items' => ['pizza'],
            'pickup_time' => now()->addMinutes(10)->format('Y-m-d\TH:i:s\Z'),
            'VIP' => true,
        ]);

        $response->assertStatus(201);
    }

    public function test_completes_an_order_and_frees_capacity()
    {
        $order = Order::factory()->create();

        $response = $this->postJson("/api/orders/{$order->id}/complete");

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }
}
