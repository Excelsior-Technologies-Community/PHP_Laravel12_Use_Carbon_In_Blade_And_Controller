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
                'expiredSubscriptions'
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

        $nextBirthday = Carbon::parse($profile->birth_date)
            ->addYears($age + 1);

        $daysUntilBirthday = now()->diffInDays(
            $nextBirthday,
            false
        );

        $subscriptionExpiry = Carbon::parse(
            $profile->subscription_expiry
        );

        $daysUntilExpiry = now()->diffInDays(
            $subscriptionExpiry,
            false
        );

        $isSubscriptionActive = $subscriptionExpiry->isFuture();

        $timeSinceCreated = Carbon::parse(
            $profile->created_at
        )->diffForHumans();

        return view(
            'profiles.calculations',
            compact(
                'profile',
                'age',
                'daysUntilBirthday',
                'daysUntilExpiry',
                'isSubscriptionActive',
                'timeSinceCreated'
            )
        );
    }
}