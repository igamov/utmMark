<?php

return [
    'utmMark' => [
        'file' => 'utmmark',
        'description' => 'utmMark snippet',
        'properties' => [
            'tpl' => [
                'type' => 'textfield',
                'value' => 'tpl.utmMark.item',
                'desc' => 'utmmark_prop_tpl',
            ],
            'toPlaceholder' => [
                'type' => 'combo-boolean',
                'value' => false,
                'desc' => 'utmmark_prop_toPlaceholder',
            ],
        ],
    ],
];