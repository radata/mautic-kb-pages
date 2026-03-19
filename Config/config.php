<?php

return [
    'name'        => 'Knowledge Base',
    'description' => 'Manage grouped support articles with knowledge base pages.',
    'author'      => 'Abdullah Kiser / Friendly Automate',
    'version'     => '1.0.6',
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
            'mautic_knowledgebase_article' => [
                'path'       => '/{groupSlug}/{slug}',
                'controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::articleAction',
                'requirements' => [
                    'groupSlug' => '(?!(?:s|api|_knowledgebase|_profiler|_wdt|css|images|js|favicon\.ico|mtc|r|redirect|mtracking\.gif)(?:/|$))[^/]+',
                    'slug'      => '[^/]+',
                ],
            ],
            'mautic_knowledgebase_group' => [
                'path'       => '/{slug}',
                'controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::groupAction',
                'requirements' => [
                    'slug' => '(?!(?:s|api|_knowledgebase|_profiler|_wdt|css|images|js|favicon\.ico|mtc|r|redirect|mtracking\.gif)$)[^/]+',
                ],
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
        'events' => [
            'mautic.kbpages.config.subscriber' => [
                'class' => \MauticPlugin\MauticKbPagesBundle\EventListener\ConfigSubscriber::class,
                'arguments' => [],
            ],
        ],
        'forms' => [
            'mautic.form.type.kbpages' => [
                'class' => \MauticPlugin\MauticKbPagesBundle\Form\Type\KbPagesType::class,
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
    ],
    'parameters' => [
        'kbpages_header_html'    => '',
        'kbpages_footer_html'    => '',
        'kbpages_custom_css'     => '',
        'kbpages_container_width' => 960,
    ],
];
