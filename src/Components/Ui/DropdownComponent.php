<?php
/* src/Components/Ui/DropdownComponent.php v3.9 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:dropdown', template: '@SurvosTabler/components/ui/dropdown.html.twig')]
final class DropdownComponent
{
    public ?array $options = null;
    public ?string $mainBtn = null;

}