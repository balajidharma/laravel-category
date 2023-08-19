<?php

namespace BalajiDharma\LaravelCategory\Models;

use BalajiDharma\LaravelCategory\Traits\CategoryTree;
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

    public function setIsEnabled($is_enabled) 
    {
        $this->attributes['is_enabled'] = $is_enabled ?? 1;
    }
}
