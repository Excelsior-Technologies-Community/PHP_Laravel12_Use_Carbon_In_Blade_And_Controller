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

        <!-- Dashboard Statistics -->
        <div class="row mb-4">

            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h2>{{ $totalProfiles }}</h2>
                        <p>Total Profiles</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h2>{{ $activeSubscriptions }}</h2>
                        <p>Active Subscriptions</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h2>{{ $expiredSubscriptions }}</h2>
                        <p>Expired Subscriptions</p>
                    </div>
                </div>
            </div>

        </div>


                <!-- Birthday Reminder -->
        <div class="card mb-4">

            <div class="card-header bg-warning">
                <h5 class="mb-0">
                    Upcoming Birthday Reminders 🎂
                </h5>
            </div>

            <div class="card-body">

                @if($upcomingBirthdays->count())

                    <div class="row">

                        @foreach($upcomingBirthdays as $profile)

                            <div class="col-md-4 mb-3">

                                <div class="card shadow-sm">

                                    <div class="card-body">

                                        <h5>
                                            {{ $profile->name }}
                                        </h5>

                                        <p class="mb-1">
                                            <strong>Birthday:</strong>
                                            {{ $profile->nextBirthday->format('d M Y') }}
                                        </p>

                                        <p class="mb-0">

                                            @if($profile->birthdayStatus == 'Today')

                                                <span class="badge bg-danger">
                                                    Birthday Today 🎉
                                                </span>

                                            @elseif($profile->birthdayStatus == 'Tomorrow')

                                                <span class="badge bg-warning">
                                                    Tomorrow
                                                </span>

                                            @elseif($profile->birthdayStatus == 'This Week')

                                                <span class="badge bg-info">
                                                    This Week
                                                </span>

                                            @else

                                                <span class="badge bg-success">
                                                    {{ $profile->daysLeft }} days left
                                                </span>

                                            @endif

                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <p class="text-muted">
                        No upcoming birthdays found.
                    </p>

                @endif

            </div>

        </div>


        <!-- Subscription Countdown -->
        <div class="card mb-4">

            <div class="card-header bg-info text-white">

                <h5 class="mb-0">
                    Subscription Countdown ⏳
                </h5>

            </div>

            <div class="card-body">

                @foreach($subscriptionCountdown as $profile)

                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">

                        <div>

                            <strong>
                                {{ $profile->name }}
                            </strong>

                            <br>

                            <small>
                                {{ \Carbon\Carbon::parse($profile->subscription_expiry)->format('d M Y') }}
                            </small>

                        </div>

                        <div>

                            @if($profile->remainingDays < 0)

                                <span class="badge bg-danger">

                                    {{ $profile->subscriptionMessage }}

                                </span>

                            @elseif($profile->remainingDays == 0)

                                <span class="badge bg-warning">

                                    {{ $profile->subscriptionMessage }}

                                </span>

                            @else

                                <span class="badge bg-success">

                                    {{ $profile->subscriptionMessage }}

                                </span>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

        <!-- Carbon Examples -->
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
                        <p><strong>Days Difference:</strong> {{ $daysDifference }}</p>
                        <p><strong>Months Difference:</strong> {{ $monthsDifference }}</p>
                        <p><strong>Past Date:</strong> {{ $isPast ? 'Yes' : 'No' }}</p>
                        <p><strong>Future Date:</strong> {{ $isFuture ? 'Yes' : 'No' }}</p>
                        <p><strong>Weekend:</strong> {{ $isWeekend ? 'Yes' : 'No' }}</p>
                    </div>

                </div>

            </div>

        </div>

        <!-- Search -->
        <div class="card mb-4">

            <div class="card-header">
                Search & Filter Profiles
            </div>

            <div class="card-body">

                <form method="GET" action="{{ route('profiles.index') }}">

                    <div class="row">

                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                placeholder="Search Name or Email">
                        </div>

                        <div class="col-md-3">
                            <select name="status" class="form-select">

                                <option value="">
                                    All Subscriptions
                                </option>

                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>
                                    Expired
                                </option>

                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                Search
                            </button>
                        </div>

                        <div class="col-md-2">
                            <a href="{{ route('profiles.index') }}" class="btn btn-secondary w-100">
                                Reset
                            </a>
                        </div>

                    </div>

                </form>

            </div>

        </div>

        <!-- Profiles -->
        <div class="card mb-4">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h5>User Profiles</h5>

                <a href="{{ route('profiles.create') }}" class="btn btn-success">
                    Add New Profile
                </a>

            </div>

            <div class="card-body">

                @if($profiles->count())

                    <table class="table table-bordered table-striped">

                        <thead class="table-dark">

                            <tr>
                                <th>ID</th>
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

                                    <td>{{ $profile->id }}</td>

                                    <td>{{ $profile->name }}</td>

                                    <td>{{ $profile->email }}</td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($profile->birth_date)->format('M d, Y') }}
                                    </td>

                                    <td>

                                        @php
                                            $expiry = \Carbon\Carbon::parse($profile->subscription_expiry);
                                        @endphp

                                        @if($expiry->isPast())
                                            <span class="badge bg-danger">
                                                Expired
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                Active
                                            </span>
                                        @endif

                                        <br>

                                        {{ $expiry->format('M d, Y') }}

                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($profile->created_at)->diffForHumans() }}
                                    </td>

                                    <td>

                                        <a href="{{ route('profiles.calculations', $profile->id) }}"
                                            class="btn btn-info btn-sm">
                                            View Calculations
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                    <!-- Pagination -->
                    <div class="mt-3">
                        <nav>
                            <ul class="pagination justify-content-center">

                                @for ($i = 1; $i <= $profiles->lastPage(); $i++)

                                    <li class="page-item {{ $profiles->currentPage() == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $profiles->url($i) }}">
                                            {{ $i }}
                                        </a>
                                    </li>

                                @endfor

                            </ul>
                        </nav>
                    </div>

                @else

                    <div class="alert alert-warning">
                        No profiles found.
                    </div>

                @endif

            </div>

        </div>

    </div>

</body>

</html>