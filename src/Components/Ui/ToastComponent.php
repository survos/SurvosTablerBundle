<?php
/* src/Components/Ui/ToastComponent.php v3.9 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Survos\TablerBundle\Components\Traits\DataAwareTrait;
use Survos\TablerBundle\Service\FixtureService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTwigComponent(name: 'ui:toast', template: '@SurvosTabler/components/ui/toast.html.twig')]
final class ToastComponent
{
    use DataAwareTrait;

    public ?string $toastId = 'simple';
    public ?int $personId = 2;
    public ?bool $show = null;
    public ?string $hideHeader = null;
    public ?string $date = null;
    public ?string $cookies = null;
    public ?string $text = 'Hello,';

    public function __construct(
        ?FixtureService $fixtureService = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->fixtureService = $fixtureService;
        $this->httpClient = $httpClient;
    }
}