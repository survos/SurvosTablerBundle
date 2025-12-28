<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Menu;

use Knp\Menu\ItemInterface;
use Survos\TablerBundle\Service\IconAliasService;
use Survos\TablerBundle\Service\RouteAliasService;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Menu builder trait with route and icon alias support.
 */
trait MenuBuilderTrait
{
    private ?AsciiSlugger $slugger = null;
    
    // Inject via constructor in implementing class
    protected ?RouteAliasService $routeAliasService = null;
    protected ?IconAliasService $iconAliasService = null;
    
    private function slugger(): AsciiSlugger
    {
        return $this->slugger ??= new AsciiSlugger();
    }
    
    /**
     * Resolve icon alias to full icon name.
     */
    protected function icon(string $alias): string
    {
        return $this->iconAliasService?->resolve($alias) ?? $alias;
    }
    
    /**
     * Add menu item. Automatically resolves icon aliases.
     */
    protected function add(
        ItemInterface $menu,
        ?string $route = null,
        array $rp = [],
        ?string $label = null,
        ?string $uri = null,
        ?string $icon = null,
        string|int|null $badge = null,
        bool $external = false,
        bool $dividerBefore = false,
        bool $dividerAfter = false,
        ?string $translationDomain = 'messages',
        bool $if = true,
    ): ItemInterface {
        if (!$if) {
            return $menu;
        }
        
        // Skip if route doesn't exist
        if ($route && !$this->routeExists($route)) {
            return $menu;
        }
        
        $label ??= $this->routeToLabel($route ?? $uri ?? '');
        $id = $this->slugger()->slug($label ?: 'item')->toString() . '_' . bin2hex(random_bytes(4));
        
        $child = $menu->addChild($id, array_filter([
            'route' => $route,
            'routeParameters' => $rp ?: null,
            'uri' => $uri,
            'label' => $label,
        ], fn($v) => $v !== null));
        
        // Resolve icon alias
        if ($icon) {
            $child->setExtra('icon', $this->icon($icon));
        }
        
        if ($badge !== null) {
            $child->setExtra('badge', $badge);
        }
        
        if ($external || ($uri && str_starts_with($uri, 'http'))) {
            $child->setLinkAttribute('target', '_blank');
            $child->setExtra('icon', $child->getExtra('icon') ?? $this->icon('external'));
        }
        
        if ($dividerBefore) {
            $child->setAttribute('divider_prepend', true);
        }
        
        if ($dividerAfter) {
            $child->setAttribute('divider_append', true);
        }
        
        if ($translationDomain) {
            $child->setExtra('translation_domain', $translationDomain);
        }
        
        $child->setExtra('safe_label', true);
        
        return $child;
    }
    
    /**
     * Add item using a route alias with automatic icon inference.
     */
    protected function addAliased(
        ItemInterface $menu,
        string $alias,
        array $rp = [],
        ?string $label = null,
        ?string $icon = null,
    ): ItemInterface {
        if (!$this->routeAliasService?->has($alias)) {
            return $menu;
        }
        
        $route = $this->routeAliasService->get($alias);
        $label ??= ucfirst($alias);
        
        // Use alias as icon if icon service has it and no explicit icon given
        $icon ??= $this->iconAliasService?->has($alias) ? $alias : null;
        
        return $this->add($menu, $route, $rp, $label, icon: $icon);
    }
    
    protected function addSubmenu(
        ItemInterface $menu,
        string $label,
        ?string $icon = null,
        ?string $translationDomain = 'messages',
    ): ItemInterface {
        $id = $this->slugger()->slug($label)->toString() . '_' . bin2hex(random_bytes(4));
        
        $child = $menu->addChild($id, ['label' => $label]);
        $child->setExtra('submenu', true);
        $child->setExtra('safe_label', true);
        
        if ($icon) {
            $child->setExtra('icon', $this->icon($icon));
        }
        
        if ($translationDomain) {
            $child->setExtra('translation_domain', $translationDomain);
        }
        
        return $child;
    }
    
    protected function addHeading(
        ItemInterface $menu,
        string $label,
        ?string $icon = null,
    ): ItemInterface {
        $child = $this->add($menu, label: $label, icon: $icon);
        $child->setExtra('heading', true);
        $child->setAttribute('class', 'menu-heading');
        return $child;
    }
    
    protected function addDivider(ItemInterface $menu): ItemInterface
    {
        $child = $menu->addChild('divider_' . bin2hex(random_bytes(4)));
        $child->setExtra('divider', true);
        return $child;
    }
    
    protected function routeExists(string $route): bool
    {
        return true; // Override in implementing class for validation
    }
    
    private function routeToLabel(string $route): string
    {
        if (!$route) {
            return '';
        }
        $label = preg_replace('/^(app_|admin_|survos_)/', '', $route);
        $label = preg_replace('/_(index|show|edit|new)$/', '', $label);
        return ucwords(str_replace('_', ' ', $label));
    }
}
