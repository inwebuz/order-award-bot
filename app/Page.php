<?php

namespace App;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Page extends Model
{
    use Translatable;

    /**
     * Statuses.
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * Show control.
     */
    const SHOW_IN_NONE = 0;
    const SHOW_IN_MENU = 1;
    const SHOW_IN_FOOTER = 2;

    /**
     * List of statuses.
     *
     * @var array
     */
    public static $statuses = [self::STATUS_ACTIVE, self::STATUS_INACTIVE];

    public static $imgSizes = [
        'medium' => [200, 200],
    ];

    protected $translatable = ['name', 'description', 'body', 'seo_title', 'meta_description', 'meta_keywords'];

    protected $guarded = [];

    public function save(array $options = [])
    {
        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->user_id && auth()->user()) {
            $this->user_id = auth()->user()->id;
        }

        parent::save();
    }

    public function scopeActive($query)
    {
        return $query->where('status', static::STATUS_ACTIVE);
    }

    public function scopeInMenu($query)
    {
        return $query->where('show_in', static::SHOW_IN_MENU);
    }

    /**
     * Get url
     */
    public function getURLAttribute()
    {
        $url = Route::has($this->slug) ? route($this->slug) : 'page/' . $this->id . '-' . $this->slug;
        return LaravelLocalization::localizeURL($url);
    }

    /**
     * Get main image
     */
    public function getImgAttribute()
    {
        return Voyager::image($this->image);
    }

    /**
     * Get medium image
     */
    public function getMediumImgAttribute()
    {
        return Voyager::image($this->getThumbnail($this->image, 'medium'));
    }

    /**
     * Get background image
     */
    public function getBgAttribute()
    {
        return Voyager::image($this->background);
    }
}
