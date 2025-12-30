<?php
/* src/Components/Ui/AvatarComponent.php v3.9 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Survos\TablerBundle\Components\Traits\DataAwareTrait;
use Survos\TablerBundle\Service\FixtureService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTwigComponent(name: 'ui:avatar', template: '@SurvosTabler/components/ui/avatar.html.twig')]
final class AvatarComponent
{
    use DataAwareTrait;

    public ?string $src = null;
    public ?string $placeholder = null;
    public ?string $personId = null;
    public ?array $person = null;
    public ?bool $link = null;
    public ?string $dropdown = null;
    public ?int $size = null;
    public ?string $thumb = null;
    public ?string $class = null;
    public ?string $shape = null;
    public ?string $color = null;
    public ?string $status = null;
    public ?string $statusText = null;
    public ?string $statusIcon = null;
    public ?string $brand = null;
    public ?string $icon = null;

    public function __construct(
        ?FixtureService $fixtureService = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->fixtureService = $fixtureService;
        $this->httpClient = $httpClient;
    }
}