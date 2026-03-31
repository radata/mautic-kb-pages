<?php

return [
    'name'        => 'Knowledge Base',
    'description' => 'Manage grouped support articles with knowledge base pages.',
    'author'      => 'Abdullah Kiser / Friendly Automate',
    'version'     => '1.1.3',
    'routes'      => [
        'main' => [
            'mautic_knowledgebase_index' => [
                'path'       => '/knowledgebase/{page}',
                'controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\KbPagesController::indexAction',
                'defaults'   => ['page' => 1],
            ],
            'mautic_knowledgebase_action' => [
                'path'       => '/knowledgebase/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\KbPagesController::executeAction',
            ],
        ],
        'public' => [
            'mautic_knowledgebase_home' => [
                'path'       => '/_knowledgebase',
                'controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::homeAction',
            ],
        ],
        'catchall' => [],
    ],
    'menu' => [
        'main' => [
            'mautic.kbpages.menu' => [
                'route'     => 'mautic_knowledgebase_index',
                'access'    => ['kbpages:items:viewown', 'kbpages:items:viewother'],
                'priority'  => 10,
                'iconClass' => 'ri-book-open-line',
            ],
        ],
    ],
    'services' => [
        'integrations' => [
            'mautic.integration.kbpages' => [
                'class'     => \MauticPlugin\MauticKbPagesBundle\Integration\KbPagesIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'request_stack',
                    'router',
                    'translator',
                    'monolog.logger.mautic',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                    'mautic.lead.field.fields_with_unique_identifier',
                ],
            ],
        ],
        'events' => [
            'mautic.kbpages.ckeditor.subscriber' => [
                'class' => \MauticPlugin\MauticKbPagesBundle\EventListener\CkEditorSubscriber::class,
            ],
            'mautic.kbpages.builder.subscriber' => [
                'class' => \MauticPlugin\MauticKbPagesBundle\EventListener\BuilderSubscriber::class,
                'arguments' => [
                    'mautic.kbpages.token_helper',
                    'mautic.helper.token_builder.factory',
                ],
            ],
            'mautic.kbpages.config.subscriber' => [
                'class' => \MauticPlugin\MauticKbPagesBundle\EventListener\ConfigSubscriber::class,
                'arguments' => [],
            ],
            'mautic.kbpages.route.subscriber' => [
                'class' => \MauticPlugin\MauticKbPagesBundle\EventListener\RouteSubscriber::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                ],
            ],
        ],
        'forms' => [
            'mautic.form.type.kbpages' => [
                'class'     => \MauticPlugin\MauticKbPagesBundle\Form\Type\KbPagesType::class,
                'arguments' => ['mautic.helper.paths'],
            ],
            'mautic.form.type.kbpages.config' => [
                'class' => \MauticPlugin\MauticKbPagesBundle\Form\Type\ConfigType::class,
            ],
        ],
        'models' => [
            'mautic.kbpages.model.kbpages' => [
                'class'     => \MauticPlugin\MauticKbPagesBundle\Model\KbPagesModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'mautic.security',
                    'event_dispatcher',
                    'router',
                    'translator',
                    'mautic.helper.user',
                    'monolog.logger.mautic',
                    'mautic.helper.core_parameters',
                ],
                'alias' => 'model.kbpages.kbpages',
            ],
        ],
        'others' => [
            'mautic.kbpages.url_generator' => [
                'class'     => \MauticPlugin\MauticKbPagesBundle\Service\KbPagesUrlGenerator::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'router',
                ],
            ],
            \MauticPlugin\MauticKbPagesBundle\Service\KbPagesUrlGenerator::class => [
                'class'     => \MauticPlugin\MauticKbPagesBundle\Service\KbPagesUrlGenerator::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'router',
                ],
            ],
            'mautic.kbpages.token_helper' => [
                'class'     => \MauticPlugin\MauticKbPagesBundle\Helper\KbPagesTokenHelper::class,
                'arguments' => [
                    'mautic.kbpages.model.kbpages',
                    'mautic.kbpages.url_generator',
                ],
            ],
            \MauticPlugin\MauticKbPagesBundle\Helper\KbPagesTokenHelper::class => [
                'class'     => \MauticPlugin\MauticKbPagesBundle\Helper\KbPagesTokenHelper::class,
                'arguments' => [
                    'mautic.kbpages.model.kbpages',
                    'mautic.kbpages.url_generator',
                ],
            ],
            'mautic.kbpages.settings' => [
                'class'     => \MauticPlugin\MauticKbPagesBundle\Service\KbPagesSettings::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.helper.integration',
                ],
            ],
            \MauticPlugin\MauticKbPagesBundle\Service\KbPagesSettings::class => [
                'class'     => \MauticPlugin\MauticKbPagesBundle\Service\KbPagesSettings::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.helper.integration',
                ],
            ],
        ],
    ],
    'parameters' => [
        'kbpages_header_html'    => '',
        'kbpages_footer_html'    => '',
        'kbpages_custom_css'     => '',
        'kbpages_container_width' => 960,
        'kbpages_public_roots'   => 'nl,en',
        'kbpages_domain_roots'   => '',
        'kbpages_default_root'   => 'nl',
        'kbpages_root_hosts'     => '',
    ],
];
