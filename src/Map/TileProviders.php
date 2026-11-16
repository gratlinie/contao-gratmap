<?php

declare(strict_types=1);

namespace Gratlinie\GratMapBundle\Map;

/**
 * Vordefinierte Kachel-Anbieter für Leaflet. {key} wird durch den API-Schlüssel des Elements ersetzt.
 */
final class TileProviders
{
    private const OSM = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

    private const PROVIDERS = [
        'osm' => [
            'url' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            'attribution' => self::OSM,
            'maxZoom' => 19,
        ],
        'osm_de' => [
            'url' => 'https://tile.openstreetmap.de/{z}/{x}/{y}.png',
            'attribution' => self::OSM,
            'maxZoom' => 18,
        ],
        'esri_gray' => [
            'url' => 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}',
            'attribution' => 'Tiles &copy; Esri &mdash; Esri, HERE, Garmin, '.self::OSM,
            'maxZoom' => 16,
        ],
        'maptiler_streets' => [
            'url' => 'https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key={key}',
            'attribution' => '&copy; <a href="https://www.maptiler.com/copyright/">MapTiler</a> '.self::OSM,
            'tileSize' => 512,
            'zoomOffset' => -1,
            'maxZoom' => 20,
            'key' => true,
        ],
        'stadia_smooth' => [
            'url' => 'https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png?api_key={key}',
            'attribution' => '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> '.self::OSM,
            'maxZoom' => 20,
            'key' => true,
        ],
        'custom' => [],
    ];

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::PROVIDERS);
    }

    /**
     * @return list<string>
     */
    public static function withKey(): array
    {
        return array_keys(array_filter(self::PROVIDERS, static fn (array $p): bool => $p['key'] ?? false));
    }

    /**
     * Leaflet-Optionen für den Anbieter; null, wenn er nicht nutzbar ist (Schlüssel/URL fehlt).
     *
     * @return array<string, mixed>|null
     */
    public static function resolve(string $provider, string $key = '', string $customUrl = '', string $customAttribution = ''): array|null
    {
        if ('custom' === $provider) {
            return '' === trim($customUrl) ? null : ['url' => trim($customUrl), 'attribution' => $customAttribution, 'maxZoom' => 19];
        }

        $config = self::PROVIDERS[$provider] ?? self::PROVIDERS['osm'];

        if (($config['key'] ?? false) && '' === trim($key)) {
            return null;
        }

        $config['url'] = str_replace('{key}', rawurlencode(trim($key)), $config['url']);
        unset($config['key']);

        return $config;
    }
}
