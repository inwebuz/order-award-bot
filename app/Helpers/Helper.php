<?php

namespace App\Helpers;

use App\Banner;
use App\BannerStats;
use App\Page;
use App\Review;
use App\Setting;
use App\StaticText;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class Helper
{

    public static function formatPrice($number)
    {
        return number_format($number, 0, '.', ' ') . ' ' . __('main.currency');
    }

    public static function formatDate(Carbon $date, $year = false)
    {
        $yearFormat = ($year) ? ', Y' : '';
        return __($date->format('F')) . ' ' . $date->format('d' . $yearFormat);
    }

    public static function formatDateSecond(Carbon $date)
    {
        return '<div>' . $date->format('d') . '</div><div>' . __($date->format('F')) . '</div>';
    }

    public static function formatViews($views = 0)
    {
        $text = $views . ' <span class="d-none d-lg-inline">';
        if (Str::endsWith($views, [11, 12, 13, 14])) {
            $text .= 'просмотров';
        } elseif (Str::endsWith($views, [2, 3, 4])) {
            $text .= 'просмотра';
        } elseif (Str::endsWith($views, 1)) {
            $text .= 'просмотр';
        } else {
            $text .= 'просмотров';
        }
        return $text . '</span>';
    }

    public static function formatOffers($offers = 0)
    {
        $text = $offers . ' ';
        if (Str::endsWith($offers, [11, 12, 13, 14])) {
            $text .= 'предложений';
        } elseif (Str::endsWith($offers, [2, 3, 4])) {
            $text .= 'предложения';
        } elseif (Str::endsWith($offers, 1)) {
            $text .= 'предложение';
        } else {
            $text .= 'предложений';
        }
        return $text;
    }

    public static function menuItems()
    {
        $menu = [];
        $menu[] = new MenuItem(new LinkItem(__('main.nav.home'), route('home')));
        $pages = Page::active()->inMenu()->orderBy('order')->get()->translate();
        if ($pages) {
            foreach ($pages as $page) {
                $url = $page->url;
                $menu[] = new MenuItem(new LinkItem($page->name, $url));
            }
        }
        return $menu;
    }

    public static function banner($type)
    {
        // $banner = Banner::where('type', $type)->active()->nowActive()->latest()->first();
        $banner = Banner::where('type', $type)->active()->nowActive()->latest()->first();
        if (!$banner) {
            $banner = Banner::where([['type', $type], ['shop_id', null]])->active()->latest()->first();
        }
        if (!$banner) {
            $banner = new Banner(['id' => 0, 'name' => '1', 'url' => '', 'image' => 'no-image.jpg']);
        }
        return $banner;
    }

    public static function banners($type)
    {
        // $banners = Banner::where('type', $type)->active()->nowActive()->latest()->get();
        $banners = Banner::where('type', $type)->active()->latest()->get();
        if (!$banners) {
            $banners = Banner::where([['type', $type], ['shop_id', null]])->active()->latest()->get();
        }
        return $banners;
    }

    public static function phone($phone)
    {
        return '+' . preg_replace('#[^\d]#', '', $phone);
    }

    public static function parsePhones($phones)
    {
        $parsed = [];
        $phones = str_replace([';'], ',', $phones);
        $phones = explode(',', $phones);
        foreach ($phones as $phone) {
            $parsed[] = [
                'original' => $phone,
                'clean' => self::phone($phone),
            ];
        }
        return $parsed;
    }

    public static function reformatText($text)
    {
        return preg_replace(['#\*(.*?)\*#', '#\#(.*?)\##', '#\|\|#'], ['<strong>$1</strong>', '<span class="text-primary">$1</span>', '<br>'], $text);
    }

    public static function formatWorkDays($days)
    {
        $days = explode(',', preg_replace('#[^0-9,]#', '', $days));
        $days = array_map('intval', $days);
        $daysStatus = [];
        for ($i = 1; $i <= 7; $i++) {
            $daysStatus[$i] = in_array($i, $days) ? true : false;
        }
        return $daysStatus;
    }

    /**
     *
     * @return array
     */
    public static function languageSwitcher()
    {
        $switcher = new LanguageSwitcher();
        $url = url()->current();
        $currentLocale = app()->getLocale();
        foreach (config('laravellocalization.supportedLocales') as $key => $value) {
            $value['url'] = LaravelLocalization::localizeURL($url, $key);
            $linkItem = new LinkItem($value['native'], $value['url']);
            $linkItem->key = $key;
            if ($key == $currentLocale) {
                // $linkItem->setActive();
                $switcher->setActive($linkItem);
            }
            $switcher->addValue($linkItem);
        }
        return $switcher;
    }

    /**
     * Return translated model.
     *
     * @param Illuminate\Database\Eloquent\Model $model
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public static function translation($model)
    {
        if (app()->getLocale() != config('voyager.multilingual.default')) {
            return $model->translate();
        }
        return $model;
    }

    /**
     * Send message via telegram bot to group
     */
    public static function toTelegram($text, $parse_mode = 'HTML', $chat_id = '')
    {
        $token = config('services.telegram.bot_token');

        if (!$chat_id) {
            $chat_id = config('services.telegram.chat_id');
        }

        $formData = [];
        $formData['chat_id'] = $chat_id;
        $formData['text'] = $text;
        if (in_array($parse_mode, ['HTML', 'Markdown'])) {
            $formData['parse_mode'] = $parse_mode;
        }

        try {
            $client = new Client([
                'base_uri' => 'https://api.telegram.org',
                'timeout'  => 2.0,
            ]);

            $client->post('/bot' . $token . '/sendMessage', [
                'form_params' => $formData,
            ]);
        } catch (Exception $e) {

        }
    }

    public static function storeFile($model, $field, $dir, $isImage = false)
    {
        if (request()->has($field)) {
            $url = request()->$field->store($dir . '/' . date('FY'), 'public');
            if (!$isImage) {
                $url = json_encode([
                    [
                        'download_link' => $url,
                        'original_name' => request()->$field->getClientOriginalName(),
                    ]
                ]);
            }
            $model->update([
                $field => $url,
            ]);
        }
        return $model;
    }

    public static function storeImage($model, $field, $dir, $thumbs = [])
    {
        $model = self::storeFile($model, $field, $dir, true);
        if ($thumbs && $model->$field) {
            $image = Image::make(storage_path('app/public/' . $model->$field));
            if ($image) {
                $ext = mb_strrchr($model->$field, '.');
                $pos = mb_strrpos($model->$field, '.');
                $fileName = mb_substr($model->$field, 0, $pos);
                foreach ($thumbs as $key => $value) {
                    $image->fit($value[0], $value[1])->save(storage_path('app/public/' . $fileName . '-' . $key . $ext));
                }
            }
        }
        return $model;
    }

    public static function checkModelActive($model)
    {
        $className = get_class($model);
        if (!isset($model->status) || !defined("$className::STATUS_ACTIVE") || (int)$model->status !== $className::STATUS_ACTIVE) {
            abort(404);
        }
    }

    public static function staticText($key, $cacheTime = 21600)
    {
        return Cache::remember($key, $cacheTime, function () use ($key) {
            return StaticText::where('key', $key)->first();
        });
    }

    public static function seoTemplate($model, $name, $replacements = [])
    {
        $texts = [
            'seo_template_' . $name . '_seo_title',
            'seo_template_' . $name . '_meta_description',
            'seo_template_' . $name . '_meta_keywords',
            'seo_template_' . $name . '_description',
            'seo_template_' . $name . '_body',
        ];
        foreach ($texts as $text) {
            $currentProperty = str_replace('seo_template_' . $name . '_', '', $text);
            if (empty($model->$currentProperty)) {
                $template = self::staticText($text);
                if ($template) {
                    $model->$currentProperty = self::replaceTemplates($template->description, $replacements);
                }
            }
        }
        return $model;
    }

    public static function replaceTemplates($text, $replacements = [])
    {
        if (!$replacements) {
            return $text;
        }
        return str_replace(
            array_map(
                function ($value) {
                    return '{' . $value . '}';
                },
                array_keys($replacements)
            ),
            array_values($replacements),
            $text
        );
    }

    public static function addInitialReview($model)
    {
        $data = [
            'name' => 'Админ',
            'body' => 'Отлично!',
            'rating' => 5,
            'status' => 1,
        ];
        $model->reviews()->create($data);
    }

    public static function sendSMS($messageId, $phoneNumber, $message)
    {
        Log::info($message);
        return true;
    }

    public static function messagePrefix()
    {
        $name = str_replace(' ', '', Str::lower(config('app.name')));
        $prefix = config('app.env') == 'production' ? $name : 'test' . $name;
        return $prefix;
    }

    public static function getTree($collection, $parent = null, $level = 1)
    {
        $filtered = $collection->filter(function($value) use ($parent) {
            return $value['parent_id'] == $parent;
        });
        $filtered->map(function($item) use ($collection, $level) {
            $item['children'] = self::getTree($collection, $item->id, $level + 1);
        });
        return $filtered;
    }

    public static function activeCategories($category, $ids = [])
    {
        $ids[] = $category->id;
        if($category->parent) {
            $ids = self::activeCategories($category->parent, $ids);
        }
        return $ids;
    }

    public static function voyagerFileUrl($file)
    {
        $file = json_decode($file);
        if (!empty($file[0])) {
            return Storage::url($file[0]->download_link);
        }
        return false;
    }
}
