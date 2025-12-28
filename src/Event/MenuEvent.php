<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Survos\TablerBundle\Menu\MenuSlot;
use Symfony\Contracts\EventDispatcher\Event;

final class MenuEvent extends Event
{
    public function __construct(
        public readonly MenuSlot $slot,
        public readonly ItemInterface $menu,
        public readonly FactoryInterface $factory,
        private array $context = [],
    ) {}
    
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->context[$key] ?? $default;
    }
    
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->context);
    }
    
    public function all(): array
    {
        return $this->context;
    }
}
