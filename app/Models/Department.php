<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];


    public function assignmentTypes(): HasMany
    {
        return $this->hasMany(AssignmentType::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
