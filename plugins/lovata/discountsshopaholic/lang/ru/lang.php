<?php return [
    'plugin'     => [
        'name'        => 'Discounts for Shopaholic',
        'description' => '',
    ],
    'field'      => [
        'hidden_description' => 'Скрытые скидки не видны в списке все скидок, но они будут влиять на цену товаров',
        'date_begin'         => 'Дата начала действия скидки',
        'date_end'           => 'Дата окончания действия скидки',
        'discount_value'     => 'Размер скидки',
        'discount_type'      => 'Тип скидки',
        'active_discount'    => 'Активная скидка',
        'discount'           => 'Скидка',
        'product'            => 'Товар',
    ],
    'discount'   => [
        'name'       => 'скидки',
        'list_title' => 'Список скидок',
    ],
    'menu'       => [
        'discount' => 'Скидки',
        'promo'    => 'Промо-акции',
    ],
    'component'  => [
        'discount_page_name'        => 'Страница скидки',
        'discount_page_description' => 'Страница с описанием скидки',
        'discount_data_name'        => 'Данные скидки',
        'discount_data_description' => '',
        'discount_list_name'        => 'Список скидок',
        'discount_list_description' => '',

        'sorting_default' => 'По-умолчанию',
        'sorting_date_begin_desc' => 'По дате начала (DESC)',
        'sorting_date_begin_asc'  => 'По дате начала (ASC)',
        'sorting_date_end_desc'   => 'По дате окончания (DESC)',
        'sorting_date_end_asc'    => 'По дате окончания (ASC)',
    ],
    'permission' => [
        'promo' => 'Управление промо-акциями',
    ],
    'type'       => [
        'percent' => 'Процент',
        'fixed'   => 'Фиксированная',
    ],
    'settings'   => [
        'discount_update_price_queue_on'   => 'Использовать очереди при запуске команды обновления цен каталога',
        'discount_update_price_queue_name' => 'Название очереди',
    ],
    'message' => [
        'update_catalog_prices_confirm' => 'Вы действительно хотите обновить цены в каталоге?',
        'update_catalog_prices_started' => 'Запущен механизм обновления цен в каталоге.',
        'update_catalog_prices_success' => 'Обновление цен в каталоге успешно завершено.',
    ],
    'button' => [
        'update_catalog_prices' => 'Обновить цены каталога',
    ],
];