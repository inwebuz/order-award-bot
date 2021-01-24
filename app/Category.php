<?php

namespace App;

use App\Events\ModelSaved;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Translatable;

    protected $dispatchesEvents = [
        'saved' => ModelSaved::class,
    ];

    protected $guarded = [];

    public $translatable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
