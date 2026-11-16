<?php

declare(strict_types=1);

namespace Gratlinie\GratMapBundle\Widget;

use Contao\StringUtil;
use Contao\System;
use Contao\Widget;

/**
 * Kleine Karte im Backend: zeigt den Standort aus den Feldern latitude/longitude, setzt sie per Klick oder
 * Ziehen des Markers und ermittelt sie auf Wunsch aus der Adresse. Speichert selbst nichts.
 */
class MapPickerWidget extends Widget
{
    protected $blnSubmitInput = false;

    protected $blnForAttribute = false;

    protected $strTemplate = 'be_widget';

    public function generate(): string
    {
        $GLOBALS['TL_CSS']['gratmap_leaflet'] = 'bundles/gratliniegratmap/vendor/leaflet/leaflet.css|static';
        $GLOBALS['TL_CSS']['gratmap_backend'] = 'bundles/gratliniegratmap/css/backend.css|static';
        $GLOBALS['TL_JAVASCRIPT']['gratmap_leaflet'] = 'bundles/gratliniegratmap/vendor/leaflet/leaflet.js|static';
        $GLOBALS['TL_JAVASCRIPT']['gratmap_backend'] = 'bundles/gratliniegratmap/js/backend.js|static';

        $lang = $GLOBALS['TL_LANG']['tl_gm_location'] ?? [];

        return \sprintf(
            '<div class="gm-picker" data-gm-picker data-gm-geocode-url="%s" data-gm-text-notfound="%s"><div class="gm-picker__bar"><button type="button" class="tl_submit" data-gm-geocode>%s</button><span class="gm-picker__hint" data-gm-status>%s</span></div><div class="gm-picker__map" data-gm-picker-map></div></div>',
            StringUtil::specialchars(System::getContainer()->get('router')->generate('gratmap_backend_geocode')),
            StringUtil::specialchars($lang['geocodeFailed'] ?? 'Keine Koordinaten gefunden.'),
            $lang['geocodeButton'] ?? 'Koordinaten aus Adresse ermitteln',
            $lang['pickerHint'] ?? 'Klick in die Karte oder Marker ziehen, um den Standort zu korrigieren.',
        );
    }
}
