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
        $args['viewParameters'] = array_merge(
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
}
