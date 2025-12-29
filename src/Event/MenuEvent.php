<?php
/* src/Event/MenuEvent.php v1.1 - Event dispatched for menu building with MenuSlot enum */

declare(strict_types=1);

namespace Survos\TablerBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Survos\TablerBundle\Menu\MenuSlot;
use Symfony\Contracts\EventDispatcher\Event;

class MenuEvent extends Event
{
    public function __construct(
        public readonly ItemInterface $menu,
        public readonly FactoryInterface $factory,
        public readonly MenuSlot $slot,
        private array $options = [],
    ) {}

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    public function set(string $key, mixed $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
