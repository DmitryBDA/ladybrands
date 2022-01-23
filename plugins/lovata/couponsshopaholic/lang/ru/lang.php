<?php return [
    'plugin'     => [
        'name'        => 'Coupons for Shopaholic',
        'description' => '',
    ],
    'field'        => [
        'usage'                  => 'Количество использований',
        'max_usage'              => 'Максимальное количество использований купона',
        'max_usage_per_user'     => 'Максимальное количество использований купона для одного пользователя',
        'coupon_count'           => 'Количество купонов',
        'user_list'              => 'Список пользователей',
        'user_list_description'  => 'Заполните это поле списком ID пользователей или email адресов, разделенных запятой. Например: <strong>test1@test.com,test2@test.com,test3@test.com</strong>.',
        'hidden_description'     => 'Скрытые купоны не видны для пользователей, но могут быть применены.',
        'use_lowercase'          => 'Использовать символы нижнего регистра (a-z)',
        'use_uppercase'          => 'Использовать символы верхнего регистра (A-Z)',
        'use_number'             => 'Использовать символы цифр (0-9)',
        'part_count'             => 'Количиство частей',
        'part_separator'         => 'Разделитель частей',
        'part_count_description' => 'Части кода будут разделены символом "-". Каждая часть будет иметь длинну, указанную в поле "Длинна".',
    ],
    'coupon'   => [
        'name'       => 'купона',
        'list_title' => 'Список купонов',
    ],
    'coupon_group'   => [
        'name'       => 'группы купона',
        'list_title' => 'Список групп купонов',
    ],
    'tab'          => [
        'coupon' => 'Купоны',
    ],
    'menu'       => [
        'coupon_group' => 'Группы купонов',
    ],
    'permission'   => [
        'coupon' => 'Управление купонами',
    ],
    'message'      => [
        'generate_coupon'              => 'Сгенерировать купоны',
        'generate_coupon_confirm'      => 'Вы хотите создать купоны с заданными параметрами?',
        'generate_coupon_success'      => 'Генерация купонов успешно завершена. Купонов сгенерировано: :count из :max.',
        'coupon_discount_info'         => 'Купон ":code" был применен',
        'error_coupon_can_not_applied' => 'Купон не может быть применен',
    ],
];