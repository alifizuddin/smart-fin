<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Projects extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'budget_id',
        'description',
    ];

    /**
     * Get the department that owns the project.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the budget associated with the project.
     */
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}