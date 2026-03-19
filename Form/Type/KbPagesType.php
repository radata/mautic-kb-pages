<?php

namespace MauticPlugin\MauticKbPagesBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Mautic\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Mautic\CoreBundle\Form\EventListener\FormExitSubscriber;
use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use MauticPlugin\MauticKbPagesBundle\Entity\KbPages;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KbPagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber(new CleanFormSubscriber([
            'summary'    => 'html',
            'content'    => 'raw',
            'headerHtml' => 'raw',
            'footerHtml' => 'raw',
            'snippetsHtml' => 'raw',
        ]));
        $builder->addEventSubscriber(new FormExitSubscriber('knowledgebase', $options));

        $data      = $options['data'] instanceof KbPages ? $options['data'] : null;
        $currentId = $data?->getId();

        $builder->add('title', TextType::class, [
            'label'      => 'plugin.kbpages.title',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
        ]);

        $builder->add('slug', TextType::class, [
            'label'      => 'plugin.kbpages.slug',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => [
                'class'       => 'form-control',
                'placeholder' => 'getting-started',
            ],
        ]);

        $builder->add('type', ChoiceType::class, [
            'label'      => 'plugin.kbpages.type',
            'label_attr' => ['class' => 'control-label'],
            'choices'    => [
                'plugin.kbpages.type.group'   => KbPages::TYPE_GROUP,
                'plugin.kbpages.type.article' => KbPages::TYPE_ARTICLE,
            ],
            'attr'       => ['class' => 'form-control'],
        ]);

        $builder->add('parent', EntityType::class, [
            'class'         => KbPages::class,
            'choice_label'  => 'title',
            'label'         => 'plugin.kbpages.parent',
            'label_attr'    => ['class' => 'control-label'],
            'required'      => false,
            'placeholder'   => 'plugin.kbpages.parent.placeholder',
            'attr'          => ['class' => 'form-control'],
            'choice_attr'   => function (?KbPages $choice): array {
                if (!$choice instanceof KbPages) {
                    return [];
                }

                $width = $this->resolveRootPreviewWidth($choice);
                if (null === $width) {
                    return [];
                }

                return ['data-root-width' => (string) $width];
            },
            'query_builder' => function (EntityRepository $repository) use ($currentId) {
                $qb = $repository->createQueryBuilder('e')
                    ->where('e.type = :groupType')
                    ->setParameter('groupType', KbPages::TYPE_GROUP)
                    ->orderBy('e.position', 'ASC')
                    ->addOrderBy('e.title', 'ASC');

                if (!empty($currentId)) {
                    $qb->andWhere('e.id != :currentId')
                        ->setParameter('currentId', $currentId);
                }

                return $qb;
            },
        ]);

        $builder->add('summary', TextareaType::class, [
            'label'      => 'plugin.kbpages.summary',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => [
                'class' => 'form-control',
                'rows'  => 4,
            ],
        ]);

        $builder->add('content', TextareaType::class, [
            'label'      => 'plugin.kbpages.content',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => [
                'class'           => 'form-control editor editor-advanced',
                'rows'            => 16,
                'allow-full-html' => 'true',
            ],
        ]);

        $builder->add('contentUseRaw', CheckboxType::class, [
            'mapped'      => false,
            'required'    => false,
            'label'       => 'plugin.kbpages.content.use_raw',
            'help'        => 'plugin.kbpages.content.use_raw.help',
            'label_attr'  => ['class' => 'control-label'],
        ]);

        $builder->add('contentRaw', TextareaType::class, [
            'mapped'      => false,
            'required'    => false,
            'label'       => 'plugin.kbpages.content.raw',
            'label_attr'  => ['class' => 'control-label'],
            'help'        => 'plugin.kbpages.content.raw.help',
            'data'        => $data?->getContent(),
            'attr'        => [
                'class' => 'form-control',
                'rows'  => 16,
                'style' => 'font-family: monospace;',
                'spellcheck' => 'false',
            ],
        ]);

        $builder->add('icon', TextType::class, [
            'label'      => 'plugin.kbpages.icon',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'help'       => 'plugin.kbpages.icon.help',
            'attr'       => [
                'class'       => 'form-control',
                'placeholder' => 'book, ti ti-book, <svg>...</svg>, icons/help.svg',
            ],
        ]);

        $builder->add('headerHtml', TextareaType::class, [
            'label'      => 'plugin.kbpages.root_header_html',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'help'       => 'plugin.kbpages.root_shell.help',
            'attr'       => [
                'class'           => 'form-control editor editor-advanced',
                'rows'            => 8,
                'allow-full-html' => true,
            ],
        ]);

        $builder->add('headerHtmlUseRaw', CheckboxType::class, [
            'mapped'      => false,
            'required'    => false,
            'label'       => 'plugin.kbpages.content.use_raw',
            'help'        => 'plugin.kbpages.content.use_raw.help',
            'label_attr'  => ['class' => 'control-label'],
        ]);

        $builder->add('headerHtmlRaw', TextareaType::class, [
            'mapped'      => false,
            'required'    => false,
            'label'       => 'plugin.kbpages.root_header_html.raw',
            'label_attr'  => ['class' => 'control-label'],
            'help'        => 'plugin.kbpages.content.raw.help',
            'data'        => $data?->getHeaderHtml(),
            'attr'        => [
                'class' => 'form-control',
                'rows'  => 8,
                'style' => 'font-family: monospace;',
                'spellcheck' => 'false',
            ],
        ]);

        $builder->add('footerHtml', TextareaType::class, [
            'label'      => 'plugin.kbpages.root_footer_html',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'help'       => 'plugin.kbpages.root_shell.help',
            'attr'       => [
                'class'           => 'form-control editor editor-advanced',
                'rows'            => 8,
                'allow-full-html' => true,
            ],
        ]);

        $builder->add('footerHtmlUseRaw', CheckboxType::class, [
            'mapped'      => false,
            'required'    => false,
            'label'       => 'plugin.kbpages.content.use_raw',
            'help'        => 'plugin.kbpages.content.use_raw.help',
            'label_attr'  => ['class' => 'control-label'],
        ]);

        $builder->add('footerHtmlRaw', TextareaType::class, [
            'mapped'      => false,
            'required'    => false,
            'label'       => 'plugin.kbpages.root_footer_html.raw',
            'label_attr'  => ['class' => 'control-label'],
            'help'        => 'plugin.kbpages.content.raw.help',
            'data'        => $data?->getFooterHtml(),
            'attr'        => [
                'class' => 'form-control',
                'rows'  => 8,
                'style' => 'font-family: monospace;',
                'spellcheck' => 'false',
            ],
        ]);

        $builder->add('containerWidth', IntegerType::class, [
            'label'      => 'plugin.kbpages.root_container_width',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'help'       => 'plugin.kbpages.root_shell.help',
            'attr'       => [
                'class' => 'form-control',
                'min'   => 480,
                'step'  => 1,
            ],
        ]);

        $builder->add('customCss', TextareaType::class, [
            'label'      => 'plugin.kbpages.root_custom_css',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'help'       => 'plugin.kbpages.root_shell.help',
            'attr'       => [
                'class' => 'form-control',
                'rows'  => 8,
            ],
        ]);

        $builder->add('snippetsHtml', TextareaType::class, [
            'label'      => 'plugin.kbpages.root_snippets_html',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'help'       => 'plugin.kbpages.root_snippets_html.help',
            'attr'       => [
                'class'      => 'form-control',
                'rows'       => 12,
                'style'      => 'font-family: monospace;',
                'spellcheck' => 'false',
            ],
        ]);

        $builder->add('position', IntegerType::class, [
            'label'      => 'plugin.kbpages.position',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => ['class' => 'form-control'],
        ]);

        $builder->add('isPublished', YesNoButtonGroupType::class, [
            'label' => 'mautic.core.form.available',
            'data'  => $data ? $data->isPublished(false) : true,
        ]);

        $builder->add('buttons', FormButtonsType::class);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            if (!is_array($data)) {
                return;
            }

            $this->applyRawOverride($data, 'content');
            $this->applyRawOverride($data, 'headerHtml');
            $this->applyRawOverride($data, 'footerHtml');

            $event->setData($data);
        });

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => KbPages::class,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function applyRawOverride(array &$data, string $field): void
    {
        $toggleField = $field.'UseRaw';
        $rawField    = $field.'Raw';

        $useRaw = in_array($data[$toggleField] ?? null, [true, 1, '1', 'true', 'on'], true);
        if ($useRaw) {
            $data[$field] = (string) ($data[$rawField] ?? '');
        }

        unset($data[$toggleField], $data[$rawField]);
    }

    private function resolveRootPreviewWidth(?KbPages $item): ?int
    {
        $current = $item;

        while ($current instanceof KbPages) {
            $width = $current->getContainerWidth();
            if (null !== $width && $width >= 480) {
                return $width;
            }

            $current = $current->getParent();
        }

        return null;
    }

    public function getBlockPrefix()
    {
        return 'knowledgebase_item';
    }
}
