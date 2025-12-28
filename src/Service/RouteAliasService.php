<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Service;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Manages route aliases with runtime validation.
 */
final class RouteAliasService
{
    /** @var array<string, bool> Runtime validation cache */
    private array $validationCache = [];

    public function __construct(
        private readonly array $configuredAliases,
        private readonly RouterInterface $router,
    ) {}

    /**
     * Get the actual route name for an alias, or null if not configured or route doesn't exist.
     */
    public function get(string $alias): ?string
    {
        $routeName = $this->configuredAliases[$alias] ?? null;

        if ($routeName === null || $routeName === false || $routeName === '') {
            return null;
        }

        // Validate route exists (cached)
        if (!$this->routeExists($routeName)) {
            return null;
        }

        return $routeName;
    }

    /**
     * Check if an aliased route is configured and exists.
     */
    public function has(string $alias): bool
    {
        return $this->get($alias) !== null;
    }

    /**
     * Get all configured aliases.
     */
    public function all(): array
    {
        return $this->configuredAliases;
    }

    /**
     * Generate URL for an alias, or null if route doesn't exist.
     */
    public function generateUrl(string $alias, array $parameters = []): ?string
    {
        $route = $this->get($alias);
        
        if ($route === null) {
            return null;
        }

        try {
            return $this->router->generate($route, $parameters);
        } catch (\Exception) {
            return null;
        }
    }

    private function routeExists(string $routeName): bool
    {
        if (isset($this->validationCache[$routeName])) {
            return $this->validationCache[$routeName];
        }

        try {
            $this->router->generate($routeName);
            $this->validationCache[$routeName] = true;
        } catch (RouteNotFoundException) {
            $this->validationCache[$routeName] = false;
        } catch (\Exception) {
            // Route exists but requires parameters - that's fine
            $this->validationCache[$routeName] = true;
        }

        return $this->validationCache[$routeName];
    }
}
