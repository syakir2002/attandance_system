<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clock_in_out';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'staff_id',
        'check_in',
        'check_out',
        'date',
        'keterangan_in',
        'keterangan_out',
        'keterangan_staff',
        'keterangan_admin',
        'keterangan_system',
        'period',
    ];

    /**
     * Mutator to map legacy keterangan_staff to keterangan_in.
     */
    public function setKeteranganStaffAttribute(?string $value): void
    {
        $this->attributes['keterangan_in'] = $value;
    }

    /**
     * Accessor for keterangan_staff fallback to keterangan_in.
     */
    public function getKeteranganStaffAttribute(): ?string
    {
        return $this->attributes['keterangan_in'] ?? ($this->attributes['keterangan_staff'] ?? null);
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
        ];
    }

    /**
     * Get the user that owns the attendance record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id', 'staff_id');
    }

    /**
     * Check if the attendance session is currently active (clocked in without clock out).
     */
    public function isActive(): bool
    {
        return is_null($this->check_out);
    }

    /**
     * Generate custom string ID: {staff_id}-{YYYYMMDD}-{session_number}.
     */
    public static function generateCustomId(string $staffId, ?string $date = null): string
    {
        $targetDate = $date ?? now()->format('Y-m-d');
        $datePart = Carbon::parse($targetDate)->format('Ymd');

        $sessionCount = static::where('staff_id', $staffId)
            ->where('date', $targetDate)
            ->count();

        $nextSession = str_pad((string) ($sessionCount + 1), 2, '0', STR_PAD_LEFT);

        return "{$staffId}-{$datePart}-{$nextSession}";
    }

    /**
     * Calculate period between check_in and check_out in H:i:s format.
     */
    public static function calculatePeriod(string $checkIn, ?string $checkOut): ?string
    {
        if (empty($checkOut)) {
            return null;
        }

        try {
            $start = Carbon::createFromTimeString($checkIn);
            $end = Carbon::createFromTimeString($checkOut);

            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $totalSeconds = $start->diffInSeconds($end);
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;

            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } catch (\Exception) {
            return null;
        }
    }
}
