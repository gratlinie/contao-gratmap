<?php

declare(strict_types=1);

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_gm_archive'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_gm_location', 'tl_gm_category'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['title'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'operations' => [
            'edit',
            'children',
            'categories' => [
                'href' => 'table=tl_gm_category',
                'icon' => 'folderC.svg',
                'prefetch' => true,
            ],
            'copy',
            'delete',
            'show',
        ],
    ],
    'palettes' => [
        'default' => '{title_legend},title',
    ],
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
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
    ],
];
