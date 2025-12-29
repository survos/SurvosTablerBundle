<?php
/* src/Twig/MenuGlobalsExtension.php v1.2 - Exposes MenuSlot enum cases to Twig globals */

declare(strict_types=1);

namespace Survos\TablerBundle\Twig;

use Survos\TablerBundle\Menu\MenuSlot;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class MenuGlobalsExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        // Create stdClass to allow MenuSlot.Navbar syntax in Twig
        $menuSlotProxy = new \stdClass();
        
        foreach (MenuSlot::cases() as $case) {
            $menuSlotProxy->{$case->name} = $case;
        }

        return [
            'MenuSlot' => $menuSlotProxy,
        ];
    }
}
