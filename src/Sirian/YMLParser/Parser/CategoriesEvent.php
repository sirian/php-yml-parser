<?php

namespace Sirian\YMLParser\Parser;

use Sirian\YMLParser\Shop;
use Symfony\Component\EventDispatcher\Event;

class CategoriesEvent extends Event
{
    private $categories;

    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    public function getCategories()
    {
        return $this->categories;
    }
}
