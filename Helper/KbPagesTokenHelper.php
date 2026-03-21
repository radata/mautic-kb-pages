<?php

namespace MauticPlugin\MauticKbPagesBundle\Helper;

use MauticPlugin\MauticKbPagesBundle\Entity\KbPages;
use MauticPlugin\MauticKbPagesBundle\Model\KbPagesModel;
use MauticPlugin\MauticKbPagesBundle\Service\KbPagesUrlGenerator;

class KbPagesTokenHelper
{
    public const REGEX = '/{kbpagelink=(.*?)}/i';

    public function __construct(
        private KbPagesModel $model,
        private KbPagesUrlGenerator $urlGenerator,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function findKbPageTokens(string $content): array
    {
        preg_match_all(self::REGEX, $content, $matches);

        $tokens = [];
        foreach ($matches[1] ?? [] as $index => $kbPageId) {
            $token = $matches[0][$index] ?? null;
            if (null === $token || isset($tokens[$token])) {
                continue;
            }

            $item = $this->model->getEntity((int) $kbPageId);
            if (!$item instanceof KbPages) {
                continue;
            }

            $url = $this->urlGenerator->generateCanonicalUrl($item, true);
            if (null === $url) {
                continue;
            }

            $tokens[$token] = $url;
        }

        return $tokens;
    }
}
