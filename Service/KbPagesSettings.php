<?php

namespace MauticPlugin\MauticKbPagesBundle\Service;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;

class KbPagesSettings
{
    public function __construct(
        private CoreParametersHelper $coreParametersHelper,
        private IntegrationHelper $integrationHelper,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getPublicSettings(): array
    {
        $features = $this->getFeatureSettings();

        return [
            'headerHtml'      => (string) $this->coreParametersHelper->get('kbpages_header_html', ''),
            'footerHtml'      => (string) $this->coreParametersHelper->get('kbpages_footer_html', ''),
            'customCss'       => (string) $this->coreParametersHelper->get('kbpages_custom_css', ''),
            'containerWidth'  => (int) $this->coreParametersHelper->get('kbpages_container_width', 960),
            'tablerCssUrl'    => $this->normalizeAbsoluteUrl((string) ($features['tabler_css_url'] ?? '')),
            'mediaCdnUrl'     => $this->normalizeBaseUrl((string) ($features['media_cdn_url'] ?? '')),
            'iconDocsUrl'     => 'https://tabler.io/icons',
        ];
    }

    public function renderIconHtml(?string $icon): string
    {
        $icon = trim((string) $icon);
        if ('' === $icon) {
            return '';
        }

        if ($this->looksLikeRawMarkup($icon)) {
            return $icon;
        }

        if ($this->looksLikeImageReference($icon)) {
            $src = $this->resolveMediaUrl($icon);

            return sprintf(
                '<img src="%s" alt="" loading="lazy" decoding="async">',
                htmlspecialchars($src, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            );
        }

        $classes = $this->normalizeTablerClass($icon);
        if ('' === $classes) {
            return '';
        }

        return sprintf(
            '<i class="%s" aria-hidden="true"></i>',
            htmlspecialchars($classes, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        );
    }

    public function rewriteMediaUrls(string $html): string
    {
        if ('' === trim($html)) {
            return $html;
        }

        $rewritten = preg_replace_callback('/\b(src|poster)=([\'"])(.*?)\2/i', function (array $matches): string {
            return sprintf(
                '%s=%s%s%s',
                $matches[1],
                $matches[2],
                htmlspecialchars($this->resolveMediaUrl(html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $matches[2]
            );
        }, $html);

        return is_string($rewritten) ? $rewritten : $html;
    }

    private function resolveMediaUrl(string $url): string
    {
        $url = trim($url);
        if ('' === $url || $this->isAbsoluteUrl($url) || str_starts_with($url, 'data:') || str_starts_with($url, '#')) {
            return $url;
        }

        $settings = $this->getPublicSettings();
        $baseUrl  = (string) ($settings['mediaCdnUrl'] ?? '');
        if ('' === $baseUrl) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return $baseUrl.$url;
        }

        return $baseUrl.'/'.ltrim($url, './');
    }

    /**
     * @return array<string, mixed>
     */
    private function getFeatureSettings(): array
    {
        $integration = $this->integrationHelper->getIntegrationObject('KbPages');
        if (!$integration || !$integration->getIntegrationSettings()) {
            return [];
        }

        $settings = $integration->getIntegrationSettings()->getFeatureSettings();

        return is_array($settings) ? $settings : [];
    }

    private function looksLikeRawMarkup(string $value): bool
    {
        return str_contains($value, '<') && str_contains($value, '>');
    }

    private function looksLikeImageReference(string $value): bool
    {
        if ($this->isAbsoluteUrl($value) || str_starts_with($value, '/') || str_starts_with($value, './') || str_starts_with($value, '../')) {
            return true;
        }

        return (bool) preg_match('/\.(svg|png|jpe?g|gif|webp|avif)(?:\?.*)?$/i', $value);
    }

    private function normalizeTablerClass(string $value): string
    {
        $value = strtolower(trim($value));
        if (preg_match('/^ti(?:\s+ti-[a-z0-9\-]+)+$/', $value)) {
            return preg_replace('/\s+/', ' ', $value) ?? $value;
        }

        $value = preg_replace('/[^a-z0-9\-]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return '' !== $value ? 'ti ti-'.$value : '';
    }

    private function isAbsoluteUrl(string $value): bool
    {
        return (bool) preg_match('#^(?:https?:)?//#i', $value);
    }

    private function normalizeAbsoluteUrl(string $value): string
    {
        $value = trim($value);

        return $this->isAbsoluteUrl($value) ? $value : '';
    }

    private function normalizeBaseUrl(string $value): string
    {
        $value = trim($value);
        if (!$this->isAbsoluteUrl($value)) {
            return '';
        }

        return rtrim($value, '/');
    }
}
