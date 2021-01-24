<?php

namespace App;

use App\Events\ModelSaved;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Translatable;

    protected $dispatchesEvents = [
        'saved' => ModelSaved::class,
    ];

    protected $guarded = [];

    public $translatable = ['name', 'description', 'units', 'button_text'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getUnitsSingularAttribute()
    {
        $units = explode('|', $this->units);
        return $units[0];
    }

    public function getUnitsPluralAttribute()
    {
        $units = explode('|', $this->units);
        if (isset($units[1])) {
            return $units[1];
        }
        return $units[0];
    }
}
