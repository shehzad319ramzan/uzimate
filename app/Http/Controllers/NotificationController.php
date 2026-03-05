<?php

namespace App\Http\Controllers;

use App\Helper\Exception;
use App\Http\Requests\NotificationSettingRequest;
use App\Http\Requests\SendNotificationRequest;
use App\Models\NotificationSetting;
use App\Models\Offer;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    private $_directory = 'auth.pages.notifications';

    private const ALLOWED_BLADES = [
        'miss-you',
        'special-day',
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
            'special-day' => NotificationSetting::TYPE_SPECIAL_DAY,
            'special-offer' => NotificationSetting::TYPE_SPECIAL_OFFER,
            'birthday' => NotificationSetting::TYPE_BIRTHDAY,
            default => $blade,
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
        return redirect()->back()->with('success', 'Settings saved.');
    }

    public function sendMissYou(SendNotificationRequest $request): RedirectResponse
    {
        $inactiveDays = $request->input('inactive_days');
        $userIds = $request->input('user_ids', []);
        $userIds = is_array($userIds) ? array_filter($userIds) : [];
        if ($request->boolean('send_to_all_inactive') || empty($userIds)) {
            $count = $this->notificationService->sendMissYouNotifications($inactiveDays ? (int) $inactiveDays : null, null, Auth::id());
        } else {
            $count = $this->notificationService->sendMissYouNotifications(null, $userIds, Auth::id());
        }
        return redirect()->back()->with('success', "Miss You notification sent to {$count} customer(s).");
    }

    public function sendSpecialDay(SendNotificationRequest $request): RedirectResponse
    {
        $request->validate(['message' => 'required|string|max:2000']);
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
        $title = $request->input('title', 'Special Day');
        $count = $this->notificationService->sendSpecialDayNotifications(
            $request->input('message'),
            $channels,
            $userIds,
            $title,
            Auth::id()
        );
        return redirect()->back()->with('success', "Special Day notification sent to {$count} customer(s).");
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
     * Send Birthday notification manually: to all with birthday today or to selected users.
     */
    public function sendBirthday(SendNotificationRequest $request): RedirectResponse
    {
        $sendToAllToday = $request->boolean('send_to_all_today');
        $userIds = (array) $request->input('user_ids', []);
        $userIds = is_array($userIds) ? array_filter($userIds) : [];

        if ($sendToAllToday) {
            $count = $this->notificationService->sendBirthdayNotifications(null, Auth::id());
        } elseif (!empty($userIds)) {
            $count = $this->notificationService->sendBirthdayNotifications($userIds, Auth::id());
        } else {
            return redirect()->back()->with('error', 'Please check "Send to all with birthday today" or select at least one customer.');
        }

        return redirect()->back()->with('success', "Birthday notification sent to {$count} customer(s).");
    }

    /**
     * Get inactive customers count and preview for "send to all" display.
     * Returns: { count: int, preview: [{ id, text }] }
     */
    public function getInactiveCustomersPreview(Request $request)
    {
        $days = (int) $request->input('days', 7);
        $data = $this->notificationService->getInactiveCustomersPreview($days, 50);
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
