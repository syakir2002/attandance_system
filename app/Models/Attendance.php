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
        'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'keterangan_staff',
    ];

    /**
     * Mutator to map legacy keterangan_staff to keterangan_in.
     */
    public function setKeteranganStaffAttribute(?string $value): void
    {
        $this->attributes['keterangan_in'] = $value;
    }

    /**
     * Accessor for keterangan_staff.
     * Merges keterangan_in and keterangan_out with '|' when displaying under a single staff note column.
     */
    public function getKeteranganStaffAttribute(): ?string
    {
        $in = trim((string) ($this->attributes['keterangan_in'] ?? ''));
        $out = trim((string) ($this->attributes['keterangan_out'] ?? ''));

        if ($in !== '' && $out !== '') {
            return "{$in} | {$out}";
        }

        if ($in !== '') {
            return $in;
        }

        if ($out !== '') {
            return $out;
        }

        $legacy = trim((string) ($this->attributes['keterangan_staff'] ?? ''));

        return $legacy !== '' ? $legacy : null;
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
     * Check if the attendance session is currently active for today.
     */
    public function isActive(): bool
    {
        return is_null($this->check_out) && Carbon::parse($this->date)->isToday();
    }

    /**
     * Check if the attendance session is incomplete (past date with no check_out).
     */
    public function isIncomplete(): bool
    {
        if ($this->status === 'incomplete') {
            return true;
        }

        return is_null($this->check_out) && Carbon::parse($this->date)->isBefore(now()->startOfDay());
    }

    /**
     * Synchronize open sessions from previous days to incomplete status without auto-cutting check_out.
     */
    public static function syncIncompleteSessions(?string $staffId = null): int
    {
        $query = static::where('date', '<', now()->toDateString())
            ->whereNull('check_out')
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'incomplete');
            });

        if ($staffId !== null) {
            $query->where('staff_id', $staffId);
        }

        return $query->update([
            'status' => 'incomplete',
            'period' => null,
        ]);
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
