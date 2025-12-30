<?php
/* src/Components/Layout/NavbarSideUserComponent.php v3.9 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Layout;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Survos\TablerBundle\Components\Traits\DataAwareTrait;
use Survos\TablerBundle\Service\FixtureService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTwigComponent(name: 'layout:navbar-side-user', template: '@SurvosTabler/components/layout/navbar-side-user.html.twig')]
final class NavbarSideUserComponent
{
    use DataAwareTrait;

    public ?int $personId = 1;
    public ?string $hideUsername = null;
    public ?bool $dark = null;

    public function __construct(
        ?FixtureService $fixtureService = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->fixtureService = $fixtureService;
        $this->httpClient = $httpClient;
    }
}