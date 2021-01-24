<?php

namespace App\View\Components;

use App\Helpers\Helper;
use Illuminate\View\Component;

class BannerMiddle extends Component
{
    public $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $banner = Helper::banner($this->type);
        return view('components.banner-middle', compact('banner'));
    }
}
