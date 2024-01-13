<?php

namespace BalajiDharma\LaravelCategory\Models;

use BalajiDharma\LaravelCategory\Exceptions\CategoryTypeAlreadyExists;
use BalajiDharma\LaravelCategory\Exceptions\CategoryTypeNotExists;
use BalajiDharma\LaravelCategory\Exceptions\MachineNameInvalidArgument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryType extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_flat' => 'boolean',
    ];

    public function getTable()
    {
        return config('category.table_names.category_types', parent::getTable());
    }

    public static function create(array $attributes = [])
    {
        if (! static::validateMachineName($attributes['machine_name'])) {
            throw MachineNameInvalidArgument::create();
        }

        $type = CategoryType::where('machine_name', $attributes['machine_name'])->first();

        if ($type) {
            throw CategoryTypeAlreadyExists::create($attributes['machine_name']);
        }

        return static::query()->create($attributes);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(config('category.models.category'));
    }

    public static function validateMachineName($machine_name)
    {
        return preg_match('/^[a-z0-9_-]+$/', $machine_name);
    }

    protected static function getCategoryTree($machine_name, $includeDisabledItems = false)
    {
        $categoryType = CategoryType::where('machine_name', $machine_name)->first();
        if (! $categoryType) {
            throw CategoryTypeNotExists::create($machine_name);
        }

        return (new Category)->toTree($categoryType->id, $includeDisabledItems);
    }
}
