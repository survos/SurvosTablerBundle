<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Twig;

use Survos\TablerBundle\Service\RouteAliasService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RouteAliasExtension extends AbstractExtension
{
    public function __construct(
        private readonly RouteAliasService $routeAliasService,
    ) {}
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('tabler_route', $this->getRoute(...)),
            new TwigFunction('tabler_route_exists', $this->routeExists(...)),
            new TwigFunction('tabler_url', $this->getUrl(...)),
        ];
    }
    
    /**
     * Get actual route name from alias.
     * Usage: {{ path(tabler_route('login')) }}
     * Returns null if route doesn't exist.
     */
    public function getRoute(string $alias): ?string
    {
        return $this->routeAliasService->get($alias);
    }
    
    /**
     * Check if aliased route exists.
     * Usage: {% if tabler_route_exists('register') %}...{% endif %}
     */
    public function routeExists(string $alias): bool
    {
        return $this->routeAliasService->has($alias);
    }
    
    /**
     * Generate URL for alias directly, or null if route doesn't exist.
     * Usage: {{ tabler_url('login') }}
     */
    public function getUrl(string $alias, array $parameters = []): ?string
    {
        return $this->routeAliasService->generateUrl($alias, $parameters);
    }
}
