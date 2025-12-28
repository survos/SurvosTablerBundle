<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Components;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('tabler:page', template: '@SurvosTabler/components/page.html.twig')]
final class TablerPage
{
    private const array LAYOUT_DEFAULTS = [
        'content'   => ['navbar' => true,  'sidebar' => false, 'header' => true,  'footer' => true],
        'dashboard' => ['navbar' => true,  'sidebar' => true,  'header' => true,  'footer' => true],
        'search'    => ['navbar' => true,  'sidebar' => false, 'header' => false, 'footer' => true],
        'minimal'   => ['navbar' => false, 'sidebar' => false, 'header' => false, 'footer' => false],
    ];

    /** Layout preset: content, dashboard, search, minimal */
    public string $layout = 'dashboard';

    /** Translation key prefix — if set, resolves title/pretitle/subtitle/content from translations */
    public ?string $transPrefix = null;

    /** Explicit overrides (bypass translation lookup) */
    public ?string $title = null;
    public ?string $pretitle = null;
    public ?string $subtitle = null;
    public ?string $content = null;
    public ?string $icon = null;

    /** Section visibility overrides (null = use layout default) */
    public ?bool $showNavbar = null;
    public ?bool $showSidebar = null;
    public ?bool $showHeader = null;
    public ?bool $showFooter = null;

    /** KnpMenu codes */
    public string $navbarMenu = 'navbar_main';
    public string $sidebarMenu = 'sidebar_main';
    public string $actionsMenu = 'page_actions';

    /** AssetMapper entrypoint */
    public string $entrypoint = 'app';

    /** Extra body classes */
    public string $bodyClass = '';

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    #[ExposeInTemplate]
    public string $resolvedTitle {
        get => $this->resolve('title') ?? '';
    }

    #[ExposeInTemplate]
    public ?string $resolvedPretitle {
        get => $this->resolve('pretitle');
    }

    #[ExposeInTemplate]
    public ?string $resolvedSubtitle {
        get => $this->resolve('subtitle');
    }

    #[ExposeInTemplate]
    public ?string $resolvedContent {
        get => $this->resolve('content');
    }

    public function shouldShow(string $section): bool
    {
        $prop = 'show' . ucfirst($section);
        
        if ($this->$prop !== null) {
            return $this->$prop;
        }

        return self::LAYOUT_DEFAULTS[$this->layout][$section] ?? true;
    }

    private function resolve(string $key): ?string
    {
        // Explicit prop wins
        if ($this->$key !== null) {
            return $this->$key;
        }

        // Try translation
        if ($this->transPrefix) {
            $transKey = "{$this->transPrefix}.{$key}";
            $translated = $this->translator->trans($transKey);
            
            // translator returns the key if no translation found
            return $translated !== $transKey ? $translated : null;
        }

        return null;
    }
}
