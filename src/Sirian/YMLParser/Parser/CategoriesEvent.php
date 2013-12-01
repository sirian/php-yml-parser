<?php

namespace Sirian\YMLParser\Parser;

use Sirian\YMLParser\Category;
use Symfony\Component\EventDispatcher\Event;

class CategoriesEvent extends Event
{
    /**
     * @var Category[]
     */
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
