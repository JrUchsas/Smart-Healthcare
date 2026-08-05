<?php

namespace App\Console\Commands;

use App\Services\DoctorScheduleService;
use Illuminate\Console\Command;

class PurgeExpiredSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedules:purge-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically purge outdated doctor schedules, refund payments for missed slots, and notify patients.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting automated purge of expired doctor schedules...');

        $count = DoctorScheduleService::purgeOutdatedSchedules();

        $this->info("Successfully purged {$count} expired schedule block(s), refunded affected patient payments, and sent notifications.");

        return Command::SUCCESS;
    }
}
