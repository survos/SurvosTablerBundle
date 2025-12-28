<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Menu;

use Survos\TablerBundle\Event\MenuEvent;
use Survos\TablerBundle\Service\IconService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Populates menus with demo items when ?menu_demo=1 is present.
 * Useful for testing layouts without app-specific menu listeners.
 */
final class DemoMenu
{
    use MenuBuilderTrait;

    private bool $clearExisting = false;

    public function __construct(
        private readonly RequestStack $requestStack,
        ?RouterInterface $router = null,
        ?IconService $iconService = null,
    ) {
        $this->router = $router;
        $this->iconService = $iconService;
    }

    private function shouldRun(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        if (!$request->query->getBoolean('menu_demo', false)) {
            return false;
        }

        $this->clearExisting = $request->query->getBoolean('menu_demo_clear', false);
        return true;
    }

    #[AsEventListener(event: MenuSlot::NAVBAR_EVENT, priority: -100)]
    public function navbar(MenuEvent $event): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }

        $this->add($menu, uri: '#', label: 'Home', icon: 'home');
        $this->add($menu, uri: '#', label: 'Products', icon: 'box');

        $more = $this->addSubmenu($menu, 'More', icon: 'dots');
        $this->add($more, uri: '#', label: 'Settings', icon: 'settings');
        $this->add($more, uri: '#', label: 'Help', icon: 'help');
    }

    #[AsEventListener(event: MenuSlot::NAVBAR_SECONDARY_EVENT, priority: -100)]
    public function navbarSecondary(MenuEvent $event): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }

        $this->add($menu, uri: '#', label: 'Docs', icon: 'book');
        $this->add($menu, uri: '#', label: 'API', icon: 'api');
    }

    #[AsEventListener(event: MenuSlot::AUTH_EVENT, priority: -100)]
    public function auth(MenuEvent $event): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }

        $user = $this->addSubmenu($menu, 'demo@example.com', icon: 'user');
        $this->add($user, uri: '#', label: 'Profile', icon: 'profile');
        $this->add($user, uri: '#', label: 'Settings', icon: 'settings');
        $this->addDivider($user);
        $this->add($user, uri: '#', label: 'Logout', icon: 'logout');
    }

    #[AsEventListener(event: MenuSlot::SIDEBAR_EVENT, priority: -100)]
    public function sidebar(MenuEvent $event): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }

        $this->addHeading($menu, 'Main');
        $this->add($menu, uri: '#', label: 'Dashboard', icon: 'dashboard');
        $this->add($menu, uri: '#', label: 'Analytics', icon: 'chart-bar');

        $this->addHeading($menu, 'Content');
        $this->add($menu, uri: '#', label: 'Pages', icon: 'file');
        $this->add($menu, uri: '#', label: 'Media', icon: 'photo');

        $nested = $this->addSubmenu($menu, 'Settings', icon: 'settings');
        $this->add($nested, uri: '#', label: 'General');
        $this->add($nested, uri: '#', label: 'Security');
    }

    #[AsEventListener(event: MenuSlot::FOOTER_EVENT, priority: -100)]
    public function footer(MenuEvent $event): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }

        $this->add($menu, uri: '#', label: 'About');
        $this->add($menu, uri: '#', label: 'Privacy');
        $this->add($menu, uri: '#', label: 'Terms');
        $this->add($menu, uri: 'https://github.com', label: 'GitHub', icon: 'github');
    }

    #[AsEventListener(event: MenuSlot::PAGE_ACTIONS_EVENT, priority: -100)]
    public function pageActions(MenuEvent $event): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }

        $this->add($menu, uri: '#', label: 'Export', icon: 'download');
        $this->add($menu, uri: '#', label: 'Create New', icon: 'plus');
    }

    #[AsEventListener(event: MenuSlot::BREADCRUMB_EVENT, priority: -100)]
    public function breadcrumb(MenuEvent $event): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $menu = $event->menu;
        if ($this->clearExisting) {
            $menu->setChildren([]);
        }

        $this->add($menu, uri: '#', label: 'Home');
        $this->add($menu, uri: '#', label: 'Products');
        $this->add($menu, uri: '#', label: 'Current Page');
    }
}
