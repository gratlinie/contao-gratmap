<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['CTE']['gm_map'] = ['Location map', 'Map and/or list of GratMap locations.'];

$GLOBALS['TL_LANG']['MSC']['gm_query'] = 'Postcode or city';
$GLOBALS['TL_LANG']['MSC']['gm_query_placeholder'] = 'e.g. 80331 or Munich';
$GLOBALS['TL_LANG']['MSC']['gm_radius'] = 'Radius';
$GLOBALS['TL_LANG']['MSC']['gm_service'] = 'Your country';
$GLOBALS['TL_LANG']['MSC']['gm_service_blank'] = 'Please select';
$GLOBALS['TL_LANG']['MSC']['gm_country'] = 'Country';
$GLOBALS['TL_LANG']['MSC']['gm_category'] = 'Category';
$GLOBALS['TL_LANG']['MSC']['gm_all'] = 'All';
$GLOBALS['TL_LANG']['MSC']['gm_search'] = 'Search';
$GLOBALS['TL_LANG']['MSC']['gm_locate'] = 'My location';
$GLOBALS['TL_LANG']['MSC']['gm_reset'] = 'Reset';
$GLOBALS['TL_LANG']['MSC']['gm_consent_text'] = 'The map is loaded from %s. Your IP address will be transmitted to the map provider.';
$GLOBALS['TL_LANG']['MSC']['gm_consent_load'] = 'Load map';
$GLOBALS['TL_LANG']['MSC']['gm_consent_remember'] = 'Always load maps';
$GLOBALS['TL_LANG']['MSC']['gm_consent_settings'] = 'Change privacy settings';
$GLOBALS['TL_LANG']['MSC']['gm_show_on_map'] = 'Show on map';
$GLOBALS['TL_LANG']['MSC']['gm_fax'] = 'Fax';
$GLOBALS['TL_LANG']['MSC']['gm_route'] = 'Get directions';
$GLOBALS['TL_LANG']['MSC']['gm_empty'] = 'No entries found.';
$GLOBALS['TL_LANG']['MSC']['gm_group_count'] = '%s entries';
$GLOBALS['TL_LANG']['MSC']['gm_group_count_one'] = '%s entry';
$GLOBALS['TL_LANG']['MSC']['gm_postal_area'] = 'Postcode area %s';
$GLOBALS['TL_LANG']['MSC']['gm_abroad'] = 'Other countries';

$GLOBALS['TL_LANG']['MSC']['gm_country_empty'] = 'No location assigned to this country.';

$GLOBALS['TL_LANG']['MSC']['gm_continent'] = [
    'EU' => 'Europe',
    'NA' => 'North America',
    'SA' => 'South America',
    'AS' => 'Asia',
    'AF' => 'Africa',
    'OC' => 'Oceania',
    'AN' => 'Antarctica',
    'XX' => 'Unassigned',
];

$GLOBALS['TL_LANG']['MSC']['gm_js'] = [
    'locating' => 'Determining your location …',
    'locateFailed' => 'Your location could not be determined.',
    'yourLocation' => 'your location',
    'searching' => 'Searching …',
    'rateLimit' => 'Too many searches. Please try again in a minute.',
    'notFound' => '“%s” could not be found.',
    'countRadius' => '%s entries within %s km of %s',
    'nearest' => 'No entries within %s km of %s. The nearest ones:',
    'count' => '%s entries',
    'groupCount' => '%s entries',
    'groupCountOne' => '%s entry',
    'service' => 'Responsible for %s:',
    'serviceEmpty' => 'No location is assigned to %s.',
];
