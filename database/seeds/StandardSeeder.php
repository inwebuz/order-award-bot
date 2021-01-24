<?php

use Illuminate\Database\Seeder;

class StandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    }

    protected function required()
    {
        return [
            'validation' => [
                'rule' => 'required',
            ],
        ];
    }

    protected function string(int $length = 191)
    {
        return [
            'validation' => [
                'rule' => 'max:' . $length,
            ],
        ];
    }

    protected function requiredString(int $length = 191)
    {
        return [
            'validation' => [
                'rule' => 'required|max:' . $length,
            ],
        ];
    }

    protected function requiredWithoutIdString(int $length = 191)
    {
        return [
            'validation' => [
                'rule' => 'required_without:id|max:' . $length
            ]
        ];
    }

    protected function requiredNumber(int $min = PHP_INT_MIN, int $max = PHP_INT_MAX)
    {
        return [
            'validation' => [
                'rule' => 'required|numeric|min:' . $min . '|max:' . $max,
            ],
            'default' => 0,
        ];
    }

    protected function number(int $min = PHP_INT_MIN, int $max = PHP_INT_MAX)
    {
        return [
            'validation' => [
                'rule' => 'numeric|min:' . $min . '|max:' . $max,
            ],
            'default' => 0,
        ];
    }

    protected function decimal($m = 12, $d = 2)
    {
        return [
            'validation' => [
                'rule' => 'numeric'
            ],
            'default' => 0,
        ];
    }

    protected function slug($origin = 'name', int $length = 191)
    {
        return [
            'validation' => [
                'rule' => 'required|max:' . $length,
            ],
            'slugify' => [
                'origin' => $origin,
            ],
            'description' => __('seeders.data_rows.details.slug_description'),
        ];
    }

    protected function slugUnique($tableName, $origin = 'name', int $length = 191)
    {
        return [
            'validation' => [
                'rule' => 'required|unique:' . $tableName . ',slug|max:' . $length,
            ],
            'slugify' => [
                'origin' => $origin,
            ],
            'description' => __('seeders.data_rows.details.slug_description'),
        ];
    }

    protected function unique($tableName, $fieldName, int $length = 191)
    {
        return [
            'validation' => [
                'rule' => 'required|unique:' . $tableName . ',' . $fieldName . '|max:' . $length,
            ],
            'description' => __('seeders.data_rows.details.unique'),
        ];
    }

    protected function dropdown($defaultSelected = '1', array $options = ['1' => 'Item 1'])
    {
        return [
            'default' => $defaultSelected,
            'options' => $options,
        ];
    }

    protected function checkbox($on = 'Да', $off = 'Нет', $checked = false)
    {
        return [
            'on' => $on,
            'off' => $off,
            'checked' => (bool)$checked,
        ];
    }

    protected function relationship($model, $table, $type, $column, $key = 'id', $label = 'name', $pivot_table = '', $pivot = 0, $taggable = 0)
    {
        return [
            'model' => $model,
            'table' => $table,
            'type' => $type,
            'column' => $column,
            'key' => $key,
            'label' => $label,
            'pivot_table' => $pivot_table,
            'pivot' => $pivot,
            'taggable' => $taggable,
        ];
    }

    protected function image($maxWidth = 1000, array $thumbs = [], int $quality = 70)
    {
        $thumbnails = [];
        foreach ($thumbs as $thumb) {
            $thumbnails[] = [
                'name' => $thumb['name'],
                'crop' => [
                    'width' => $thumb['width'],
                    'height' => $thumb['height'],
                ],
            ];
        }
        $description = 'Рекомендованный размер: ';
        if (isset($thumbs[0]['width']) && isset($thumbs[0]['height'])) {
            $maxHeight = floor($maxWidth * $thumbs[0]['height'] / $thumbs[0]['width']);
        } else {
            $maxHeight = $maxWidth;
        }
        $description .= $maxWidth . 'x' . $maxHeight;
        return [
            'resize' => [
                'width' => (int)$maxWidth,
                'height' => 'null',
            ],
            'upsize' => false,
            'quality' => $quality . '%',
            'thumbnails' => $thumbnails,
            'description' => $description,
        ];
        // {"resize":{"width":"1000","height":"null"},"thumbnails":[{"name":"medium","crop":{"width":"360","height":"240"}}],"description":"Рекомендованный размер: 1000x667"}
        //"upsize": false,
        //    "quality": "100%"
    }

    protected function file(array $mimes = [], $required = true)
    {
        $description = 'Рекомендованные форматы файлов: ' . (count($mimes) ? implode(', ', $mimes) : __('seeders.data_rows.details.any_files'));
        $rules = [];
        if ($required) {
            $rules[] = 'required_without:id';
        }
        if (count($mimes)) {
            $rules[] = 'mimes:' . implode(',', $mimes);
        }
        return [
            'validation' => [
                'rule' => (count($rules)) ? implode('|', $rules) : '',
            ],
            'description' => $description,
        ];
    }




    /* single row fields */
    protected function simpleRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => __('seeders.data_rows.text'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 0,
            'edit'         => 0,
            'add'          => 0,
            'delete'       => 0,
            'order'        => 100,
            /////
        ], $data);
    }

    protected function idRow($data = [])
    {
        return array_merge([
            'type'         => 'number',
            'display_name' => __('seeders.data_rows.id'),
            'required'     => 1,
            'browse'       => 0,
            'read'         => 0,
            'edit'         => 0,
            'add'          => 0,
            'delete'       => 0,
            'order'        => 1,
            /////
        ], $data);
    }

    protected function textRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => __('seeders.data_rows.text'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 5,
            'details'      => $this->string(),
        ], $data);
    }

    protected function textAreaRow($data = [])
    {
        return array_merge([
            'type'         => 'text_area',
            'display_name' => __('seeders.data_rows.text'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 5,
            'details'      => $this->string(5000),
        ], $data);
    }

    protected function nameRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => __('seeders.data_rows.name'),
            'required'     => 1,
            'browse'       => 1,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 2,
            'details'      => $this->requiredString(),
        ], $data);
    }

    protected function titleRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => __('seeders.data_rows.title'),
            'required'     => 1,
            'browse'       => 1,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 2,
            'details'      => $this->requiredString(),
        ], $data);
    }

    protected function slugRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => __('seeders.data_rows.slug'),
            'required'     => 1,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 3,
            'details'      => $this->slug(),
        ], $data);
    }

    protected function uniqueRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => __('seeders.data_rows.unique_code'),
            'required'     => 1,
            'browse'       => 1,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 4,
            'details'      => $this->unique('pages', 'slug'),
        ], $data);
    }

    protected function emailRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => __('seeders.data_rows.email'),
            'required'     => 1,
            'browse'       => 1,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 3,
            'details'      => $this->requiredString(),
        ], $data);
    }

    protected function descriptionRow($data = [])
    {
        return array_merge([
            'type'         => 'text_area',
            'display_name' => __('seeders.data_rows.description'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 4,
            'details'      => $this->string(1000),
        ], $data);
    }

    protected function bodyRow($data = [])
    {
        return array_merge([
            'type'         => 'rich_text_box',
            'display_name' => __('seeders.data_rows.body'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 5,
            /////
        ], $data);
    }

    protected function imageRow($data = [])
    {
        return array_merge([
            'type'         => 'image',
            'display_name' => __('seeders.data_rows.image'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 6,
            'details'      => $this->image(1000, [
                [
                    'name' => 'small',
                    'width' => '100',
                    'height' => '100',
                ],
                [
                    'name' => 'medium',
                    'width' => '336',
                    'height' => '300',
                ],
                [
                    'name' => 'large',
                    'width' => '600',
                    'height' => '600',
                ],
            ]),
        ], $data);
    }

    protected function fileRow($data = [])
    {
        return array_merge([
            'type'         => 'file',
            'display_name' => __('seeders.data_rows.file'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 6,
            'details'      => $this->file(),
        ], $data);
    }

    protected function priceRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => __('seeders.data_rows.price'),
            'required'     => 1,
            'browse'       => 1,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 7,
            'details'      => $this->requiredNumber(),
        ], $data);
    }

    protected function dropdownRow($data = [])
    {
        return array_merge([
            'type'         => 'select_dropdown',
            'display_name' => __('seeders.data_rows.select_dropdown'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 7,
            'details'      => $this->dropdown(),
        ], $data);
    }

    protected function parentIdRow($data = [])
    {
        return array_merge([
            'type'         => 'select_dropdown',
            'display_name' => __('seeders.data_rows.parent'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 7,
            'details'      => [
                'default' => '',
                'null' => '',
                'options' => [
                    '' => '-- Нет --',
                ],
                'relationship' => [
                    'key' => 'id',
                    'label' => 'name',
                ],
            ],
        ], $data);
    }

    protected function statusRow($data = [])
    {
        return array_merge([
            'type'         => 'checkbox',
            'display_name' => __('seeders.data_rows.page_status'),
            'required'     => 0,
            'browse'       => 1,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 20,
            'details'      => $this->checkbox(),
        ], $data);
    }

    protected function statusDropdownRow($data = [])
    {
        return array_merge([
            'type'         => 'select_dropdown',
            'display_name' => __('seeders.data_rows.page_status'),
            'required'     => 0,
            'browse'       => 1,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 20,
            'details'      => $this->dropdown(0, [
                "2" => "В ожидании",
                "1" => "Активна",
                "0" => "Не активна",
            ]),
        ], $data);
    }

    protected function seoTitleRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => __('seeders.data_rows.seo_title'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 100,
            'details'      => $this->string(),
        ], $data);
    }

    protected function metaDescriptionRow($data = [])
    {
        return array_merge([
            'type'         => 'text_area',
            'display_name' => __('seeders.data_rows.meta_description'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 101,
            'details'      => $this->string(),
        ], $data);
    }

    protected function metaKeywordsRow($data = [])
    {
        return array_merge([
            'type'         => 'text_area',
            'display_name' => __('seeders.data_rows.meta_keywords'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 102,
            'details'      => $this->string(),
        ], $data);
    }

    protected function hiddenRow($data = [])
    {
        return array_merge([
            'type'         => 'hidden',
            'display_name' => 'Hidden',
            'required'     => 0,
            'browse'       => 0,
            'read'         => 0,
            'edit'         => 0,
            'add'          => 0,
            'delete'       => 0,
            'order'        => 12,
            /////
        ], $data);
    }

    protected function numberRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => 'Number',
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 12,
            'details'      => $this->number(),
        ], $data);
    }

    protected function tinyNumberRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => 'Number',
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 12,
            'details'      => $this->number(-128, 127),
        ], $data);
    }

    protected function relationshipRow($data = [])
    {
        return array_merge([
            'type'         => 'relationship',
            'display_name' => 'Belongs To',
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 2,
            'details'      => $this->relationship('', '', '', ''),
        ], $data);
    }

    protected function decimalRow($data = [])
    {
        return array_merge([
            'type'         => 'text',
            'display_name' => 'Number',
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 12,
            'details'      => $this->decimal(12, 2),
        ], $data);
    }

    protected function hiddenNumberRow($data = [])
    {
        return array_merge([
            'type'         => 'hidden',
            'display_name' => 'Number',
            'required'     => 0,
            'browse'       => 0,
            'read'         => 0,
            'edit'         => 0,
            'add'          => 0,
            'delete'       => 0,
            'order'        => 12,
            'details'      => $this->number(),
        ], $data);
    }

    protected function timestampRow($data = [])
    {
        return array_merge([
            'type'         => 'timestamp',
            'display_name' => __('seeders.data_rows.timestamp'),
            'required'     => 0,
            'browse'       => 1,
            'read'         => 1,
            'edit'         => 1,
            'add'          => 1,
            'delete'       => 1,
            'order'        => 50,
            /////
        ], $data);
    }

    protected function createdAtRow($data = [])
    {
        return array_merge([
            'type'         => 'timestamp',
            'display_name' => __('seeders.data_rows.created_at'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 0,
            'add'          => 0,
            'delete'       => 0,
            'order'        => 90,
            /////
        ], $data);
    }

    protected function updatedAtRow($data = [])
    {
        return array_merge([
            'type'         => 'timestamp',
            'display_name' => __('seeders.data_rows.updated_at'),
            'required'     => 0,
            'browse'       => 0,
            'read'         => 1,
            'edit'         => 0,
            'add'          => 0,
            'delete'       => 0,
            'order'        => 91,
            /////
        ], $data);
    }
}
