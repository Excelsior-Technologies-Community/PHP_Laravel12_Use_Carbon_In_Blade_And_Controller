<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbon Examples - Laravel 12</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Carbon Date Examples</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Basic Date Operations</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Current Date:</strong> {{ $currentDate }}</p>
                        <p><strong>Current Date & Time:</strong> {{ $currentDateTime }}</p>
                        <p><strong>Today is:</strong> {{ $currentDayName }}</p>
                        <p><strong>Yesterday:</strong> {{ $yesterday }}</p>
                        <p><strong>Tomorrow:</strong> {{ $tomorrow }}</p>
                        <p><strong>Next Week:</strong> {{ $nextWeek }}</p>
                        <p><strong>Last Month:</strong> {{ $lastMonth }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Time Zone Examples</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>New York Time:</strong> {{ $newYorkTime }}</p>
                        <p><strong>London Time:</strong> {{ $londonTime }}</p>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Date Calculations</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Days since Jan 1, 2024:</strong> {{ $daysDifference }} days</p>
                        <p><strong>Months since Jan 1, 2024:</strong> {{ $monthsDifference }} months</p>
                        <p><strong>Is Jan 1, 2023 in past?</strong> {{ $isPast ? 'Yes' : 'No' }}</p>
                        <p><strong>Is Jan 1, 2025 in future?</strong> {{ $isFuture ? 'Yes' : 'No' }}</p>
                        <p><strong>Is today weekend?</strong> {{ $isWeekend ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>User Profiles</h5>
                <a href="{{ route('profiles.create') }}" class="btn btn-primary">Add New Profile</a>
            </div>
            <div class="card-body">
                @if($profiles->count() > 0)
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Birth Date</th>
                                <th>Subscription Expiry</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($profiles as $profile)
                                <tr>
                                    <td>{{ $profile->name }}</td>
                                    <td>{{ $profile->email }}</td>
                                    <td>{{ \Carbon\Carbon::parse($profile->birth_date)->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $expiry = \Carbon\Carbon::parse($profile->subscription_expiry);
                                            $class = $expiry->isPast() ? 'text-danger' : 'text-success';
                                        @endphp
                                        <span class="{{ $class }}">
                                            {{ $expiry->format('M d, Y') }}
                                            @if($expiry->isPast())
                                                (Expired)
                                            @else
                                                ({{ $expiry->diffForHumans() }})
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($profile->created_at)->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('profiles.calculations', $profile->id) }}" 
                                           class="btn btn-sm btn-info">
                                            View Calculations
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-center">No profiles found. <a href="{{ route('profiles.create') }}">Create one</a></p>
                @endif
            </div>
        </div>
        
        <!-- FIXED: Example using Carbon directly in Blade -->
        <div class="card">
            <div class="card-header">
                <h5>Using Carbon Directly in Blade</h5>
            </div>
            <div class="card-body">
                @php
                    use Carbon\Carbon;
                @endphp
                
                <p><strong>Using @php directive:</strong> 
                    {{ Carbon::parse('2024-12-25')->format('F j, Y') }} - Christmas Day
                </p>
                
                <p><strong>Using now() helper:</strong> 
                    {{ now()->addDays(7)->format('l, F j, Y') }} (One week from now)
                </p>
                
                <p><strong>Human readable:</strong> 
                    This page was loaded {{ now()->diffForHumans() }}
                </p>
                
                <!-- Using Carbon facade -->
                <p><strong>Using Carbon facade:</strong> 
                    {{ \Carbon\Carbon::parse('1999-12-31')->format('Y-m-d') }} - Last day of 1999
                </p>
                
                <!-- More examples -->
                <p><strong>Start of current month:</strong> 
                    {{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}
                </p>
                
                <p><strong>End of current year:</strong> 
                    {{ \Carbon\Carbon::now()->endOfYear()->format('Y-m-d') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>