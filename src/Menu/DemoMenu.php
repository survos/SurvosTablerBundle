<?php
/* src/Menu/DemoMenu.php v1.0 - Demo menu matching Tabler preview (enabled by ?menu_demo=1) */

declare(strict_types=1);

namespace Survos\TablerBundle\Menu;

use Survos\TablerBundle\Event\KnpMenuEvent;
use Survos\TablerBundle\Event\MenuEvent;
use Survos\TablerBundle\Service\IconService;
use Survos\TablerBundle\Service\RouteAliasService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class DemoMenu
{
    use MenuBuilderTrait;

    public function __construct(
        private readonly RequestStack $requestStack,
        protected ?RouterInterface $router = null,
        protected ?RouteAliasService $routeAliasService = null,
        protected ?IconService $iconService = null,
    ) {}

    private function isEnabled(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request?->query->getBoolean('menu_demo', false) ?? false;
    }

    private function shouldClear(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request?->query->getBoolean('menu_demo_clear', false) ?? false;
    }

    private function clearMenu(mixed $menu): void
    {
        if ($this->shouldClear()) {
            foreach ($menu->getChildren() as $name => $child) {
                $menu->removeChild($name);
            }
        }
    }

    #[AsEventListener(event: MenuSlot::NAVBAR_EVENT, priority: -100)]
    public function navbar(MenuEvent $event): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $menu = $event->getMenu();
        $this->clearMenu($menu);

        // Home
        $this->add($menu, uri: '/', label: 'Home', icon: 'tabler:home');

        // Interface dropdown
        $interface = $this->addSubmenu($menu, 'Interface', icon: 'tabler:package');
        $this->add($interface, uri: '/page/alerts', label: 'Alerts');
        $this->add($interface, uri: '/page/accordion', label: 'Accordion');

        // Authentication nested dropdown
        $auth = $this->addSubmenu($interface, 'Authentication');
        $this->add($auth, uri: '/page/sign-in', label: 'Sign in');
        $this->add($auth, uri: '/page/sign-in-link', label: 'Sign in link');
        $this->add($auth, uri: '/page/sign-in-illustration', label: 'Sign in with illustration');
        $this->add($auth, uri: '/page/sign-in-cover', label: 'Sign in with cover');
        $this->add($auth, uri: '/page/sign-up', label: 'Sign up');
        $this->add($auth, uri: '/page/forgot-password', label: 'Forgot password');
        $this->add($auth, uri: '/page/terms-of-service', label: 'Terms of service');
        $this->add($auth, uri: '/page/auth-lock', label: 'Lock screen');
        $this->add($auth, uri: '/page/2-step-verification', label: '2 step verification');
        $this->add($auth, uri: '/page/2-step-verification-code', label: '2 step verification code');

        $this->add($interface, uri: '/page/blank', label: 'Blank page');
        $this->add($interface, uri: '/page/badges', label: 'Badges');
        $this->add($interface, uri: '/page/buttons', label: 'Buttons');
        $this->add($interface, uri: '/page/cards', label: 'Cards');
        $this->add($interface, uri: '/page/carousel', label: 'Carousel');
        $this->add($interface, uri: '/page/charts', label: 'Charts');
        $this->add($interface, uri: '/page/colors', label: 'Colors');
        $this->add($interface, uri: '/page/colorpicker', label: 'Color picker');
        $this->add($interface, uri: '/page/datagrid', label: 'Data grid');
        $this->add($interface, uri: '/page/datatables', label: 'Datatables');
        $this->add($interface, uri: '/page/dropdowns', label: 'Dropdowns');
        $this->add($interface, uri: '/page/dropzone', label: 'Dropzone');

        // Error pages nested
        $errors = $this->addSubmenu($interface, 'Error pages');
        $this->add($errors, uri: '/page/error-404', label: '404 page');
        $this->add($errors, uri: '/page/error-500', label: '500 page');
        $this->add($errors, uri: '/page/error-maintenance', label: 'Maintenance');

        $this->add($interface, uri: '/page/flags', label: 'Flags');
        $this->add($interface, uri: '/page/form-elements', label: 'Form elements');
        $this->add($interface, uri: '/page/lightbox', label: 'Lightbox');
        $this->add($interface, uri: '/page/lists', label: 'Lists');
        $this->add($interface, uri: '/page/modals', label: 'Modal');
        $this->add($interface, uri: '/page/maps', label: 'Map');
        $this->add($interface, uri: '/page/map-fullsize', label: 'Map fullsize');
        $this->add($interface, uri: '/page/maps-vector', label: 'Map vector');
        $this->add($interface, uri: '/page/navigation', label: 'Navigation');
        $this->add($interface, uri: '/page/offcanvas', label: 'Offcanvas');
        $this->add($interface, uri: '/page/pagination', label: 'Pagination', icon: 'tabler:pie-chart');
        $this->add($interface, uri: '/page/placeholder', label: 'Placeholder');
        $this->add($interface, uri: '/page/steps', label: 'Steps');
        $this->add($interface, uri: '/page/stars-rating', label: 'Stars rating');
        $this->add($interface, uri: '/page/tabs', label: 'Tabs');
        $this->add($interface, uri: '/page/tags', label: 'Tags');
        $this->add($interface, uri: '/page/tables', label: 'Tables');
        $this->add($interface, uri: '/page/toasts', label: 'Toasts');
        $this->add($interface, uri: '/page/typography', label: 'Typography');
        $this->add($interface, uri: '/page/tinymce', label: 'TinyMCE');

        // Extra dropdown
        $extra = $this->addSubmenu($menu, 'Extra', icon: 'tabler:star');
        $this->add($extra, uri: '/page/empty', label: 'Empty page');
        $this->add($extra, uri: '/page/cookie-banner', label: 'Cookie banner');
        $this->add($extra, uri: '/page/chat', label: 'Chat');
        $this->add($extra, uri: '/page/activity', label: 'Activity');
        $this->add($extra, uri: '/page/gallery', label: 'Gallery');
        $this->add($extra, uri: '/page/invoice', label: 'Invoice');
        $this->add($extra, uri: '/page/search-results', label: 'Search results');
        $this->add($extra, uri: '/page/pricing', label: 'Pricing cards');
        $this->add($extra, uri: '/page/pricing-table', label: 'Pricing table');
        $this->add($extra, uri: '/page/faq', label: 'FAQ');
        $this->add($extra, uri: '/page/users', label: 'Users');
        $this->add($extra, uri: '/page/license', label: 'License');
        $this->add($extra, uri: '/page/logs', label: 'Logs');
        $this->add($extra, uri: '/page/music', label: 'Music');
        $this->add($extra, uri: '/page/photogrid', label: 'Photogrid');
        $this->add($extra, uri: '/page/tasks', label: 'Tasks');
        $this->add($extra, uri: '/page/uptime', label: 'Uptime monitor');
        $this->add($extra, uri: '/page/widgets', label: 'Widgets');
        $this->add($extra, uri: '/page/wizard', label: 'Wizard');
        $this->add($extra, uri: '/page/settings', label: 'Settings');
        $this->add($extra, uri: '/page/trial-ended', label: 'Trial ended');
        $this->add($extra, uri: '/page/job-listing', label: 'Job listing');
        $this->add($extra, uri: '/page/page-loader', label: 'Page loader');

        // Dashboard
        $this->add($menu, uri: '/', label: 'Dashboard', icon: 'tabler:home');
    }

    #[AsEventListener(event: MenuSlot::NAVBAR_SECONDARY_EVENT, priority: -100)]
    public function navbarSecondary(MenuEvent $event): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $menu = $event->menu;
        $this->clearMenu($menu);

        // Layout dropdown
        $layout = $this->addSubmenu($menu, 'Layout', icon: 'tabler:layout-2');
        $this->add($layout, uri: '/page/layout-horizontal', label: 'Horizontal');
        $this->add($layout, uri: '/page/layout-vertical', label: 'Vertical');
        $this->add($layout, uri: '/page/layout-vertical-transparent', label: 'Vertical transparent');
        $this->add($layout, uri: '/page/layout-vertical-right', label: 'Vertical right');
        $this->add($layout, uri: '/page/layout-condensed', label: 'Condensed');
        $this->add($layout, uri: '/page/layout-combo', label: 'Combo');
        $this->add($layout, uri: '/page/layout-navbar-dark', label: 'Navbar dark');
        $this->add($layout, uri: '/page/layout-navbar-sticky', label: 'Navbar sticky');
        $this->add($layout, uri: '/page/layout-navbar-overlap', label: 'Navbar overlap');
        $this->add($layout, uri: '/page/layout-rtl', label: 'RTL mode');
        $this->add($layout, uri: '/page/layout-fluid', label: 'Fluid');
        $this->add($layout, uri: '/page/layout-fluid-vertical', label: 'Fluid vertical');

        // Icons (with count placeholder)
        $this->add($menu, uri: '/page/icons', label: '5237 icons', icon: 'tabler:ghost');

        // Emails
        $this->add($menu, uri: '/page/emails', label: 'Emails', icon: 'tabler:mail-opened');

        // Illustrations
        $this->add($menu, uri: '/page/illustrations', label: 'Illustrations', icon: 'tabler:brand-figma');

        // Help dropdown
        $help = $this->addSubmenu($menu, 'Help', icon: 'tabler:lifebuoy');
        $this->add($help, uri: '/page/docs', label: 'Documentation');
        $this->add($help, uri: '/page/changelog', label: 'Changelog');
        $this->add($help, uri: 'https://github.com/tabler/tabler', label: 'Source code', external: true);
        $this->add($help, uri: 'https://github.com/tabler/tabler/issues', label: 'Report an issue', external: true);
    }

    #[AsEventListener(event: MenuSlot::FOOTER_EVENT, priority: -100)]
    public function footer(MenuEvent $event): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $menu = $event->getMenu();
        $this->clearMenu($menu);

        $this->add($menu, uri: '/', label: 'Home', icon: 'tabler:home');
        $this->add($menu, uri: 'https://github.com/survos', label: 'GitHub', icon: 'tabler:brand-github', external: true);
        $this->add($menu, uri: '/data', label: 'Data');
        $this->add($menu, uri: '/menu', label: 'Menu', icon: 'tabler:menu');
        $this->add($menu, uri: 'https://tabler.io', label: 'Tabler.io', icon: 'tabler:external-link', external: true);
    }
}
