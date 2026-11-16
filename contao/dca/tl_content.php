<?php

declare(strict_types=1);

use Contao\System;
use Gratlinie\GratMapBundle\Map\TileProviders;

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'gmTileProvider';

$GLOBALS['TL_DCA']['tl_content']['palettes']['gm_map'] = '{type_legend},type,headline;{gm_source_legend},gmArchives;{gm_view_legend},gmView,gmGroupBy,gmHomeCountry,gmFilters,gmRadii,gmRoute,size;{gm_map_legend},gmTileProvider,gmHeight,gmZoom,gmMarker,gmMarkerSize,gmCluster,gmConsent,gmPrivacyLevel;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['subpalettes']['gmTileProvider_custom'] = 'gmTileUrl,gmTileAttribution';

foreach (TileProviders::withKey() as $provider) {
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['gmTileProvider_'.$provider] = 'gmTileKey';
}

$GLOBALS['TL_DCA']['tl_content']['fields'] += [
    'gmArchives' => [
        'inputType' => 'checkbox',
        'foreignKey' => 'tl_gm_archive.title',
        'eval' => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'clr'],
        'sql' => ['type' => 'blob', 'notnull' => false],
    ],
    'gmView' => [
        'inputType' => 'select',
        'options' => ['map_list', 'map', 'list'],
        'reference' => &$GLOBALS['TL_LANG']['tl_content']['gm_ref']['view'],
        'eval' => ['tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 16, 'default' => 'map_list'],
    ],
    'gmGroupBy' => [
        'inputType' => 'select',
        'options' => ['', 'country', 'postal'],
        'reference' => &$GLOBALS['TL_LANG']['tl_content']['gm_ref']['groupBy'],
        'eval' => ['tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 16, 'default' => 'postal'],
    ],
    'gmHomeCountry' => [
        'inputType' => 'select',
        'options_callback' => static fn (): array => System::getContainer()->get('contao.intl.countries')->getCountries(),
        'eval' => ['chosen' => true, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 2, 'default' => 'DE'],
    ],
    'gmFilters' => [
        'inputType' => 'checkbox',
        'options' => ['radius', 'country', 'category'],
        'reference' => &$GLOBALS['TL_LANG']['tl_content']['gm_ref']['filters'],
        'eval' => ['multiple' => true, 'tl_class' => 'clr'],
        'sql' => ['type' => 'blob', 'notnull' => false],
    ],
    'gmRadii' => [
        'inputType' => 'text',
        'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 64, 'default' => '25,50,100,200'],
    ],
    'gmRoute' => [
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => ['type' => 'boolean', 'default' => true],
    ],
    'gmZoom' => [
        'inputType' => 'select',
        'options' => range(2, 18),
        'eval' => ['includeBlankOption' => true, 'blankOptionLabel' => '-', 'tl_class' => 'w50'],
        'sql' => ['type' => 'smallint', 'unsigned' => true, 'default' => 0],
    ],
    'gmTileProvider' => [
        'inputType' => 'select',
        'options' => TileProviders::keys(),
        'reference' => &$GLOBALS['TL_LANG']['tl_content']['gm_ref']['tiles'],
        'eval' => ['submitOnChange' => true, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 32, 'default' => 'osm'],
    ],
    'gmTileKey' => [
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 128, 'decodeEntities' => true, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 128, 'default' => ''],
    ],
    'gmTileUrl' => [
        'inputType' => 'text',
        'eval' => ['mandatory' => true, 'maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'long clr'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ],
    'gmTileAttribution' => [
        'inputType' => 'text',
        'eval' => ['maxlength' => 255, 'allowHtml' => true, 'tl_class' => 'long clr'],
        'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
    ],
    'gmHeight' => [
        'inputType' => 'text',
        'eval' => ['maxlength' => 32, 'tl_class' => 'w50'],
        'sql' => ['type' => 'string', 'length' => 32, 'default' => ''],
    ],
    'gmMarker' => [
        'inputType' => 'fileTree',
        'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'svg,png,webp', 'tl_class' => 'clr'],
        'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
    ],
    'gmMarkerSize' => [
        'inputType' => 'text',
        'eval' => ['rgxp' => 'natural', 'maxlength' => 3, 'tl_class' => 'w50'],
        'sql' => ['type' => 'smallint', 'unsigned' => true, 'default' => 32],
    ],
    'gmCluster' => [
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => ['type' => 'boolean', 'default' => true],
    ],
    'gmPrivacyLevel' => [
        'inputType' => 'select',
        'options' => [1, 2, 3, 4],
        'reference' => &$GLOBALS['TL_LANG']['tl_content']['gm_ref']['privacyLevel'],
        'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
        'sql' => ['type' => 'smallint', 'unsigned' => true, 'default' => 2],
    ],
    'gmConsent' => [
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => ['type' => 'boolean', 'default' => true],
    ],
];
