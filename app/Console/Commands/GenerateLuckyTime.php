<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LuckyTimeSession;

class GenerateLuckyTime extends Command
{
    protected $signature = 'lucky-time:generate';
    protected $description = 'Generates the daily 15-minute 50% flash sale';

    public function handle()
    {
        // Generate random start time today between 00:00 and 23:44
        $start = now()->startOfDay()->addMinutes(rand(0, 1424));
        $end = $start->copy()->addMinutes(15);

        LuckyTimeSession::create([
            'start_time' => $start,
            'end_time' => $end,
            'discount_percentage' => 50.00,
            'status' => 'scheduled',
        ]);

        $this->info("Lucky time scheduled for: {$start->format('H:i')}");
    }
}