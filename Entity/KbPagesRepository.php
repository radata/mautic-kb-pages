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
        return $this->createQueryBuilder('e')
            ->where('e.type = :groupType')
            ->andWhere('e.isPublished = :published')
            ->setParameter('groupType', KbPages::TYPE_GROUP)
            ->setParameter('published', true)
            ->orderBy('e.position', 'ASC')
            ->addOrderBy('e.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPublishedGroupBySlug(string $slug): ?KbPages
    {
        return $this->findOneBy([
            'slug'        => $slug,
            'type'        => KbPages::TYPE_GROUP,
            'isPublished' => true,
        ]);
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

    public function findPublishedArticleBySlugs(string $groupSlug, string $slug): ?KbPages
    {
        return $this->createQueryBuilder('article')
            ->innerJoin('article.parent', 'parentGroup')
            ->where('article.type = :articleType')
            ->andWhere('article.slug = :slug')
            ->andWhere('article.isPublished = :published')
            ->andWhere('parentGroup.type = :groupType')
            ->andWhere('parentGroup.slug = :groupSlug')
            ->andWhere('parentGroup.isPublished = :published')
            ->setParameter('articleType', KbPages::TYPE_ARTICLE)
            ->setParameter('groupType', KbPages::TYPE_GROUP)
            ->setParameter('slug', $slug)
            ->setParameter('groupSlug', $groupSlug)
            ->setParameter('published', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
