<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date Calculations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Date Calculations for {{ $profile->name }}</h1>

        <div class="card">
            <div class="card-header">
                <h5>Profile Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Email:</strong> {{ $profile->email }}</p>
                <p><strong>Birth Date:</strong> {{ \Carbon\Carbon::parse($profile->birth_date)->format('F j, Y') }}</p>
                <p><strong>Subscription Expiry:</strong> {{ \Carbon\Carbon::parse($profile->subscription_expiry)->format('F j, Y') }}</p>
            </div>
        </div>

        <div class="row mt-4">

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Age Calculations</h5>
                    </div>

                    <div class="card-body">

                        <p><strong>Current Age:</strong> {{ $age }} years old</p>

                        <p><strong>Days until next birthday:</strong>
                            @if($daysUntilBirthday > 0)
                            {{ $daysUntilBirthday }} days
                            @elseif($daysUntilBirthday == 0)
                            Today 🎂
                            @else
                            {{ abs($daysUntilBirthday) }} days ago
                            @endif
                        </p>

                        <p><strong>Next Birthday:</strong> {{ $nextBirthday->format('F j, Y') }}</p>

                        <p>
                            <strong>Birthday Status:</strong>
                            @if($isBirthdayToday)
                            <span class="text-success">Today 🎂</span>
                            @else
                            {{ $nextBirthday->diffForHumans() }}
                            @endif
                        </p>

                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Subscription Status</h5>
                    </div>

                    <div class="card-body">

                        <p>
                            <strong>Status:</strong>
                            @if($isSubscriptionActive)
                            <span class="text-success">Active</span>
                            @else
                            <span class="text-danger">Expired</span>
                            @endif
                        </p>

                        <p>
                            <strong>Subscription Message:</strong>
                            {{ $subscriptionMessage }}
                        </p>

                        <p>
                            <strong>Expiry Date:</strong>
                            {{ \Carbon\Carbon::parse($profile->subscription_expiry)->format('F j, Y') }}
                        </p>

                        <p>
                            <strong>Human Readable:</strong>
                            {{ \Carbon\Carbon::parse($profile->subscription_expiry)->diffForHumans() }}
                        </p>

                    </div>
                </div>
            </div>

        </div>


        <div class="card mt-4">

            <div class="card-header">
                <h5>Other Calculations</h5>
            </div>

            <div class="card-body">

                <p><strong>Profile created:</strong> {{ $timeSinceCreated }}</p>

                <p>
                    <strong>Account Created:</strong>
                    {{ $createdDate }}
                </p>

                <p>
                    <strong>Account Age:</strong>
                    {{ $accountAgeDays }} days
                </p>

                <p>
                    <strong>Membership Duration:</strong>
                    {{ $accountAgeMonths }} months
                </p>

                @php
                $created = \Carbon\Carbon::parse($profile->created_at);
                $now = now();

                $hoursSinceCreated = $created->diffInHours($now);
                $weeksSinceCreated = $created->diffInWeeks($now);
                @endphp

                <p><strong>Hours since created:</strong> {{ $hoursSinceCreated }} hours</p>

                <p><strong>Weeks since created:</strong> {{ $weeksSinceCreated }} weeks</p>

                <p><strong>Start of week created:</strong> {{ $created->startOfWeek()->format('Y-m-d') }}</p>

                <p><strong>End of month created:</strong> {{ $created->endOfMonth()->format('Y-m-d') }}</p>

            </div>

        </div>


        <a href="{{ route('profiles.index') }}" class="btn btn-primary mt-3">
            Back to Profiles
        </a>

    </div>
</body>

</html>