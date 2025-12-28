<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Menu;

use Survos\TablerBundle\Event\MenuEvent;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Populates menus with demo items when ?menu_demo=1 is present.
 * Useful for testing layouts without app-specific menu listeners.
 * 
 * Runs with low priority to override app listeners, or can clear existing items.
 */
#[Autoconfigure(tags: ['survos_tabler.demo_menu'])]
final class DemoMenu
{
    use MenuBuilderTrait;
    
    private bool $enabled = false;
    private bool $clearExisting = false;
    
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {}
    
    private function shouldRun(): bool
    {
        if ($this->enabled) {
            return true;
        }
        
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }
        
        $this->clearExisting = $request->query->getBoolean('menu_demo_clear', false);
        return $request->query->getBoolean('menu_demo', false);
    }
    
    #[AsEventListener(event: MenuSlot::NAVBAR_EVENT, priority: -100)]
    public function navbar(MenuEvent $event): void
    {
        if (!$this->shouldRun()) return;
        
        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }
        
        $this->add($menu, uri: '#', label: 'Home', icon: 'tabler:home');
        $this->add($menu, uri: '#', label: 'Products', icon: 'tabler:box');
        
        $more = $this->addSubmenu($menu, 'More', icon: 'tabler:dots');
        $this->add($more, uri: '#', label: 'Settings');
        $this->add($more, uri: '#', label: 'Help');
    }
    
    #[AsEventListener(event: MenuSlot::NAVBAR_SECONDARY_EVENT, priority: -100)]
    public function navbarSecondary(MenuEvent $event): void
    {
        if (!$this->shouldRun()) return;
        
        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }
        
        $this->add($menu, uri: '#', label: 'Docs', icon: 'tabler:book');
        $this->add($menu, uri: '#', label: 'API', icon: 'tabler:code');
    }
    
    #[AsEventListener(event: MenuSlot::AUTH_EVENT, priority: -100)]
    public function auth(MenuEvent $event): void
    {
        if (!$this->shouldRun()) return;
        
        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }
        
        $user = $this->addSubmenu($menu, 'demo@example.com', icon: 'tabler:user');
        $this->add($user, uri: '#', label: 'Profile', icon: 'tabler:user-circle');
        $this->add($user, uri: '#', label: 'Settings', icon: 'tabler:settings');
        $this->addDivider($user);
        $this->add($user, uri: '#', label: 'Logout', icon: 'tabler:logout');
    }
    
    #[AsEventListener(event: MenuSlot::SIDEBAR_EVENT, priority: -100)]
    public function sidebar(MenuEvent $event): void
    {
        if (!$this->shouldRun()) return;
        
        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }
        
        $this->addHeading($menu, 'Main');
        $this->add($menu, uri: '#', label: 'Dashboard', icon: 'tabler:home');
        $this->add($menu, uri: '#', label: 'Analytics', icon: 'tabler:chart-bar');
        
        $this->addHeading($menu, 'Content');
        $this->add($menu, uri: '#', label: 'Pages', icon: 'tabler:file');
        $this->add($menu, uri: '#', label: 'Media', icon: 'tabler:photo');
        
        $nested = $this->addSubmenu($menu, 'Settings', icon: 'tabler:settings');
        $this->add($nested, uri: '#', label: 'General');
        $this->add($nested, uri: '#', label: 'Security');
        $this->add($nested, uri: '#', label: 'Notifications');
    }
    
    #[AsEventListener(event: MenuSlot::FOOTER_EVENT, priority: -100)]
    public function footer(MenuEvent $event): void
    {
        if (!$this->shouldRun()) return;
        
        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }
        
        $this->add($menu, uri: '#', label: 'About');
        $this->add($menu, uri: '#', label: 'Privacy');
        $this->add($menu, uri: '#', label: 'Terms');
        $this->add($menu, uri: 'https://github.com', label: 'GitHub', icon: 'tabler:brand-github');
    }
    
    #[AsEventListener(event: MenuSlot::PAGE_ACTIONS_EVENT, priority: -100)]
    public function pageActions(MenuEvent $event): void
    {
        if (!$this->shouldRun()) return;
        
        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }
        
        $this->add($menu, uri: '#', label: 'Export', icon: 'tabler:download');
        $this->add($menu, uri: '#', label: 'Create New', icon: 'tabler:plus');
    }
    
    #[AsEventListener(event: MenuSlot::BREADCRUMB_EVENT, priority: -100)]
    public function breadcrumb(MenuEvent $event): void
    {
        if (!$this->shouldRun()) return;
        
        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }
        
        $this->add($menu, uri: '#', label: 'Home');
        $this->add($menu, uri: '#', label: 'Products');
        $this->add($menu, uri: '#', label: 'Current Page');
    }
}
