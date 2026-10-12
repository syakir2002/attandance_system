<?php

namespace App\Console\Commands;

use App\Models\Attendance;
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
    protected $description = 'Mark past unclosed attendance sessions as incomplete without cutting clock out.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $updated = Attendance::syncIncompleteSessions();

        $this->info("Updated {$updated} past attendance record(s) to 'incomplete' status. Clock out left as NULL.");

        return self::SUCCESS;
    }
}
