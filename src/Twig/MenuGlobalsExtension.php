<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Twig;

use Survos\TablerBundle\Menu\MenuSlot;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Exposes MenuSlot enum and legacy constants to Twig templates.
 */
final class MenuGlobalsExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        $globals = [];
        
        // Expose enum cases as MenuSlot.Navbar, MenuSlot.Sidebar, etc.
        $menuSlotProxy = new \stdClass();
        foreach (MenuSlot::cases() as $case) {
            $menuSlotProxy->{$case->name} = $case;
        }
        $globals['MenuSlot'] = $menuSlotProxy;
        
        // Expose event constants as top-level globals for legacy compatibility
        // NAVBAR_MENU, SIDEBAR_MENU, etc.
        $reflection = new \ReflectionEnum(MenuSlot::class);
        foreach ($reflection->getConstants() as $name => $value) {
            if (is_string($value)) {
                $globals[$name] = $value;
            }
        }
        
        return $globals;
    }
}
