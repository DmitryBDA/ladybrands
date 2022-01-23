<?php return [
    'plugin'     => [
        'name'        => 'Discounts for Shopaholic',
        'description' => '',
    ],
    'field'      => [
        'hidden_description' => 'Hidden discounts are not visible in the list of discounts, but they will affect the price',
        'date_begin'         => 'Date of the beginning of the discount',
        'date_end'           => 'Date of the ending of the discount',
        'discount_value'     => 'Discount value',
        'discount_type'      => 'Discount type',
        'active_discount'    => 'Active discount',
        'discount'           => 'Discount',
        'product'            => 'Product',
    ],
    'discount'   => [
        'name'       => 'discount',
        'list_title' => 'Discount list',
    ],
    'menu'       => [
        'discount' => 'Discounts',
        'promo'    => 'Promo',
    ],
    'component'  => [
        'discount_page_name'        => 'Discount page',
        'discount_page_description' => 'Discount content page',
        'discount_data_name'        => 'Discount data',
        'discount_data_description' => 'Get discount data',
        'discount_list_name'        => 'Discount list',
        'discount_list_description' => 'Get discount list',

        'sorting_default' => 'Default',
        'sorting_date_begin_desc' => 'By date begin (DESC)',
        'sorting_date_begin_asc'  => 'By date begin (ASC)',
        'sorting_date_end_desc'   => 'By date end (DESC)',
        'sorting_date_end_asc'    => 'By date end (ASC)',
    ],
    'permission' => [
        'promo' => 'Manage promo blocks',
    ],
    'type'       => [
        'percent' => 'Percent',
        'fixed'   => 'Fixed',
    ],
    'settings'   => [
        'discount_update_price_queue_on'   => 'Use queue when you run command of updating catalog price',
        'discount_update_price_queue_name' => 'Queue name',
    ],
    'message' => [
        'update_catalog_prices_confirm' => 'Are you sure you want to update the prices in the catalog?',
        'update_catalog_prices_started' => 'The mechanism for updating prices in the catalog has been launched.',
        'update_catalog_prices_success' => 'Updating prices in the catalog has been successfully completed.',
    ],
    'button' => [
        'update_catalog_prices' => 'Update catalog prices',
    ],
];