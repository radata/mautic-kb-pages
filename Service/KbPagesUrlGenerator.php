<?php

namespace MauticPlugin\MauticKbPagesBundle\Service;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use MauticPlugin\MauticKbPagesBundle\Entity\KbPages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class KbPagesUrlGenerator
{
    public function __construct(
        private CoreParametersHelper $coreParametersHelper,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function generateCanonicalUrl(KbPages $item, bool $absolute = false): ?string
    {
        $segments = $this->getCanonicalPathSegments($item);
        if ([] === $segments) {
            return null;
        }

        $referenceType = $absolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH;

        try {
            if (1 === count($segments)) {
                return $this->router->generate('mautic_knowledgebase_group', [
                    'slug' => $segments[0],
                ], $referenceType);
            }

            return $this->router->generate('mautic_knowledgebase_tree', [
                'rootSlug' => array_shift($segments),
                'slugPath' => implode('/', $segments),
            ], $referenceType);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return string[]
     */
    public function getCanonicalPathSegments(KbPages $item): array
    {
        $segments = $item->getPathSlugs();
        if ([] === $segments) {
            return [];
        }

        $domainRoots = $this->getConfiguredDomainRoots();
        if (1 === count($segments) && isset($domainRoots[$segments[0]])) {
            return [$domainRoots[$segments[0]]];
        }

        $registeredRoots = $this->getRegisteredPublicRoots();
        foreach ($segments as $index => $segment) {
            if (in_array($segment, $registeredRoots, true)) {
                return array_slice($segments, $index);
            }
        }

        return $segments;
    }

    /**
     * @return array<string, string>
     */
    private function getConfiguredDomainRoots(): array
    {
        $value = trim((string) $this->coreParametersHelper->get('kbpages_domain_roots', ''));
        if ('' === $value) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $value);
        if (!is_array($lines)) {
            return [];
        }

        $map = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ('' === $line || !str_contains($line, '=')) {
                continue;
            }

            [$domain, $root] = array_map('trim', explode('=', $line, 2));
            $domain = $this->normalizeDomainKey($domain);
            $root   = $this->normalizeSlug($root);

            if ('' === $domain || '' === $root) {
                continue;
            }

            $map[$domain] = $root;
        }

        return $map;
    }

    /**
     * @return string[]
     */
    private function getRegisteredPublicRoots(): array
    {
        $value = (string) $this->coreParametersHelper->get('kbpages_public_roots', '');
        if ('' === trim($value)) {
            return [];
        }

        $roots = preg_split('/[\s,]+/', strtolower($value), -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($roots)) {
            return [];
        }

        $roots = array_map(fn (string $root): string => $this->normalizeSlug($root), $roots);

        return array_values(array_filter(array_unique($roots)));
    }

    private function normalizeSlug(string $value): string
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug) ?? '';

        return trim($slug, '-');
    }

    private function normalizeDomainKey(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace('.', '-', $value);
        $value = preg_replace('/[^a-z0-9\-]+/', '-', $value) ?? '';

        return trim($value, '-');
    }
}
