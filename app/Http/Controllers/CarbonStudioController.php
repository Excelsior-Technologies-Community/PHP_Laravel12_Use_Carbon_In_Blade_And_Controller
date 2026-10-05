<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CarbonStudioController extends Controller
{
    /**
     * Display the Carbon Expiry Radar & Date Analytics Studio
     */
    public function index(Request $request)
    {
        $now = Carbon::now();

        // 1. Subscription Expiry Radar Ratios & Badges
        $profiles = UserProfile::all()->map(function ($profile) use ($now) {
            $expiry = Carbon::parse($profile->subscription_expiry);
            $diffDays = (int) round($now->diffInDays($expiry, false));
            
            // Business working days calculation excluding weekends
            $businessDays = $now->diffInWeekdays($expiry, false);

            if ($expiry->gt($now->copy()->addDays(7))) {
                $status = 'Active';
                $badgeClass = 'bg-success';
            } elseif ($expiry->gte($now) && $expiry->lte($now->copy()->addDays(7))) {
                $status = 'Expiring Soon';
                $badgeClass = 'bg-warning text-dark';
            } elseif ($expiry->lt($now) && $expiry->gte($now->copy()->subDays(7))) {
                $status = 'Grace Period';
                $badgeClass = 'bg-info text-white';
            } else {
                $status = 'Expired';
                $badgeClass = 'bg-danger';
            }

            $profile->expiry_status = $status;
            $profile->badge_class = $badgeClass;
            $profile->diff_days = $diffDays;
            $profile->business_days = $businessDays;
            $profile->expiry_human = $expiry->diffForHumans();
            $profile->is_weekend_expiry = $expiry->isWeekend();

            return $profile;
        });

        $subscriptionStats = [
            'total' => $profiles->count(),
            'active' => $profiles->where('expiry_status', 'Active')->count(),
            'expiring_soon' => $profiles->where('expiry_status', 'Expiring Soon')->count(),
            'grace_period' => $profiles->where('expiry_status', 'Grace Period')->count(),
            'expired' => $profiles->where('expiry_status', 'Expired')->count(),
        ];

        // 2. Event Countdown & Working Business Days Calculator
        $targetDateInput = $request->input('target_date', $now->copy()->addDays(14)->format('Y-m-d H:i'));
        $targetCarbon = Carbon::parse($targetDateInput);
        $countdownSeconds = max(0, $now->diffInSeconds($targetCarbon, false));
        $remainingBusinessDays = $now->diffInWeekdays($targetCarbon, false);
        $targetIsWeekend = $targetCarbon->isWeekend();
        $nextBusinessDay = $now->copy()->addWeekdays(5)->format('F j, Y');

        // 3. Recurring Schedule & Reminder Dispatcher
        $schedules = [
            [
                'title' => 'Weekly Team Sync & Sprint Review',
                'date' => $now->copy()->next(Carbon::MONDAY)->format('F j, Y (l)'),
                'human' => $now->copy()->next(Carbon::MONDAY)->diffForHumans(),
                'type' => 'Weekly',
                'badge' => 'bg-primary',
            ],
            [
                'title' => 'Monthly Subscription Billing Cycle',
                'date' => $now->copy()->addMonth()->startOfMonth()->format('F j, Y (l)'),
                'human' => $now->copy()->addMonth()->startOfMonth()->diffForHumans(),
                'type' => 'Monthly',
                'badge' => 'bg-success',
            ],
            [
                'title' => 'Quarterly System Performance Audit',
                'date' => $now->copy()->endOfQuarter()->format('F j, Y (l)'),
                'human' => $now->copy()->endOfQuarter()->diffForHumans(),
                'type' => 'Quarterly',
                'badge' => 'bg-warning text-dark',
            ],
            [
                'title' => 'Year-End Financial Reporting Deadline',
                'date' => $now->copy()->endOfYear()->format('F j, Y (l)'),
                'human' => $now->copy()->endOfYear()->diffForHumans(),
                'type' => 'Annual',
                'badge' => 'bg-danger',
            ],
        ];

        // 4. Date Range Filtering Radar
        $dateFilter = $request->input('range', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $filteredProfilesQuery = UserProfile::query();

        match ($dateFilter) {
            'today' => $filteredProfilesQuery->whereDate('created_at', Carbon::today()),
            'yesterday' => $filteredProfilesQuery->whereDate('created_at', Carbon::yesterday()),
            'last_7_days' => $filteredProfilesQuery->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()]),
            'this_month' => $filteredProfilesQuery->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year),
            'last_month' => $filteredProfilesQuery->whereMonth('created_at', Carbon::now()->subMonth()->month),
            'this_year' => $filteredProfilesQuery->whereYear('created_at', Carbon::now()->year),
            'custom' => ($dateFrom && $dateTo) ? $filteredProfilesQuery->whereBetween('created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]) : null,
            default => null,
        };

        $filteredProfiles = $filteredProfilesQuery->latest()->get();

        // 5. Age & Birthday Distribution Breakdown
        $allProfiles = UserProfile::all();
        $ageGroups = [
            'young' => 0,   // 18-25
            'adult' => 0,   // 26-40
            'senior' => 0,  // 40+
        ];

        foreach ($allProfiles as $p) {
            $age = Carbon::parse($p->birth_date)->age;
            if ($age <= 25) {
                $ageGroups['young']++;
            } elseif ($age <= 40) {
                $ageGroups['adult']++;
            } else {
                $ageGroups['senior']++;
            }
        }

        $upcomingBirthdays = $allProfiles->map(function ($p) use ($now) {
            $birth = Carbon::parse($p->birth_date);
            $nextBirth = Carbon::create($now->year, $birth->month, $birth->day);
            if ($nextBirth->lt($now->startOfDay())) {
                $nextBirth->addYear();
            }
            $p->next_birthday_date = $nextBirth->format('F j, Y');
            $p->days_to_birthday = (int) round($now->diffInDays($nextBirth, false));
            $p->turned_age = $birth->age + 1;
            return $p;
        })->sortBy('days_to_birthday')->values()->take(5);

        return view('carbon_studio.index', compact(
            'profiles',
            'subscriptionStats',
            'targetDateInput',
            'targetCarbon',
            'countdownSeconds',
            'remainingBusinessDays',
            'targetIsWeekend',
            'nextBusinessDay',
            'schedules',
            'dateFilter',
            'dateFrom',
            'dateTo',
            'filteredProfiles',
            'ageGroups',
            'upcomingBirthdays'
        ));
    }

    /**
     * Renew or Extend User Subscription using Carbon
     */
    public function renewSubscription(Request $request, $id)
    {
        $profile = UserProfile::findOrFail($id);
        $days = (int) $request->input('days', 30);
        $now = Carbon::now();
        $currentExpiry = Carbon::parse($profile->subscription_expiry);

        // If subscription is past, start from now; else extend current expiry
        $newExpiry = $currentExpiry->isPast() ? $now->copy()->addDays($days) : $currentExpiry->copy()->addDays($days);
        $profile->subscription_expiry = $newExpiry;
        $profile->save();

        return redirect()->back()->with('success', "Subscription for {$profile->name} renewed for {$days} days! New Expiry: " . $newExpiry->format('F j, Y H:i'));
    }

    /**
     * Multi-Format Date Report Exporter (CSV, XLSX, JSON)
     */
    public function exportDataset(Request $request)
    {
        $format = strtolower($request->query('format', 'csv'));
        $filename = 'carbon_analytics_report_' . date('Y_m_d_His') . ".{$format}";
        $now = Carbon::now();

        $dataset = UserProfile::all()->map(function ($p) use ($now) {
            $birth = Carbon::parse($p->birth_date);
            $expiry = Carbon::parse($p->subscription_expiry);
            $created = Carbon::parse($p->created_at);

            return [
                'ID' => $p->id,
                'Name' => $p->name,
                'Email' => $p->email,
                'Birth Date' => $birth->format('F j, Y'),
                'Age (Years)' => $birth->age,
                'Subscription Expiry' => $expiry->format('Y-m-d H:i:s'),
                'Status' => $expiry->gt($now) ? 'Active' : 'Expired',
                'Days Remaining / Overdue' => (int) round($now->diffInDays($expiry, false)),
                'Business Days Remaining' => $now->diffInWeekdays($expiry, false),
                'Account Created' => $created->format('Y-m-d H:i:s'),
                'Relative Age' => $created->diffForHumans(),
            ];
        });

        if ($format === 'json') {
            return response()->json($dataset, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        // CSV or XLSX spreadsheet download
        $headers = [
            'Content-Type' => $format === 'xlsx' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($dataset) {
            $file = fopen('php://output', 'w');
            if ($dataset->isNotEmpty()) {
                fputcsv($file, array_keys($dataset->first()));
                foreach ($dataset as $row) {
                    fputcsv($file, $row);
                }
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
