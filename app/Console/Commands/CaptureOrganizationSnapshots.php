<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CaptureOrganizationSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stratos:capture-snapshots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Captures a monthly snapshot and calculates Stratos IQ for all active organizations';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Talent\StratosIqService $iqService)
    {
        $this->info('Starting snapshot capture for all organizations...');
        
        $organizations = \App\Models\Organizations::all();
        $count = 0;

        foreach ($organizations as $org) {
            try {
                $iqService->captureSnapshot($org, ['triggered_by' => 'cron']);
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to capture snapshot for Org ID {$org->id}: " . $e->getMessage());
            }
        }

        $this->info("Completed. Successfully captured {$count} snapshots.");
    }
}
