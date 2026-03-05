<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendMissYouNotificationsCommand extends Command
{
    protected $signature = 'notifications:miss-you';

    protected $description = 'Send Miss You notifications to customers inactive beyond the configured days (run daily via cron)';

    public function handle(NotificationService $notificationService): int
    {
        $count = $notificationService->sendMissYouNotifications();
        $this->info("Miss You notifications sent to {$count} customer(s).");
        return self::SUCCESS;
    }
}
