<?php

namespace App\Helpers;

class Rating
{
    public $min = 1;
    public $max = 5;
    public $value;
    public $active;
	
	/**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct($value, $active = false)
    {
        $this->active = (bool)$active;
        $value = (int)$value;
        if ($value < $this->min) {
        	$value = $this->min;
        } elseif ($value > $this->max) {
        	$value = $this->max;
        }
        $this->value = (int)$value;
	}

	/**
     * Check if it is active rating (selectable)
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
	}
}
