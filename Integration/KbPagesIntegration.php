<?php

namespace MauticPlugin\MauticKbPagesBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class KbPagesIntegration extends AbstractIntegration
{
    protected bool $coreIntegration = false;

    public function getName(): string
    {
        return 'KbPages';
    }

    public function getDisplayName(): string
    {
        return 'Knowledge Base Pages';
    }

    public function getSecretKeys(): array
    {
        return [];
    }

    public function getRequiredKeyFields(): array
    {
        return [];
    }

    public function getAuthenticationType(): string
    {
        return 'none';
    }

    public function appendToForm(&$builder, $data, $formArea): void
    {
        if ('features' !== $formArea) {
            return;
        }

        $builder->add('tabler_css_url', TextType::class, [
            'label'    => 'plugin.kbpages.integration.tabler_css_url',
            'required' => false,
            'data'     => $data['tabler_css_url'] ?? '',
            'help'     => 'plugin.kbpages.integration.tabler_css_url.help',
            'attr'     => [
                'class'       => 'form-control',
                'placeholder' => 'https://cdn.example.com/tabler-icons.min.css',
            ],
        ]);

        $builder->add('media_cdn_url', TextType::class, [
            'label'    => 'plugin.kbpages.integration.media_cdn_url',
            'required' => false,
            'data'     => $data['media_cdn_url'] ?? '',
            'help'     => 'plugin.kbpages.integration.media_cdn_url.help',
            'attr'     => [
                'class'       => 'form-control',
                'placeholder' => 'https://cdn.example.com/knowledgebase',
            ],
        ]);
    }
}
