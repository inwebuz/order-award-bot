<?php

namespace App\Traits;

use App\Translation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

trait Translatable
{
    /**
     * Get all of the models's translations.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Get the translation attribute.
     *
     * @return \App\Translation
     */
    public function getTranslationAttribute()
    {
        return $this->translations->firstWhere('language', app()->getLocale());
    }

    /**
     * Get the translation for locale.
     *
     * @return \App\Translation
     */
    public function t($locale = 'ru')
    {
        return $this->translations->firstWhere('language', $locale);
    }

    /**
     * Get the translation for locale.
     */
    public function translateModel($locale)
    {
        if ($locale != config('app.locale')) {
            $translation = $this->t($locale);
            foreach($this->translatable as $field) {
                $this->$field = $translation->content[$field] ?? $this->$field;
            }
        }
        return $this;
    }
}
