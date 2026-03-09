<?php

namespace App\Http\Controllers;

use App\Helper\Exception;
use App\Http\Requests\NotificationSettingRequest;
use App\Http\Requests\SendNotificationRequest;
use App\Models\NotificationSetting;
use App\Models\Offer;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    private $_directory = 'auth.pages.notifications';

    private const ALLOWED_BLADES = [
        'miss-you',
        'special-offer',
        'birthday',
    ];

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    
    public function index(string $blade): View|RedirectResponse
    {
        if (! in_array($blade, self::ALLOWED_BLADES, true)) {
            abort(404);
        }

        try {
            $view = $this->_directory . '.' . $blade;
            $type = $this->bladeToType($blade);
            $setting = $this->notificationService->getSetting($type);
            $data = ['blade' => $blade, 'setting' => $setting];

            if ($blade === 'miss-you') {
                $data['inactiveOptions'] = [7, 14, 21, 30, 60, 90];
            }
            if ($blade === 'special-offer') {
                $data['offers'] = Offer::select('id', 'title', 'site_id')->with('site')->orderBy('title')->get();
            }

            return view($view, $data);
        } catch (\Throwable $th) {
            return Exception::handle($th);
        }
    }

    protected function bladeToType(string $blade): string
    {
        return match ($blade) {
            'miss-you' => NotificationSetting::TYPE_MISS_YOU,
            'special-offer' => NotificationSetting::TYPE_SPECIAL_OFFER,
            'birthday' => NotificationSetting::TYPE_BIRTHDAY,
            default => $blade,
        };
    }

    protected function typeToBlade(string $type): string
    {
        return match ($type) {
            NotificationSetting::TYPE_MISS_YOU => 'miss-you',
            NotificationSetting::TYPE_SPECIAL_OFFER => 'special-offer',
            NotificationSetting::TYPE_BIRTHDAY => 'birthday',
            default => $type,
        };
    }


    public function updateSettings(Request $request, string $type): RedirectResponse
    {
        $type = $this->bladeToType(str_replace('_', '-', $type));
        $config = [];
        if ($request->has('inactive_days')) {
            $config['inactive_days'] = (int) $request->input('inactive_days');
        }
        if ($request->has('message_template')) {
            $config['message_template'] = $request->input('message_template');
        }
        if ($request->has('reward_points')) {
            $config['reward_points'] = (int) $request->input('reward_points');
        }
        if ($request->has('channels')) {
            $config['channels'] = (array) $request->input('channels');
        }
        $this->notificationService->updateSetting($type, $config);
        if ($request->has('is_active')) {
            $setting = $this->notificationService->getSetting($type);
            $setting->update(['is_active' => $request->boolean('is_active')]);
        }
        $blade = $this->typeToBlade($type);
        return redirect()->route('notifications.index', $blade)->with('success', 'Settings saved.');
    }

    public function sendMissYou(SendNotificationRequest $request): RedirectResponse|JsonResponse
    {
        $inactiveDays = $request->input('inactive_days');
        $userIds = (array) $request->input('user_ids', []);
        $userIds = array_filter(array_map('intval', $userIds));
        $channels = array_values(array_intersect((array) $request->input('channels', []), ['email', 'push']));
        $channelsOverride = $channels !== [] ? $channels : null;

        if ($request->boolean('send_to_all_inactive') || empty($userIds)) {
            $daysParam = $inactiveDays;
            if ($daysParam === 'all') {
                $userIds = $this->notificationService->getAllCustomerIds();
                $count = $this->notificationService->sendMissYouNotifications(null, $userIds, Auth::id(), $channelsOverride);
            } elseif ($daysParam !== null && $daysParam !== '') {
                $days = (int) $daysParam;
                $count = $this->notificationService->sendMissYouNotifications($days, null, Auth::id(), $channelsOverride);
            } else {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Please specify inactive days or select customers.'], 422);
                }
                return redirect()->back()->with('error', 'Please specify inactive days or select at least one customer.');
            }
        } else {
            $count = $this->notificationService->sendMissYouNotifications(null, $userIds, Auth::id(), $channelsOverride);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Miss You notification sent to {$count} customer(s).", 'count' => $count]);
        }
        return redirect()->back()->with('success', "Miss You notification sent to {$count} customer(s).");
    }

    public function sendSpecialOffer(SendNotificationRequest $request): RedirectResponse
    {
        $request->validate(['message' => 'required|string|max:2000', 'offer_id' => 'required|exists:offers,id']);
        $offerId = $request->input('offer_id');
        if (empty($offerId)) {
            return redirect()->back()->with('error', 'Please select an offer.');
        }
        $offer = Offer::find($offerId);
        if (!$offer) {
            return redirect()->back()->with('error', 'Offer not found.');
        }

        $sendToAll = $request->boolean('send_to_all');
        $userIds = (array) $request->input('user_ids', []);
        $userIds = is_array($userIds) ? array_filter($userIds) : [];
        if ($sendToAll) {
            $userIds = $this->notificationService->getAllCustomerIds();
        }
        if (empty($userIds)) {
            return redirect()->back()->with('error', 'Please select at least one customer or check "Send to all customers".');
        }

        $channels = (array) $request->input('channels', ['email', 'push']);
        $count = $this->notificationService->sendSpecialOfferNotifications(
            $offer,
            $request->input('message'),
            $userIds,
            $channels,
            Auth::id()
        );
        return redirect()->back()->with('success', "Special Offer notification sent to {$count} customer(s).");
    }

    /**
     * Send Birthday notification manually: by dynamic filter (today, 1–7 days before, or all customers) or to selected users.
     * Returns JSON when request expects JSON (e.g. AJAX from segment table).
     */
    public function sendBirthday(SendNotificationRequest $request): RedirectResponse|JsonResponse
    {
        $userIds = (array) $request->input('user_ids', []);
        $userIds = is_array($userIds) ? array_filter(array_map('intval', $userIds)) : [];
        $birthdayFilter = $request->input('birthday_filter', 'today');
        $allCustomers = $birthdayFilter === 'all';
        $channels = array_values(array_intersect((array) $request->input('channels', []), ['email', 'push']));
        $channelsOverride = $channels !== [] ? $channels : null;

        if (!empty($userIds)) {
            $count = $this->notificationService->sendBirthdayNotifications($userIds, Auth::id(), null, false, $channelsOverride);
        } elseif ($allCustomers) {
            $count = $this->notificationService->sendBirthdayNotifications(null, Auth::id(), null, true, $channelsOverride);
        } elseif ($birthdayFilter === 'today' || $birthdayFilter === '0') {
            $count = $this->notificationService->sendBirthdayNotifications(null, Auth::id(), 0, false, $channelsOverride);
        } elseif (is_numeric($birthdayFilter) && (int) $birthdayFilter >= 1 && (int) $birthdayFilter <= 365) {
            $count = $this->notificationService->sendBirthdayNotifications(null, Auth::id(), (int) $birthdayFilter, false, $channelsOverride);
        } else {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Please choose a valid filter or select at least one customer.'], 422);
            }
            return redirect()->back()->with('error', 'Please choose "Send to" (e.g. today, 1 day before, or all customers) or select at least one customer.');
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Birthday notification sent to {$count} customer(s).", 'count' => $count]);
        }
        return redirect()->back()->with('success', "Birthday notification sent to {$count} customer(s).");
    }

    /**
     * Get inactive customers count and preview for "send to all" display.
     * Returns: { count: int, preview: [{ id, text }] }
     * Query param days: 7, 14, 21, 30, 60, or "all" for all customers.
     */
    public function getInactiveCustomersPreview(Request $request)
    {
        $daysParam = $request->input('days', 7);
        if ($daysParam === 'all') {
            $users = $this->notificationService->getAllCustomers();
            $preview = $users->take(100)->map(fn ($u) => [
                'id' => $u->id,
                'text' => (trim($u->first_name . ' ' . $u->last_name) ?: $u->email) . ' (' . $u->email . ')',
            ])->values()->all();
            return response()->json(['count' => $users->count(), 'preview' => $preview]);
        }
        $days = (int) $daysParam;
        $data = $this->notificationService->getInactiveCustomersPreview($days, 100);
        return response()->json($data);
    }

    /**
     * Get counts per inactive segment for the segment table (7, 14, 21, 30, 60 days, all).
     */
    public function getInactiveCounts(Request $request)
    {
        $data = $this->notificationService->getInactiveCounts();
        return response()->json($data);
    }

    /**
     * Get counts per segment for the birthday segment table.
     * Returns: { today: int, 1: int, ..., 7: int, all: int }
     */
    public function getBirthdayCounts(Request $request)
    {
        $data = $this->notificationService->getBirthdayCounts();
        return response()->json($data);
    }

    /**
     * Get birthday filter preview: list of users matching the selected filter (today, 1–7 days before, or all).
     * Returns: { count: int, preview: [{ id, text, email, date_of_birth }] }
     */
    public function getBirthdayPreview(Request $request)
    {
        $filter = $request->input('filter', 'today');
        if (! in_array($filter, ['today', '0', 'all'], true) && (! is_numeric($filter) || (int) $filter < 1 || (int) $filter > 365)) {
            $filter = 'today';
        }
        $data = $this->notificationService->getBirthdayPreview($filter, 100);
        return response()->json($data);
    }

    /**
     * Get inactive customers for Select2 AJAX (paginated; does not load all).
     * Returns: { results: [{ id, text }], more: boolean }
     */
    public function getInactiveCustomers(Request $request)
    {
        $days = (int) $request->input('days', 7);
        $page = (int) $request->input('page', 1);
        $search = $request->input('search');
        $paginator = $this->notificationService->getInactiveCustomersPaginated($days, $page, 20, $search);
        $results = $paginator->getCollection()->map(fn ($u) => [
            'id' => $u->id,
            'text' => (trim($u->first_name . ' ' . $u->last_name) ?: $u->email) . ' (' . $u->email . ')',
        ])->values()->all();
        return response()->json([
            'results' => $results,
            'more' => $paginator->hasMorePages(),
        ]);
    }

    /**
     * Get all customers for Select2 AJAX (paginated; does not load all).
     * Returns: { results: [{ id, text }], more: boolean }
     */
    public function getCustomers(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $search = $request->input('search');
        $paginator = $this->notificationService->getAllCustomersPaginated($page, 20, $search);
        $results = $paginator->getCollection()->map(fn ($u) => [
            'id' => $u->id,
            'text' => (trim($u->first_name . ' ' . $u->last_name) ?: $u->email) . ' (' . $u->email . ')',
        ])->values()->all();
        return response()->json([
            'results' => $results,
            'more' => $paginator->hasMorePages(),
        ]);
    }
}
