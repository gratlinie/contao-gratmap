<?php

declare(strict_types=1);

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_gm_category'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_gm_archive',
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'fields' => ['sorting'],
            'headerFields' => ['title'],
            'panelLayout' => 'search,limit',
            'defaultSearchField' => 'title',
        ],
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
        ],
    ],
    'palettes' => [
        'default' => '{title_legend},title,color',
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
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'color' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 6, 'colorpicker' => true, 'isHexColor' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => 'string', 'length' => 6, 'default' => ''],
        ],
    ],
];
