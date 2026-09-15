<?php

namespace Phuture\App\Helper;

use League\CommonMark\Normalizer\TextNormalizerInterface;

class HeadingSlug implements TextNormalizerInterface
{
    /**
     * Characters a slug is built out of, everything else being dropped
     */
    protected const KEPT = '\p{L}\p{Nd}\p{Nl}\p{M}_\s-';

    /**
     * Turn the text of a heading into the anchor GitHub would have given it
     *
     * The order matters: punctuation, symbols and emoji are dropped before the
     * spaces become dashes, and a run of spaces leaves a dash for each of them,
     * which is what the anchors written in a document downloaded from GitHub
     * were made against.
     *
     * @param string $text Text of the heading
     * @param array $context Prefix, maximum length and node the text belongs to, as the caller knows them
     * @return string
     */
    public function normalize(string $text, array $context = []): string
    {
        $slug = ($context['prefix'] ?? '') . $text;
        $slug = mb_strtolower(trim($slug), 'UTF-8');
        $slug = preg_replace('/[^' . self::KEPT . ']+/u', '', $slug) ?? $slug;
        $slug = preg_replace('/\s/u', '-', $slug) ?? $slug;

        $length = $context['length'] ?? 0;

        return $length > 0 ? mb_substr($slug, 0, $length, 'UTF-8') : $slug;
    }
}
