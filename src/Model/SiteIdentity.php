<?php
/* src/Model/SiteIdentity.php v1.0 */

declare(strict_types=1);

namespace Survos\TablerBundle\Model;

final readonly class SiteIdentity
{
    /**
     * @param array<string,string> $links
     * @param array<string,string> $social
     * @param array<string,string> $meta
     * @param array<string,mixed>  $header
     */
    public function __construct(
        public string $code,
        public string $title,
        public string $description = '',
        public string $abbrHtml = '',
        public ?string $logo = null,
        public ?string $logoSmall = null,
        public ?string $homepageRoute = null,
        public ?string $homepageUrl = null,
        public array $links = [],
        public array $social = [],
        public array $meta = [],
        public array $header = [],
    ) {
    }

    public function hasLogo(): bool
    {
        return (bool) $this->logo;
    }

    public function brandHtml(): string
    {
        // When no logo, we render abbrHtml as safe HTML; fall back to title.
        return $this->abbrHtml ?: htmlspecialchars($this->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Convenience accessors used by Twig.
     */
    public function link(string $key): ?string
    {
        return $this->links[$key] ?? null;
    }

    public function social(string $key): ?string
    {
        return $this->social[$key] ?? null;
    }

    public function headerBool(string $key, bool $default = false): bool
    {
        $v = $this->header[$key] ?? $default;
        return is_bool($v) ? $v : $default;
    }

    /**
     * @return array<string,mixed>
     */
    public function authConfig(): array
    {
        $auth = $this->header['auth'] ?? [];
        return is_array($auth) ? $auth : [];
    }
}
