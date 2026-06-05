<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'type'];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
}
