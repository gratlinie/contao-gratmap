<?php

declare(strict_types=1);

namespace Gratlinie\GratMapBundle\Controller\Route;

use Gratlinie\GratMapBundle\Geocoding\Geocoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Geocoding-Endpunkte: Frontend (Umkreissuche, gedrosselt je IP) und Backend (Koordinaten aus der Adresse).
 */
class GeocodeController
{
    private const FRONTEND_LIMIT_PER_MINUTE = 20;

    public function __construct(
        private readonly Geocoder $geocoder,
        private readonly CacheInterface $cache,
    ) {
    }

    #[Route('/_gratmap/geocode', name: 'gratmap_geocode', defaults: ['_scope' => 'frontend', '_token_check' => false], methods: ['GET'])]
    public function frontend(Request $request): JsonResponse
    {
        $bucket = 'gratmap_rate_'.sha1((string) $request->getClientIp()).'_'.intdiv(time(), 60);
        $count = $this->cache->get($bucket, static function (ItemInterface $item): int {
            $item->expiresAfter(70);

            return 0;
        });

        if ($count >= self::FRONTEND_LIMIT_PER_MINUTE) {
            return $this->json(['error' => 'rate_limit'], 429);
        }

        $this->cache->delete($bucket);
        $this->cache->get($bucket, static function (ItemInterface $item) use ($count): int {
            $item->expiresAfter(70);

            return $count + 1;
        });

        $countries = array_filter(
            explode(',', (string) $request->query->get('c', '')),
            static fn (string $c): bool => 1 === preg_match('/^[a-z]{2}$/i', $c),
        );

        $result = $this->geocoder->geocodeQuery((string) $request->query->get('q', ''), array_values($countries));

        return null === $result ? $this->json(['error' => 'not_found'], 404) : $this->json($result);
    }

    #[Route('/%contao.backend.route_prefix%/_gratmap/geocode', name: 'gratmap_backend_geocode', defaults: ['_scope' => 'backend'], methods: ['GET'])]
    public function backend(Request $request): JsonResponse
    {
        $q = $request->query;
        $result = $this->geocoder->geocodeAddress(
            (string) $q->get('street', ''),
            (string) $q->get('postal', ''),
            (string) $q->get('city', ''),
            (string) $q->get('country', ''),
        );

        return null === $result ? $this->json(['error' => 'not_found'], 404) : $this->json($result);
    }

    private function json(array $data, int $status = 200): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        $response->setPrivate();
        $response->headers->set('X-Robots-Tag', 'noindex');

        return $response;
    }
}
