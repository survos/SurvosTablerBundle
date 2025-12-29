<?php
/* src/Components/MenuComponent.php v2.0 - Menu component using MenuSlot events (not final - can be extended) */

declare(strict_types=1);

namespace Survos\TablerBundle\Components;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Twig\Helper;
use Survos\TablerBundle\Event\KnpMenuEvent;
use Survos\TablerBundle\Menu\MenuSlot;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('menu', template: '@SurvosTabler/components/menu.html.twig')]
class MenuComponent
{
    public function __construct(
        private array $menuOptions,
        protected Helper $helper,
        protected FactoryInterface $factory,
        protected EventDispatcherInterface $eventDispatcher,
    ) {}

    public ?string $title = null;
    public ?string $caller = null;

    #[ExposeInTemplate]
    public string $type; // shortcut

    public string $eventName = '';

    public string $menuAlias = KnpMenuEvent::class;

    public array $path = [];

    public array $options = [];

    public bool $print = false;

    public ItemInterface $menuItem;

    public string|bool|null $translationDomain = false;

    public string $wrapperClass = '';

    public function mount(string $type, ?string $caller = null, array $path = [], array $options = []): void
    {
        assert($caller);
        $this->type = $type;
        $this->path = $path;
        $this->options = $options;
        
        // Support both MenuSlot constants and legacy KnpMenuEvent constants
        $eventName = $this->resolveEventName($type);

        $menu = $this->factory->createItem($options['name'] ?? KnpMenuEvent::class);

        $options = (new OptionsResolver())
            ->setDefaults($this->menuOptions)
            ->resolve($options);
        $options['caller'] = $caller;

        // Dispatch both old and new event formats for compatibility
        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options), $eventName);
        $this->menuItem = $this->helper->get($menu, $path, $options);
    }

    private function resolveEventName(string $type): string
    {
        // Try MenuSlot constant first
        if (defined(MenuSlot::class . '::' . $type)) {
            return constant(MenuSlot::class . '::' . $type);
        }
        
        // Fall back to KnpMenuEvent constant
        if (defined(KnpMenuEvent::class . '::' . $type)) {
            return constant(KnpMenuEvent::class . '::' . $type);
        }
        
        // Assume it's already an event name
        return $type;
    }

    public function hasItems(): bool
    {
        return $this->menuItem->hasChildren();
    }
}
