<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    // ===== Relations =====

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ===== Helpers =====

    // Taille lisible ex: 1.2 MB
    public function getReadableSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size  = $this->size;
        $i     = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
