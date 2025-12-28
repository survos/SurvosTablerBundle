<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Service;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Survos\TablerBundle\Event\MenuEvent;
use Survos\TablerBundle\Menu\MenuSlot;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class MenuDispatcher
{
    public function __construct(
        private FactoryInterface $factory,
        private EventDispatcherInterface $dispatcher,
    ) {}
    
    public function dispatch(MenuSlot|string $slot, array $context = []): ItemInterface
    {
        if (is_string($slot)) {
            $slot = MenuSlot::fromLegacy($slot);
        }
        
        $menu = $this->factory->createItem($slot->value);
        
        $event = new MenuEvent($slot, $menu, $this->factory, $context);
        $this->dispatcher->dispatch($event, $slot->eventName());
        
        return $menu;
    }
    
    public function hasItems(MenuSlot|string $slot, array $context = []): bool
    {
        return $this->dispatch($slot, $context)->hasChildren();
    }
}
