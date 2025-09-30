<?php

namespace Tests\Unit;

use App\Jobs\CompleteOrderJob;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;
    private int $kitchenCapacity;
    public function setUp(): void {
        parent::setUp();
        $this->kitchenCapacity = (int) Config::get('kitchen.capacity');
    }

    public function test_it_creates_order_when_capacity_not_full()
    {
        Queue::fake();

        $repo = $this->createMock(OrderRepository::class);

        $repo->method('countActiveOrders')->willReturn($this->kitchenCapacity - 1);
        $repo->method('create')->willReturn(new Order([
            'id' => 1,
            'items' => ['burger'],
            'pickup_time' =>  now(),
            'is_vip' => false,
            'status' => 'active',
        ]));

        $service = new OrderService($repo);

        $order = $service->createOrder([
            'items' => ['burger'],
            'pickup_time' => now()->addMinutes(10)->format('Y-m-d\TH:i:s\Z'),
            'VIP' => false,
        ]);

        $this->assertInstanceOf(Order::class, $order);

        Queue::assertPushed(CompleteOrderJob::class);
    }


    public function test_it_rejects_order_when_capacity_full_and_not_vip()
    {
        Queue::fake();

        $repo = $this->createMock(OrderRepository::class);

        $repo->method('countActiveOrders')->willReturn($this->kitchenCapacity);

        $service = new OrderService($repo);

        $order = $service->createOrder([
            'items' => ['pizza'],
            'pickup_time' => now()->addMinutes(10)->format('Y-m-d\TH:i:s\Z'),
            'VIP' => false,
        ]);

        $this->assertNull($order);
        Queue::assertNothingPushed();
    }

    public function test_it_allows_vip_order_even_when_capacity_full()
    {
        Queue::fake();

        $repo = $this->createMock(OrderRepository::class);

        $repo->method('countActiveOrders')->willReturn($this->kitchenCapacity);
        $repo->method('create')->willReturn(new Order([
            'id' => 2,
            'items' => ['steak'],
            'pickup_time' => now(),
            'is_vip' => true,
            'status' => 'active',
        ]));

        $service = new OrderService($repo);

        $order = $service->createOrder([
            'items' => ['pizza'],
            'pickup_time' => now()->addMinutes(10)->format('Y-m-d\TH:i:s\Z'),
            'VIP' => true,
        ]);

        $this->assertInstanceOf(Order::class, $order);
        Queue::assertPushed(CompleteOrderJob::class);
    }

}
