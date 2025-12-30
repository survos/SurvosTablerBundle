<?php
/* src/Components/Ui/TableComponent.php v3.9 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Survos\TablerBundle\Components\Traits\DataAwareTrait;
use Survos\TablerBundle\Service\FixtureService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTwigComponent(name: 'ui:table', template: '@SurvosTabler/components/ui/table.html.twig')]
final class TableComponent
{
    use DataAwareTrait;

    public ?string $mobile = null;
    public ?string $card = null;
    public ?string $stripped = null;
    public ?string $nowrap = null;
    public ?string $avatars = null;
    public ?string $buttons = null;

    public function __construct(
        ?FixtureService $fixtureService = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->fixtureService = $fixtureService;
        $this->httpClient = $httpClient;
    }
}