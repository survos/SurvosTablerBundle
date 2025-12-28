<?php

namespace Survos\TablerBundle\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('carousel', template: '@SurvosTabler/components/carousel.html.twig')]
final class CarouselComponent
{
    public array $slides = [];
}
