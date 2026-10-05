<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbon Expiry Radar & Date Analytics Studio - Laravel 12</title>

    <!-- Bootstrap 5 CSS & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; }
        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .countdown-box { font-family: 'Courier New', Courier, monospace; letter-spacing: 1px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('carbon.studio') }}">
                <i class="fa-solid fa-clock-rotate-left text-warning me-2"></i>Carbon Date Radar Studio
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('profiles.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-users me-1"></i> View Profiles Index
                </a>
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-download me-1"></i> Export Report
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="{{ route('carbon.studio.export', ['format' => 'csv']) }}"><i class="fa-solid fa-file-csv text-success me-2"></i> CSV Dataset</a></li>
                        <li><a class="dropdown-item" href="{{ route('carbon.studio.export', ['format' => 'xlsx']) }}"><i class="fa-solid fa-file-excel text-primary me-2"></i> XLSX Spreadsheet</a></li>
                        <li><a class="dropdown-item" href="{{ route('carbon.studio.export', ['format' => 'json']) }}"><i class="fa-solid fa-file-code text-warning me-2"></i> JSON Payload</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container py-4">

        <!-- Flash Alert -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Header Title Banner -->
        <div class="bg-white p-4 rounded-4 shadow-sm border mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <h3 class="fw-bold mb-1 text-dark">
                    <i class="fa-solid fa-calendar-days text-primary me-2"></i>Event & Subscription Expiry Schedule Radar
                </h3>
                <p class="text-muted mb-0 small">Carbon-powered subscription grace period tracking, business days calculation & date range analytics</p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-primary px-3 py-2 fs-6">Laravel {{ app()->version() }} · Carbon 3</span>
            </div>
        </div>

        <!-- 1. Subscription Status KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success card-hover h-100">
                    <div class="text-muted small fw-semibold">ACTIVE SUBSCRIPTIONS</div>
                    <div class="h2 fw-bold text-success mb-0 mt-1">{{ $subscriptionStats['active'] }}</div>
                    <small class="text-muted">Expiry > 7 Days</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning card-hover h-100">
                    <div class="text-muted small fw-semibold">EXPIRING SOON</div>
                    <div class="h2 fw-bold text-warning mb-0 mt-1">{{ $subscriptionStats['expiring_soon'] }}</div>
                    <small class="text-muted">Expiry Within 7 Days</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-info card-hover h-100">
                    <div class="text-muted small fw-semibold">GRACE PERIOD</div>
                    <div class="h2 fw-bold text-info mb-0 mt-1">{{ $subscriptionStats['grace_period'] }}</div>
                    <small class="text-muted">Expired Within 7 Days</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-danger card-hover h-100">
                    <div class="text-muted small fw-semibold">FULLY EXPIRED</div>
                    <div class="h2 fw-bold text-danger mb-0 mt-1">{{ $subscriptionStats['expired'] }}</div>
                    <small class="text-muted">Expired > 7 Days Ago</small>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Left Column: Subscription Expiry & Grace Period Radar Table -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Subscription Radar & Business Days</h5>
                            <p class="text-muted small mb-0">Calculates working days excluding weekends (Carbon <code>diffInWeekdays</code>)</p>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>User Profile</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th>Total Days</th>
                                        <th>Business Days</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($profiles as $profile)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $profile->name }}</div>
                                                <small class="text-muted">{{ $profile->email }}</small>
                                            </td>
                                            <td>
                                                <div class="small fw-semibold">{{ \Carbon\Carbon::parse($profile->subscription_expiry)->format('F j, Y') }}</div>
                                                <small class="text-muted">{{ $profile->expiry_human }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $profile->badge_class }} px-2.5 py-1">
                                                    {{ $profile->expiry_status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($profile->diff_days >= 0)
                                                    <span class="text-success fw-bold">+{{ $profile->diff_days }} days</span>
                                                @else
                                                    <span class="text-danger fw-bold">{{ $profile->diff_days }} days</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <i class="fa-solid fa-briefcase me-1 text-primary"></i>{{ abs($profile->business_days) }} workdays
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <!-- Renew Dropdown -->
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        Renew / Extend
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                        <li>
                                                            <form action="{{ route('carbon.studio.renew', $profile->id) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="days" value="30">
                                                                <button type="submit" class="dropdown-item">+ 30 Days (1 Month)</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('carbon.studio.renew', $profile->id) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="days" value="90">
                                                                <button type="submit" class="dropdown-item">+ 90 Days (Quarter)</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('carbon.studio.renew', $profile->id) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="days" value="365">
                                                                <button type="submit" class="dropdown-item">+ 365 Days (1 Year)</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No user profiles found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Event Countdown & Business Days Calculator -->
            <div class="col-lg-4">
                <!-- Event Live Seconds Countdown Widget -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white">
                    <div class="card-header bg-transparent border-secondary pt-3 px-3 pb-2">
                        <span class="fw-bold small"><i class="fa-solid fa-stopwatch text-warning me-2"></i>Event Countdown & Workdays Calculator</span>
                    </div>
                    <div class="card-body p-3">
                        <form action="{{ route('carbon.studio') }}" method="GET" class="mb-3">
                            <label class="form-label small text-muted">Target Event Date & Time</label>
                            <div class="input-group input-group-sm">
                                <input type="datetime-local" name="target_date" class="form-control bg-secondary text-white border-0" value="{{ date('Y-m-d\TH:i', strtotime($targetDateInput)) }}">
                                <button type="submit" class="btn btn-warning fw-bold">Calculate</button>
                            </div>
                        </form>

                        <div class="bg-black p-3 rounded-3 text-center mb-3">
                            <div class="text-muted small mb-1">Live Countdown Target</div>
                            <div class="fw-bold text-warning">{{ $targetCarbon->format('F j, Y H:i:s') }}</div>
                            <div id="liveCountdownDisplay" class="h3 fw-bold text-success my-2 countdown-box">00d 00h 00m 00s</div>
                            <small class="text-muted d-block">{{ $targetCarbon->diffForHumans() }}</small>
                        </div>

                        <div class="p-2.5 rounded-3 bg-secondary bg-opacity-25 border border-secondary text-white small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Working Business Days:</span>
                                <strong class="text-info">{{ $remainingBusinessDays }} Business Days</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Target Day Type:</span>
                                <strong>{{ $targetIsWeekend ? 'Weekend (Sat/Sun)' : 'Regular Weekday' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>+5 Working Days Date:</span>
                                <strong class="text-warning">{{ $nextBusinessDay }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recurring Schedule & Reminders Widget -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-3 px-3 pb-0">
                        <h6 class="fw-bold mb-1"><i class="fa-solid fa-bell text-danger me-2"></i>Recurring Schedule & Reminders</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="list-group list-group-flush">
                            @foreach($schedules as $sched)
                                <div class="list-group-item px-0 py-2 border-0">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold text-dark small">{{ $sched['title'] }}</span>
                                        <span class="badge {{ $sched['badge'] }}">{{ $sched['type'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted extra-small">
                                        <small><i class="fa-regular fa-calendar me-1"></i>{{ $sched['date'] }}</small>
                                        <small class="fw-semibold text-primary">{{ $sched['human'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Carbon Date Range Analytics & Exporter -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h5 class="fw-bold mb-1"><i class="fa-solid fa-filter text-info me-2"></i>Carbon Date Range Analytics & Filter Radar</h5>
                    <p class="text-muted small mb-0">Filter user profiles dynamically using Carbon date scopes & range pickers</p>
                </div>
                <!-- Range Preset Buttons -->
                <div class="btn-group btn-group-sm mt-3 mt-md-0" role="group">
                    <a href="{{ route('carbon.studio', ['range' => 'all']) }}" class="btn {{ $dateFilter == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                    <a href="{{ route('carbon.studio', ['range' => 'today']) }}" class="btn {{ $dateFilter == 'today' ? 'btn-primary' : 'btn-outline-primary' }}">Today</a>
                    <a href="{{ route('carbon.studio', ['range' => 'yesterday']) }}" class="btn {{ $dateFilter == 'yesterday' ? 'btn-primary' : 'btn-outline-primary' }}">Yesterday</a>
                    <a href="{{ route('carbon.studio', ['range' => 'last_7_days']) }}" class="btn {{ $dateFilter == 'last_7_days' ? 'btn-primary' : 'btn-outline-primary' }}">7 Days</a>
                    <a href="{{ route('carbon.studio', ['range' => 'this_month']) }}" class="btn {{ $dateFilter == 'this_month' ? 'btn-primary' : 'btn-outline-primary' }}">This Month</a>
                    <a href="{{ route('carbon.studio', ['range' => 'this_year']) }}" class="btn {{ $dateFilter == 'this_year' ? 'btn-primary' : 'btn-outline-primary' }}">This Year</a>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Custom Date Range Picker Form -->
                <form action="{{ route('carbon.studio') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                    <input type="hidden" name="range" value="custom">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold mb-1">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold mb-1">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-info btn-sm text-white fw-bold w-100">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Apply Filter
                        </button>
                    </div>
                </form>

                <div class="row g-4">
                    <!-- Left: Age & Birthday Visualizer Breakdown -->
                    <div class="col-md-5">
                        <h6 class="fw-bold text-uppercase text-muted small mb-3">User Age Groups Breakdown (Carbon <code>age</code>)</h6>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span>Young (18-25 Years)</span>
                                <span>{{ $ageGroups['young'] }} Users</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ $subscriptionStats['total'] > 0 ? round(($ageGroups['young'] / $subscriptionStats['total']) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span>Adult (26-40 Years)</span>
                                <span>{{ $ageGroups['adult'] }} Users</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" style="width: {{ $subscriptionStats['total'] > 0 ? round(($ageGroups['adult'] / $subscriptionStats['total']) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span>Senior (40+ Years)</span>
                                <span>{{ $ageGroups['senior'] }} Users</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-warning text-dark" style="width: {{ $subscriptionStats['total'] > 0 ? round(($ageGroups['senior'] / $subscriptionStats['total']) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-uppercase text-muted small mb-3 mt-4">Upcoming Birthdays Widget</h6>
                        <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                            @forelse($upcomingBirthdays as $bday)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <div class="fw-semibold text-dark small"><i class="fa-solid fa-cake-candles text-danger me-2"></i>{{ $bday->name }}</div>
                                        <small class="text-muted">{{ $bday->next_birthday_date }} (Turning {{ $bday->turned_age }})</small>
                                    </div>
                                    <span class="badge bg-danger rounded-pill">{{ $bday->days_to_birthday }} days left</span>
                                </div>
                            @empty
                                <div class="p-3 text-muted small text-center">No upcoming birthdays.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Right: Filtered Results List -->
                    <div class="col-md-7">
                        <h6 class="fw-bold text-uppercase text-muted small mb-3">Filtered Profile Records ({{ $filteredProfiles->count() }} Records)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped border align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Birth Date & Age</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($filteredProfiles as $fp)
                                        <tr>
                                            <td class="fw-semibold text-dark">{{ $fp->name }}</td>
                                            <td>
                                                <div>{{ \Carbon\Carbon::parse($fp->birth_date)->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($fp->birth_date)->age }} Years Old</small>
                                            </td>
                                            <td>
                                                <div>{{ \Carbon\Carbon::parse($fp->created_at)->format('Y-m-d H:i') }}</div>
                                                <small class="text-primary">{{ \Carbon\Carbon::parse($fp->created_at)->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">No profiles matched the selected date range.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap Bundle JS & Countdown Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const targetTime = new Date("{{ $targetCarbon->toIso8601String() }}").getTime();
            const display = document.getElementById('liveCountdownDisplay');

            function updateCountdown() {
                const now = new Date().getTime();
                const diff = targetTime - now;

                if (diff <= 0) {
                    display.textContent = "00d 00h 00m 00s (EVENT PASSED)";
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                const dStr = String(days).padStart(2, '0');
                const hStr = String(hours).padStart(2, '0');
                const mStr = String(minutes).padStart(2, '0');
                const sStr = String(seconds).padStart(2, '0');

                display.textContent = `${dStr}d ${hStr}h ${mStr}m ${sStr}s`;
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    </script>
</body>
</html>
