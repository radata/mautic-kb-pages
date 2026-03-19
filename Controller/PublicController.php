<?php

namespace MauticPlugin\MauticKbPagesBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\PageBundle\Model\PageModel;
use MauticPlugin\MauticKbPagesBundle\Entity\KbPages;
use MauticPlugin\MauticKbPagesBundle\Model\KbPagesModel;
use MauticPlugin\MauticKbPagesBundle\Service\KbPagesSettings;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends CommonController
{
    public function rootAction(): Response
    {
        $defaultRoot = $this->getConfiguredRootSlug();

        if ('' !== $defaultRoot) {
            $repo  = $this->getKnowledgebaseModel()->getRepository();
            $group = $repo->findPublishedGroupBySlug($defaultRoot);

            if ($group instanceof KbPages) {
                return $this->renderGroupResponse($group);
            }
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

    public function groupAction(string $slug): Response
    {
        $repo  = $this->getKnowledgebaseModel()->getRepository();
        $group = $repo->findPublishedGroupBySlug($slug);

        if (!$group instanceof KbPages) {
            throw $this->createNotFoundException();
        }

        return $this->renderGroupResponse($group);
    }

    public function articleAction(string $groupSlug, string $slug): Response
    {
        $repo    = $this->getKnowledgebaseModel()->getRepository();
        $article = $repo->findPublishedArticleBySlugs($groupSlug, $slug);

        if (!$article instanceof KbPages) {
            throw $this->createNotFoundException();
        }

        return new Response(
            $this->renderView(
                '@MauticKbPages/Public/article.html.twig',
                $this->getPublicViewParameters([
                    'article'        => $article,
                    'articleContent' => $this->renderPublicHtml($article->getContent()),
                    'articleIconHtml' => $this->getSettingsProvider()->renderIconHtml($article->getIcon()),
                    'group'          => $article->getParent(),
                ])
            )
        );
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    private function getPublicViewParameters(array $parameters): array
    {
        $kbSettings = $this->getSettingsProvider()->getPublicSettings();

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

        return $this->getSettingsProvider()->rewriteMediaUrls($rendered);
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
                    'articles'     => array_map(function (KbPages $article): array {
                        return [
                            'entity'   => $article,
                            'iconHtml' => $this->getSettingsProvider()->renderIconHtml($article->getIcon()),
                        ];
                    }, $repo->findPublishedArticlesByGroup($group)),
                ])
            )
        );
    }

    private function getConfiguredRootSlug(): string
    {
        $slug = strtolower(trim((string) $this->coreParametersHelper->get('kbpages_default_root', '')));
        $slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug) ?? '';

        return trim($slug, '-');
    }

    private function getPageModel(): PageModel
    {
        $model = $this->getModel('page');
        \assert($model instanceof PageModel);

        return $model;
    }

    private function getSettingsProvider(): KbPagesSettings
    {
        $service = $this->container->get('mautic.kbpages.settings');
        \assert($service instanceof KbPagesSettings);

        return $service;
    }
}
