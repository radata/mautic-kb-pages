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
                [
                    'groups' => $repo->findPublishedGroups(),
                ]
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
                [
                    'group'    => $group,
                    'articles' => $repo->findPublishedArticlesByGroup($group),
                ]
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
                [
                    'article' => $article,
                    'group'   => $article->getParent(),
                ]
            )
        );
    }

    private function getKnowledgebaseModel(): KbPagesModel
    {
        $model = $this->getModel('kbpages');
        \assert($model instanceof KbPagesModel);

        return $model;
    }
}
