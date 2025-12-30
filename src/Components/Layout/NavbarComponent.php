<?php
/* src/Components/Layout/NavbarComponent.php v3.9 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Layout;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Survos\TablerBundle\Components\Traits\DataAwareTrait;
use Survos\TablerBundle\Service\FixtureService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTwigComponent(name: 'layout:navbar', template: '@SurvosTabler/components/layout/navbar.html.twig')]
final class NavbarComponent
{
    use DataAwareTrait;

    public ?string $breakpoint = 'md';
    public ?bool $condensed = null;
    public ?bool $showTheme = true;
    public ?bool $showNotifications = true;
    public ?bool $showApps = true;
    public ?bool $showLanguage = true;
    public ?bool $showUser = true;
    public ?string $sticky = null;
    public ?string $transparent = null;
    public ?string $background = null;
    public ?string $overlap = null;
    public ?string $class = null;
    public ?string $backgroundColor = null;
    public ?bool $dark = null;
    public ?string $hideBrand = null;
    public ?string $smallLogo = null;
    public ?string $hideLogo = null;
    public ?string $showTitle = null;
    public ?string $hideUsername = null;
    public ?string $personId = null;
    public ?string $hideSearch = null;
    public ?string $sample = null;
    public ?string $hideIcons = null;
    public ?string $hideMenu = null;
    public ?string $fluidSearch = null;
    public ?string $darkSecondary = null;

    public function __construct(
        ?FixtureService $fixtureService = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->fixtureService = $fixtureService;
        $this->httpClient = $httpClient;
    }
}