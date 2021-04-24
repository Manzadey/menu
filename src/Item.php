<?php

namespace Manzadey\Menu;

use Illuminate\Support\Str;

class Item
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $link = '#';

    /**
     * @var string
     */
    public $route = '';

    /**
     * @var null|string
     */
    public $id = null;

    /**
     * @var bool
     */
    public $isChildren = false;

    /**
     * @var bool
     */
    public $isActive = false;

    /**
     * @var \Illuminate\Support\Collection
     */
    public $children;

    /**
     * @var int
     */
    public $count = 0;

    /**
     * @var bool
     */
    private $hasChildren = false;

    public function __construct()
    {
        $this->children = collect();
    }

    public function name(string $name) : self
    {
        $this->name = $name;
        if($this->id === null) {
            $this->id = Str::slug($name, '_');
        }

        return $this;
    }

    public function link(string $link) : self
    {
        $this->link = $link;
        $this->setActive();

        return $this;
    }

    public function route(string $route, array $parameters = []) : self
    {
        $this->route = $route;
        $this->link  = route($route, $parameters);
        $this->setActive();

        return $this;
    }

    public function add(Item $item) : self
    {
        $this->count++;
        $item->isChildren = $this->hasChildren = true;
        $item->setId($item->id . '_' . $this->count);
        $this->children->push($item);

        return $this;
    }

    public function hasChildren() : bool
    {
        return $this->hasChildren;
    }

    public function setId(string $id) : self
    {
        $this->id = $id;

        return $this;
    }

    private function setActive() : void
    {
        if($this->link !== '#') {
            $uri   = '';
            $parse = parse_url($this->link, PHP_URL_PATH);

            if($parse) {
                $uri = Str::replaceFirst('/', '', $parse);
            }

            $this->isActive = request()->path() === $uri;
        }
    }
}
