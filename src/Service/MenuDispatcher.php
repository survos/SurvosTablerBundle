<?php
/* src/Service/MenuDispatcher.php v1.0 - Dispatches menu slot events */

declare(strict_types=1);

namespace Survos\TablerBundle\Service;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Survos\TablerBundle\Event\MenuEvent;
use Survos\TablerBundle\Menu\MenuSlot;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuDispatcher
{
    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly EventDispatcherInterface $dispatcher,  // Matches bundle definition
    ) {}

    public function dispatch(MenuSlot $slot, array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $event = new MenuEvent($menu, $this->factory, $slot, $options);
        $this->dispatcher->dispatch($event, $slot->eventName());

        return $menu;
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }
}
