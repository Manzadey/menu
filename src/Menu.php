<?php

namespace Manzadey\Menu;

use Illuminate\Support\Collection;

class Menu
{
    private array $items = [];

    public int $count = 0;

    public array $attributes = [];

    public function newItem() : Item
    {
        return new Item;
    }

    public function add(Item $item) : self
    {
        $this->items[] = $this->handleItem($item);

        return $this;
    }

    public function addItem(string $name, string $link) : self
    {
        $this->add($this->newItem()->name($name)->link($link));

        return $this;
    }

    public function get() : Collection
    {
        return collect($this->items)->filter(static function(Item $item) {
            if($item->hasChildren()) {
                return $item->children->isNotEmpty();
            }

            return true;
        });
    }

    public function getAll(Collection $items = null, array &$collect = []) : Collection
    {
        if($items === null) {
            $items = collect($this->items);
        }

        foreach ($items as $item) {
            if($item->hasChildren()) {
                $this->getAll($item->childrens, $collect);
                continue;
            }

            $collect[] = $item;
        }

        return collect($collect);
    }

    /**
     * @param mixed ...$names
     *
     * @return Collection
     */
    public function getByName(...$names) : Collection
    {
        return $this->getAll()->filter(static function(Item $item) use ($names) : bool {
            return in_array($item->name, $names, true);
        });
    }

    public function handleItem(Item $item) : Item
    {
        $item->setId($item->id . '_' . ++$this->count);

        return $item;
    }

    public function hasAttribute(string $key) : bool
    {
        return isset($this->attributes[$key]);
    }

    public function getAttribute(string $key)
    {
        return $this->hasAttribute($key) ? $this->attributes[$key] : null;
    }
}
