<?php

namespace MauticPlugin\MauticKbPagesBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\CoreBundle\Model\FormModel;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\CoreBundle\Translation\Translator;
use MauticPlugin\MauticKbPagesBundle\Entity\KbPages;
use MauticPlugin\MauticKbPagesBundle\Form\Type\KbPagesType;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class KbPagesModel extends FormModel
{
    public function __construct(
        EntityManagerInterface $em,
        CorePermissions $security,
        EventDispatcherInterface $dispatcher,
        UrlGeneratorInterface $router,
        Translator $translator,
        UserHelper $userHelper,
        LoggerInterface $logger,
        CoreParametersHelper $coreParametersHelper,
    ) {
        parent::__construct($em, $security, $dispatcher, $router, $translator, $userHelper, $logger, $coreParametersHelper);
    }

    public function getActionRouteBase()
    {
        return 'knowledgebase';
    }

    public function getPermissionBase()
    {
        return 'kbpages:items';
    }

    public function createForm($entity, FormFactoryInterface $formFactory, $action = null, $options = []): FormInterface
    {
        if (!$entity instanceof KbPages) {
            throw new MethodNotAllowedHttpException(['KbPages']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(KbPagesType::class, $entity, $options);
    }

    public function getRepository()
    {
        return $this->em->getRepository(KbPages::class);
    }

    public function getEntity($id = null): ?object
    {
        if (null === $id) {
            return new KbPages();
        }

        return parent::getEntity($id);
    }

    public function saveEntity($entity, $unlock = true): void
    {
        if (!$entity instanceof KbPages) {
            throw new MethodNotAllowedHttpException(['KbPages']);
        }

        if (!$entity->getType()) {
            $entity->setType(KbPages::TYPE_GROUP);
        }

        if ($entity->isGroup()) {
            $entity->setParent(null);
        }

        $entity->setSlug($this->createUniqueSlug($entity));
        $entity->setPosition((int) $entity->getPosition());

        parent::saveEntity($entity, $unlock);
    }

    private function createUniqueSlug(KbPages $entity): string
    {
        $baseSlug = $this->slugify((string) ($entity->getSlug() ?: $entity->getTitle() ?: 'page'));
        $slug     = $baseSlug;
        $counter  = 2;

        do {
            $existing = $this->getRepository()->findOneBy(['slug' => $slug]);

            if (!$existing instanceof KbPages || $existing->getId() === $entity->getId()) {
                return $slug;
            }

            $slug = sprintf('%s-%d', $baseSlug, $counter);
            ++$counter;
        } while (true);
    }

    private function slugify(string $value): string
    {
        $value = trim($value);
        $value = function_exists('iconv') ? (string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) : $value;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return '' !== $value ? $value : 'page';
    }
}
