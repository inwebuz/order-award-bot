<?php

namespace App\Helpers;

class LanguageSwitcher
{
    private $active;
    private $values = [];

    public function addValue(LinkItem $linkItem)
    {
    	$this->values[] = $linkItem;
    }

    public function getValues()
    {
    	return $this->values;
    }

    public function getActive()
    {
    	return $this->active;
    }

    public function setActive(LinkItem $linkItem)
    {
    	$this->active = $linkItem;
    }
}
