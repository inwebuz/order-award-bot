<?php

namespace App\Listeners;

use App\Translation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SaveTranslations
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $request = request();
        $model = $event->model;
        $translations = $request->input('translations', []);
        if (is_array($translations)) {
            foreach ($translations as $language => $translationValues) {
                $content = [];
                foreach ($model->translatable as $field) {
                    $value = !empty($translationValues[$field]) ? $translationValues[$field] : '';
                    $content[$field] = $value;
                }
                $translation = $model->translations()->firstOrCreate([
                    'language' => $language,
                ], [
                    'content' => [],
                ]);
                $translation->content = $content;
                $translation->save();
            }
        }
    }
}
