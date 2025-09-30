<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['items', 'pickup_time', 'is_vip', 'status', 'priority'];

    protected $casts = [
        'pickup_time' => 'datetime',
        'items' => 'array'
    ];

    /**
     * Automatically update priority basen IsVip Attribute
     */
    public function setIsVipAttribute($value): void
    {
        $this->attributes['is_vip'] = $value;
        $this->attributes['priority'] = (bool)$value;
    }
}
