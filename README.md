<h1 align="center">Laravel Category</h1>
<h3 align="center">Create database-based Category to your Laravel projects.</h3>
<p align="center">
<a href="https://packagist.org/packages/balajidharma/laravel-category"><img src="https://poser.pugx.org/balajidharma/laravel-category/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/balajidharma/laravel-category"><img src="https://poser.pugx.org/balajidharma/laravel-category/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/balajidharma/laravel-category"><img src="https://poser.pugx.org/balajidharma/laravel-category/license" alt="License"></a>
</p>

## Table of Contents

- [Installation](#installation)
- [Demo](#demo)
- [Create Category Type](#create-category-type)
- [Create Category](#create-category)
- [Create multiple Category](#create-multiple-menu-items)
- [Category Tree](#category-tree)

## Installation
- Install the package via composer
```bash
composer require balajidharma/laravel-category
```
- Publish the migration and the config/category.php config file with
```bash
php artisan vendor:publish --provider="BalajiDharma\LaravelCategory\CategoryServiceProvider"
```
- Run the migrations
```bash
php artisan migrate
```

## Demo
The "[Basic Laravel Admin Penel](https://github.com/balajidharma/basic-laravel-admin-panel)" starter kit come with Laravel Category

## Create Category Type
```php

use BalajiDharma\LaravelCategory\Models\CategoryType;

CategoryType::create([
    'name' => 'Product Category',
    'machine_name' => 'product_category',
    'description' => 'Site Product Category',
]);
```

## Category Tree
- Get a category tree by using category type id
```php
use BalajiDharma\LaravelCategory\Models\Category;

$items = (new Category)->toTree($type->id);
```

- Get a category tree by using the category machine name
```php
use BalajiDharma\LaravelCategory\Models\CategoryType;

$items = CategoryType::getCategoryTree('product_category');
```
