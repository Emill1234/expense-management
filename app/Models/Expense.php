<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['description', 'amount', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}