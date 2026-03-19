<?php

namespace MauticPlugin\MauticKbPagesBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractStandardFormController;
use MauticPlugin\MauticKbPagesBundle\Entity\KbPages;
use Symfony\Component\HttpFoundation\Request;

class KbPagesController extends AbstractStandardFormController
{
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

        if (($viewParameters['item'] ?? null) instanceof KbPages) {
            $viewParameters['previewUrl'] = $this->buildPreviewUrl($viewParameters['item']);
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
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request || !$request->hasSession()) {
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
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request || !$request->hasSession()) {
            return 'ASC';
        }

        return strtoupper((string) $request->getSession()->get(
            'mautic.'.$this->getSessionBase().'.orderbydir',
            $this->getDefaultOrderDirection()
        ));
    }

    private function buildPreviewUrl(KbPages $item): ?string
    {
        $segments = $this->getCanonicalPreviewSegments($item);
        if ([] === $segments) {
            return null;
        }

        if (1 === count($segments)) {
            return $this->generateUrl('mautic_knowledgebase_group', [
                'slug' => $segments[0],
            ]);
        }

        return $this->generateUrl('mautic_knowledgebase_tree', [
            'rootSlug' => array_shift($segments),
            'slugPath' => implode('/', $segments),
        ]);
    }

    /**
     * @return string[]
     */
    private function getCanonicalPreviewSegments(KbPages $item): array
    {
        $segments = $item->getPathSlugs();
        if ([] === $segments) {
            return [];
        }

        $domainRoots = $this->getConfiguredDomainRoots();
        if (1 === count($segments) && isset($domainRoots[$segments[0]])) {
            return [$domainRoots[$segments[0]]];
        }

        $registeredRoots = $this->getRegisteredRoots();
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
