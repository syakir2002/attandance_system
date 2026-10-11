<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCloseAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto close open attendance sessions at the 19:00 cutoff and save a default 17:30 clock out.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoffTime = Carbon::createFromTimeString('24:00:00');
        $defaultCheckout = '17:30:00';
        $now = now();

        if ($now->lessThan($cutoffTime)) {
            $this->info('No attendance records were auto-closed because the cutoff time has not been reached yet.');

            return self::SUCCESS;
        }

        $records = Attendance::whereNull('check_out')
            ->where('date', $now->toDateString())
            ->get();

        $updated = 0;

        foreach ($records as $record) {
            if (empty($record->check_in)) {
                continue;
            }

            $record->update([
                'check_out' => $defaultCheckout,
                'period' => Attendance::calculatePeriod($record->check_in, $defaultCheckout),
                'keterangan_system' => 'Auto cut off oleh sistem pada 19:00. Check-out default diset ke 17:30.',
            ]);

            $updated++;
        }

        $this->info("Auto-closed {$updated} open attendance record(s). Default checkout saved as {$defaultCheckout}.");

        return self::SUCCESS;
    }
}
