<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CompleteOrderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(OrderService $orderService): void
    {
        if ($this->order->status === 'active') {
            $orderService->completeOrder($this->order);

            Log::info('Order completed successfully', [
                'order_id' => $this->order->id,
                'completed_at' => now()->toDateTimeString(),
            ]);
        }
    }
}
