<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProfile;
use Carbon\Carbon;

class UserProfileController extends Controller
{
    public function index(Request $request)
    {
        $currentDate = Carbon::now()->format('m/d/Y');
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $currentDayName = Carbon::now()->format('l');

        $yesterday = Carbon::yesterday()->format('m/d/Y');
        $tomorrow = Carbon::tomorrow()->format('m/d/Y');
        $nextWeek = Carbon::now()->addWeek()->format('m/d/Y');
        $lastMonth = Carbon::now()->subMonth()->format('F Y');

        $newYorkTime = Carbon::now('America/New_York')->format('Y-m-d H:i:s');
        $londonTime = Carbon::now('Europe/London')->format('Y-m-d H:i:s');

        $age = Carbon::parse('1990-05-15')->age;

        $customDate = Carbon::create(2024, 12, 25, 15, 30, 0);

        $startDate = Carbon::parse('2024-01-01');
        $endDate = Carbon::now();

        $daysDifference = $startDate->diffInDays($endDate);
        $monthsDifference = $startDate->diffInMonths($endDate);

        $isPast = Carbon::parse('2023-01-01')->isPast();
        $isFuture = Carbon::parse('2027-01-01')->isFuture();
        $isWeekend = Carbon::now()->isWeekend();

        $query = UserProfile::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter
        if ($request->status == 'active') {
            $query->where('subscription_expiry', '>', now());
        }

        if ($request->status == 'expired') {
            $query->where('subscription_expiry', '<', now());
        }

        // Pagination
        $profiles = $query->oldest()->paginate(3);

        // Statistics
        $totalProfiles = UserProfile::count();

        $activeSubscriptions = UserProfile::where(
            'subscription_expiry',
            '>',
            now()
        )->count();

        $expiredSubscriptions = UserProfile::where(
            'subscription_expiry',
            '<',
            now()
        )->count();

        // ===============================
        // Upcoming Birthday Reminders
        // ===============================

        $upcomingBirthdays = UserProfile::all()
            ->map(function ($profile) {

                $birthDate = Carbon::parse($profile->birth_date);

                $nextBirthday = Carbon::create(
                    now()->year,
                    $birthDate->month,
                    $birthDate->day
                );

                if ($nextBirthday->lt(now()->startOfDay())) {
                    $nextBirthday->addYear();
                }

                $profile->nextBirthday = $nextBirthday;

                $profile->daysLeft = round(
                    now()->diffInDays($nextBirthday, false)
                );

                if ($profile->daysLeft == 0) {
                    $profile->birthdayStatus = 'Today';
                } elseif ($profile->daysLeft == 1) {
                    $profile->birthdayStatus = 'Tomorrow';
                } elseif ($profile->daysLeft <= 7) {
                    $profile->birthdayStatus = 'This Week';
                } else {
                    $profile->birthdayStatus = 'Upcoming';
                }

                return $profile;
            })
            ->sortBy('daysLeft')
            ->take(5);

        // ===============================
        // Subscription Countdown
        // ===============================

        $subscriptionCountdown = UserProfile::all()
            ->map(function ($profile) {

                $expiry = Carbon::parse($profile->subscription_expiry);

                $profile->remainingDays = round(
                    now()->diffInDays($expiry, false)
                );

                if ($expiry->isPast()) {

                    $profile->subscriptionMessage =
                        'Expired ' .
                        abs($profile->remainingDays) .
                        ' days ago';
                } elseif ($expiry->isToday()) {

                    $profile->subscriptionMessage =
                        'Expires Today';
                } else {

                    $profile->subscriptionMessage =
                        'Expires in ' .
                        $profile->remainingDays .
                        ' days';
                }

                return $profile;
            })
            ->sortBy('remainingDays')
            ->values();

        return view(
            'profiles.index',
            compact(
                'currentDate',
                'currentDateTime',
                'currentDayName',
                'yesterday',
                'tomorrow',
                'nextWeek',
                'lastMonth',
                'newYorkTime',
                'londonTime',
                'age',
                'customDate',
                'daysDifference',
                'monthsDifference',
                'isPast',
                'isFuture',
                'isWeekend',
                'profiles',
                'totalProfiles',
                'activeSubscriptions',
                'expiredSubscriptions',
                'upcomingBirthdays',
                'subscriptionCountdown'
            )
        );
    }

    public function create()
    {
        return view('profiles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:user_profiles',
            'birth_date' => 'required|date',
            'subscription_expiry' => 'required|date',
        ]);

        UserProfile::create([
            'name' => $request->name,
            'email' => $request->email,
            'birth_date' => $request->birth_date,
            'subscription_expiry' => $request->subscription_expiry,
        ]);

        return redirect()
            ->route('profiles.index')
            ->with('success', 'Profile Created Successfully');
    }

    public function showCalculations($id)
    {
        $profile = UserProfile::findOrFail($id);

        $age = Carbon::parse($profile->birth_date)->age;

        $birthDate = Carbon::parse($profile->birth_date);

        $nextBirthday = Carbon::create(
            now()->year,
            $birthDate->month,
            $birthDate->day
        );

        if ($nextBirthday->lt(now()->startOfDay())) {
            $nextBirthday->addYear();
        }

        $daysUntilBirthday = round(
            now()->diffInDays($nextBirthday, false)
        );

        $isBirthdayToday = $nextBirthday->isToday();

        $subscriptionExpiry = Carbon::parse(
            $profile->subscription_expiry
        );

        $daysUntilExpiry = round(
            now()->diffInDays($subscriptionExpiry, false)
        );

        $isSubscriptionActive = !$subscriptionExpiry->isPast();

        if ($subscriptionExpiry->isPast()) {
            $subscriptionMessage = 'Expired ' . abs($daysUntilExpiry) . ' days ago';
        } elseif ($subscriptionExpiry->isToday()) {
            $subscriptionMessage = 'Expires Today';
        } else {
            $subscriptionMessage = 'Expires in ' . $daysUntilExpiry . ' days';
        }

        $timeSinceCreated = Carbon::parse(
            $profile->created_at
        )->diffForHumans();

        $createdDate = Carbon::parse($profile->created_at)
            ->format('F j, Y');

        $accountAgeDays = round(
            Carbon::parse($profile->created_at)
                ->diffInDays(now())
        );

        $accountAgeMonths = round(
            Carbon::parse($profile->created_at)
                ->diffInMonths(now())
        );

        return view(
            'profiles.calculations',
            compact(
                'profile',
                'age',
                'nextBirthday',
                'daysUntilBirthday',
                'isBirthdayToday',
                'daysUntilExpiry',
                'subscriptionMessage',
                'isSubscriptionActive',
                'timeSinceCreated',
                'createdDate',
                'accountAgeDays',
                'accountAgeMonths'
            )
        );
    }
}
