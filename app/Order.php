<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_OPEN = -1;
    const STATUS_REJECTED = 0;
    const STATUS_CLOSE = 1;

    protected $guarded = [];

    public static function statuses()
    {
        return [
            self::STATUS_OPEN => __('Status Open'),
            // self::STATUS_REJECTED => __('Status Rejected'),
            self::STATUS_CLOSE => __('Status Closed'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return $this->statuses()[$this->status];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
