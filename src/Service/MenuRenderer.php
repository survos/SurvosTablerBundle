<?php
/* src/Service/MenuRenderer.php v1.0 - Renders menus for different slots */

declare(strict_types=1);

namespace Survos\TablerBundle\Service;

use Knp\Menu\ItemInterface;
use Knp\Menu\Twig\Helper;
use Survos\TablerBundle\Menu\MenuSlot;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuRenderer
{
    private const TEMPLATE_MAP = [
        'navbar' => 'tabler_navbar.html.twig',
        'navbar_secondary' => 'tabler_navbar.html.twig',
        'navbar_tertiary' => 'tabler_navbar.html.twig',
        'sidebar' => 'tabler_sidebar.html.twig',
        'footer' => 'tabler_footer.html.twig',
        'auth' => 'tabler_auth.html.twig',
        'page_actions' => 'tabler_actions.html.twig',
        'breadcrumb' => 'tabler_breadcrumb.html.twig',
        'search' => 'tabler_search.html.twig',
    ];

    public function __construct(
        private readonly MenuDispatcher $dispatcher,
        private readonly Helper $knpHelper,
        private readonly RequestStack $requestStack,
        private readonly string $templatePrefix = '@SurvosTabler/menu/',
    ) {}

    public function render(MenuSlot $slot, array $options = []): string
    {
        $menu = $this->dispatcher->dispatch($slot, $options);
        
        if (!$menu->hasChildren()) {
            return '';
        }

        $template = $this->templatePrefix . (self::TEMPLATE_MAP[$slot->value] ?? 'tabler_navbar.html.twig');
        
        return $this->knpHelper->render($menu, [
            'template' => $template,
            'allow_safe_labels' => true,
            'currentClass' => 'active',
        ]);
    }

    public function getMenu(MenuSlot $slot, array $options = []): ItemInterface
    {
        return $this->dispatcher->dispatch($slot, $options);
    }

    public function hasItems(MenuSlot $slot, array $options = []): bool
    {
        return $this->dispatcher->dispatch($slot, $options)->hasChildren();
    }
}
