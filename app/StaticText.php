<?php

namespace App;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class StaticText extends Model
{
    use Translatable;

    protected $translatable = ['name', 'description'];

}
