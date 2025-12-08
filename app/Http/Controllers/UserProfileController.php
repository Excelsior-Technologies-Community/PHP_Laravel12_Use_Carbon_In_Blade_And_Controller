<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProfile;
use Carbon\Carbon;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Example 1: Get current date in different formats
        $currentDate = Carbon::now()->format('m/d/Y');
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $currentDayName = Carbon::now()->format('l'); // Monday, Tuesday, etc.
        
        // Example 2: Date manipulation
        $yesterday = Carbon::yesterday()->format('m/d/Y');
        $tomorrow = Carbon::tomorrow()->format('m/d/Y');
        $nextWeek = Carbon::now()->addWeek()->format('m/d/Y');
        $lastMonth = Carbon::now()->subMonth()->format('F Y');
        
        // Example 3: Working with time zones
        $newYorkTime = Carbon::now('America/New_York')->format('Y-m-d H:i:s');
        $londonTime = Carbon::now('Europe/London')->format('Y-m-d H:i:s');
        
        // Example 4: Calculate age from birth date
        $age = Carbon::parse('1990-05-15')->age;
        
        // Example 5: Create from specific date
        $customDate = Carbon::create(2024, 12, 25, 15, 30, 0);
        
        // Example 6: Difference between dates
        $startDate = Carbon::parse('2024-01-01');
        $endDate = Carbon::now();
        $daysDifference = $startDate->diffInDays($endDate);
        $monthsDifference = $startDate->diffInMonths($endDate);
        
        // Example 7: Check if date is in past/future
        $isPast = Carbon::parse('2023-01-01')->isPast();
        $isFuture = Carbon::parse('2025-01-01')->isFuture();
        $isWeekend = Carbon::now()->isWeekend();
        
        // Get user profiles
        $profiles = UserProfile::all();
        
        return view('profiles.index', compact(
            'currentDate',
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
            'currentDateTime',
            'currentDayName'
        ));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('profiles.create');
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user_profiles',
            'birth_date' => 'required|date',
            'subscription_expiry' => 'required|date',
        ]);
        
        // Example: Add 30 days from today as default subscription if not provided
        $subscriptionExpiry = $request->subscription_expiry 
            ? Carbon::parse($request->subscription_expiry)
            : Carbon::now()->addDays(30);
        
        UserProfile::create([
            'name' => $request->name,
            'email' => $request->email,
            'birth_date' => $request->birth_date,
            'subscription_expiry' => $subscriptionExpiry,
        ]);
        
        return redirect()->route('profiles.index')
            ->with('success', 'Profile created successfully!');
    }
    
    /**
     * Display date calculations for a specific user
     */
    public function showCalculations($id)
    {
        $profile = UserProfile::findOrFail($id);
        
        // Calculate age
        $age = Carbon::parse($profile->birth_date)->age;
        
        // Days until birthday
        $nextBirthday = Carbon::parse($profile->birth_date)
            ->addYears($age + 1)
            ->startOfDay();
        $daysUntilBirthday = Carbon::now()->diffInDays($nextBirthday, false);
        
        // Subscription status
        $subscriptionExpiry = Carbon::parse($profile->subscription_expiry);
        $daysUntilExpiry = Carbon::now()->diffInDays($subscriptionExpiry, false);
        $isSubscriptionActive = $subscriptionExpiry->isFuture();
        
        // Time since created
        $createdAt = Carbon::parse($profile->created_at);
        $timeSinceCreated = $createdAt->diffForHumans();
        
        return view('profiles.calculations', compact(
            'profile',
            'age',
            'daysUntilBirthday',
            'daysUntilExpiry',
            'isSubscriptionActive',
            'timeSinceCreated'
        ));
    }
}