<?php
/* src/Components/LocaleSwitcherComponent.php v1.0 - Locale switcher dropdown */

declare(strict_types=1);

namespace Survos\TablerBundle\Components;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent(name: 'tabler:locale-switcher', template: '@SurvosTabler/components/locale-switcher.html.twig')]
final class LocaleSwitcherComponent
{
    // Flag code overrides (locale => flag country code)
    private const FLAG_MAP = [
        'en' => 'us',
        'es' => 'mx', 
        'uk' => 'ua',
        'hi' => 'in',
        'zh' => 'cn',
        'ja' => 'jp',
        'ko' => 'kr',
        'ar' => 'sa',
        'he' => 'il',
        'fa' => 'ir',
    ];

    public string $variant = 'ghost-secondary';
    public string $size = '';
    public bool $showFlag = false;
    public bool $showFullName = true;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
        #[Autowire('%kernel.enabled_locales%')]
        private readonly array $enabledLocales,
    ) {}

    #[ExposeInTemplate]
    public function getCurrentLocale(): string
    {
        return $this->getRequest()?->getLocale() ?? 'en';
    }

    #[ExposeInTemplate]
    public function getLocales(): array
    {
        if (count($this->enabledLocales) <= 1) {
            return [];
        }

        $request = $this->getRequest();
        if (!$request) {
            return [];
        }

        $host = $request->getHttpHost();
        $hostParts = explode('.', $host);
        $isSubdomainBased = count($hostParts) === 3 && in_array($hostParts[0], $this->enabledLocales, true);

        $locales = [];
        foreach ($this->enabledLocales as $locale) {
            $locales[] = [
                'code' => $locale,
                'flag' => self::FLAG_MAP[$locale] ?? $locale,
                'url' => $isSubdomainBased 
                    ? $this->buildSubdomainUrl($locale, $hostParts, $request->getPathInfo())
                    : $this->buildPathUrl($locale),
                'current' => $locale === $this->getCurrentLocale(),
            ];
        }

        return $locales;
    }

    #[ExposeInTemplate]
    public function hasMultipleLocales(): bool
    {
        return count($this->enabledLocales) > 1;
    }

    private function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    private function buildSubdomainUrl(string $locale, array $hostParts, string $pathInfo): string
    {
        $hostParts[0] = $locale;
        return 'https://' . implode('.', $hostParts) . $pathInfo;
    }

    private function buildPathUrl(string $locale): string
    {
        $request = $this->getRequest();
        $route = $request->attributes->get('_route');
        $params = $request->attributes->get('_route_params', []);

        if (!$route) {
            return '#';
        }

        return $this->urlGenerator->generate($route, array_merge($params, ['_locale' => $locale]));
    }
}
