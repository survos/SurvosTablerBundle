<?php
/* src/Twig/MenuExtension.php v1.0 - Twig functions for menu rendering */

declare(strict_types=1);

namespace Survos\TablerBundle\Twig;

use Knp\Menu\ItemInterface;
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
            new TwigFunction('tabler_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
            new TwigFunction('tabler_menu_has_items', [$this, 'hasItems']),
        ];
    }

    public function renderMenu(string $slot, array $options = []): string
    {
        return $this->renderer->render($slot, $options);
    }

    public function hasItems(string $slot, array $options = []): bool
    {
        return $this->renderer->hasItems($slot, $options);
    }
}
