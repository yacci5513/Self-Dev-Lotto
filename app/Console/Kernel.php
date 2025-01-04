<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // 매일 오후 9시에 실행 (로또 추첨 시간 이후)
        $schedule->command('lotto:fetch-latest')
                ->dailyAt('21:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/lotto-fetch.log'));
    }
} 