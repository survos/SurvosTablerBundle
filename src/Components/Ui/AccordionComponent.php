<?php
/* src/Components/Ui/AccordionComponent.php v3.9 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:accordion', template: '@SurvosTabler/components/ui/accordion.html.twig')]
final class AccordionComponent
{
    public ?int $count = 4;
    public ?string $id = 'default';
    public ?string $toggleIcon = 'chevron-down';
    public ?string $type = null;
    public ?string $showIcon = null;

}