<?php
/* src/Service/SiteIdentityResolver.php v1.0 */

declare(strict_types=1);

namespace Survos\TablerBundle\Service;

use Survos\TablerBundle\Model\SiteIdentity;

final class SiteIdentityResolver
{
    /**
     * @param array<string,mixed> $tablerConfig The merged survos_tabler config
     */
    public function __construct(
        private array $tablerConfig,
        private readonly ?ContextService $contextService = null,
    ) {
    }

    public function resolve(): SiteIdentity
    {
        /** @var array<string,mixed> $app */
        $app = $this->tablerConfig['app'] ?? [];

        // Optional tenant override hook.
        // For now, if ContextService later exposes e.g. ->getSiteIdentityOverrides(): array,
        // you can merge it here without changing templates.
        $overrides = [];
        if ($this->contextService && method_exists($this->contextService, 'getSiteIdentityOverrides')) {
            /** @var mixed $maybe */
            $maybe = $this->contextService->getSiteIdentityOverrides();
            if (is_array($maybe)) {
                $overrides = $maybe;
            }
        }

        $app = $this->deepMerge($app, $overrides);

        $code = (string)($app['code'] ?? 'my-project');
        $title = (string)($app['title'] ?? 'My Project');
        $description = (string)($app['description'] ?? '');

        $abbr = (string)($app['abbr'] ?? '');
        $logo = isset($app['logo']) ? (is_string($app['logo']) ? $app['logo'] : null) : null;
        $logoSmall = isset($app['logo_small']) ? (is_string($app['logo_small']) ? $app['logo_small'] : null) : null;

        $homepageRoute = isset($app['homepage_route']) && is_string($app['homepage_route']) ? $app['homepage_route'] : null;
        $homepageUrl = isset($app['homepage_url']) && is_string($app['homepage_url']) ? $app['homepage_url'] : null;

        $links = $this->stringMap($app['links'] ?? []);
        $social = $this->stringMap($app['social'] ?? []);
        $meta = $this->stringMap($app['meta'] ?? []);
        $header = is_array($app['header'] ?? null) ? $app['header'] : [];

        return new SiteIdentity(
            code: $code,
            title: $title,
            description: $description,
            abbrHtml: $abbr,
            logo: $logo,
            logoSmall: $logoSmall,
            homepageRoute: $homepageRoute,
            homepageUrl: $homepageUrl,
            links: $links,
            social: $social,
            meta: $meta,
            header: $header,
        );
    }

    /**
     * @param mixed $value
     * @return array<string,string>
     */
    private function stringMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }
        $out = [];
        foreach ($value as $k => $v) {
            if (is_string($k) && is_string($v) && $v !== '') {
                $out[$k] = $v;
            }
        }
        return $out;
    }

    /**
     * Conservative deep merge: assoc arrays merge recursively; scalars override.
     *
     * @param array<string,mixed> $base
     * @param array<string,mixed> $override
     * @return array<string,mixed>
     */
    private function deepMerge(array $base, array $override): array
    {
        foreach ($override as $k => $v) {
            if (is_string($k) && array_key_exists($k, $base) && is_array($base[$k]) && is_array($v)) {
                /** @var array<string,mixed> $baseChild */
                $baseChild = $base[$k];
                /** @var array<string,mixed> $ovChild */
                $ovChild = $v;
                $base[$k] = $this->deepMerge($baseChild, $ovChild);
                continue;
            }
            if (is_string($k)) {
                $base[$k] = $v;
            }
        }
        return $base;
    }
}
