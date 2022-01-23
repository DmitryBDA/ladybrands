<?php return [
    'plugin'    => [
        'name'        => 'Filter for Shopaholic',
        'description' => 'Filter of products',
    ],
    'field'     => [
        'in_filter'               => 'Show property in the filter',
        'filter_type'             => 'Type of filter display',
        'filter_name'             => 'Filter name',
        'filter_name_description' => 'If you do not specify a filter name, the property name will be used',
    ],
    'type'      => [
        'radio'          => 'RADIO - filter by one value from radio button',
        'between'        => 'Filter by range',
        'checkbox'       => 'CHECKBOX - filter by several values from the list',
        'select'         => 'SELECT -  filter by one value from select',
        'switch'         => 'SWITCH - filter by one value from switch button',
        'select_between' => 'Filter by range from select',
    ],
    'component' => [
        'filter_name'        => 'Filter panel',
        'filter_description' => 'Render custom filter panel',
    ],
];
