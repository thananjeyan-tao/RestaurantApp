<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $order = $this->orderService->createOrder($data);
            if (!$order) {
                $nextAvailableTime = $this->orderService->getNextAvailableOrderTime();
                return response()->json([
                    'message' => 'Too Many Orders',
                    'next_available_time' => $nextAvailableTime->toDateTimeString(),
                ], 429);
            }

            return response()->json([
                'message' => 'Order created!',
                'data'    => new OrderResource($order)
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);

            return response()->json([
                'message' => 'Something went wrong while creating the order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function activeOrders(): OrderCollection
    {
        return new OrderCollection($this->orderService->getActiveOrders());
    }

    public function completeOrder(int $orderId): JsonResponse
    {
        $order = $this->orderService->findOrder($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $this->orderService->completeOrder($order);

        return response()->json(['message' => 'Order completed!']);
    }

    public function priorityQueue(): OrderCollection
    {
        return new OrderCollection($this->orderService->priorityQueue());
    }
}
