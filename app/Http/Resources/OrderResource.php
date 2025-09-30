<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'items'         => $this->items,
            'pickup_time'   => $this->pickup_time,
            'is_vip'        => $this->is_vip,
            'status'        => $this->status,
            'priority'      => $this->priority,
            'created_at'    => $this->created_at,
        ];
    }
}
