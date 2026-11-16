<?php

declare(strict_types=1);

namespace Gratlinie\GratMapBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\DataContainer;
use Contao\Message;
use Contao\Widget;
use Doctrine\DBAL\Connection;
use Gratlinie\GratMapBundle\Geocoding\Geocoder;

class LocationListener
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Geocoder $geocoder,
    ) {
    }

    /**
     * @return array<int, string>
     */
    #[AsCallback(table: 'tl_gm_location', target: 'fields.categories.options')]
    public function categoryOptions(DataContainer|null $dc = null): array
    {
        $pid = (int) ($dc?->activeRecord?->pid ?? 0);

        if (!$pid && $dc?->id) {
            $pid = (int) $this->connection->fetchOne('SELECT pid FROM tl_gm_location WHERE id=?', [$dc->id]);
        }

        $query = 'SELECT id, title FROM tl_gm_category'.($pid ? ' WHERE pid=?' : '').' ORDER BY sorting';

        return $this->connection->fetchAllKeyValue($query, $pid ? [$pid] : []);
    }

    /**
     * Fehlen Koordinaten, werden sie beim Speichern aus der Adresse ermittelt.
     */
    #[AsCallback(table: 'tl_gm_location', target: 'config.onsubmit')]
    public function geocodeOnSubmit(DataContainer $dc): void
    {
        $record = $dc->getCurrentRecord();

        if (!$record || ('' !== (string) $record['latitude'] && '' !== (string) $record['longitude'])) {
            return;
        }

        $result = $this->geocoder->geocodeAddress((string) $record['street'], (string) $record['postal'], (string) $record['city'], (string) $record['country']);

        if (null === $result) {
            Message::addInfo($GLOBALS['TL_LANG']['tl_gm_location']['geocodeFailed'] ?? 'Für diese Adresse wurden keine Koordinaten gefunden. Bitte den Punkt auf der Karte setzen.');

            return;
        }

        $this->connection->update('tl_gm_location', [
            'latitude' => (string) $result['lat'],
            'longitude' => (string) $result['lng'],
        ], ['id' => $dc->id]);
    }

    #[AsCallback(table: 'tl_gm_location', target: 'fields.latitude.save')]
    #[AsCallback(table: 'tl_gm_location', target: 'fields.longitude.save')]
    public function normalizeCoordinate(mixed $value): string
    {
        $value = str_replace(',', '.', trim((string) $value));

        return '' === $value ? '' : (string) round((float) $value, 7);
    }

    #[AsHook('addCustomRegexp')]
    public function validateCoordinate(string $regexp, mixed $input, Widget $widget): bool
    {
        if (!\in_array($regexp, ['gmLatitude', 'gmLongitude'], true)) {
            return false;
        }

        $value = str_replace(',', '.', trim((string) $input));

        if ('' === $value) {
            return true;
        }

        $max = 'gmLatitude' === $regexp ? 90 : 180;

        if (!is_numeric($value) || abs((float) $value) > $max) {
            $widget->addError($GLOBALS['TL_LANG']['tl_gm_location']['invalidCoordinate'] ?? 'Bitte eine gültige Koordinate in Dezimalgrad eingeben (z. B. 51.12345).');
        }

        return true;
    }
}
