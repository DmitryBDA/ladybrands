<?php return [
    'plugin'       => [
        'name'        => 'Coupons for Shopaholic',
        'description' => '',
    ],
    'field'        => [
        'usage'                  => 'Usage',
        'max_usage'              => 'Max usage per coupon',
        'max_usage_per_user'     => 'Max usage per user',
        'coupon_count'           => 'Coupon count',
        'user_list'              => 'User list',
        'user_list_description'  => 'Fill in the field with the user IDs or user emails address separated by commas. For example: <strong>test1@test.com,test2@test.com,test3@test.com</strong>.',
        'hidden_description'     => 'Hidden coupons are not visible to the user, but he can apply them.',
        'use_lowercase'          => 'Use lowercase (a-z)',
        'use_uppercase'          => 'Use uppercase (A-Z)',
        'use_number'             => 'Use numbers (0-9)',
        'part_count'             => 'Part count',
        'part_separator'         => 'Part separator',
        'part_count_description' => 'The parts of the code will be separated by a symbol "-". Each part will be the length specified in the field "Length".',
    ],
    'coupon'       => [
        'name'       => 'coupon',
        'list_title' => 'Coupon list',
    ],
    'coupon_group' => [
        'name'       => 'coupon group',
        'list_title' => 'Coupon group list',
    ],
    'tab'          => [
        'coupon' => 'Coupons',
    ],
    'menu'         => [
        'coupon_group' => 'Coupon groups',
    ],
    'permission'   => [
        'coupon' => 'Manage coupons'
    ],
    'message'      => [
        'generate_coupon'              => 'Generate coupons',
        'generate_coupon_confirm'      => 'Do you want to create coupons with these parameters?',
        'generate_coupon_success'      => 'Generation of coupons was successfully completed. Coupons generated: :count of :max.',
        'coupon_discount_info'         => 'Coupon ":code" was applied',
        'error_coupon_can_not_applied' => 'Coupon can not be applied',
    ],
];