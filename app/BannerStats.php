<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Banner;

class BannerStats extends Model
{
    protected $guarded = [];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
}
