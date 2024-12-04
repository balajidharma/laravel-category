<?php

namespace BalajiDharma\LaravelCategory\Models;

use ArrayAccess;
use BalajiDharma\LaravelCategory\Exceptions\CategoryTypeNotExists;
use BalajiDharma\LaravelCategory\Traits\CategoryTree;
use BalajiDharma\LaravelCategory\Traits\HasCategories;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use CategoryTree, HasCategories;

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
        'enabled' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->setHighestWeightNumber();
            $model->setSlug();
        });
    }

    public function getTable()
    {
        return config('category.table_names.categories', parent::getTable());
    }

    public function categoryType(): BelongsTo
    {
        return $this->BelongsTo(config('category.models.category_type'));
    }

    public function setHighestWeightNumber(): void
    {
        $this->weight = $this->weight ?? $this->getHighestWeightNumber() + 1;
    }

    public function getHighestWeightNumber(): int
    {
        return (int) static::query()->where('category_type_id', $this->attributes['category_type_id'])->max('weight');
    }

    public function setSlug()
    {
        $slug = $this->slug ?? $this->name;
        $slug = \Str::slug($slug);

        $regexOperators = [
            'mysql' => 'RLIKE',
            'pgsql' => '~',
            'sqlite' => 'REGEXP'
        ];

        $driver = DB::connection()->getDriverName();
        $regexOperator = $regexOperators[$driver] ?? 'mysql';

        if ($this->id) {
            $similarSlugs = Category::where(function (Builder $q) use ($slug) {
                $q->where('slug', '=', $slug)
                    ->where('category_type_id', $this->category_type_id)
                    ->where('id', '!=', $this->id);
            })->where(function (Builder $q) use ($slug, $regexOperator) {
                $q->where('id', '!=', $this->id)
                    ->where('category_type_id', '!=', $this->category_type_id)
                    ->orWhereRaw("slug {$regexOperator} '^{$slug}(-[0-9]+)?$'");
            })->select('slug')->get();
        } else {
            $similarSlugs = Category::where(function (Builder $q) use ($slug, $regexOperator) {
                $q->where('slug', '=', $slug)
                    ->where('category_type_id', $this->category_type_id)
                    ->orWhereRaw("slug {$regexOperator} '^{$slug}(-[0-9]+)?$'");
            })->select('slug')->get();
        }

        if ($similarSlugs->count()) {
            $valid = 0;
            $i = 1;
            do {
                $newSlug = $slug.'-'.$i;
                if ($similarSlugs->firstWhere('slug', $newSlug)) {
                    $i++;
                } else {
                    $valid = 1;
                    $slug = $newSlug;
                }
            } while ($valid < 1);
        }
        $this->slug = $slug;
    }

    public static function findOrCreate(array|ArrayAccess|Category $values, string $type)
    {
        return collect($values)->map(function ($value) use ($type) {
            if ($value instanceof self) {
                return $value;
            }

            if (is_array($value)) {
                return static::findOrCreateFromArray($value, $type);
            }

            return static::findOrCreateFromString(trim($value), $type);
        });
    }

    public static function findOrCreateFromArray(array $value, string $type)
    {
        $categoryType = static::findCategoryType($type);

        return static::firstOrCreate(
            [
                'name' => $value['name'],
                'category_type_id' => $categoryType->id,
            ],
            $value
        );
    }

    public static function findFromString(string $name, string $type)
    {
        return static::query()
            ->whereRelation('categoryType', 'machine_name', $type)
            ->where('enabled', true)
            ->where('name', $name)
            ->first();
    }

    public static function findOrCreateFromString(string $name, string $type)
    {
        $categoryType = static::findCategoryType($type);

        $category = static::findFromString($name, $type);

        if (! $category) {
            $category = static::create([
                'name' => $name,
                'category_type_id' => $categoryType->id,
            ]);
        }

        return $category;
    }

    public static function findCategoryType(string $type)
    {
        $categoryType = CategoryType::where('machine_name', $type)->first();

        if (! $categoryType) {
            throw CategoryTypeNotExists::create($type);
        }

        return $categoryType;
    }
}
