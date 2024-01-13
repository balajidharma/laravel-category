<?php

namespace BalajiDharma\LaravelCategory\Models;

use BalajiDharma\LaravelCategory\Traits\CategoryTree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use CategoryTree {
        CategoryTree::boot as treeBoot;
    }

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

    public function getTable()
    {
        return config('category.table_names.categories', parent::getTable());
    }

    public function categoryType(): BelongsTo
    {
        return $this->BelongsTo(config('category.models.category_type'));
    }

    public function setWeightAttribute($weight)
    {
        $this->attributes['weight'] = $weight ?? 0;
    }

    public function setSlugAttribute($slug)
    {
        $slug = $slug ?? $this->name;
        $slug = \Str::slug($slug);

        if ($this->id) {
            $similarSlugs = Category::where(function (Builder $q) use ($slug) {
                $q->where('slug', '=', $slug)
                    ->where('id', '!=', $this->id);
            })->where(function (Builder $q) use ($slug) {
                $q->where('id', '!=', $this->id)
                    ->orWhereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'");
            })->select('slug')->get();
        } else {
            $similarSlugs = Category::where(function (Builder $q) use ($slug) {
                $q->where('slug', '=', $slug)
                    ->orWhereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'");
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
        $this->attributes['slug'] = $slug;
    }
}
