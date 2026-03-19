<?php

return [
    'name'        => 'Knowledge Base',
    'description' => 'Manage grouped support articles with knowledge base pages.',
    'author'      => 'Abdullah Kiser / Friendly Automate',
    'version'     => '1.0.2',
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
                'path'       => '/kb',
                'controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::homeAction',
            ],
            'mautic_knowledgebase_group' => [
                'path'       => '/kb/{slug}',
                'controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::groupAction',
            ],
            'mautic_knowledgebase_article' => [
                'path'       => '/kb/{groupSlug}/{slug}',
                'controller' => 'MauticPlugin\MauticKbPagesBundle\Controller\PublicController::articleAction',
            ],
        ],
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
        'forms' => [
            'mautic.form.type.kbpages' => [
                'class' => \MauticPlugin\MauticKbPagesBundle\Form\Type\KbPagesType::class,
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
];
