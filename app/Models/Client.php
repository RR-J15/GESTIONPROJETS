<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'address',
        'notes',
    ];

    // ===== Relations =====

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}