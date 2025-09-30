<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    public function countActiveOrders(bool $vip = false): int
    {
        return Order::where('status', 'active')
            ->where('is_vip', $vip)
            ->count();
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function getActiveOrders(): Collection
    {
        return Order::where('status', 'active')
            ->oldest()
            ->get();
    }

    public function completeOrder(Order $order): void
    {
        $order->update(['status' => 'completed']);
    }

    public function findById(int $id): ?Order
    {
        return Order::find($id);
    }

    public function priorityQueue(): Collection
    {
        return Order::where('status', 'active')
            ->orderBy('priority', 'desc')
            ->oldest()
            ->get();
    }

    public function getLastNormalActiveOrder(): Order {
        return Order::where('status', 'active')
            ->latest()
            ->first();
    }
}
