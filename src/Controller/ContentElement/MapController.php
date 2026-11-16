<?php

declare(strict_types=1);

namespace Gratlinie\GratMapBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Intl\Countries;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FilesModel;
use Contao\StringUtil;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Gratlinie\GratMapBundle\Map\TileProviders;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Standortkarte: Karte (Leaflet, Cluster) und/oder Liste mit Filtern und Umkreissuche. Die Liste wird
 * serverseitig ausgegeben (funktioniert ohne JS und ohne Karten-Einwilligung), gratmap.js filtert und
 * sortiert sie und verbindet sie mit der Karte.
 */
#[AsContentElement('gm_map', category: 'includes')]
class MapController extends AbstractContentElementController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Countries $countries,
        private readonly Studio $studio,
        private readonly UrlGeneratorInterface $router,
        #[Autowire('%contao.web_dir%')]
        private readonly string $webDir,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $archives = array_map('intval', StringUtil::deserialize($model->gmArchives, true));
        $filters = StringUtil::deserialize($model->gmFilters, true);
        $home = strtoupper((string) $model->gmHomeCountry ?: 'DE');
        $countryNames = $this->countries->getCountries();

        $categories = $archives ? $this->connection->fetchAllAssociative(
            'SELECT id, title, color FROM tl_gm_category WHERE pid IN (?) ORDER BY sorting',
            [$archives],
            [ArrayParameterType::INTEGER],
        ) : [];
        $categoryTitles = array_column($categories, 'title', 'id');

        $items = [];

        foreach ($this->fetchLocations($archives) as $row) {
            $items[] = $this->buildItem($row, $model, $countryNames, $categoryTitles);
        }

        usort($items, static function (array $a, array $b) use ($home): int {
            return [$a['country'] !== $home, $a['countryName'], $a['postal'], $a['title']]
                <=> [$b['country'] !== $home, $b['countryName'], $b['postal'], $b['title']];
        });

        $groups = $this->group($items, (string) $model->gmGroupBy, $home);
        $usedCountries = array_values(array_unique(array_column($items, 'country')));
        $countryOptions = [];

        foreach ($usedCountries as $code) {
            $countryOptions[$code] = $countryNames[$code] ?? $code;
        }

        $view = \in_array($model->gmView, ['map_list', 'map', 'list'], true) ? $model->gmView : 'map_list';
        $radii = array_values(array_filter(array_map('intval', explode(',', (string) $model->gmRadii))));

        $tiles = TileProviders::resolve((string) $model->gmTileProvider, (string) $model->gmTileKey, (string) $model->gmTileUrl, StringUtil::decodeEntities((string) $model->gmTileAttribution))
            ?? TileProviders::resolve('osm');

        $config = [
            'view' => $view,
            'home' => $home,
            'countries' => $usedCountries,
            'radii' => $radii ?: [25, 50, 100, 200],
            'cluster' => (bool) $model->gmCluster,
            'consent' => (bool) $model->gmConsent,
            'tiles' => $tiles,
            'marker' => $this->markerConfig($model),
            'geocodeUrl' => $this->router->generate('gratmap_geocode'),
            'labels' => $this->labels(),
            'assets' => [
                'leafletJs' => $this->asset('vendor/leaflet/leaflet.js'),
                'leafletCss' => $this->asset('vendor/leaflet/leaflet.css'),
                'clusterJs' => $this->asset('vendor/markercluster/leaflet.markercluster.js'),
                'clusterCss' => $this->asset('vendor/markercluster/MarkerCluster.css'),
            ],
            'points' => array_values(array_filter(array_map(static fn (array $i): array|null => null === $i['lat'] ? null : [
                'id' => $i['id'],
                'lat' => $i['lat'],
                'lng' => $i['lng'],
                'title' => $i['title'],
            ], $items))),
        ];

        $template->set('gm_view', $view);
        $template->set('gm_items', $items);
        $template->set('gm_groups', $groups);
        $template->set('gm_filters', [
            'radius' => \in_array('radius', $filters, true),
            'country' => \in_array('country', $filters, true) && \count($countryOptions) > 1,
            'category' => \in_array('category', $filters, true) && \count($categoryTitles) > 0,
        ]);
        $template->set('gm_radii', $config['radii']);
        $template->set('gm_country_options', $countryOptions);
        $template->set('gm_category_options', $categoryTitles);
        $template->set('gm_route', (bool) $model->gmRoute);
        $template->set('gm_height', $this->sanitizeLength((string) $model->gmHeight));
        $template->set('gm_config', $config);
        $template->set('gm_tile_host', preg_replace('/^\{s\}\./', '', (string) parse_url((string) $tiles['url'], PHP_URL_HOST)));
        $template->set('gm_css', $this->asset('css/gratmap.css'));
        $template->set('gm_js', $this->asset('js/gratmap.js'));

        return $template->getResponse();
    }

    /**
     * @param list<int> $archives
     *
     * @return list<array<string, mixed>>
     */
    private function fetchLocations(array $archives): array
    {
        if (!$archives) {
            return [];
        }

        $time = time();

        return $this->connection->fetchAllAssociative(
            "SELECT * FROM tl_gm_location WHERE pid IN (?) AND published=1 AND (start='' OR start<=?) AND (stop='' OR stop>?)",
            [$archives, $time, $time],
            [ArrayParameterType::INTEGER],
        );
    }

    /**
     * @param array<string, mixed>  $row
     * @param array<string, string> $countryNames
     * @param array<int, string>    $categoryTitles
     *
     * @return array<string, mixed>
     */
    private function buildItem(array $row, ContentModel $model, array $countryNames, array $categoryTitles): array
    {
        $country = strtoupper((string) $row['country']);
        $lat = is_numeric($row['latitude']) ? (float) $row['latitude'] : null;
        $lng = is_numeric($row['longitude']) ? (float) $row['longitude'] : null;
        $categoryIds = array_values(array_intersect(array_map('intval', StringUtil::deserialize($row['categories'], true)), array_keys($categoryTitles)));

        $logo = null;

        if ($row['logo'] && ($file = FilesModel::findByUuid($row['logo']))) {
            $logo = $this->studio->createFigureBuilder()
                ->fromFilesModel($file)
                ->setSize($model->size)
                ->disableMetadata()
                ->buildIfResourceExists();
        }

        $website = trim((string) $row['website']);

        return [
            'id' => (int) $row['id'],
            'title' => StringUtil::decodeEntities((string) $row['title']),
            'contact' => (string) $row['contact'],
            'street' => (string) $row['street'],
            'postal' => (string) $row['postal'],
            'city' => (string) $row['city'],
            'country' => $country,
            'countryName' => $countryNames[$country] ?? $country,
            'lat' => null === $lng ? null : $lat,
            'lng' => null === $lat ? null : $lng,
            'phone' => (string) $row['phone'],
            'phoneHref' => preg_replace('/[^\d+]/', '', (string) $row['phone']),
            'email' => StringUtil::encodeEmail((string) $row['email']),
            'website' => $website,
            'websiteLabel' => preg_replace('#^https?://(www\.)?|/$#', '', $website),
            'logo' => $logo,
            'description' => StringUtil::restoreBasicEntities((string) $row['description']),
            'categories' => $categoryIds,
            'categoryTitles' => array_map(static fn (int $id): string => $categoryTitles[$id], $categoryIds),
            'route' => 'https://www.google.com/maps/dir/?api=1&destination='.rawurlencode(trim($row['street'].', '.$row['postal'].' '.$row['city'].', '.($countryNames[$country] ?? $country), ' ,')),
        ];
    }

    /**
     * @param list<array<string, mixed>> $items
     *
     * @return list<array{key: string, label: string, cities: string, items: list<array<string, mixed>>}>
     */
    private function group(array $items, string $groupBy, string $home): array
    {
        if (!\in_array($groupBy, ['country', 'postal'], true)) {
            return [['key' => 'all', 'label' => '', 'cities' => '', 'items' => $items]];
        }

        $groups = [];

        foreach ($items as $item) {
            if ('postal' === $groupBy && $item['country'] === $home && '' !== $item['postal']) {
                $digit = $item['postal'][0];
                $key = 'postal-'.$digit;
                $label = \sprintf($GLOBALS['TL_LANG']['MSC']['gm_postal_area'] ?? 'PLZ-Bereich %s', $digit);
            } else {
                $key = 'country-'.strtolower($item['country']);
                $label = $item['countryName'];
            }

            $groups[$key] ??= ['key' => $key, 'label' => $label, 'items' => []];
            $groups[$key]['items'][] = $item;
        }

        foreach ($groups as &$group) {
            $group['cities'] = implode(', ', array_values(array_unique(array_filter(array_column($group['items'], 'city')))));
        }

        return array_values($groups);
    }

    /**
     * @return array{url: string, size: int}|null
     */
    private function markerConfig(ContentModel $model): array|null
    {
        if (!$model->gmMarker || !($file = FilesModel::findByUuid($model->gmMarker)) || !is_file($this->projectDir.'/'.$file->path)) {
            return null;
        }

        return [
            'url' => '/'.implode('/', array_map('rawurlencode', explode('/', $file->path))),
            'size' => max(12, min(128, (int) $model->gmMarkerSize ?: 32)),
        ];
    }

    /**
     * Texte für gratmap.js, %s-Platzhalter bleiben erhalten.
     *
     * @return array<string, string>
     */
    private function labels(): array
    {
        $labels = [];

        foreach (['locating', 'locateFailed', 'yourLocation', 'searching', 'rateLimit', 'notFound', 'countRadius', 'nearest', 'count', 'groupCount', 'groupCountOne'] as $key) {
            $labels[$key] = (string) ($GLOBALS['TL_LANG']['MSC']['gm_js'][$key] ?? $key);
        }

        return $labels;
    }

    private function asset(string $path): string
    {
        $file = $this->webDir.'/bundles/gratliniegratmap/'.$path;

        return '/bundles/gratliniegratmap/'.$path.(is_file($file) ? '?v='.substr(md5((string) filemtime($file)), 0, 8) : '');
    }

    private function sanitizeLength(string $value): string
    {
        $value = trim($value);

        return 1 === preg_match('/^\d+(\.\d+)?(px|rem|em|vh|svh|dvh|cqi|cqh|%)$/', $value) ? $value : '';
    }
}
