<?php

namespace App\Listeners;

use App\Events\OfferCreated;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class SendOfferNotificationListener
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

  
    public function handle(OfferCreated $event): void
    {
        try {
            $customers = $this->notificationService->getAllCustomers();
            $userIds = $customers->pluck('id')->all();
            if (empty($userIds)) {
                return;
            }
            $setting = $this->notificationService->getSetting(\App\Models\NotificationSetting::TYPE_SPECIAL_OFFER);
            $channels = $setting->getConfigValue('channels', ['email', 'push']);
            $count = $this->notificationService->sendSpecialOfferNotifications(
                $event->offer,
                $event->notificationMessage,
                $userIds,
                $channels
            );
            Log::info('Offer notification sent after creation', ['offer_id' => $event->offer->id, 'count' => $count]);
        } catch (\Throwable $e) {
            Log::error('SendOfferNotificationListener failed: ' . $e->getMessage(), ['offer_id' => $event->offer->id]);
        }
    }
}
