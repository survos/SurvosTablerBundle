<?php
/* src/Components/Ui/AlertComponent.php v3.9 - Generated 2025-12-30 */

declare(strict_types=1);

namespace Survos\TablerBundle\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:alert', template: '@SurvosTabler/components/ui/alert.html.twig')]
final class AlertComponent
{
    public ?string $icon = null;
    public ?string $type = 'success';
    public ?bool $important = null;
    public ?bool $minor = null;
    public ?string $showClose = null;
    public ?string $avatar = null;
    public ?string $description = 'description';
    public ?array $list = null;
    public ?string $title = 'This';
    public ?string $action = 'Action';
    public ?string $link = 'Link';
    public ?string $buttons = null;

}