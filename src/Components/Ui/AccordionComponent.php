<?php
/* src/Components/Ui/AccordionComponent.php v4.8 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Survos\TablerBundle\Components\Traits\DataAwareTrait;
use Survos\TablerBundle\Service\FixtureService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTwigComponent(name: 'ui:accordion', template: '@SurvosTabler/components/ui/accordion.html.twig')]
final class AccordionComponent
{
    use DataAwareTrait;

    public ?int $count = 4;
    public ?string $id = 'default';
    public ?string $toggleIcon = 'chevron-down';
    public ?string $type = null;
    public ?string $showIcon = null;
    public ?iterable $questions = null;

    public function __construct(
        ?FixtureService $fixtureService = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->fixtureService = $fixtureService;
        $this->httpClient = $httpClient;
    }
}