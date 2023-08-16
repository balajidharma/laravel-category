<?php

return [
    'models' => [
        'category_type' => BalajiDharma\LaraveCategory\Models\CategoryType::class,
        'category' => BalajiDharma\LaraveCategory\Models\Category::class,
    ],

    'table_names' => [
        'category_types' => 'category_types',
        'categories' => 'categories',
        'model_has_categories' => 'model_has_categories',
    ]
];
