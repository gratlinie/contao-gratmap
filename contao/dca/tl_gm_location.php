<?php

declare(strict_types=1);

use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;

$GLOBALS['TL_DCA']['tl_gm_location'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_gm_archive',
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid,published' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'fields' => ['country', 'postal', 'title'],
            'headerFields' => ['title'],
            'panelLayout' => 'filter;sort,search,limit',
            'defaultSearchField' => 'title',
        ],
        'label' => [
            'fields' => ['title', 'postal', 'city', 'country'],
            'format' => '%s <span class="label-info">%s %s (%s)</span>',
        ],
        'operations' => [
            'edit',
            'copy',
            'cut',
            'delete',
            'toggle',
            'show',
        ],
    ],
    'palettes' => [
        'default' => '{title_legend},title,contact,categories;{address_legend},street,postal,city,country;{service_legend:hide},serviceCountries;{geo_legend},latitude,longitude,mapPicker;{contact_legend},phone,fax,email,website;{media_legend},logo;{text_legend:hide},description;{publish_legend},published,start,stop',
    ],
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'pid' => [
            'foreignKey' => 'tl_gm_archive.title',
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'sorting' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'title' => [
            'search' => true,
            'sorting' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'contact' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'categories' => [
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['multiple' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'blob', 'notnull' => false],
        ],
        'street' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'postal' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 16, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 16, 'default' => ''],
        ],
        'city' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'country' => [
            'filter' => true,
            'sorting' => true,
            'inputType' => 'select',
            'options_callback' => static fn (): array => System::getContainer()->get('contao.intl.countries')->getCountries(),
            'default' => 'DE',
            'eval' => ['mandatory' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 2, 'default' => 'DE'],
        ],
        'serviceCountries' => [
            'inputType' => 'checkboxWizard',
            'options_callback' => static fn (): array => System::getContainer()->get('contao.intl.countries')->getCountries(),
            'eval' => ['multiple' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'blob', 'notnull' => false],
        ],
        'latitude' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 16, 'rgxp' => 'gmLatitude', 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 16, 'default' => ''],
        ],
        'longitude' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 16, 'rgxp' => 'gmLongitude', 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 16, 'default' => ''],
        ],
        'mapPicker' => [
            'inputType' => 'gmMapPicker',
            'eval' => ['tl_class' => 'clr', 'doNotShow' => true],
        ],
        'phone' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
        ],
        'fax' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
        ],
        'email' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'website' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'rgxp' => 'url', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'logo' => [
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => '%contao.image.valid_extensions%', 'tl_class' => 'clr'],
            'sql' => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
        ],
        'description' => [
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'basicEntities' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'text', 'notnull' => false],
        ],
        'published' => [
            'toggle' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'start' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => ''],
        ],
        'stop' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => ''],
        ],
    ],
];
