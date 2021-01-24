<?php

namespace App\Helpers;

class LinkItem
{
    public $name;
    public $url;
	public $status;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
	
	/**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct($name, $url, $status = self::STATUS_ACTIVE)
    {
        $this->name = $name;
        $this->url = $url;
		$this->status = $status;
	}

    /**
     * 
     * @return bool
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * 
     * @return void
     */
    public function setActive()
    {
    	$this->status = self::STATUS_ACTIVE;
    }

    /**
     * 
     * @return void
     */
    public function setInactive()
    {
    	$this->status = self::STATUS_INACTIVE;
    }
}
