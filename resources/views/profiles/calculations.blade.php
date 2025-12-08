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
                <p><strong>Birth Date:</strong> 
                    {{ \Carbon\Carbon::parse($profile->birth_date)->format('F j, Y') }}
                </p>
                <p><strong>Subscription Expiry:</strong> 
                    {{ \Carbon\Carbon::parse($profile->subscription_expiry)->format('F j, Y') }}
                </p>
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
                            @if($daysUntilBirthday >= 0)
                                {{ $daysUntilBirthday }} days
                            @else
                                {{ abs($daysUntilBirthday) }} days ago
                            @endif
                        </p>
                        <p><strong>Birthday in human readable:</strong> 
                            @php
                                $nextBirthday = \Carbon\Carbon::parse($profile->birth_date)
                                    ->addYears($age + 1);
                            @endphp
                            {{ $nextBirthday->diffForHumans() }}
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
                        <p><strong>Days until expiry:</strong> 
                            @if($daysUntilExpiry > 0)
                                {{ $daysUntilExpiry }} days remaining
                            @elseif($daysUntilExpiry == 0)
                                Expires today!
                            @else
                                Expired {{ abs($daysUntilExpiry) }} days ago
                            @endif
                        </p>
                        <p><strong>Expiry in human readable:</strong> 
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
                
                @php
                    // More examples directly in Blade
                    $created = \Carbon\Carbon::parse($profile->created_at);
                    $now = now();
                    
                    $hoursSinceCreated = $created->diffInHours($now);
                    $weeksSinceCreated = $created->diffInWeeks($now);
                    
                    $birthDate = \Carbon\Carbon::parse($profile->birth_date);
                    $nextBirthday = $birthDate->addYears($age + 1);
                    $isBirthdayToday = $birthDate->isBirthday();
                @endphp
                
                <p><strong>Hours since created:</strong> {{ $hoursSinceCreated }} hours</p>
                <p><strong>Weeks since created:</strong> {{ $weeksSinceCreated }} weeks</p>
                <p><strong>Is today their birthday?</strong> 
                    {{ $isBirthdayToday ? 'Yes! 🎂' : 'No' }}
                </p>
                
                <!-- Using Carbon methods directly -->
                <p><strong>Start of week created:</strong> 
                    {{ $created->startOfWeek()->format('Y-m-d') }}
                </p>
                <p><strong>End of month created:</strong> 
                    {{ $created->endOfMonth()->format('Y-m-d') }}
                </p>
            </div>
        </div>
        
        <a href="{{ route('profiles.index') }}" class="btn btn-primary mt-3">Back to Profiles</a>
    </div>
</body>
</html>