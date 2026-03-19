<?php

namespace MauticPlugin\MauticKbPagesBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\RouteEvent;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Route;

class RouteSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CoreParametersHelper $coreParametersHelper,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CoreEvents::BUILD_ROUTE => ['onBuildRoute', 100],
        ];
    }

    public function onBuildRoute(RouteEvent $event): void
    {
        if ('public' !== $event->getType()) {
            return;
        }

        $roots         = $this->getRegisteredRoots();
        $collection    = $event->getCollection();
        $hostCondition = $this->getRootHostCondition();

        $collection->add('mautic_knowledgebase_root', new Route(
            '/',
            ['_controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::rootAction'],
            [],
            [],
            '',
            [],
            [],
            $hostCondition
        ));

        $collection->add('mautic_knowledgebase_root_snippets', new Route(
            '/_snippets',
            ['_controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::snippetsAction'],
            [],
            [],
            '',
            [],
            [],
            $hostCondition
        ));

        if ('' !== $hostCondition) {
            $collection->add('mautic_knowledgebase_host_tree', new Route(
                '/{slugPath}',
                ['_controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::treeAction'],
                [
                    'slugPath' => $this->getHostTreeRequirement($roots),
                ],
                [],
                '',
                [],
                [],
                $hostCondition
            ));
        }

        if ([] === $roots) {
            return;
        }

        $requirement = '(?:'.implode('|', array_map(static fn (string $root): string => preg_quote($root, '/'), $roots)).')';

        $collection->add('mautic_knowledgebase_snippets', new Route(
            '/{rootSlug}/_snippets',
            ['_controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::snippetsAction'],
            [
                'rootSlug' => $requirement,
            ],
            []
        ));

        $collection->add('mautic_knowledgebase_tree', new Route(
            '/{rootSlug}/{slugPath}',
            ['_controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::treeAction'],
            [
                'rootSlug' => $requirement,
                'slugPath' => '.+',
            ],
            []
        ));

        $collection->add('mautic_knowledgebase_group', new Route(
            '/{slug}',
            ['_controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::groupAction'],
            [
                'slug' => $requirement,
            ],
            []
        ));
    }

    /**
     * @return string[]
     */
    private function getRegisteredRoots(): array
    {
        $value = (string) $this->coreParametersHelper->get('kbpages_public_roots', '');
        if ('' === trim($value)) {
            return [];
        }

        $roots = preg_split('/[\s,]+/', strtolower($value), -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($roots)) {
            return [];
        }

        $roots = array_map(static function (string $root): string {
            $normalized = trim($root);
            $normalized = preg_replace('/[^a-z0-9\-]+/', '-', $normalized) ?? '';

            return trim($normalized, '-');
        }, $roots);

        $roots = array_values(array_filter(array_unique($roots)));

        return $roots;
    }

    private function getRootHostCondition(): string
    {
        $value = (string) $this->coreParametersHelper->get('kbpages_root_hosts', '');
        if ('' === trim($value)) {
            return '';
        }

        $hosts = preg_split('/[\s,]+/', strtolower($value), -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($hosts) || [] === $hosts) {
            return '';
        }

        $patterns = array_map(static function (string $host): string {
            $normalized = trim($host);
            $normalized = preg_replace('/[^a-z0-9\.\-\*]+/', '', $normalized) ?? '';
            $normalized = trim($normalized, '.');

            if ('' === $normalized) {
                return '';
            }

            if (str_starts_with($normalized, '*.')) {
                return '(?:.+\\.)?'.preg_quote(substr($normalized, 2), '/');
            }

            return preg_quote($normalized, '/');
        }, $hosts);

        $patterns = array_values(array_filter(array_unique($patterns)));

        if ([] === $patterns) {
            return '';
        }

        return "context.getHost() matches '/^(?:".implode('|', $patterns).")$/i'";
    }

    /**
     * @param string[] $roots
     */
    private function getHostTreeRequirement(array $roots): string
    {
        $reserved = [
            's',
            'api',
            '_knowledgebase',
            '_snippets',
            '_profiler',
            '_wdt',
            'css',
            'images',
            'js',
            'favicon.ico',
            'mtc',
            'r',
            'redirect',
            'mtracking.gif',
        ];

        $excluded = array_values(array_filter(array_unique(array_merge($reserved, $roots))));
        $pattern  = implode('|', array_map(static fn (string $value): string => preg_quote($value, '/'), $excluded));

        return '(?!(?:'.$pattern.')(?:/|$)).+';
    }
}
