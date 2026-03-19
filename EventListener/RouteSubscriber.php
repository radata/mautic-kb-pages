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

        $roots = $this->getRegisteredRoots();
        if ([] === $roots) {
            return;
        }

        $requirement = '(?:'.implode('|', array_map(static fn (string $root): string => preg_quote($root, '/'), $roots)).')';
        $collection  = $event->getCollection();

        $collection->add('mautic_knowledgebase_article', new Route(
            '/{groupSlug}/{slug}',
            ['_controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::articleAction'],
            [
                'groupSlug' => $requirement,
                'slug'      => '[^/]+',
            ]
        ));

        $collection->add('mautic_knowledgebase_group', new Route(
            '/{slug}',
            ['_controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::groupAction'],
            [
                'slug' => $requirement,
            ]
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
}
