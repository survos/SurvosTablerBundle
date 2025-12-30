<?php
/* src/Components/Parts/CheckboxesListComponent.php v3.9 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Parts;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'parts:checkboxes-list', template: '@SurvosTabler/components/parts/checkboxes-list.html.twig')]
final class CheckboxesListComponent
{
    public ?iterable $items = null;
    public ?string $class = null;

}