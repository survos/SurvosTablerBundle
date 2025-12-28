<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Twig;

use Knp\Menu\ItemInterface;
use Survos\TablerBundle\Menu\MenuSlot;
use Survos\TablerBundle\Service\MenuRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class MenuExtension extends AbstractExtension
{
    public function __construct(
        private readonly MenuRenderer $renderer,
    ) {}
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('tabler_menu', $this->render(...), ['is_safe' => ['html']]),
            new TwigFunction('tabler_menu_has_items', $this->hasItems(...)),
            new TwigFunction('tabler_menu_get', $this->getMenu(...)),
        ];
    }
    
    public function render(MenuSlot|string $slot, array $context = []): string
    {
        return $this->renderer->render($slot, $context);
    }
    
    public function hasItems(MenuSlot|string $slot, array $context = []): bool
    {
        return $this->renderer->hasItems($slot, $context);
    }
    
    public function getMenu(MenuSlot|string $slot, array $context = []): ItemInterface
    {
        return $this->renderer->getMenu($slot, $context);
    }
}
