<?php

declare(strict_types=1);

namespace Gratlinie\GratMapBundle\Geocoding;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Adresse → Koordinaten über Nominatim (OpenStreetMap). Läuft nur serverseitig, damit keine
 * Besucher-IP an Dritte geht. Ergebnisse werden 30 Tage gecacht, Anfragen auf eine pro Sekunde gedrosselt
 * (Nutzungsbedingungen von Nominatim).
 */
class Geocoder
{
    private const ENDPOINT = 'https://nominatim.openstreetmap.org/search';

    private static float $lastRequest = 0.0;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface|null $logger = null,
    ) {
    }

    /**
     * Strukturierte Suche für gepflegte Adressen.
     *
     * @return array{lat: float, lng: float, label: string}|null
     */
    public function geocodeAddress(string $street, string $postal, string $city, string $country): array|null
    {
        $params = array_filter([
            'street' => trim($street),
            'postalcode' => trim($postal),
            'city' => trim($city),
            'countrycodes' => strtolower(trim($country)),
        ]);

        if (!isset($params['postalcode']) && !isset($params['city'])) {
            return null;
        }

        $result = $this->query($params);

        if (null === $result && isset($params['street'])) {
            unset($params['street']);
            $result = $this->query($params);
        }

        return $result;
    }

    /**
     * Freie Suche (PLZ oder Ort) für die Umkreissuche im Frontend.
     *
     * @param list<string> $countries
     *
     * @return array{lat: float, lng: float, label: string}|null
     */
    public function geocodeQuery(string $query, array $countries = []): array|null
    {
        $query = trim($query);

        if ('' === $query || mb_strlen($query) > 120) {
            return null;
        }

        $params = ['q' => $query];

        if ($countries) {
            $params['countrycodes'] = implode(',', array_map('strtolower', $countries));
        }

        return $this->query($params);
    }

    /**
     * @param array<string, string> $params
     *
     * @return array{lat: float, lng: float, label: string}|null
     */
    private function query(array $params): array|null
    {
        $params += ['format' => 'jsonv2', 'limit' => '1', 'accept-language' => 'de'];
        ksort($params);

        $key = 'gratmap_geo_'.sha1(serialize($params));

        $result = $this->cache->get($key, function (ItemInterface $item) use ($params): array {
            $item->expiresAfter(86400 * 30);

            return $this->request($params) ?? [];
        });

        if (!$result) {
            $this->cache->delete($key);

            return null;
        }

        return $result;
    }

    /**
     * @param array<string, string> $params
     *
     * @return array{lat: float, lng: float, label: string}|null
     */
    private function request(array $params): array|null
    {
        $wait = 1.0 - (microtime(true) - self::$lastRequest);

        if ($wait > 0) {
            usleep((int) ($wait * 1_000_000));
        }

        self::$lastRequest = microtime(true);

        $host = $this->requestStack->getMainRequest()?->getHost() ?: 'localhost';

        try {
            $response = $this->httpClient->request('GET', self::ENDPOINT, [
                'query' => $params,
                'timeout' => 8,
                'headers' => ['User-Agent' => 'GratMap/1.0 (Contao; '.$host.')'],
            ]);

            $data = $response->toArray(false);
        } catch (\Throwable $e) {
            $this->logger?->warning('GratMap: Geocoding fehlgeschlagen: '.$e->getMessage());

            return null;
        }

        if (!isset($data[0]['lat'], $data[0]['lon'])) {
            return null;
        }

        return [
            'lat' => round((float) $data[0]['lat'], 7),
            'lng' => round((float) $data[0]['lon'], 7),
            'label' => (string) ($data[0]['display_name'] ?? ''),
        ];
    }
}
