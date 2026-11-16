<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_content']['gm_source_legend'] = 'Locations';
$GLOBALS['TL_LANG']['tl_content']['gm_view_legend'] = 'Display and filters';
$GLOBALS['TL_LANG']['tl_content']['gm_map_legend'] = 'Map';

$GLOBALS['TL_LANG']['tl_content']['gmArchives'] = ['Collections', 'Location collections to display.'];
$GLOBALS['TL_LANG']['tl_content']['gmView'] = ['View', 'Map, list or both.'];
$GLOBALS['TL_LANG']['tl_content']['gmGroupBy'] = ['Group list', 'Home country by postcode area, other countries by country.'];
$GLOBALS['TL_LANG']['tl_content']['gmHomeCountry'] = ['Home country', 'Listed first; the country is omitted from addresses there.'];
$GLOBALS['TL_LANG']['tl_content']['gmFilters'] = ['Filters', 'Country and category filters only appear if there is more than one country or any categories.'];
$GLOBALS['TL_LANG']['tl_content']['gmRadii'] = ['Radii (km)', 'Comma-separated, the second value is preselected.'];
$GLOBALS['TL_LANG']['tl_content']['gmRoute'] = ['“Get directions” link', 'Opens Google Maps with the address as destination.'];
$GLOBALS['TL_LANG']['tl_content']['gmTileProvider'] = ['Map style', 'Tile provider.'];
$GLOBALS['TL_LANG']['tl_content']['gmTileKey'] = ['API key', 'Key of the map provider.'];
$GLOBALS['TL_LANG']['tl_content']['gmTileUrl'] = ['Tile URL', 'Leaflet scheme, e.g. https://{s}.example.com/{z}/{x}/{y}.png'];
$GLOBALS['TL_LANG']['tl_content']['gmTileAttribution'] = ['Attribution', 'HTML allowed.'];
$GLOBALS['TL_LANG']['tl_content']['gmHeight'] = ['Map height', 'With unit, e.g. 32rem or 60svh. Empty = default.'];
$GLOBALS['TL_LANG']['tl_content']['gmMarker'] = ['Custom marker', 'SVG, PNG or WebP. Empty = default pin in the accent colour.'];
$GLOBALS['TL_LANG']['tl_content']['gmMarkerSize'] = ['Marker size (px)', ''];
$GLOBALS['TL_LANG']['tl_content']['gmCluster'] = ['Cluster points', 'Group nearby points; click zooms in.'];
$GLOBALS['TL_LANG']['tl_content']['gmConsent'] = ['Two-click solution', 'Load the map only after a click (privacy).'];

$GLOBALS['TL_LANG']['tl_content']['gm_ref'] = [
    'view' => ['map_list' => 'Map and list', 'map' => 'Map only', 'list' => 'List only'],
    'groupBy' => ['' => 'No grouping', 'country' => 'By country', 'postal' => 'By postcode area'],
    'filters' => ['radius' => 'Radius search', 'country' => 'Country', 'category' => 'Category'],
    'tiles' => [
        'osm' => 'OpenStreetMap',
        'osm_de' => 'OpenStreetMap Germany',
        'esri_gray' => 'Esri Light Gray',
        'maptiler_streets' => 'MapTiler Streets (API key)',
        'stadia_smooth' => 'Stadia Alidade Smooth (API key)',
        'custom' => 'Custom tile URL',
    ],
];
