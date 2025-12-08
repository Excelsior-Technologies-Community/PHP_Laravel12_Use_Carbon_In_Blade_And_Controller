<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Create User Profile</h1>
        
        <form action="{{ route('profiles.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
                <label for="birth_date" class="form-label">Birth Date</label>
                <input type="date" class="form-control" id="birth_date" name="birth_date" 
                       max="{{ now()->format('Y-m-d') }}" required>
                <small class="text-muted">Maximum: Today ({{ now()->format('Y-m-d') }})</small>
            </div>
            
            <div class="mb-3">
                <label for="subscription_expiry" class="form-label">Subscription Expiry</label>
                <input type="date" class="form-control" id="subscription_expiry" name="subscription_expiry" 
                       min="{{ now()->format('Y-m-d') }}" 
                       value="{{ now()->addDays(30)->format('Y-m-d') }}">
                <small class="text-muted">Default: 30 days from today ({{ now()->addDays(30)->format('Y-m-d') }})</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Profile</button>
            <a href="{{ route('profiles.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>