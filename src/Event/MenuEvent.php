<?php
/* src/Event/MenuEvent.php v2.0 - Simplified: constant value = constant name */

declare(strict_types=1);

namespace Survos\TablerBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MenuEvent extends Event
{
    // Menu slot constants - value equals name for simplicity
    public const BANNER = 'BANNER';
    public const NAVBAR_END = 'NAVBAR_END';
    public const NAVBAR_MENU = 'NAVBAR_MENU';
    public const NAVBAR_MENU_END = 'NAVBAR_MENU_END';
    public const SIDEBAR = 'SIDEBAR';
    public const PAGE_ACTIONS = 'PAGE_ACTIONS';
    public const BREADCRUMB = 'BREADCRUMB';
    public const FOOTER = 'FOOTER';
    public const FOOTER_END = 'FOOTER_END';
    public const AUTH = 'AUTH';
    public const SEARCH = 'SEARCH';

    public function __construct(
        public readonly ItemInterface $menu,
        public readonly FactoryInterface $factory,
        public readonly array $options = [],
        public readonly array $childOptions = [],
    ) {}

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }

    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Get all menu slot constants as name => value pairs
     */
    public static function getConstants(): array
    {
        $reflection = new \ReflectionClass(__CLASS__);
        $constants = [];
        foreach ($reflection->getConstants() as $name => $value) {
            // Only include our menu constants (uppercase, value matches name)
            if ($name === $value && preg_match('/^[A-Z_]+$/', $name)) {
                $constants[$name] = $value;
            }
        }
        return $constants;
    }
}
