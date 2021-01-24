<?php

namespace App;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use Translatable;

    protected $translatable = ['name', 'description', 'button_text', 'url'];

    /**
     * Statuses.
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PENDING = 2;

    /**
     * Types.
     */
    const TYPE_SLIDE = 'slide';
    const TYPE_HOME_1 = 'home_1';
    const TYPE_HOME_2 = 'home_2';
    const TYPE_HOME_3 = 'home_3';
    const TYPE_MIDDLE_1 = 'middle_1';
    const TYPE_MIDDLE_2 = 'middle_2';
    const TYPE_MIDDLE_3 = 'middle_3';


    /**
     * List of statuses.
     *
     * @var array
     */
    public static $statuses = [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_PENDING];

    protected $guarded = [];

    /**
     * Scope a query to only include active banners.
     *
     * @param  $query  \Illuminate\Database\Eloquent\Builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', static::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include active banners.
     *
     * @param  $query  \Illuminate\Database\Eloquent\Builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNowActive($query)
    {
        return $query->where([['active_from', '>=', date('Y-m-d H:i:s')], ['active_to', '<=', date('Y-m-d H:i:s')]]);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     *
     */
    public function bannerStats()
    {
        return $this->hasMany(BannerStats::class);
    }

    /**
     * Get main image
     */
    public function getImgAttribute()
    {
        return Voyager::image($this->image);
    }

    /**
     * Get increment clicks url
     */
    public function getIncrementClicksUrlAttribute()
    {
        return route('banner.increment.clicks', $this->id);
    }

    /**
     * Get increment views url
     */
    public function getIncrementViewsUrlAttribute()
    {
        return route('banner.increment.views', $this->id);
    }

    public static function types()
    {
        return [
            self::TYPE_SLIDE => 'Слайд',
            self::TYPE_HOME_1 => 'Главная 1',
            self::TYPE_HOME_2 => 'Главная 2',
            self::TYPE_HOME_3 => 'Главная 3',
            self::TYPE_MIDDLE_1 => 'Середина 1',
            self::TYPE_MIDDLE_2 => 'Середина 2',
            self::TYPE_MIDDLE_3 => 'Середина 3',
        ];
    }
}
