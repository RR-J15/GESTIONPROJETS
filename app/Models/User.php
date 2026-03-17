<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'position',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];



    protected $attributes = [
        'role' => 'membre', // ← valeur par défaut si null
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role'      => UserRole::class,
        ];
    }




    // ===== Relations =====

    // Projets où il est membre
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Tâches qui lui sont assignées
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    // Tâches qu'il a créées
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    // Projets qu'il a créés
    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    // Commentaires qu'il a postés
    public function taskComments()
    {
        return $this->hasMany(TaskComment::class);
    }

    // ===== Helpers Rôles =====

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isMembre(): bool
    {
        return $this->role === UserRole::Membre;
    }
}
