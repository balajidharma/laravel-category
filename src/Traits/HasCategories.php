<?php

namespace BalajiDharma\LaravelCategory\Traits;

use ArrayAccess;
use BalajiDharma\LaravelCategory\Models\Category;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasCategories
{
    /**
     * @return mixed
     */
    public function modelCategories(): MorphToMany
    {
        return $this->morphToMany(
            config('category.models.category'),
            'model',
            config('category.table_names.model_has_categories'),
            config('category.column_names.model_morph_key')
        )->orderBy(config('category.table_names.model_has_categories').'.weight', 'asc');
    }

    public static function getCategoryClassName(): string
    {
        return config('category.models.category', Category::class);
    }

    public function getCategoriesByType($type)
    {
        return $this->modelCategories()->whereRelation('categoryType', function ($query) use ($type) {
            if (is_array($type)) {
                return $query->whereIn('machine_name', $type);
            } elseif (is_string($type)) {
                return $query->where('machine_name', $type);
            }
        })
            ->where('enabled', true);
    }

    public function attachTags(array|ArrayAccess|Category $categories, string $type): static
    {
        $className = static::getCategoryClassName();
        $categories = array_filter($categories);
        $categoryType = $className::findCategoryType($type);
        $categories = collect($className::findOrCreate($categories, $type));
        $syncData = [];
        $weight = 1;
        foreach ($categories as $category) {
            $syncData[$category->id] = ['weight' => $weight, 'category_type_id' => $categoryType->id];
            $weight++;
        }

        $this->modelCategories()->wherePivot('category_type_id', $categoryType->id)->syncWithoutDetaching($syncData);

        return $this;
    }

    public function attachTag(string|Category $category, string $type)
    {
        return $this->attachTags([$category], $type);
    }

    public function detachTags(array|ArrayAccess $categories, $type): static
    {
        $categories = static::convertToTags($categories, $type);

        collect($categories)
            ->filter()
            ->each(fn (Category $category) => $this->modelCategories()->detach($category));

        return $this;
    }

    public function detachTag(string|Category $category, $type): static
    {
        return $this->detachTags([$category], $type);
    }

    public function syncTags(array|ArrayAccess|Category $categories, string $type): static
    {
        $className = static::getCategoryClassName();
        $categories = array_filter($categories);
        $categoryType = $className::findCategoryType($type);
        $categories = collect($className::findOrCreate($categories, $type));
        $syncData = [];
        $weight = 1;
        foreach ($categories as $category) {
            $syncData[$category->id] = ['weight' => $weight, 'category_type_id' => $categoryType->id];
            $weight++;
        }

        $this->modelCategories()->wherePivot('category_type_id', $categoryType->id)->sync($syncData);

        return $this;
    }

    public function syncCategories(array $ids, string $type): static
    {
        $className = static::getCategoryClassName();
        $categoryType = $className::findCategoryType($type);

        $syncData = [];
        $weight = 1;
        foreach ($ids as $id) {
            $syncData[$id] = ['weight' => $weight, 'category_type_id' => $categoryType->id];
            $weight++;
        }
        $this->modelCategories()->wherePivot('category_type_id', $categoryType->id)->sync($syncData);
        return $this;
    }

    protected static function convertToTags($values, $type)
    {
        if ($values instanceof Category) {
            $values = [$values];
        }

        return collect($values)->map(function ($value) use ($type) {
            if ($value instanceof Category) {
                return $value;
            }

            $className = static::getCategoryClassName();

            return $className::findFromString($value, $type);
        });
    }
}
