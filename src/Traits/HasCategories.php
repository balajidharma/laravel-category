<?php

namespace BalajiDharma\LaravelCategory\Traits;

use ArrayAccess;
use BalajiDharma\LaravelCategory\Models\Category;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;

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

    public function attachCategories(array|ArrayAccess|Category $categories, string $type): static
    {
        $className = static::getCategoryClassName();
        $categories = collect($className::findOrCreate($categories, $type));
        $syncData = [];
        $weight = 1;
        foreach ($categories as $category) {
            $syncData[$category->id] = ['weight' => $weight];
            $weight++;
        }

        $this->modelCategories()->sync($syncData);

        return $this;
    }

    public function attachCategory(string|Category $category, ?string $type = null)
    {
        return $this->attachCategories([$category], $type);
    }

    public function detachCategories(array|ArrayAccess $categories, ?string $type = null): static
    {
        $categories = static::convertToCategories($categories, $type);

        collect($categories)
            ->filter()
            ->each(fn (Category $category) => $this->tags()->detach($category));

        return $this;
    }

    public function detachCategory(string|Category $category, ?string $type = null): static
    {
        return $this->detachCategorys([$category], $type);
    }

    public function syncCategories(string|array|ArrayAccess $categories): static
    {
        if (is_string($categories)) {
            $categories = Arr::wrap($categories);
        }

        $className = static::getCategoryClassName();

        $categories = collect($className::findOrCreate($categories));

        $this->tags()->sync($categories->pluck('id')->toArray());

        return $this;
    }
}
