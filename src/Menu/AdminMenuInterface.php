<?php

namespace Survos\TablerBundle\Menu;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;


interface AdminMenuInterface
{
    public function addMenuItem(ItemInterface $menu, $options): ItemInterface;
}
