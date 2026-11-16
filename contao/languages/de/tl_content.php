<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_content']['gm_source_legend'] = 'Standorte';
$GLOBALS['TL_LANG']['tl_content']['gm_view_legend'] = 'Darstellung und Filter';
$GLOBALS['TL_LANG']['tl_content']['gm_map_legend'] = 'Karte';

$GLOBALS['TL_LANG']['tl_content']['gmArchives'] = ['Sammlungen', 'Standortsammlungen, die angezeigt werden.'];
$GLOBALS['TL_LANG']['tl_content']['gmView'] = ['Ansicht', 'Karte, Liste oder beides.'];
$GLOBALS['TL_LANG']['tl_content']['gmGroupBy'] = ['Liste gruppieren', 'Im Heimatland nach PLZ-Bereich, andere Länder nach Land.'];
$GLOBALS['TL_LANG']['tl_content']['gmHomeCountry'] = ['Heimatland', 'Wird zuerst gelistet; das Land wird bei Adressen dort nicht ausgegeben.'];
$GLOBALS['TL_LANG']['tl_content']['gmFilters'] = ['Filter', 'Land- und Kategorienfilter erscheinen nur, wenn es mehr als ein Land bzw. Kategorien gibt.'];
$GLOBALS['TL_LANG']['tl_content']['gmRadii'] = ['Umkreise (km)', 'Kommagetrennt, der zweite Wert ist vorausgewählt.'];
$GLOBALS['TL_LANG']['tl_content']['gmRadiiInvalid'] = 'Bitte nur ganze Kilometerwerte, durch Komma getrennt, eingeben (z. B. 25,50,100).';
$GLOBALS['TL_LANG']['tl_content']['gmRoute'] = ['Link „Route planen“', 'Öffnet Google Maps mit der Adresse als Ziel.'];
$GLOBALS['TL_LANG']['tl_content']['gmTileProvider'] = ['Kartenstil', 'Anbieter der Kartenkacheln.'];
$GLOBALS['TL_LANG']['tl_content']['gmTileKey'] = ['API-Schlüssel', 'Schlüssel des Kartenanbieters.'];
$GLOBALS['TL_LANG']['tl_content']['gmTileUrl'] = ['Kachel-URL', 'Leaflet-Schema, z. B. https://{s}.example.com/{z}/{x}/{y}.png'];
$GLOBALS['TL_LANG']['tl_content']['gmTileAttribution'] = ['Quellenangabe', 'HTML erlaubt.'];
$GLOBALS['TL_LANG']['tl_content']['gmHeight'] = ['Kartenhöhe', 'Mit Einheit, z. B. 32rem oder 60svh. Leer = Standard.'];
$GLOBALS['TL_LANG']['tl_content']['gmMarker'] = ['Eigener Marker', 'SVG, PNG oder WebP. Leer = Standard-Pin in der Akzentfarbe.'];
$GLOBALS['TL_LANG']['tl_content']['gmMarkerSize'] = ['Markergröße (px)', ''];
$GLOBALS['TL_LANG']['tl_content']['gmCluster'] = ['Punkte bündeln', 'Nahe Punkte zusammenfassen; Klick zoomt hinein.'];
$GLOBALS['TL_LANG']['tl_content']['gmConsent'] = ['Zwei-Klick-Lösung', 'Karte erst nach Klick laden (Datenschutz).'];

$GLOBALS['TL_LANG']['tl_content']['gm_ref'] = [
    'view' => ['map_list' => 'Karte und Liste', 'map' => 'Nur Karte', 'list' => 'Nur Liste'],
    'groupBy' => ['' => 'Nicht gruppieren', 'country' => 'Nach Land', 'postal' => 'Nach PLZ-Bereich'],
    'filters' => ['radius' => 'Umkreissuche', 'country' => 'Land', 'category' => 'Kategorie'],
    'tiles' => [
        'osm' => 'OpenStreetMap',
        'osm_de' => 'OpenStreetMap Deutschland',
        'esri_gray' => 'Esri Light Gray',
        'maptiler_streets' => 'MapTiler Streets (API-Schlüssel)',
        'stadia_smooth' => 'Stadia Alidade Smooth (API-Schlüssel)',
        'custom' => 'Eigene Kachel-URL',
    ],
];
