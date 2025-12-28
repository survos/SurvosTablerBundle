<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Service;

use Knp\Menu\ItemInterface;
use Knp\Menu\Twig\Helper;
use Survos\TablerBundle\Menu\MenuSlot;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Centralized menu rendering with slot-specific configuration.
 */
final class MenuRenderer
{
    public function __construct(
        private readonly MenuDispatcher $dispatcher,
        private readonly Helper $knpHelper,
        private readonly RequestStack $requestStack,
        private readonly string $templatePrefix = '@SurvosTabler/menu/',
    ) {}
    
    public function render(MenuSlot|string $slot, array $context = []): string
    {
        if (is_string($slot)) {
            $slot = MenuSlot::fromLegacy($slot);
        }
        
        $menu = $this->dispatcher->dispatch($slot, $context);
        
        if (!$menu->hasChildren()) {
            return $this->wrapDebug($slot, '', 0);
        }
        
        $config = $this->getSlotConfig($slot);
        $html = $this->knpHelper->render($menu, $config['options']);
        
        return $this->wrapDebug($slot, $html, count($menu->getChildren()));
    }
    
    public function hasItems(MenuSlot|string $slot, array $context = []): bool
    {
        return $this->dispatcher->dispatch($slot, $context)->hasChildren();
    }
    
    public function getMenu(MenuSlot|string $slot, array $context = []): ItemInterface
    {
        return $this->dispatcher->dispatch($slot, $context);
    }
    
    private function getSlotConfig(MenuSlot $slot): array
    {
        return match ($slot) {
            MenuSlot::Navbar,
            MenuSlot::NavbarSecondary,
            MenuSlot::NavbarTertiary => [
                'options' => [
                    'template' => $this->templatePrefix . 'navbar.html.twig',
                    'currentClass' => 'active',
                    'ancestorClass' => 'active',
                    'allow_safe_labels' => true,
                    'rootAttributes' => ['class' => 'navbar-nav'],
                ],
            ],
            
            MenuSlot::Sidebar => [
                'options' => [
                    'template' => $this->templatePrefix . 'sidebar.html.twig',
                    'currentClass' => 'active',
                    'ancestorClass' => 'active',
                    'allow_safe_labels' => true,
                    'rootAttributes' => ['class' => 'navbar-nav pt-lg-3'],
                ],
            ],
            
            MenuSlot::Auth => [
                'options' => [
                    'template' => $this->templatePrefix . 'auth.html.twig',
                    'allow_safe_labels' => true,
                    'rootAttributes' => ['class' => 'navbar-nav'],
                ],
            ],
            
            MenuSlot::Footer => [
                'options' => [
                    'template' => $this->templatePrefix . 'footer.html.twig',
                    'allow_safe_labels' => true,
                    'rootAttributes' => ['class' => 'list-inline list-inline-dots mb-0'],
                ],
            ],
            
            MenuSlot::PageActions => [
                'options' => [
                    'template' => $this->templatePrefix . 'actions.html.twig',
                    'allow_safe_labels' => true,
                    'rootAttributes' => ['class' => 'btn-list'],
                ],
            ],
            
            MenuSlot::Breadcrumb => [
                'options' => [
                    'template' => $this->templatePrefix . 'breadcrumb.html.twig',
                    'allow_safe_labels' => true,
                    'rootAttributes' => ['class' => 'breadcrumb', 'aria-label' => 'breadcrumbs'],
                ],
            ],
            
            MenuSlot::Search => [
                'options' => [
                    'template' => $this->templatePrefix . 'search.html.twig',
                    'allow_safe_labels' => true,
                ],
            ],
        };
    }
    
    private function isDebug(): bool
    {
        return $this->requestStack->getCurrentRequest()
            ?->query->getBoolean('menu_debug', false) ?? false;
    }
    
    private function wrapDebug(MenuSlot $slot, string $html, int $count): string
    {
        if (!$this->isDebug()) {
            return $html;
        }
        
        $hasContent = $count > 0;
        $borderColor = $hasContent ? '#4caf50' : '#ff9800';
        $bgColor = $hasContent ? 'rgba(76, 175, 80, 0.08)' : 'rgba(255, 152, 0, 0.08)';
        
        return sprintf(
            '<div style="border: 2px dashed %s; background: %s; position: relative; min-height: 28px; padding: 2px;">
                <span style="position: absolute; top: -9px; left: 8px; background: %s; padding: 1px 6px; font-family: ui-monospace, monospace; font-size: 10px; color: #fff; border-radius: 3px; line-height: 1.4;">
                    %s (%d)
                </span>
                %s
            </div>',
            $borderColor,
            $bgColor,
            $borderColor,
            $slot->value,
            $count,
            $html
        );
    }
}
