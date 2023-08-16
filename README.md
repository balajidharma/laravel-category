<h1 align="center">Laravel Category</h1>
<h3 align="center">Create database-based Category to your Laravel projects.</h3>
<p align="center">
<a href="https://packagist.org/packages/balajidharma/laravel-menu"><img src="https://poser.pugx.org/balajidharma/laravel-category/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/balajidharma/laravel-menu"><img src="https://poser.pugx.org/balajidharma/laravel-category/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/balajidharma/laravel-menu"><img src="https://poser.pugx.org/balajidharma/laravel-category/license" alt="License"></a>
</p>

## Table of Contents

- [Installation](#installation)
- [Demo](#demo)
- [Create Category Type](#create-type)
- [Create Category](#create-category)
- [Create multiple Category](#create-multiple-menu-items)
- [Category Tree](#menu-tree)

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
