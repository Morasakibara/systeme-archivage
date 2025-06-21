<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
        protected $fillable = [
        'user_id',
        'titre',
        'message',
        'type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead(Builder $query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType(Builder $query, $type)
    {
        return $query->where('type', $type);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function getIconAttribute()
    {
        $icons = [
            'INFO' => 'ℹ️',
            'SUCCESS' => '✅',
            'WARNING' => '⚠️',
            'ERROR' => '❌',
        ];

        return $icons[$this->type] ?? 'ℹ️';
    }

}
