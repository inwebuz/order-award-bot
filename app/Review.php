<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    const STATUS_PENDING = -1;
    const STATUS_REJECTED = 0;
    const STATUS_APPROVED = 1;

    protected $guarded = [];

    public static function statuses()
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_REJECTED => __('Rejected'),
            self::STATUS_APPROVED => __('Approved'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return $this->statuses()[$this->status];
    }
}
