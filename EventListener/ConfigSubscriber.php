<?php

namespace MauticPlugin\MauticKbPagesBundle\EventListener;

use Mautic\ConfigBundle\ConfigEvents;
use Mautic\ConfigBundle\Event\ConfigBuilderEvent;
use Mautic\ConfigBundle\Event\ConfigEvent;
use MauticPlugin\MauticKbPagesBundle\Form\Type\ConfigType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigEvents::CONFIG_ON_GENERATE => ['onConfigGenerate', 0],
            ConfigEvents::CONFIG_PRE_SAVE    => ['onConfigSave', 0],
        ];
    }

    public function onConfigGenerate(ConfigBuilderEvent $event): void
    {
        $event->addForm([
            'bundle'     => 'MauticKbPagesBundle',
            'formAlias'  => 'kbpages_config',
            'formType'   => ConfigType::class,
            'formTheme'  => '@MauticKbPages/FormTheme/Config/_config_kbpages_config_widget.html.twig',
            'parameters' => $event->getParametersFromConfig('MauticKbPagesBundle'),
        ]);
    }

    public function onConfigSave(ConfigEvent $event): void
    {
        $values = $event->getConfig();

        if (!isset($values['kbpages_config'])) {
            return;
        }

        $containerWidth = (int) ($values['kbpages_config']['kbpages_container_width'] ?? 960);
        $values['kbpages_config']['kbpages_container_width'] = $containerWidth >= 480 ? $containerWidth : 960;
        $values['kbpages_config']['kbpages_public_roots']   = $this->normalizeRoots((string) ($values['kbpages_config']['kbpages_public_roots'] ?? ''));

        $event->setConfig($values);
    }

    private function normalizeRoots(string $value): string
    {
        $roots = preg_split('/[\s,]+/', strtolower($value), -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($roots)) {
            return '';
        }

        $roots = array_map(static function (string $root): string {
            $normalized = trim($root);
            $normalized = preg_replace('/[^a-z0-9\-]+/', '-', $normalized) ?? '';

            return trim($normalized, '-');
        }, $roots);

        $roots = array_values(array_filter(array_unique($roots)));

        return implode(',', $roots);
    }
}
