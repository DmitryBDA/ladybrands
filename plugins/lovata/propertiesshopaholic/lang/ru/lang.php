<?php return [
    'plugin'         => [
        'name'        => 'Properties for Shopaholic',
        'description' => 'Дополнительные свойства товаров и предложений',
    ],
    'field'          => [
        'product_property_list'                 => 'Свойства товаров',
        'offer_property_list'                   => 'Свойства товарных предложений',
        'property_groups'                       => 'Группы свойства',
        'properties'                            => 'Свойства',
        'property_set_is_global'                => 'Включить набор свойств как глобальный',
        'inherit_property_set'                  => 'Включить наследование набора свойств из родительской категории',
        'property_value_with_urlencode'         => 'Использовать функцию urlencode() при формировании значения свойства, используемого в URL.',
        'property_value_without_str_slug'       => 'Не использовать метод Str::slug() при формировании значения свойства, используемого в URL.',
        'property_inheriting_property_set'      => 'Включить для категорий наследование наборов свойств от родительских категорий.',
        'import_path_to_property_list'          => 'Путь к узлу со списокм свойств',
        'import_path_to_property_list_example'  => 'ЗначенияСвойств/ЗначенияСвойства',
        'import_path_to_property_id'            => 'Путь к узлу с ID свойства',
        'import_path_to_property_id_example'    => 'ИД',
        'import_path_to_property_value'         => 'Путь к узлу со списком значений свойства',
        'import_path_to_property_value_example' => 'Значения/Значение',
        'hide_property_import_from_csv'         => 'Скрыть кнопку "Импорт из CSV" для свойств',
        'hide_property_import_from_xml'         => 'Скрыть кнопку "Импорт из XML" для свойств',
    ],
    'menu'           => [
        'property'                 => 'Свойства товаров',
        'property_description'     => 'Управление свойствами товаров',
        'property_set'             => 'Наборы свойств',
        'property_set_description' => 'Управление наборами свойств',
        'group'                    => 'Группы свойств',
        'group_description'        => 'Управление группами свойств',
    ],
    'property'       => [
        'name'         => 'свойства',
        'list_title'   => 'Список свойств',
        'import_title' => 'Импорт свойств',
        'export_title' => 'Экспорт свойств',
    ],
    'property_value' => [
        'name'       => 'значения свойства',
        'list_title' => 'Список значений свойств',
    ],
    'property_set'   => [
        'name'       => 'набора',
        'list_title' => 'Список наборов свойств',
    ],
    'group'          => [
        'name'       => 'группы',
        'list_title' => 'Список групп',
    ],
    'permission'     => [
        'property' => 'Управление свойствами товаров',
        'group'    => 'Управление группами свойств',
    ],
    'message'        => [
        'import_available_property_type'  => 'Допустимые значения типов полей: input, number, textarea, rich_editor, single_checkbox, switch, checkbox, balloon_selector, tag_list, select, radio, date, colorpicker, mediafinder.',
        'import_available_property_value' => 'Допустимые значения свойств: "red|green|blue"',
    ],
];