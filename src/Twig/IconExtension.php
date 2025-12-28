<?php

declare(strict_types=1);

namespace Survos\TablerBundle\Twig;

use Survos\TablerBundle\Service\IconService;
use Symfony\UX\Icons\Twig\UXIconRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class IconExtension extends AbstractExtension
{
    public function __construct(
        private readonly IconService $iconService,
        private readonly ?UXIconRuntime $uxIconRuntime = null,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', $this->renderIcon(...), ['is_safe' => ['html']]),
            new TwigFunction('icon_name', $this->getIconName(...)),
            new TwigFunction('icon_preset', $this->getPreset(...)),
            new TwigFunction('icon_exists', $this->iconExists(...)),
        ];
    }

    /**
     * Render icon with smart resolution and optional styling.
     */
    public function renderIcon(string $icon, array $attributes = []): string
    {
        $class = $attributes['class'] ?? 'icon';
        unset($attributes['class']);

        // Check for style preset first
        if ($preset = $this->iconService->getPreset($icon)) {
            $resolvedIcon = $preset['icon'];
            $class = trim($class . ' ' . $preset['class']);
        } else {
            $resolvedIcon = $this->iconService->resolve($icon);
        }

        $attributes['class'] = $class;

        // Use UX Icons runtime if available
        if ($this->uxIconRuntime) {
            try {
                return $this->uxIconRuntime->renderIcon($resolvedIcon, $attributes);
            } catch (\Exception $e) {
                // Icon not found or other error - fall through to fallback
            }
        }

        // Fallback: render as span with data attribute
        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= sprintf(' %s="%s"', htmlspecialchars($key), htmlspecialchars((string) $value));
        }

        return sprintf(
            '<span data-icon="%s"%s title="%s">[%s]</span>',
            htmlspecialchars($resolvedIcon),
            $attrString,
            htmlspecialchars($resolvedIcon),
            htmlspecialchars($icon)
        );
    }

    /**
     * Get resolved icon name without rendering.
     */
    public function getIconName(string $icon): string
    {
        if ($preset = $this->iconService->getPreset($icon)) {
            return $preset['icon'];
        }

        return $this->iconService->resolve($icon);
    }

    /**
     * Get full preset data.
     */
    public function getPreset(string $name): ?array
    {
        return $this->iconService->getPreset($name);
    }

    /**
     * Check if icon alias or preset exists.
     */
    public function iconExists(string $icon): bool
    {
        return $this->iconService->has($icon) || $this->iconService->hasPreset($icon);
    }
}
