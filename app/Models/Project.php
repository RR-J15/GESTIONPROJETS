<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'quote_id',
        'created_by',
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'budget',
    ];

    protected function casts(): array
    {
        return [
            'status'     => ProjectStatus::class,
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    // ===== Relations =====

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // ===== Helpers =====

    public function isTermine(): bool
    {
        return $this->status === ProjectStatus::Termine;
    }

    public function progression(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;

        $terminees = $this->tasks()
                          ->where('status', 'termine')
                          ->count();

        return (int) round(($terminees / $total) * 100);
    }
}