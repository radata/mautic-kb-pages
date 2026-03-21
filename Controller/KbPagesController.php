<?php

namespace MauticPlugin\MauticKbPagesBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractStandardFormController;
use MauticPlugin\MauticKbPagesBundle\Entity\KbPages;
use MauticPlugin\MauticKbPagesBundle\Service\KbPagesUrlGenerator;
use Symfony\Component\HttpFoundation\Request;

class KbPagesController extends AbstractStandardFormController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'mautic.kbpages.url_generator' => KbPagesUrlGenerator::class,
        ]);
    }

    protected function getModelName(): string
    {
        return 'kbpages';
    }

    protected function getJsLoadMethodPrefix(): string
    {
        return 'knowledgebase';
    }

    protected function getRouteBase(): string
    {
        return 'knowledgebase';
    }

    protected function getSessionBase($objectId = null): string
    {
        return 'knowledgebase';
    }

    protected function getTemplateBase(): string
    {
        return '@MauticKbPages/Knowledgebase';
    }

    protected function getTranslationBase(): string
    {
        return 'mautic.kbpages';
    }

    protected function getViewArguments(array $args, $action): array
    {
        $viewParameters = array_merge(
            [
                'permissionBase'  => $this->getPermissionBase(),
                'mauticContent'   => $this->getJsLoadMethodPrefix(),
                'actionRoute'     => $this->getActionRoute(),
                'indexRoute'      => $this->getIndexRoute(),
                'translationBase' => $this->getTranslationBase(),
                'modelName'       => $this->getModelName(),
            ],
            $args['viewParameters'] ?? []
        );

        if (isset($viewParameters['items']) && is_array($viewParameters['items'])) {
            $viewParameters['previewUrls'] = [];
            foreach ($viewParameters['items'] as $item) {
                if (!$item instanceof KbPages) {
                    continue;
                }

                $viewParameters['previewUrls'][$item->getId()] = $this->buildPreviewUrl($item);
            }
        }

        $viewParameters['editorPreviewWidth'] = $this->resolveEditorPreviewWidth(
            ($viewParameters['item'] ?? null) instanceof KbPages ? $viewParameters['item'] : null
        );

        if (($viewParameters['item'] ?? null) instanceof KbPages) {
            $viewParameters['previewUrl'] = $this->buildPreviewUrl($viewParameters['item']);
            $viewParameters['snippetsPreviewUrl'] = $this->buildSnippetsPreviewUrl($viewParameters['item']);
        }

        $args['viewParameters'] = $viewParameters;

        return $args;
    }

    protected function getEntityClass(): string
    {
        return KbPages::class;
    }

    protected function getDefaultOrderColumn()
    {
        return 'title';
    }

    public function indexAction(Request $request, int $page = 1)
    {
        return parent::indexStandard($request, $page);
    }

    public function newAction(Request $request)
    {
        return parent::newStandard($request);
    }

    public function editAction(Request $request, int $objectId, bool $ignorePost = false)
    {
        return parent::editStandard($request, $objectId, $ignorePost);
    }

    public function viewAction(Request $request, int $objectId)
    {
        return parent::viewStandard($request, $objectId, 'kbpages');
    }

    public function cloneAction(Request $request, int $objectId)
    {
        return parent::cloneStandard($request, $objectId);
    }

    public function deleteAction(Request $request, int $objectId)
    {
        return parent::deleteStandard($request, $objectId);
    }

    public function batchDeleteAction(Request $request)
    {
        return parent::batchDeleteStandard($request);
    }

    /**
     * @param KbPages[] $items
     */
    protected function getIndexItems($start, $limit, $filter, $orderBy, $orderByDir, array $args = [])
    {
        if (!$this->shouldSortListByPath()) {
            return parent::getIndexItems($start, $limit, $filter, $orderBy, $orderByDir, $args);
        }

        $items = $this->getModel($this->getModelName())->getEntities(array_merge(
            [
                'filter'           => $filter,
                'orderBy'          => $orderBy,
                'orderByDir'       => $orderByDir,
                'ignore_paginator' => true,
            ],
            $args
        ));

        if (!is_array($items)) {
            $items = iterator_to_array($items);
        }

        $items = $this->sortItemsByHierarchy($items);
        $count = count($items);

        if ($limit > 0) {
            $items = array_slice($items, max(0, (int) $start), (int) $limit);
        }

        return [$count, $items];
    }

    /**
     * @param KbPages[] $items
     *
     * @return KbPages[]
     */
    private function sortItemsByHierarchy(array $items): array
    {
        $direction = $this->getCurrentOrderDirection();
        $itemsById = [];
        $children  = [];

        foreach ($items as $item) {
            if (!$item instanceof KbPages || null === $item->getId()) {
                continue;
            }

            $itemsById[$item->getId()] = $item;
        }

        foreach ($items as $item) {
            if (!$item instanceof KbPages) {
                continue;
            }

            $parent   = $item->getParent();
            $parentId = $parent instanceof KbPages ? (int) $parent->getId() : 0;

            if ($parentId > 0 && !isset($itemsById[$parentId])) {
                $parentId = 0;
            }

            $children[$parentId][] = $item;
        }

        foreach ($children as &$siblings) {
            usort($siblings, function (KbPages $left, KbPages $right) use ($direction): int {
                $result = $left->getPosition() <=> $right->getPosition();

                if (0 === $result && $left->isGroup() !== $right->isGroup()) {
                    $result = $left->isGroup() ? -1 : 1;
                }

                if (0 === $result) {
                    $result = strnatcasecmp((string) $left->getTitle(), (string) $right->getTitle());
                }

                if (0 === $result) {
                    $result = strnatcasecmp(implode('/', $left->getPathSlugs()), implode('/', $right->getPathSlugs()));
                }

                if ('DESC' === $direction) {
                    $result *= -1;
                }

                return $result;
            });
        }
        unset($siblings);

        $ordered = [];
        $appendChildren = function (int $parentId) use (&$appendChildren, &$ordered, $children): void {
            foreach ($children[$parentId] ?? [] as $child) {
                $ordered[] = $child;
                if (null !== $child->getId()) {
                    $appendChildren((int) $child->getId());
                }
            }
        };

        $appendChildren(0);

        return $ordered;
    }

    private function shouldSortListByPath(): bool
    {
        try {
            $request = $this->getCurrentRequest();
        } catch (\RuntimeException) {
            return true;
        }

        if (!$request->hasSession()) {
            return true;
        }

        $orderBy = (string) $request->getSession()->get(
            'mautic.'.$this->getSessionBase().'.orderby',
            ''
        );

        return '' === $orderBy
            || str_ends_with($orderBy, '.title')
            || str_ends_with($orderBy, '.slug');
    }

    private function getCurrentOrderDirection(): string
    {
        try {
            $request = $this->getCurrentRequest();
        } catch (\RuntimeException) {
            return 'ASC';
        }

        if (!$request->hasSession()) {
            return 'ASC';
        }

        return strtoupper((string) $request->getSession()->get(
            'mautic.'.$this->getSessionBase().'.orderbydir',
            $this->getDefaultOrderDirection()
        ));
    }

    private function buildPreviewUrl(KbPages $item): ?string
    {
        return $this->getKbPagesUrlGenerator()->generateCanonicalUrl($item);
    }

    private function buildSnippetsPreviewUrl(KbPages $item): ?string
    {
        if (!$item->isGroup() || $item->getParent() instanceof KbPages) {
            return null;
        }

        $canonicalSegments = $this->getKbPagesUrlGenerator()->getCanonicalPathSegments($item);
        $pathSegments      = $item->getPathSlugs();
        if (1 !== count($canonicalSegments) || $canonicalSegments !== $pathSegments) {
            return null;
        }

        return $this->generateUrl('mautic_knowledgebase_snippets', [
            'rootSlug' => $canonicalSegments[0],
        ]);
    }

    private function resolveEditorPreviewWidth(?KbPages $item): int
    {
        $fallback = max(480, (int) $this->coreParametersHelper->get('kbpages_container_width', 960));
        $current  = $item;

        while ($current instanceof KbPages) {
            $width = $current->getContainerWidth();
            if (null !== $width && $width >= 480) {
                return $width;
            }

            $current = $current->getParent();
        }

        return $fallback;
    }

    private function getRootAncestor(KbPages $item): KbPages
    {
        $current = $item;

        while ($current->getParent() instanceof KbPages) {
            $current = $current->getParent();
        }

        return $current;
    }

    private function getKbPagesUrlGenerator(): KbPagesUrlGenerator
    {
        $service = $this->container->get('mautic.kbpages.url_generator');
        \assert($service instanceof KbPagesUrlGenerator);

        return $service;
    }
}
