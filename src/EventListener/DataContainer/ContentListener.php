<?php

declare(strict_types=1);

namespace Gratlinie\GratMapBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;

class ContentListener
{
    /**
     * Umkreise als kommagetrennte, aufsteigend sortierte Kilometerwerte speichern (z. B. „25, 50,100“ → „25,50,100“).
     */
    #[AsCallback(table: 'tl_content', target: 'fields.gmRadii.save')]
    public function normalizeRadii(mixed $value): string
    {
        $value = trim((string) $value);

        if ('' === $value) {
            return '';
        }

        $parts = preg_split('/[\s,;]+/', $value, -1, PREG_SPLIT_NO_EMPTY);

        if (array_filter($parts, static fn (string $part): bool => !ctype_digit($part) || 0 === (int) $part)) {
            throw new \RuntimeException($GLOBALS['TL_LANG']['tl_content']['gmRadiiInvalid'] ?? 'Bitte nur ganze Kilometerwerte, durch Komma getrennt, eingeben (z. B. 25,50,100).');
        }

        $radii = array_unique(array_map('intval', $parts));
        sort($radii);

        return implode(',', $radii);
    }
}
