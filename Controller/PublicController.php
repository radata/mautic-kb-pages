<?php

namespace MauticPlugin\MauticKbPagesBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\MauticKbPagesBundle\Entity\KbPages;
use MauticPlugin\MauticKbPagesBundle\Model\KbPagesModel;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends CommonController
{
    public function homeAction(): Response
    {
        $repo = $this->getKnowledgebaseModel()->getRepository();

        return new Response(
            $this->renderView(
                '@MauticKbPages/Public/index.html.twig',
                $this->getPublicViewParameters([
                    'groups' => $repo->findPublishedGroups(),
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

        return new Response(
            $this->renderView(
                '@MauticKbPages/Public/group.html.twig',
                $this->getPublicViewParameters([
                    'group'    => $group,
                    'articles' => $repo->findPublishedArticlesByGroup($group),
                ])
            )
        );
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
                    'article' => $article,
                    'group'   => $article->getParent(),
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
        return array_merge(
            [
                'kbSettings' => [
                    'headerHtml'    => (string) $this->coreParametersHelper->get('kbpages_header_html', ''),
                    'footerHtml'    => (string) $this->coreParametersHelper->get('kbpages_footer_html', ''),
                    'customCss'     => (string) $this->coreParametersHelper->get('kbpages_custom_css', ''),
                    'containerWidth' => (int) $this->coreParametersHelper->get('kbpages_container_width', 960),
                ],
            ],
            $parameters
        );
    }

    private function getKnowledgebaseModel(): KbPagesModel
    {
        $model = $this->getModel('kbpages');
        \assert($model instanceof KbPagesModel);

        return $model;
    }
}
