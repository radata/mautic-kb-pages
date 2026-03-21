<?php

namespace MauticPlugin\MauticKbPagesBundle\EventListener;

use Mautic\CoreBundle\Event\BuilderEvent;
use Mautic\CoreBundle\Helper\BuilderTokenHelperFactory;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\PageBundle\Event\PageDisplayEvent;
use Mautic\PageBundle\PageEvents;
use MauticPlugin\MauticKbPagesBundle\Helper\KbPagesTokenHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BuilderSubscriber implements EventSubscriberInterface
{
    private const KB_PAGE_TOKEN_REGEX = '{kbpagelink=(.*?)}';

    public function __construct(
        private KbPagesTokenHelper $tokenHelper,
        private BuilderTokenHelperFactory $builderTokenHelperFactory,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EmailEvents::EMAIL_ON_BUILD   => ['onBuilderBuild', 0],
            EmailEvents::EMAIL_ON_SEND    => ['onEmailGenerate', 0],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailGenerate', 0],
            PageEvents::PAGE_ON_BUILD     => ['onBuilderBuild', 0],
            PageEvents::PAGE_ON_DISPLAY   => ['onPageDisplay', 0],
        ];
    }

    public function onBuilderBuild(BuilderEvent $event): void
    {
        if (!$event->tokensRequested(self::KB_PAGE_TOKEN_REGEX)) {
            return;
        }

        $tokenHelper = $this->builderTokenHelperFactory->getBuilderTokenHelper(
            'kbpages',
            'kbpages:items',
            'MauticKbPagesBundle',
            'kbpages'
        );

        $event->addTokensFromHelper($tokenHelper, self::KB_PAGE_TOKEN_REGEX, 'title', 'id', true);
    }

    public function onEmailGenerate(EmailSendEvent $event): void
    {
        $tokens = $this->tokenHelper->findKbPageTokens($event->getContent().$event->getPlainText());
        if ([] === $tokens) {
            return;
        }

        $event->addTokens($tokens);
    }

    public function onPageDisplay(PageDisplayEvent $event): void
    {
        $content = $event->getContent();
        $tokens  = $this->tokenHelper->findKbPageTokens($content);

        if ([] !== $tokens) {
            $content = str_ireplace(array_keys($tokens), $tokens, $content);
        }

        $event->setContent($content);
    }
}
