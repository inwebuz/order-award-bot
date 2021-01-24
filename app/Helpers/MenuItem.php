<?php

namespace App\Helpers;

class MenuItem
{
    public $main;
	private $items = [];
	
	/**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(LinkItem $main)
    {
        $this->main = $main;
	}

	public function addItem(LinkItem $item)
	{
		$this->items[] = $item;
	}

	public function getItems()
	{
		return $this->items;
	}

	public function hasItems()
	{
		return count($this->items);
	}

    public function __get($get) {
        return $this->main->$get ?? null;
    }
}
