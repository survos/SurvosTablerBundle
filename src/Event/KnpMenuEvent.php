<?php

/*
 * Based on AdminLTE bundle, that used KnpMenu
 */

namespace Survos\TablerBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Collect all MenuItemInterface objects that should be rendered in the menu/navigation section.
 */
class KnpMenuEvent extends Event
{

    public function __construct(protected ItemInterface $menu, protected FactoryInterface $factory, private array $options = [], private array $childOptions = [])
    {
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getChildOptions(): array
    {
        return $this->childOptions;
    }
}
