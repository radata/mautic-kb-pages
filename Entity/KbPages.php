<?php

namespace MauticPlugin\MauticKbPagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class KbPages extends FormEntity
{
    public const TYPE_GROUP   = 'group';
    public const TYPE_ARTICLE = 'article';

    private $id;
    private ?string $title = null;
    private ?string $slug = null;
    private string $type = self::TYPE_GROUP;
    private ?string $summary = null;
    private ?string $content = null;
    private ?string $icon = null;
    private int $position = 0;
    private ?self $parent = null;

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields'  => ['slug'],
            'message' => 'mautic.kbpages.slug.unique',
        ]));

        $metadata->addPropertyConstraint('title', new Assert\NotBlank([
            'message' => 'mautic.kbpages.title.required',
        ]));

        $metadata->addPropertyConstraint('type', new Assert\Choice([
            'choices' => [self::TYPE_GROUP, self::TYPE_ARTICLE],
            'message' => 'mautic.kbpages.type.invalid',
        ]));

        $metadata->addConstraint(new Assert\Callback('validateHierarchy'));
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('kb_pages')
            ->setCustomRepositoryClass(KbPagesRepository::class)
            ->addIndex(['slug'], 'kb_pages_slug_search')
            ->addIndex(['type', 'is_published'], 'kb_pages_type_published')
            ->addIndex(['parent_id', 'is_published'], 'kb_pages_parent_published')
            ->addIndex(['position'], 'kb_pages_position_search');

        $builder->addId();

        $builder->createField('title', 'string')
            ->columnName('title')
            ->length(191)
            ->build();

        $builder->createField('slug', 'string')
            ->columnName('slug')
            ->length(191)
            ->build();

        $builder->createField('type', 'string')
            ->columnName('type')
            ->length(25)
            ->build();

        $builder->createField('summary', 'text')
            ->columnName('summary')
            ->nullable()
            ->build();

        $builder->createField('content', 'text')
            ->columnName('content')
            ->nullable()
            ->build();

        $builder->createField('icon', 'string')
            ->columnName('icon')
            ->length(191)
            ->nullable()
            ->build();

        $builder->createField('position', 'integer')
            ->columnName('position')
            ->build();

        $builder->createManyToOne('parent', self::class)
            ->addJoinColumn('parent_id', 'id', true, false, 'SET NULL')
            ->build();
    }

    public function validateHierarchy(ExecutionContextInterface $context): void
    {
        if ($this->isGroup() && null !== $this->parent) {
            $context->buildViolation('mautic.kbpages.parent.group_only')
                ->atPath('parent')
                ->addViolation();
        }

        if ($this->isArticle() && null === $this->parent) {
            $context->buildViolation('mautic.kbpages.parent.required')
                ->atPath('parent')
                ->addViolation();
        }

        if (null !== $this->parent) {
            if (null !== $this->id && $this->parent->getId() === $this->id) {
                $context->buildViolation('mautic.kbpages.parent.self')
                    ->atPath('parent')
                    ->addViolation();
            }

            if (!$this->parent->isGroup()) {
                $context->buildViolation('mautic.kbpages.parent.invalid')
                    ->atPath('parent')
                    ->addViolation();
            }
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function isGroup(): bool
    {
        return self::TYPE_GROUP === $this->type;
    }

    public function isArticle(): bool
    {
        return self::TYPE_ARTICLE === $this->type;
    }

    public function getName(): ?string
    {
        return $this->title;
    }

    public function setName(string $name): self
    {
        return $this->setTitle($name);
    }
}
