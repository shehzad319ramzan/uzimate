<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendBirthdayNotificationsCommand extends Command
{
    protected $signature = 'notifications:birthday';

    protected $description = 'Send Birthday notifications and award points to customers whose birthday is today (run daily via cron)';

    public function handle(NotificationService $notificationService): int
    {
        $count = $notificationService->sendBirthdayNotifications();
        $this->info("Birthday notifications sent to {$count} customer(s).");
        return self::SUCCESS;
    }
}
