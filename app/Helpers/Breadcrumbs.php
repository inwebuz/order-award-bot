<?php

namespace App\Helpers;

class Breadcrumbs
{
	private $items = [];
	
	/**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct($homePage = true)
    {
        if ($homePage) {
            $this->addItem(new LinkItem(__('main.nav.home'), route('home')));
        }
	}

	public function addItem(LinkItem $item)
	{
		$this->items[] = $item;
	}

	public function getItems()
	{
		return $this->items;
	}
}
