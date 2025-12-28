<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Menu;

enum MenuSlot: string
{
    case Navbar = 'navbar';
    case NavbarSecondary = 'navbar_secondary';
    case NavbarTertiary = 'navbar_tertiary';
    case Sidebar = 'sidebar';
    case Footer = 'footer';
    case Auth = 'auth';
    case PageActions = 'page_actions';
    case Breadcrumb = 'breadcrumb';
    case Search = 'search';
    
    // Event name constants for #[AsEventListener] attributes
    public const NAVBAR_EVENT = 'tabler.menu.navbar';
    public const NAVBAR_SECONDARY_EVENT = 'tabler.menu.navbar_secondary';
    public const NAVBAR_TERTIARY_EVENT = 'tabler.menu.navbar_tertiary';
    public const SIDEBAR_EVENT = 'tabler.menu.sidebar';
    public const FOOTER_EVENT = 'tabler.menu.footer';
    public const AUTH_EVENT = 'tabler.menu.auth';
    public const PAGE_ACTIONS_EVENT = 'tabler.menu.page_actions';
    public const BREADCRUMB_EVENT = 'tabler.menu.breadcrumb';
    public const SEARCH_EVENT = 'tabler.menu.search';
    
    // Legacy aliases
    public const NAVBAR_MENU = self::NAVBAR_EVENT;
    public const NAVBAR_MENU2 = self::NAVBAR_SECONDARY_EVENT;
    public const NAVBAR_MENU3 = self::NAVBAR_TERTIARY_EVENT;
    public const SIDEBAR_MENU = self::SIDEBAR_EVENT;
    public const FOOTER_MENU = self::FOOTER_EVENT;
    public const AUTH_MENU = self::AUTH_EVENT;
    public const PAGE_MENU = self::PAGE_ACTIONS_EVENT;
    public const BREADCRUMB_MENU = self::BREADCRUMB_EVENT;
    public const SEARCH_MENU = self::SEARCH_EVENT;
    
    public function eventName(): string
    {
        return 'tabler.menu.' . $this->value;
    }
    
    public static function fromLegacy(string $legacy): self
    {
        return match ($legacy) {
            'NAVBAR_MENU', 'navbar' => self::Navbar,
            'NAVBAR_MENU2', 'navbar_secondary' => self::NavbarSecondary,
            'NAVBAR_MENU3', 'navbar_tertiary' => self::NavbarTertiary,
            'SIDEBAR_MENU', 'sidebar' => self::Sidebar,
            'FOOTER_MENU', 'footer' => self::Footer,
            'AUTH_MENU', 'auth' => self::Auth,
            'PAGE_MENU', 'page_actions' => self::PageActions,
            'BREADCRUMB_MENU', 'breadcrumb' => self::Breadcrumb,
            'SEARCH_MENU', 'search' => self::Search,
            default => throw new \InvalidArgumentException("Unknown menu slot: $legacy"),
        };
    }
}
