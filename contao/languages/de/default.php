<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['CTE']['gm_map'] = ['Standortkarte', 'Karte und/oder Liste mit Standorten aus GratMap.'];

$GLOBALS['TL_LANG']['MSC']['gm_query'] = 'PLZ oder Ort';
$GLOBALS['TL_LANG']['MSC']['gm_query_placeholder'] = 'z. B. 80331 oder München';
$GLOBALS['TL_LANG']['MSC']['gm_radius'] = 'Umkreis';
$GLOBALS['TL_LANG']['MSC']['gm_service'] = 'Ihr Land';
$GLOBALS['TL_LANG']['MSC']['gm_service_blank'] = 'Bitte wählen';
$GLOBALS['TL_LANG']['MSC']['gm_country'] = 'Land';
$GLOBALS['TL_LANG']['MSC']['gm_category'] = 'Kategorie';
$GLOBALS['TL_LANG']['MSC']['gm_all'] = 'Alle';
$GLOBALS['TL_LANG']['MSC']['gm_search'] = 'Suchen';
$GLOBALS['TL_LANG']['MSC']['gm_locate'] = 'Mein Standort';
$GLOBALS['TL_LANG']['MSC']['gm_reset'] = 'Zurücksetzen';
$GLOBALS['TL_LANG']['MSC']['gm_consent_text'] = 'Die Karte wird von %s geladen. Dabei wird Ihre IP-Adresse an den Kartenanbieter übertragen.';
$GLOBALS['TL_LANG']['MSC']['gm_consent_load'] = 'Karte laden';
$GLOBALS['TL_LANG']['MSC']['gm_consent_remember'] = 'Karten immer laden';
$GLOBALS['TL_LANG']['MSC']['gm_consent_settings'] = 'Datenschutz-Einstellungen ändern';
$GLOBALS['TL_LANG']['MSC']['gm_show_on_map'] = 'Auf der Karte zeigen';
$GLOBALS['TL_LANG']['MSC']['gm_fax'] = 'Telefax';
$GLOBALS['TL_LANG']['MSC']['gm_route'] = 'Route planen';
$GLOBALS['TL_LANG']['MSC']['gm_empty'] = 'Keine Einträge gefunden.';
$GLOBALS['TL_LANG']['MSC']['gm_group_count'] = '%s Einträge';
$GLOBALS['TL_LANG']['MSC']['gm_group_count_one'] = '%s Eintrag';
$GLOBALS['TL_LANG']['MSC']['gm_postal_area'] = 'PLZ-Bereich %s';
$GLOBALS['TL_LANG']['MSC']['gm_abroad'] = 'Weitere Länder';

$GLOBALS['TL_LANG']['MSC']['gm_country_empty'] = 'Kein Standort für dieses Land hinterlegt.';

$GLOBALS['TL_LANG']['MSC']['gm_continent'] = [
    'EU' => 'Europa',
    'NA' => 'Nordamerika',
    'SA' => 'Südamerika',
    'AS' => 'Asien',
    'AF' => 'Afrika',
    'OC' => 'Ozeanien',
    'AN' => 'Antarktis',
    'XX' => 'Ohne Zuordnung',
];

$GLOBALS['TL_LANG']['MSC']['gm_js'] = [
    'locating' => 'Standort wird ermittelt …',
    'locateFailed' => 'Der Standort konnte nicht ermittelt werden.',
    'yourLocation' => 'Ihrem Standort',
    'searching' => 'Suche läuft …',
    'rateLimit' => 'Zu viele Suchanfragen. Bitte versuchen Sie es in einer Minute erneut.',
    'notFound' => '„%s“ wurde nicht gefunden.',
    'countRadius' => '%s Einträge im Umkreis von %s km um %s',
    'nearest' => 'Im Umkreis von %s km um %s gibt es keinen Eintrag. Die nächstgelegenen:',
    'count' => '%s Einträge',
    'groupCount' => '%s Einträge',
    'groupCountOne' => '%s Eintrag',
    'service' => 'Zuständig für %s:',
    'serviceEmpty' => 'Für %s ist kein Standort hinterlegt.',
];
