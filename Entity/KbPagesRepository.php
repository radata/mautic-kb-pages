<?php

namespace MauticPlugin\MauticKbPagesBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

class KbPagesRepository extends CommonRepository
{
    /**
     * @return KbPages[]
     */
    public function findPublishedGroups(): array
    {
        return $this->findPublishedGroupsByParent();
    }

    /**
     * @return KbPages[]
     */
    public function findPublishedGroupsByParent(?KbPages $parent = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.type = :groupType')
            ->andWhere('e.isPublished = :published')
            ->setParameter('groupType', KbPages::TYPE_GROUP)
            ->setParameter('published', true)
            ->orderBy('e.position', 'ASC')
            ->addOrderBy('e.title', 'ASC');

        if ($parent instanceof KbPages) {
            $qb->andWhere('e.parent = :parent')
                ->setParameter('parent', $parent);
        } else {
            $qb->andWhere('e.parent IS NULL');
        }

        return $qb->getQuery()->getResult();
    }

    public function findPublishedGroupBySlug(string $slug, ?KbPages $parent = null): ?KbPages
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.slug = :slug')
            ->andWhere('e.type = :groupType')
            ->andWhere('e.isPublished = :published')
            ->setParameter('slug', $slug)
            ->setParameter('groupType', KbPages::TYPE_GROUP)
            ->setParameter('published', true);

        if ($parent instanceof KbPages) {
            $qb->andWhere('e.parent = :parent')
                ->setParameter('parent', $parent);
        } else {
            $qb->andWhere('e.parent IS NULL');
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return KbPages[]
     */
    public function findPublishedArticlesByGroup(KbPages $group): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.type = :articleType')
            ->andWhere('e.parent = :parent')
            ->andWhere('e.isPublished = :published')
            ->setParameter('articleType', KbPages::TYPE_ARTICLE)
            ->setParameter('parent', $group)
            ->setParameter('published', true)
            ->orderBy('e.position', 'ASC')
            ->addOrderBy('e.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPublishedArticleByGroupAndSlug(KbPages $group, string $slug): ?KbPages
    {
        return $this->createQueryBuilder('article')
            ->where('article.type = :articleType')
            ->andWhere('article.slug = :slug')
            ->andWhere('article.isPublished = :published')
            ->andWhere('article.parent = :group')
            ->setParameter('articleType', KbPages::TYPE_ARTICLE)
            ->setParameter('slug', $slug)
            ->setParameter('group', $group)
            ->setParameter('published', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPublishedChildBySlug(KbPages $parent, string $slug): ?KbPages
    {
        return $this->createQueryBuilder('e')
            ->where('e.parent = :parent')
            ->andWhere('e.slug = :slug')
            ->andWhere('e.isPublished = :published')
            ->setParameter('parent', $parent)
            ->setParameter('slug', $slug)
            ->setParameter('published', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
