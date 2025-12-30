<?php
/* src/Components/MenuComponent.php v1.1 - Menu component that dispatches events */

declare(strict_types=1);

namespace Survos\TablerBundle\Components;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Twig\Helper;
use Survos\TablerBundle\Event\MenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('tabler:menu', template: '@SurvosTabler/components/menu.html.twig')]
class MenuComponent
{
    public string $type;
    public ?string $caller = null;
    public array $path = [];
    public array $options = [];

    #[ExposeInTemplate]
    public ItemInterface $menuItem;

    public function __construct(
        private readonly array $menuOptions,
        private readonly Helper $helper,
        private readonly FactoryInterface $factory,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function mount(
        string $type,
        ?string $caller = null,
        array $path = [],
        array $options = [],
    ): void {
        $this->type = $type;
        $this->caller = $caller;
        $this->path = $path;

        // Merge default menuOptions with passed options
        $this->options = array_merge($this->menuOptions, $options);
        $this->options['caller'] = $caller;
        $this->options['type'] = $type;

        // Convert type to event name: 'NAVBAR_MENU' → 'tabler.menu.navbar_menu'
        $eventName = $type;

        // Create root menu
        $menu = $this->factory->createItem('menu');

        // Dispatch event to let listeners add items
        $event = new MenuEvent($menu, $this->factory, $this->options);
        dd($event);
        $this->eventDispatcher->dispatch($event, $eventName);

        // Use helper to navigate path (for breadcrumbs, submenus)
        $this->menuItem = $this->helper->get($menu, $path, $this->options);
    }

    #[ExposeInTemplate]
    public function hasChildren(): bool
    {
        return $this->menuItem->hasChildren();
    }
}
