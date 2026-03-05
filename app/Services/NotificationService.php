<?php

namespace App\Services;

use App\Constants\Constants;
use App\Mail\NotificationMail;
use App\Models\CustomerLog;
use App\Models\NotificationCampaign;
use App\Models\NotificationLog;
use App\Models\NotificationSetting;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        protected FcmService $fcmService
    ) {}

 
    public function getSetting(string $type): NotificationSetting
    {
        $setting = NotificationSetting::firstOrCreate(
            ['type' => $type],
            ['config' => $this->defaultConfig($type), 'is_active' => true]
        );
        if (empty($setting->config)) {
            $setting->update(['config' => $this->defaultConfig($type)]);
            $setting->refresh();
        }
        return $setting;
    }

    protected function defaultConfig(string $type): array
    {
        return match ($type) {
            NotificationSetting::TYPE_MISS_YOU => [
                'inactive_days' => 7,
                'message_template' => 'We miss you, {{name}}! Come back and check our latest updates.',
                'channels' => ['email', 'push'],
            ],
            NotificationSetting::TYPE_SPECIAL_DAY => [
                'message_template' => '',
                'channels' => ['email', 'push'],
            ],
            NotificationSetting::TYPE_SPECIAL_OFFER => [
                'message_template' => 'New offer: {{offer_title}}! Get great rewards.',
                'channels' => ['email', 'push'],
            ],
            NotificationSetting::TYPE_BIRTHDAY => [
                'message_template' => 'Happy Birthday, {{name}}! You\'ve earned {{points}} points.',
                'reward_points' => 5,
                'channels' => ['email', 'push'],
            ],
            default => ['channels' => ['email', 'push']],
        };
    }

    public function getInactiveCustomers(int $inactiveDays): \Illuminate\Database\Eloquent\Collection
    {
        $cutoff = Carbon::now()->subDays($inactiveDays);
        $lastLoginUserIds = CustomerLog::where('action_type', 'login')
            ->selectRaw('user_id, MAX(created_at) as last_login_at')
            ->groupBy('user_id')
            ->havingRaw('MAX(created_at) < ?', [$cutoff])
            ->pluck('user_id');

        $neverLoggedUserIds = User::role(Constants::CUSTOMER)
            ->whereDoesntHave('customerLogs', fn ($q) => $q->where('action_type', 'login'))
            ->pluck('id');

        $userIds = $lastLoginUserIds->merge($neverLoggedUserIds)->unique()->filter();

        return User::role(Constants::CUSTOMER)
            ->whereIn('id', $userIds)
            ->get();
    }


    public function getBirthdayCustomers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::role(Constants::CUSTOMER)
            ->whereNotNull('date_of_birth')
            ->whereRaw('MONTH(date_of_birth) = ?', [Carbon::now()->month])
            ->whereRaw('DAY(date_of_birth) = ?', [Carbon::now()->day])
            ->get();
    }


    public function getAllCustomers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::role(Constants::CUSTOMER)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }


    public function getAllCustomerIds(): array
    {
        return User::role(Constants::CUSTOMER)->pluck('id')->toArray();
    }


    public function getAllCustomersPaginated(int $page = 1, int $perPage = 20, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $perPage = min(max((int) $perPage, 1), 50);
        $query = User::role(Constants::CUSTOMER)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->orderBy('email');
        if ($search && strlen(trim($search)) > 0) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }
        return $query->paginate($perPage, ['id', 'first_name', 'last_name', 'email'], 'page', $page);
    }


    public function getInactiveCustomersPaginated(int $inactiveDays, int $page = 1, int $perPage = 20, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $perPage = min(max((int) $perPage, 1), 50);
        $cutoff = Carbon::now()->subDays($inactiveDays);
        $query = User::role(Constants::CUSTOMER)
            ->where(function ($q) use ($cutoff) {
                $q->whereDoesntHave('customerLogs', fn ($q2) => $q2->where('action_type', 'login'))
                    ->orWhereIn('id', function ($sub) use ($cutoff) {
                        $sub->select('user_id')
                            ->from('customer_logs')
                            ->where('action_type', 'login')
                            ->groupBy('user_id')
                            ->havingRaw('MAX(created_at) < ?', [$cutoff]);
                    });
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->orderBy('email');
        if ($search && strlen(trim($search)) > 0) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }
        return $query->paginate($perPage, ['id', 'first_name', 'last_name', 'email'], 'page', $page);
    }

    public function getInactiveCustomersPreview(int $inactiveDays, int $previewLimit = 50): array
    {
        $paginator = $this->getInactiveCustomersPaginated($inactiveDays, 1, $previewLimit);
        $preview = $paginator->getCollection()->map(fn ($u) => [
            'id' => $u->id,
            'text' => (trim($u->first_name . ' ' . $u->last_name) ?: $u->email) . ' (' . $u->email . ')',
        ])->values()->all();

        return [
            'count' => $paginator->total(),
            'preview' => $preview,
        ];
    }

    public function replacePlaceholders(string $template, User $user, ?Offer $offer = null, ?int $points = null): string
    {
        $name = trim($user->first_name . ' ' . $user->last_name) ?: $user->email;
        $replace = [
            '{{name}}' => $name,
            '{{first_name}}' => $user->first_name ?? '',
            '{{email}}' => $user->email,
        ];
        if ($offer) {
            $replace['{{offer_title}}'] = $offer->title;
        }
        if ($points !== null) {
            $replace['{{points}}'] = (string) $points;
        }
        return str_replace(array_keys($replace), array_values($replace), $template);
    }


    public function sendToUser(
        User $user,
        string $type,
        string $title,
        string $body,
        array $channels = ['email', 'push'],
        ?string $relatedModelType = null,
        ?string $relatedModelId = null,
        array $pushData = []
    ): array {
        $channels = array_intersect($channels, ['email', 'push']);
        $results = [];
        $context = ['notification_type' => $type, 'user_id' => $user->id, 'user_email' => $user->email];

        foreach ($channels as $channel) {
            if ($channel === 'email') {
                try {
                    $recipientName = trim($user->first_name . ' ' . $user->last_name) ?: null;
                    Mail::to($user->email)->send(new NotificationMail($title, $body, $type, $recipientName));
                    $results['email'] = true;
                    Log::info('Notification email sent', array_merge($context, ['channel' => 'email']));
                    NotificationLog::create([
                        'user_id' => $user->id,
                        'type' => $type,
                        'channel' => 'email',
                        'title' => $title,
                        'body' => $body,
                        'related_model_type' => $relatedModelType,
                        'related_model_id' => $relatedModelId,
                        'sent_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    $results['email'] = false;
                    Log::error('Notification email failed', array_merge($context, [
                        'channel' => 'email',
                        'reason' => $e->getMessage(),
                        'exception' => get_class($e),
                    ]));
                }
            }
            if ($channel === 'push') {
                $pushLog = function ($sent) use ($user, $type, $title, $body, $relatedModelType, $relatedModelId) {
                    NotificationLog::create([
                        'user_id' => $user->id,
                        'type' => $type,
                        'channel' => 'push',
                        'title' => $title,
                        'body' => $body,
                        'related_model_type' => $relatedModelType,
                        'related_model_id' => $relatedModelId,
                        'sent_at' => $sent ? now() : null,
                    ]);
                };
                if (empty($user->fcm_token)) {
                    $results['push'] = false;
                    Log::warning('Notification push skipped: no FCM token', array_merge($context, ['channel' => 'push', 'reason' => 'User has no FCM token']));
                    $pushLog(false);
                } else {
                    $sent = $this->fcmService->sendToToken($user->fcm_token, $title, $body, $pushData);
                    $results['push'] = $sent;
                    if ($sent) {
                        Log::info('Notification push sent', array_merge($context, ['channel' => 'push']));
                        $pushLog(true);
                    } else {
                        $fcmError = $this->fcmService->getLastError();
                        Log::error('Notification push failed', array_merge($context, [
                            'channel' => 'push',
                            'reason' => $fcmError ?? 'FCM returned false (no error message captured).',
                        ]));
                        $pushLog(false);
                    }
                }
            }
        }

        return $results;
    }


    public function sendMissYouNotifications(?int $inactiveDays = null, ?array $userIds = null, ?int $createdBy = null): int
    {
        $type = NotificationSetting::TYPE_MISS_YOU;
        $setting = $this->getSetting($type);
        if (!$setting->is_active) {
            Log::info('Notification batch skipped: setting inactive', ['notification_type' => $type]);
            return 0;
        }
        $days = $inactiveDays ?? (int) $setting->getConfigValue('inactive_days', 7);
        $template = $setting->getConfigValue('message_template', 'We miss you, {{name}}!');
        $channels = $setting->getConfigValue('channels', ['email', 'push']);
        $title = 'We miss you!';

        $users = $userIds
            ? User::whereIn('id', $userIds)->get()
            : $this->getInactiveCustomers($days);
        $users = $users->filter(fn ($u) => $u->hasRole(Constants::CUSTOMER));
        $total = $users->count();
        if ($total === 0) {
            Log::info('Notification batch completed: no customers to send', ['notification_type' => $type]);
            return 0;
        }

        Log::info('Notification batch started', [
            'notification_type' => $type,
            'user_count' => $total,
            'channels' => $channels,
            'inactive_days' => $days,
        ]);

        $stats = ['users_processed' => 0, 'email_sent' => 0, 'email_failed' => 0, 'push_sent' => 0, 'push_failed' => 0];
        foreach ($users as $user) {
            $body = $this->replacePlaceholders($template, $user);
            $results = $this->sendToUser($user, $type, $title, $body, $channels);
            $stats['users_processed']++;
            foreach ($results as $ch => $ok) {
                if ($ch === 'email') {
                    $ok ? $stats['email_sent']++ : $stats['email_failed']++;
                } else {
                    $ok ? $stats['push_sent']++ : $stats['push_failed']++;
                }
            }
        }

        $this->createCampaignRecord([
            'type' => $type,
            'name' => 'Miss You – ' . now()->format('M j, Y H:i'),
            'message' => $template,
            'channels' => $channels,
            'target_type' => $userIds ? 'selected' : 'all_inactive',
            'target_config' => $userIds ? ['user_ids' => $userIds] : ['inactive_days' => $days],
            'created_by' => $createdBy,
        ]);

        Log::info('Notification batch completed', array_merge(['notification_type' => $type], $stats));
        return $stats['users_processed'];
    }

    public function sendSpecialDayNotifications(string $message, array $channels, array $userIds, string $title = 'Special Day', ?int $createdBy = null): int
    {
        $type = NotificationSetting::TYPE_SPECIAL_DAY;
        $users = User::whereIn('id', $userIds)->get();
        $total = $users->count();
        if ($total === 0) {
            Log::info('Notification batch completed: no users to send', ['notification_type' => $type]);
            return 0;
        }

        Log::info('Notification batch started', [
            'notification_type' => $type,
            'user_count' => $total,
            'channels' => $channels,
        ]);

        $stats = ['users_processed' => 0, 'email_sent' => 0, 'email_failed' => 0, 'push_sent' => 0, 'push_failed' => 0];
        foreach ($users as $user) {
            $body = $this->replacePlaceholders($message, $user);
            $results = $this->sendToUser($user, $type, $title, $body, $channels);
            $stats['users_processed']++;
            foreach ($results as $ch => $ok) {
                if ($ch === 'email') {
                    $ok ? $stats['email_sent']++ : $stats['email_failed']++;
                } else {
                    $ok ? $stats['push_sent']++ : $stats['push_failed']++;
                }
            }
        }

        $this->createCampaignRecord([
            'type' => $type,
            'name' => $title . ' – ' . now()->format('M j, Y H:i'),
            'message' => $message,
            'channels' => $channels,
            'target_type' => 'selected',
            'target_config' => ['user_ids' => $userIds],
            'created_by' => $createdBy,
        ]);

        Log::info('Notification batch completed', array_merge(['notification_type' => $type], $stats));
        return $stats['users_processed'];
    }


    public function sendSpecialOfferNotifications(Offer $offer, ?string $customMessage, array $userIds, array $channels = ['email', 'push'], ?int $createdBy = null): int
    {
        $type = NotificationSetting::TYPE_SPECIAL_OFFER;
        $setting = $this->getSetting($type);
        $template = $customMessage ?: $setting->getConfigValue('message_template', 'New offer: {{offer_title}}!');
        $title = 'New Offer: ' . $offer->title;
        $users = User::whereIn('id', $userIds)->get();
        $total = $users->count();
        if ($total === 0) {
            Log::info('Notification batch completed: no users to send', ['notification_type' => $type, 'offer_id' => $offer->id]);
            return 0;
        }

        Log::info('Notification batch started', [
            'notification_type' => $type,
            'offer_id' => $offer->id,
            'user_count' => $total,
            'channels' => $channels,
        ]);

        $stats = ['users_processed' => 0, 'email_sent' => 0, 'email_failed' => 0, 'push_sent' => 0, 'push_failed' => 0];
        foreach ($users as $user) {
            $body = $this->replacePlaceholders($template, $user, $offer);
            $results = $this->sendToUser(
                $user,
                $type,
                $title,
                $body,
                $channels,
                Offer::class,
                (string) $offer->id,
                ['offer_id' => $offer->id]
            );
            $stats['users_processed']++;
            foreach ($results as $ch => $ok) {
                if ($ch === 'email') {
                    $ok ? $stats['email_sent']++ : $stats['email_failed']++;
                } else {
                    $ok ? $stats['push_sent']++ : $stats['push_failed']++;
                }
            }
        }

        $this->createCampaignRecord([
            'type' => $type,
            'name' => 'Special Offer: ' . $offer->title . ' – ' . now()->format('M j, Y H:i'),
            'message' => $template,
            'channels' => $channels,
            'target_type' => count($userIds) > 0 ? 'selected' : 'all',
            'target_config' => ['user_ids' => $userIds, 'offer_id' => $offer->id],
            'created_by' => $createdBy,
        ]);

        Log::info('Notification batch completed', array_merge(['notification_type' => $type, 'offer_id' => $offer->id], $stats));
        return $stats['users_processed'];
    }


    public function sendBirthdayNotifications(?array $userIds = null, ?int $createdBy = null): int
    {
        $type = NotificationSetting::TYPE_BIRTHDAY;
        $setting = $this->getSetting($type);
        if (!$setting->is_active) {
            Log::info('Notification batch skipped: setting inactive', ['notification_type' => $type]);
            return 0;
        }
        $template = $setting->getConfigValue('message_template', 'Happy Birthday, {{name}}! You\'ve earned {{points}} points.');
        $points = (int) $setting->getConfigValue('reward_points', 5);
        $channels = $setting->getConfigValue('channels', ['email', 'push']);
        $title = 'Happy Birthday!';

        $users = $userIds
            ? User::whereIn('id', $userIds)->get()
            : $this->getBirthdayCustomers();
        $users = $users->filter(fn ($u) => $u->hasRole(Constants::CUSTOMER));
        $total = $users->count();
        if ($total === 0) {
            Log::info('Notification batch completed: no customers to send', ['notification_type' => $type]);
            return 0;
        }

        Log::info('Notification batch started', [
            'notification_type' => $type,
            'user_count' => $total,
            'channels' => $channels,
        ]);

        $stats = ['users_processed' => 0, 'email_sent' => 0, 'email_failed' => 0, 'push_sent' => 0, 'push_failed' => 0];
        foreach ($users as $user) {
            $body = $this->replacePlaceholders($template, $user, null, $points);
            $results = $this->sendToUser($user, $type, $title, $body, $channels);
            $stats['users_processed']++;
            foreach ($results as $ch => $ok) {
                if ($ch === 'email') {
                    $ok ? $stats['email_sent']++ : $stats['email_failed']++;
                } else {
                    $ok ? $stats['push_sent']++ : $stats['push_failed']++;
                }
            }
            if ($points > 0) {
                $balanceBefore = (int) CustomerLog::where('user_id', $user->id)->whereNotNull('points_affected')->sum('points_affected');
                CustomerLog::create([
                    'user_id' => $user->id,
                    'action_type' => 'birthday',
                    'action_category' => 'system',
                    'description' => "Birthday reward: {$points} points",
                    'points_affected' => $points,
                    'points_balance_before' => $balanceBefore,
                    'points_balance_after' => $balanceBefore + $points,
                ]);
            }
        }

        $this->createCampaignRecord([
            'type' => $type,
            'name' => 'Birthday – ' . now()->format('M j, Y H:i'),
            'message' => $template,
            'channels' => $channels,
            'target_type' => $userIds ? 'selected' : 'all_today',
            'target_config' => $userIds ? ['user_ids' => $userIds] : [],
            'created_by' => $createdBy,
        ]);

        Log::info('Notification batch completed', array_merge(['notification_type' => $type], $stats));
        return $stats['users_processed'];
    }

    /**
     * Create a notification_campaigns record when a batch send completes.
     */
    protected function createCampaignRecord(array $data): NotificationCampaign
    {
        return NotificationCampaign::create([
            'type' => $data['type'],
            'name' => $data['name'],
            'message' => $data['message'],
            'channels' => $data['channels'],
            'target_type' => $data['target_type'],
            'target_config' => $data['target_config'] ?? null,
            'scheduled_at' => null,
            'sent_at' => now(),
            'status' => NotificationCampaign::STATUS_SENT,
            'created_by' => $data['created_by'] ?? null,
        ]);
    }

    public function updateSetting(string $type, array $config): NotificationSetting
    {
        $setting = $this->getSetting($type);
        $merged = array_merge($setting->config ?? [], $config);
        $setting->update(['config' => $merged]);
        return $setting->fresh();
    }
}
