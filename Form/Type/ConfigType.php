<?php

namespace MauticPlugin\MauticKbPagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('kbpages_header_html', TextareaType::class, [
            'label'      => 'plugin.kbpages.config.header_html',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => [
                'class'           => 'form-control editor editor-advanced',
                'rows'            => 8,
                'allow-full-html' => true,
            ],
        ]);

        $builder->add('kbpages_footer_html', TextareaType::class, [
            'label'      => 'plugin.kbpages.config.footer_html',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => [
                'class'           => 'form-control editor editor-advanced',
                'rows'            => 8,
                'allow-full-html' => true,
            ],
        ]);

        $builder->add('kbpages_container_width', IntegerType::class, [
            'label'      => 'plugin.kbpages.config.container_width',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => [
                'class' => 'form-control',
                'min'   => 480,
                'step'  => 1,
            ],
        ]);

        $builder->add('kbpages_public_roots', TextType::class, [
            'label'      => 'plugin.kbpages.config.public_roots',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => [
                'class'       => 'form-control',
                'placeholder' => 'nl,en',
            ],
        ]);

        $builder->add('kbpages_custom_css', TextareaType::class, [
            'label'      => 'plugin.kbpages.config.custom_css',
            'label_attr' => ['class' => 'control-label'],
            'required'   => false,
            'attr'       => [
                'class' => 'form-control',
                'rows'  => 10,
            ],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'kbpages_config';
    }
}
