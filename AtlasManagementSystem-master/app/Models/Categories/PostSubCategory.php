<?php

namespace App\Models\Categories;

use Illuminate\Database\Eloquent\Model;

class PostSubCategory extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'post_id',
        'sub_category_id',
    ];
}
