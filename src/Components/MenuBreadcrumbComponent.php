<?php

namespace Survos\TablerBundle\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('menu_breadcrumb', template: '@SurvosTabler/components/menu_breadcrumb.html.twig')]
class MenuBreadcrumbComponent extends MenuComponent
{
    public string|bool|null $translationDomain = false;
}
