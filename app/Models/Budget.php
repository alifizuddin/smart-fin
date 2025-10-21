<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Projects;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'project_id',
        'total_amount',
        'used_amount',
        'period_start',
        'period_end',
        'status',
        'created_by',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function project(): BelongsTo
    {
        return $this->belongsTo(Projects::class);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function expenses()
    {
        return $this->hasMany(Expense::class)->latest(); // Order by most recent
    }   
}