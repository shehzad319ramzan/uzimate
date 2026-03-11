<?php

namespace App\Http\Controllers\Api;

use App\Models\NotificationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationApiController extends ApiBaseController
{
    /**
     * Get current user's notification preferences (enable/disable push and email).
     */
    public function settings(): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => [
                'application_notifications' => (bool) ($user->push_notifications_enabled ?? true),
                'email_notifications' => (bool) ($user->email_notifications_enabled ?? true),
            ],
        ], 200);
    }

    /**
     * Update notification preferences. Send application_notifications and/or email_notifications (boolean).
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'application_notifications' => ['nullable', 'boolean'],
            'email_notifications' => ['nullable', 'boolean'],
        ]);

        $user = Auth::user();
        $updates = [];
        if ($request->has('application_notifications')) {
            $updates['push_notifications_enabled'] = $request->boolean('application_notifications');
        }
        if ($request->has('email_notifications')) {
            $updates['email_notifications_enabled'] = $request->boolean('email_notifications');
        }
        if ($updates !== []) {
            $user->update($updates);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated.',
            'data' => [
                'application_notifications' => (bool) ($user->fresh()->push_notifications_enabled ?? true),
                'email_notifications' => (bool) ($user->fresh()->email_notifications_enabled ?? true),
            ],
        ], 200);
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = min((int) $request->get('per_page', 20), 50);
        $channel = $request->query('channel', 'push');

        $query = NotificationLog::where('user_id', $user->id)
            ->orderByRaw('COALESCE(sent_at, created_at) DESC')
            ->orderByDesc('created_at');

        if (in_array($channel, ['push', 'email'], true)) {
            $query->where('channel', $channel);
        }

        $logs = $query->paginate($perPage);

        $items = $logs->getCollection()->map(function (NotificationLog $log) {
            return $this->formatNotificationItem($log);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $items,
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ], 200);
    }


    public function show(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();

        $log = NotificationLog::where('user_id', $user->id)->find($id);
        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        $markRead = $request->boolean('mark_read', true);
        if ($markRead && !$log->opened_at) {
            $log->update(['opened_at' => now()]);
            $log->refresh();
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatNotificationItem($log),
        ], 200);
    }


    public function markAsRead(int $id): JsonResponse
    {
        $user = Auth::user();

        $log = NotificationLog::where('user_id', $user->id)->find($id);
        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        if (!$log->opened_at) {
            $log->update(['opened_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Marked as read',
            'data' => $this->formatNotificationItem($log->fresh()),
        ], 200);
    }


    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();

        NotificationLog::where('user_id', $user->id)
            ->whereNull('opened_at')
            ->update(['opened_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ], 200);
    }

    protected function formatNotificationItem(NotificationLog $log): array
    {
        return [
            'id' => $log->id,
            'title' => $log->title ?? 'Notification',
            'message' => $log->body,
            'type' => $log->type,
            'type_label' => $this->typeLabel($log->type),
            'channel' => $log->channel,
            'sent_at' => $log->sent_at?->toIso8601String(),
            'sent_at_formatted' => $log->sent_at?->format('h:i A'),
            'is_read' => $log->opened_at !== null,
            'opened_at' => $log->opened_at?->toIso8601String(),
            'related_model_type' => $log->related_model_type,
            'related_model_id' => $log->related_model_id,
            'metadata' => $log->metadata,
        ];
    }

    protected function typeLabel(string $type): string
    {
        return match ($type) {
            'miss_you' => 'We miss you',
            'special_day' => 'Special day',
            'special_offer' => 'Special offer',
            'birthday' => 'Birthday',
            default => str_replace('_', ' ', ucfirst($type)),
        };
    }
}
