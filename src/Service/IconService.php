<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Service;

/**
 * Icon management with aliases, auto-inference, and style presets.
 * Integrates with Symfony UX Icons.
 */
final class IconService
{
    private const ICON_ALIASES = [
        // Navigation
        'home' => 'home',
        'dashboard' => 'dashboard',
        'menu' => 'menu-2',
        'back' => 'arrow-left',
        'forward' => 'arrow-right',

        // Auth
        'login' => 'login',
        'logout' => 'logout',
        'register' => 'user-plus',
        'profile' => 'user-circle',
        'user' => 'user',
        'users' => 'users',
        'password' => 'lock',

        // CRUD
        'create' => 'plus',
        'new' => 'plus',
        'add' => 'plus',
        'edit' => 'edit',
        'delete' => 'trash',
        'remove' => 'x',
        'save' => 'device-floppy',
        'cancel' => 'x',
        'view' => 'eye',
        'show' => 'eye',
        'hide' => 'eye-off',
        'list' => 'list',
        'index' => 'list',
        'browse' => 'list-search',
        'grid' => 'grid-dots',

        // Actions
        'search' => 'search',
        'filter' => 'filter',
        'sort' => 'arrows-sort',
        'refresh' => 'refresh',
        'sync' => 'refresh',
        'download' => 'download',
        'upload' => 'upload',
        'export' => 'file-export',
        'import' => 'file-import',
        'print' => 'printer',
        'copy' => 'copy',
        'share' => 'share',
        'link' => 'link',
        'external' => 'external-link',

        // Status
        'success' => 'check',
        'error' => 'x',
        'warning' => 'alert-triangle',
        'info' => 'info-circle',
        'help' => 'help',

        // Settings
        'settings' => 'settings',
        'config' => 'adjustments',
        'admin' => 'shield',
        'tools' => 'tools',
        'commands' => 'terminal-2',

        // Content
        'file' => 'file',
        'folder' => 'folder',
        'image' => 'photo',
        'document' => 'file-text',
        'database' => 'database',
        'api' => 'api',
        'code' => 'code',

        // Social
        'github' => 'brand-github',
        'twitter' => 'brand-twitter',
        'facebook' => 'brand-facebook',
        'linkedin' => 'brand-linkedin',
        'youtube' => 'brand-youtube',

        // Misc
        'calendar' => 'calendar',
        'clock' => 'clock',
        'location' => 'map-pin',
        'globe' => 'world',
        'language' => 'language',
        'locale' => 'world',
        'star' => 'star',
        'heart' => 'heart',
        'bookmark' => 'bookmark',
        'notification' => 'bell',
        'email' => 'mail',
        'message' => 'message',

        // Arrows
        'up' => 'arrow-up',
        'down' => 'arrow-down',
        'left' => 'arrow-left',
        'right' => 'arrow-right',
        'dropdown' => 'chevron-down',
        'expand' => 'chevron-down',
        'collapse' => 'chevron-up',

        // Toggle
        'check' => 'check',
        'close' => 'x',
        'plus' => 'plus',
        'minus' => 'minus',
        'more' => 'dots',

        // Layout
        'point' => 'point',
    ];

    private const STYLE_PRESETS = [
        'success' => ['icon' => 'check', 'class' => 'text-success'],
        'error' => ['icon' => 'x', 'class' => 'text-danger'],
        'danger' => ['icon' => 'alert-triangle', 'class' => 'text-danger'],
        'warning' => ['icon' => 'alert-triangle', 'class' => 'text-warning'],
        'info' => ['icon' => 'info-circle', 'class' => 'text-info'],
        'primary' => ['icon' => 'circle-check', 'class' => 'text-primary'],
        'secondary' => ['icon' => 'circle', 'class' => 'text-secondary'],
        'muted' => ['icon' => 'circle', 'class' => 'text-muted'],

        // Action styles
        'add' => ['icon' => 'plus', 'class' => 'text-success'],
        'create' => ['icon' => 'plus', 'class' => 'text-success'],
        'delete' => ['icon' => 'trash', 'class' => 'text-danger'],
        'remove' => ['icon' => 'x', 'class' => 'text-danger'],
        'edit' => ['icon' => 'edit', 'class' => 'text-warning'],

        // Status styles
        'active' => ['icon' => 'circle-check', 'class' => 'text-success'],
        'inactive' => ['icon' => 'circle-x', 'class' => 'text-muted'],
        'pending' => ['icon' => 'clock', 'class' => 'text-warning'],
        'locked' => ['icon' => 'lock', 'class' => 'text-danger'],
        'unlocked' => ['icon' => 'lock-open', 'class' => 'text-success'],
    ];

    private const ROUTE_SUFFIX_MAP = [
        'index' => 'list',
        'list' => 'list',
        'browse' => 'browse',
        'search' => 'search',
        'show' => 'show',
        'view' => 'view',
        'new' => 'new',
        'create' => 'create',
        'add' => 'add',
        'edit' => 'edit',
        'update' => 'edit',
        'delete' => 'delete',
        'remove' => 'remove',
        'login' => 'login',
        'logout' => 'logout',
        'register' => 'register',
        'profile' => 'profile',
        'settings' => 'settings',
        'admin' => 'admin',
        'dashboard' => 'dashboard',
        'homepage' => 'home',
        'home' => 'home',
        'export' => 'export',
        'import' => 'import',
        'download' => 'download',
        'upload' => 'upload',
    ];

    private array $resolvedAliases;
    private array $resolvedPresets;

    public function __construct(
        private readonly array $configuredAliases = [],
        private readonly array $configuredPresets = [],
        private readonly string $defaultPrefix = 'tabler',
    ) {
        $this->resolvedAliases = array_merge(self::ICON_ALIASES, $this->configuredAliases);
        $this->resolvedPresets = array_merge(self::STYLE_PRESETS, $this->configuredPresets);
    }

    /**
     * Resolve icon name from alias or pass-through.
     * Adds default prefix if needed.
     */
    public function resolve(string $icon): string
    {
        // Already has a prefix (e.g., tabler:home, fa6-solid:user)
        if (str_contains($icon, ':')) {
            return $icon;
        }

        // Check alias
        $resolved = $this->resolvedAliases[$icon] ?? $icon;

        // Add default prefix
        return $this->defaultPrefix . ':' . $resolved;
    }

    /**
     * Get style preset (icon + class).
     * @return array{icon: string, class: string}|null
     */
    public function getPreset(string $name): ?array
    {
        if (!isset($this->resolvedPresets[$name])) {
            return null;
        }

        $preset = $this->resolvedPresets[$name];

        return [
            'icon' => $this->resolve($preset['icon']),
            'class' => $preset['class'] ?? '',
        ];
    }

    /**
     * Check if alias exists.
     */
    public function has(string $alias): bool
    {
        return isset($this->resolvedAliases[$alias]);
    }

    /**
     * Check if style preset exists.
     */
    public function hasPreset(string $name): bool
    {
        return isset($this->resolvedPresets[$name]);
    }

    /**
     * Infer icon from route name.
     * e.g., 'app_project_edit' => 'tabler:edit', 'museum_index' => 'tabler:list'
     */
    public function inferFromRoute(?string $route): ?string
    {
        if (!$route) {
            return null;
        }

        // Extract last segment: app_project_edit => edit
        $parts = explode('_', $route);
        $suffix = end($parts);

        if (isset(self::ROUTE_SUFFIX_MAP[$suffix])) {
            return $this->resolve(self::ROUTE_SUFFIX_MAP[$suffix]);
        }

        // Try second-to-last for patterns like project_show_details
        if (count($parts) >= 2) {
            $secondLast = $parts[count($parts) - 2];
            if (isset(self::ROUTE_SUFFIX_MAP[$secondLast])) {
                return $this->resolve(self::ROUTE_SUFFIX_MAP[$secondLast]);
            }
        }

        // Check if any part matches an alias
        foreach (array_reverse($parts) as $part) {
            if (isset($this->resolvedAliases[$part])) {
                return $this->resolve($part);
            }
        }

        return null;
    }

    /**
     * Get all resolved aliases.
     */
    public function getAliases(): array
    {
        return $this->resolvedAliases;
    }

    /**
     * Get all resolved presets.
     */
    public function getPresets(): array
    {
        return $this->resolvedPresets;
    }

    /**
     * Get the default icon prefix.
     */
    public function getDefaultPrefix(): string
    {
        return $this->defaultPrefix;
    }
}
