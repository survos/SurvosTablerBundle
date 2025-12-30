<?php
/* src/Components/Cards/TabsComponent.php v4.8 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Cards;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Survos\TablerBundle\Components\Traits\DataAwareTrait;
use Survos\TablerBundle\Service\FixtureService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTwigComponent(name: 'cards:tabs', template: '@SurvosTabler/components/cards/tabs.html.twig')]
final class TabsComponent
{
    use DataAwareTrait;

    public ?string $id = null;
    public ?string $hideText = null;
    public ?bool $reverse = null;
    public ?string $justified = null;
    public ?string $icons = null;
    public ?string $activity = null;
    public ?bool $disabled = null;
    public ?string $dropdown = null;
    public ?string $settings = null;
    public ?string $animation = null;

    public function __construct(
        ?FixtureService $fixtureService = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->fixtureService = $fixtureService;
        $this->httpClient = $httpClient;
    }
}