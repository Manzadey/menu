<?php

namespace Manzadey\Menu;

use Illuminate\Support\Collection;

final class Menu
{
    private array $items = [];

    public int $count = 0;

    final public function newItem() : Item
    {
        return new Item;
    }

    final public function add(Item $item) : self
    {
        $this->items[] = $this->handleItem($item);

        return $this;
    }

    final public function addItem(string $name, string $link) : self
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

    final public function getAll(Collection $items = null, array &$collect = []) : Collection
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
    final public function getByName(...$names) : Collection
    {
        return $this->getAll()->filter(fn(Item $item) : bool => in_array($item->name, $names, true));
    }

    private function handleItem(Item $item) : Item
    {
        $item->setId($item->id . '_' . ++$this->count);

        return $item;
    }
}
