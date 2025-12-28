<?php

namespace Survos\TablerBundle\Twig\Components;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'tabler:page-header', template: '@SurvosTabler/components/tabler/page_header.html.twig')]
final class TablerPageHeader
{

    public function __construct(
        public ?string $preTitle=null,
        public ?string $title=null,
        public ?string $subTitle=null,
        private ?RequestStack $requestStack=null
    )
    {
        if ($preTitle === null) {
//            $this->preTitle = $this->requestStack->getCurrentRequest();
        }
        if ($title === null) {

        }
    }





}
