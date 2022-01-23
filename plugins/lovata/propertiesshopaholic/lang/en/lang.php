<?php return [
    'plugin'         => [
        'name'        => 'Properties for Shopaholic',
        'description' => 'Addition properties of products and offers',
    ],
    'field'          => [
        'product_property_list'                 => 'Product properties',
        'offer_property_list'                   => 'Offer properties',
        'property_groups'                       => 'Property groups',
        'properties'                            => 'Properties',
        'property_set_is_global'                => 'Enable property set as global',
        'inherit_property_set'                  => 'Enable inheriting property sets from parent category',
        'property_value_with_urlencode'         => 'Use urlencode() function when generating value of property used in URL.',
        'property_value_without_str_slug'       => 'Do not use Str::slug() method when generating value of property used in URL.',
        'property_inheriting_property_set'      => 'Enable for categories inheriting property sets from parent categories.',
        'import_path_to_property_list'          => 'Path to node with property list',
        'import_path_to_property_list_example'  => 'properties/property',
        'import_path_to_property_id'            => 'Path to node with property ID',
        'import_path_to_property_id_example'    => 'id',
        'import_path_to_property_value'         => 'Path to node with property values',
        'import_path_to_property_value_example' => 'values/value',
        'hide_property_import_from_csv'         => 'Hide "Import from CSV" button for properties',
        'hide_property_import_from_xml'         => 'Hide "Import from XML" button for properties',
    ],
    'menu'           => [
        'property'                 => 'Properties',
        'property_description'     => 'Manage properties',
        'property_set'             => 'Property sets',
        'property_set_description' => 'Manage property sets',
        'group'                    => 'Property groups',
        'group_description'        => 'Manage property groups',
    ],
    'property'       => [
        'name'         => 'property',
        'list_title'   => 'Property list',
        'import_title' => 'Import properties',
        'export_title' => 'Export properties',
    ],
    'property_value' => [
        'name'       => 'property value',
        'list_title' => 'Property value list',
    ],
    'property_set'   => [
        'name'       => 'property set',
        'list_title' => 'Property set list',
    ],
    'group'          => [
        'name'       => 'group',
        'list_title' => 'Group list',
    ],
    'permission'     => [
        'property' => 'Manage properties',
        'group'    => 'Manage property groups',
    ],
    'message'        => [
        'import_available_property_type'  => 'Available property types: input, number, textarea, rich_editor, single_checkbox, switch, checkbox, balloon_selector, tag_list, select, radio, date, colorpicker, mediafinder.',
        'import_available_property_value' => 'Available property values: "red|green|blue"',
    ],
];