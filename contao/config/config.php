<?php

declare(strict_types=1);

use Contao\ArrayUtil;
use Gratlinie\GratMapBundle\Widget\MapPickerWidget;

ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 1, [
    'gratmap' => [
        'gm_locations' => [
            'tables' => ['tl_gm_archive', 'tl_gm_location', 'tl_gm_category'],
        ],
    ],
]);

$GLOBALS['BE_FFL']['gmMapPicker'] = MapPickerWidget::class;
