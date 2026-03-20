<?php

namespace MauticPlugin\MauticKbPagesBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\FormBundle\Model\FormModel;
use Mautic\PageBundle\Model\PageModel;
use MauticPlugin\MauticKbPagesBundle\Entity\KbPages;
use MauticPlugin\MauticKbPagesBundle\Model\KbPagesModel;
use MauticPlugin\MauticKbPagesBundle\Service\KbPagesSettings;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends CommonController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            KbPagesSettings::class => KbPagesSettings::class,
        ]);
    }

    public function rootAction(): Response
    {
        $group = $this->resolveRootGroup();

        if ($group instanceof KbPages) {
            return $this->renderGroupResponse($group);
        }

        return $this->homeAction();
    }

    public function homeAction(): Response
    {
        $repo = $this->getKnowledgebaseModel()->getRepository();
        $groups = array_map(function (KbPages $group): array {
            return [
                'entity'    => $group,
                'iconHtml'  => $this->getSettingsProvider()->renderIconHtml($group->getIcon()),
                'url'       => $this->generatePublicUrl($group),
            ];
        }, $repo->findPublishedGroups());

        return new Response(
            $this->renderView(
                '@MauticKbPages/Public/index.html.twig',
                $this->getPublicViewParameters([
                    'groups' => $groups,
                ])
            )
        );
    }

    public function snippetsAction(?string $rootSlug = null): Response
    {
        $visibleRootGroup = null !== $rootSlug ? $this->resolveVisibleRootGroupBySlug($rootSlug) : $this->resolveRootGroup();
        if (!$visibleRootGroup instanceof KbPages) {
            throw $this->createNotFoundException();
        }

        $ownerRootGroup = $this->getRootAncestor($visibleRootGroup);

        return new Response(
            $this->renderView(
                '@MauticKbPages/Public/snippets.html.twig',
                $this->getPublicViewParameters([
                    'rootGroup'       => $visibleRootGroup,
                    'rootUrl'         => $this->generatePublicUrl($visibleRootGroup),
                    'snippetsContent' => $this->renderPublicHtml($ownerRootGroup->getSnippetsHtml()),
                ], $ownerRootGroup)
            )
        );
    }

    public function groupAction(string $slug): Response
    {
        $group = $this->resolveVisibleGroupBySlug($slug);

        if (!$group instanceof KbPages) {
            throw $this->createNotFoundException();
        }

        return $this->renderGroupResponse($group);
    }

    public function treeAction(string $slugPath, ?string $rootSlug = null): Response
    {
        $rootGroup = null !== $rootSlug ? $this->resolveVisibleRootGroupBySlug($rootSlug) : $this->resolveRootGroup();

        if (!$rootGroup instanceof KbPages) {
            throw $this->createNotFoundException();
        }

        $item = $this->resolveTreeItem($rootGroup, $slugPath);
        if (!$item instanceof KbPages) {
            throw $this->createNotFoundException();
        }

        if ($item->isGroup()) {
            return $this->renderGroupResponse($item);
        }

        return $this->renderArticleResponse($item);
    }

    public function articleAction(string $groupSlug, string $slug): Response
    {
        return $this->treeAction($slug, $groupSlug);
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    private function getPublicViewParameters(array $parameters, ?KbPages $settingsGroup = null): array
    {
        $kbSettings = $this->getSettingsProvider()->getPublicSettings($settingsGroup);

        return array_merge(
            [
                'kbSettings' => [
                    'headerHtml'      => $this->renderPublicHtml((string) ($kbSettings['headerHtml'] ?? '')),
                    'footerHtml'      => $this->renderPublicHtml((string) ($kbSettings['footerHtml'] ?? '')),
                    'customCss'       => (string) ($kbSettings['customCss'] ?? ''),
                    'containerWidth'  => (int) ($kbSettings['containerWidth'] ?? 960),
                    'tablerCssUrl'    => (string) ($kbSettings['tablerCssUrl'] ?? ''),
                    'mediaCdnUrl'     => (string) ($kbSettings['mediaCdnUrl'] ?? ''),
                    'iconDocsUrl'     => (string) ($kbSettings['iconDocsUrl'] ?? ''),
                ],
            ],
            $parameters
        );
    }

    private function renderPublicHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $rendered = preg_replace_callback('/{pagelink=(\d+)}/', function (array $matches): string {
            return $this->resolvePageLinkToken($matches[0], (int) $matches[1]);
        }, $html);

        $rendered = is_string($rendered) ? $rendered : $html;
        $rendered = $this->renderFormTokens($rendered);

        return $this->getSettingsProvider()->rewriteMediaUrls($rendered);
    }

    private function renderFormTokens(string $html): string
    {
        $rendered = preg_replace_callback('/{form=(\d+)}/i', function (array $matches): string {
            return $this->resolveFormToken((int) $matches[1]);
        }, $html);

        return is_string($rendered) ? $rendered : $html;
    }

    private function resolveFormToken(int $formId): string
    {
        try {
            $formModel = $this->getFormModel();
            $form      = $formModel->getEntity($formId);

            if (null === $form || !$form->isPublished(false)) {
                return '';
            }

            $formHtml = $form->isPublished() ? $formModel->getContent($form) : '';
            if ('' === $formHtml) {
                return '';
            }

            $formModel->populateValuesWithGetParameters($form, $formHtml);

            return $formHtml;
        } catch (\Throwable) {
            return '';
        }
    }

    private function resolvePageLinkToken(string $token, int $pageId): string
    {
        try {
            $pageModel = $this->getPageModel();
            $page      = $pageModel->getEntity($pageId);

            if (null === $page) {
                return $token;
            }

            return $pageModel->generateUrl($page, true);
        } catch (\Throwable) {
            return $token;
        }
    }

    private function getKnowledgebaseModel(): KbPagesModel
    {
        $model = $this->getModel('kbpages');
        \assert($model instanceof KbPagesModel);

        return $model;
    }

    private function renderGroupResponse(KbPages $group): Response
    {
        $repo = $this->getKnowledgebaseModel()->getRepository();

        return new Response(
            $this->renderView(
                '@MauticKbPages/Public/group.html.twig',
                $this->getPublicViewParameters([
                    'group'        => $group,
                    'groupContent' => $this->renderPublicHtml($group->getContent()),
                    'groupIconHtml' => $this->getSettingsProvider()->renderIconHtml($group->getIcon()),
                    'parentUrl'    => $group->getParent() instanceof KbPages ? $this->generatePublicUrl($group->getParent()) : null,
                    'childGroups'  => array_map(function (KbPages $childGroup): array {
                        return [
                            'entity'   => $childGroup,
                            'iconHtml' => $this->getSettingsProvider()->renderIconHtml($childGroup->getIcon()),
                            'url'      => $this->generatePublicUrl($childGroup),
                        ];
                    }, $repo->findPublishedGroupsByParent($group)),
                    'articles'     => array_map(function (KbPages $article): array {
                        return [
                            'entity'   => $article,
                            'iconHtml' => $this->getSettingsProvider()->renderIconHtml($article->getIcon()),
                            'url'      => $this->generatePublicUrl($article),
                        ];
                    }, $repo->findPublishedArticlesByGroup($group)),
                ], $this->getRootAncestor($group))
            )
        );
    }

    private function renderArticleResponse(KbPages $article): Response
    {
        $group = $article->getParent();

        return new Response(
            $this->renderView(
                '@MauticKbPages/Public/article.html.twig',
                $this->getPublicViewParameters([
                    'article'         => $article,
                    'articleContent'  => $this->renderPublicHtml($article->getContent()),
                    'articleIconHtml' => $this->getSettingsProvider()->renderIconHtml($article->getIcon()),
                    'group'           => $group,
                    'groupUrl'        => $group instanceof KbPages ? $this->generatePublicUrl($group) : null,
                ], $this->getRootAncestor($article))
            )
        );
    }

    private function resolveRootGroup(): ?KbPages
    {
        $rootSlug = $this->resolveRootSlug();
        if ('' === $rootSlug) {
            return null;
        }

        return $this->resolveVisibleRootGroupBySlug($rootSlug);
    }

    private function resolveRootSlug(): string
    {
        $request = $this->getCurrentRequest();
        $host    = strtolower($request->getHost());
        $hostKey = $this->normalizeDomainKey($host);
        $map     = $this->getConfiguredDomainRoots();

        if (isset($map[$hostKey])) {
            return $map[$hostKey];
        }

        return $this->getConfiguredRootSlug();
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

    private function getConfiguredRootSlug(): string
    {
        return $this->normalizeSlug((string) $this->coreParametersHelper->get('kbpages_default_root', ''));
    }

    private function useHiddenRootUrls(): bool
    {
        $route = (string) $this->getCurrentRequest()->attributes->get('_route', '');

        return $this->hasHostScopedRoutes()
            && in_array($route, ['mautic_knowledgebase_root', 'mautic_knowledgebase_host_tree'], true);
    }

    private function generatePublicUrl(KbPages $item): ?string
    {
        $segments = $this->getPathSegments($item);
        if ([] === $segments) {
            return null;
        }

        $rootGroup = $this->resolveRootGroup();
        $rootPath  = $rootGroup instanceof KbPages ? $this->getPathSegments($rootGroup) : [];
        if (
            $this->useHiddenRootUrls()
            && [] !== $rootPath
            && array_slice($segments, 0, count($rootPath)) === $rootPath
        ) {
            $descendantSegments = array_slice($segments, count($rootPath));

            if ([] === $descendantSegments) {
                return $this->generateUrl('mautic_knowledgebase_root');
            }

            return $this->generateUrl('mautic_knowledgebase_host_tree', [
                'slugPath' => implode('/', $descendantSegments),
            ]);
        }

        return $this->generateCanonicalPublicUrl($this->getCanonicalPathSegments($item));
    }

    /**
     * @param string[] $segments
     */
    private function generateCanonicalPublicUrl(array $segments): ?string
    {
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
    private function getPathSegments(KbPages $item): array
    {
        $segments = [];
        $current  = $item;

        while ($current instanceof KbPages) {
            $slug = (string) $current->getSlug();
            if ('' === $slug) {
                return [];
            }

            array_unshift($segments, $slug);
            $current = $current->getParent();
        }

        return $segments;
    }

    /**
     * @return string[]
     */
    private function getCanonicalPathSegments(KbPages $item): array
    {
        $segments = $this->getPathSegments($item);
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

    private function getRootAncestor(KbPages $item): KbPages
    {
        $current = $item;

        while ($current->getParent() instanceof KbPages) {
            $current = $current->getParent();
        }

        return $current;
    }

    private function resolveVisibleRootGroupBySlug(string $slug): ?KbPages
    {
        return $this->resolveVisibleGroupBySlug($slug);
    }

    private function resolveVisibleGroupBySlug(string $slug): ?KbPages
    {
        $repo  = $this->getKnowledgebaseModel()->getRepository();
        $group = $repo->findPublishedGroupBySlug($slug);

        if ($group instanceof KbPages) {
            return $group;
        }

        $hostGroup = $this->resolveHostDomainGroup();
        if (!$hostGroup instanceof KbPages) {
            return null;
        }

        return $repo->findPublishedGroupBySlug($slug, $hostGroup);
    }

    private function resolveHostDomainGroup(): ?KbPages
    {
        $host = strtolower($this->getCurrentRequest()->getHost());
        if ('' === $host) {
            return null;
        }

        $hostKey = $this->normalizeDomainKey($host);
        if ('' === $hostKey) {
            return null;
        }

        return $this->getKnowledgebaseModel()->getRepository()->findPublishedGroupBySlug($hostKey);
    }

    private function resolveTreeItem(KbPages $rootGroup, string $slugPath): ?KbPages
    {
        $segments = array_values(array_filter(explode('/', trim($slugPath, '/')), static fn (string $segment): bool => '' !== $segment));
        if ([] === $segments) {
            return $rootGroup;
        }

        $repo    = $this->getKnowledgebaseModel()->getRepository();
        $current = $rootGroup;

        foreach ($segments as $index => $segment) {
            $child = $repo->findPublishedChildBySlug($current, $segment);
            if (!$child instanceof KbPages) {
                return null;
            }

            $isLast = $index === count($segments) - 1;
            if (!$isLast && !$child->isGroup()) {
                return null;
            }

            $current = $child;
        }

        return $current;
    }

    private function hasHostScopedRoutes(): bool
    {
        return '' !== trim((string) $this->coreParametersHelper->get('kbpages_root_hosts', ''));
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

    private function getPageModel(): PageModel
    {
        $model = $this->getModel('page');
        \assert($model instanceof PageModel);

        return $model;
    }

    private function getFormModel(): FormModel
    {
        $model = $this->getModel('form');
        \assert($model instanceof FormModel);

        return $model;
    }

    private function getSettingsProvider(): KbPagesSettings
    {
        $service = $this->container->get(KbPagesSettings::class);
        \assert($service instanceof KbPagesSettings);

        return $service;
    }
}
