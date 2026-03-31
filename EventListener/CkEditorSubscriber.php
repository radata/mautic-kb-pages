<?php

namespace MauticPlugin\MauticKbPagesBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomAssetsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Extends CKEditor's htmlSupport.allow list so that blockquote/notification-card
 * classes (quote-card, info-card, etc.) are preserved in all admin editors,
 * not only in fields that carry the allow-full-html attribute.
 */
class CkEditorSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_ASSETS => ['extendHtmlSupport', 0],
        ];
    }

    public function extendHtmlSupport(CustomAssetsEvent $event): void
    {
        $event->addScriptDeclaration(<<<'JS'
(function () {
    function patch() {
        if (typeof Mautic === 'undefined' || typeof Mautic.GetCkEditorConfigOptions !== 'function') {
            return;
        }

        var original = Mautic.GetCkEditorConfigOptions;

        Mautic.GetCkEditorConfigOptions = function (ckEditorToolbarOptions, tokenCallback, textarea) {
            var config = original.call(this, ckEditorToolbarOptions, tokenCallback, textarea);

            // allow-full-html already enables everything; only patch the restricted mode
            var allowFullHtml = textarea && typeof textarea.attr('allow-full-html') !== 'undefined';
            if (!allowFullHtml && config.htmlSupport && Array.isArray(config.htmlSupport.allow)) {
                config.htmlSupport.allow.push(
                    { name: 'blockquote', classes: true, attributes: true, styles: true },
                    { name: 'p',          classes: true },
                    { name: 'i',          classes: true },
                    { name: 'code',       classes: true },
                    { name: 'section',    classes: true, attributes: true, styles: true },
                    { name: 'figure',     classes: true },
                    { name: 'div',        classes: true, attributes: true, styles: true }
                );
            }

            return config;
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', patch);
    } else {
        patch();
    }
}());
JS, 'bodyClose');
    }
}
