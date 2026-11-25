<?php

declare(strict_types=1);

namespace Gratlinie\GratMapBundle\Map;

/**
 * Zuordnung ISO-3166-1-alpha-2 -> Kontinent, für die Gruppierung der Liste nach Kontinent.
 *
 * Quelle: datasets/country-codes (Public Domain), Spalte "Continent" – die UN-M49-Einteilung.
 * Bewusst als feste Tabelle im Bundle: keine Laufzeitabhängigkeit, keine Netzabfrage.
 */
final class Continents
{
    public const ORDER = ['EU', 'NA', 'SA', 'AS', 'AF', 'OC', 'AN'];

    private const MAP = [
        'AD' => 'EU', 'AE' => 'AS', 'AF' => 'AS', 'AG' => 'NA', 'AI' => 'NA', 'AL' => 'EU', 'AM' => 'AS', 'AO' => 'AF',
        'AQ' => 'AN', 'AR' => 'SA', 'AS' => 'OC', 'AT' => 'EU', 'AU' => 'OC', 'AW' => 'NA', 'AX' => 'EU', 'AZ' => 'AS',
        'BA' => 'EU', 'BB' => 'NA', 'BD' => 'AS', 'BE' => 'EU', 'BF' => 'AF', 'BG' => 'EU', 'BH' => 'AS', 'BI' => 'AF',
        'BJ' => 'AF', 'BL' => 'NA', 'BM' => 'NA', 'BN' => 'AS', 'BO' => 'SA', 'BQ' => 'NA', 'BR' => 'SA', 'BS' => 'NA',
        'BT' => 'AS', 'BV' => 'AN', 'BW' => 'AF', 'BY' => 'EU', 'BZ' => 'NA', 'CA' => 'NA', 'CC' => 'AS', 'CD' => 'AF',
        'CF' => 'AF', 'CG' => 'AF', 'CH' => 'EU', 'CI' => 'AF', 'CK' => 'OC', 'CL' => 'SA', 'CM' => 'AF', 'CN' => 'AS',
        'CO' => 'SA', 'CR' => 'NA', 'CU' => 'NA', 'CV' => 'AF', 'CW' => 'NA', 'CX' => 'OC', 'CY' => 'EU', 'CZ' => 'EU',
        'DE' => 'EU', 'DJ' => 'AF', 'DK' => 'EU', 'DM' => 'NA', 'DO' => 'NA', 'DZ' => 'AF', 'EC' => 'SA', 'EE' => 'EU',
        'EG' => 'AF', 'EH' => 'AF', 'ER' => 'AF', 'ES' => 'EU', 'ET' => 'AF', 'FI' => 'EU', 'FJ' => 'OC', 'FK' => 'SA',
        'FM' => 'OC', 'FO' => 'EU', 'FR' => 'EU', 'GA' => 'AF', 'GB' => 'EU', 'GD' => 'NA', 'GE' => 'AS', 'GF' => 'SA',
        'GG' => 'EU', 'GH' => 'AF', 'GI' => 'EU', 'GL' => 'NA', 'GM' => 'AF', 'GN' => 'AF', 'GP' => 'NA', 'GQ' => 'AF',
        'GR' => 'EU', 'GS' => 'AN', 'GT' => 'NA', 'GU' => 'OC', 'GW' => 'AF', 'GY' => 'SA', 'HK' => 'AS', 'HM' => 'AN',
        'HN' => 'NA', 'HR' => 'EU', 'HT' => 'NA', 'HU' => 'EU', 'ID' => 'AS', 'IE' => 'EU', 'IL' => 'AS', 'IM' => 'EU',
        'IN' => 'AS', 'IO' => 'AS', 'IQ' => 'AS', 'IR' => 'AS', 'IS' => 'EU', 'IT' => 'EU', 'JE' => 'EU', 'JM' => 'NA',
        'JO' => 'AS', 'JP' => 'AS', 'KE' => 'AF', 'KG' => 'AS', 'KH' => 'AS', 'KI' => 'OC', 'KM' => 'AF', 'KN' => 'NA',
        'KP' => 'AS', 'KR' => 'AS', 'KW' => 'AS', 'KY' => 'NA', 'KZ' => 'AS', 'LA' => 'AS', 'LB' => 'AS', 'LC' => 'NA',
        'LI' => 'EU', 'LK' => 'AS', 'LR' => 'AF', 'LS' => 'AF', 'LT' => 'EU', 'LU' => 'EU', 'LV' => 'EU', 'LY' => 'AF',
        'MA' => 'AF', 'MC' => 'EU', 'MD' => 'EU', 'ME' => 'EU', 'MF' => 'NA', 'MG' => 'AF', 'MH' => 'OC', 'MK' => 'EU',
        'ML' => 'AF', 'MM' => 'AS', 'MN' => 'AS', 'MO' => 'AS', 'MP' => 'OC', 'MQ' => 'NA', 'MR' => 'AF', 'MS' => 'NA',
        'MT' => 'EU', 'MU' => 'AF', 'MV' => 'AS', 'MW' => 'AF', 'MX' => 'NA', 'MY' => 'AS', 'MZ' => 'AF', 'NA' => 'AF',
        'NC' => 'OC', 'NE' => 'AF', 'NF' => 'OC', 'NG' => 'AF', 'NI' => 'NA', 'NL' => 'EU', 'NO' => 'EU', 'NP' => 'AS',
        'NR' => 'OC', 'NU' => 'OC', 'NZ' => 'OC', 'OM' => 'AS', 'PA' => 'NA', 'PE' => 'SA', 'PF' => 'OC', 'PG' => 'OC',
        'PH' => 'AS', 'PK' => 'AS', 'PL' => 'EU', 'PM' => 'NA', 'PN' => 'OC', 'PR' => 'NA', 'PS' => 'AS', 'PT' => 'EU',
        'PW' => 'OC', 'PY' => 'SA', 'QA' => 'AS', 'RE' => 'AF', 'RO' => 'EU', 'RS' => 'EU', 'RU' => 'EU', 'RW' => 'AF',
        'SA' => 'AS', 'SB' => 'OC', 'SC' => 'AF', 'SD' => 'AF', 'SE' => 'EU', 'SG' => 'AS', 'SH' => 'AF', 'SI' => 'EU',
        'SJ' => 'EU', 'SK' => 'EU', 'SL' => 'AF', 'SM' => 'EU', 'SN' => 'AF', 'SO' => 'AF', 'SR' => 'SA', 'SS' => 'AF',
        'ST' => 'AF', 'SV' => 'NA', 'SX' => 'NA', 'SY' => 'AS', 'SZ' => 'AF', 'TC' => 'NA', 'TD' => 'AF', 'TF' => 'AN',
        'TG' => 'AF', 'TH' => 'AS', 'TJ' => 'AS', 'TK' => 'OC', 'TL' => 'OC', 'TM' => 'AS', 'TN' => 'AF', 'TO' => 'OC',
        'TR' => 'AS', 'TT' => 'NA', 'TV' => 'OC', 'TW' => 'AS', 'TZ' => 'AF', 'UA' => 'EU', 'UG' => 'AF', 'UM' => 'OC',
        'US' => 'NA', 'UY' => 'SA', 'UZ' => 'AS', 'VA' => 'EU', 'VC' => 'NA', 'VE' => 'SA', 'VG' => 'NA', 'VI' => 'NA',
        'VN' => 'AS', 'VU' => 'OC', 'WF' => 'OC', 'WS' => 'OC', 'YE' => 'AS', 'YT' => 'AF', 'ZA' => 'AF', 'ZM' => 'AF',
        'ZW' => 'AF'
    ];

    public static function of(string $country): string
    {
        return self::MAP[strtoupper($country)] ?? '';
    }

    /**
     * Sortierschlüssel: bekannte Kontinente in der Reihenfolge von ORDER, unbekannte ans Ende.
     */
    public static function rank(string $continent): int
    {
        $pos = array_search($continent, self::ORDER, true);

        return false === $pos ? \count(self::ORDER) : $pos;
    }
}
