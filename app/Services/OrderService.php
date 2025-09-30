<?php

namespace App\Services;

use App\Jobs\CompleteOrderJob;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected int $kitchenCapacity;

    protected int $orderAutoCompleteTime;

    public function __construct(protected OrderRepository $repo)
    {
        $this->kitchenCapacity = config('kitchen.capacity');
        $this->orderAutoCompleteTime = config('kitchen.order_auto_complete_time');
    }

    public function createOrder(array $data): ?Order
    {
        return DB::transaction(function () use ($data) {

            $activeOrders = $this->repo->countActiveOrders(false);

            if ($activeOrders >= $this->kitchenCapacity && !$data['VIP']) {
                return null;
            }

            $order = $this->repo->create([
                'items' => $data['items'],
                'pickup_time' => $data['pickup_time'],
                'is_vip' => $data['VIP'],
                'status' => 'active',
            ]);

            CompleteOrderJob::dispatch($order)->delay(now()->addSeconds($this->orderAutoCompleteTime))->afterCommit();

            return $order;

        });
    }

    public function getActiveOrders()
    {
        return $this->repo->getActiveOrders();
    }

    public function completeOrder(Order $order): void
    {
        $this->repo->completeOrder($order);
    }

    public function findOrder(int $id): ?Order
    {
        return $this->repo->findById($id);
    }

    public function priorityQueue()
    {
        return $this->repo->priorityQueue();
    }

    public function getNextAvailableOrderTime(): Carbon
    {
        $lastActiveNormalOrder = $this->repo->getLastNormalActiveOrder();

        return $lastActiveNormalOrder->created_at->addSeconds($this->orderAutoCompleteTime);
    }
}
