<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'callendar_info';

    /**
     * Disable default created_at/updated_at timestamps since table uses a single 'timestamps' column.
     *
     * @var bool
     */
    public $timestamps = false;

    const CREATED_AT = null;

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'timestamps',
        'status',
        'display_style',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'timestamps' => 'datetime',
        'status' => 'integer',
    ];

    /**
     * Scope for public events.
     */
    public function scopePublic($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for private events.
     */
    public function scopePrivate($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Determine if event is public.
     */
    public function isPublic(): bool
    {
        return (int) $this->status === 1;
    }

    /**
     * Determine if event is private.
     */
    public function isPrivate(): bool
    {
        return (int) $this->status === 0;
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function booted(): void
    {
        static::creating(function ($event) {
            if (empty($event->timestamps)) {
                $event->timestamps = now();
            }
            if ($event->status === null) {
                $event->status = 0; // Default private
            }
        });
    }
}
