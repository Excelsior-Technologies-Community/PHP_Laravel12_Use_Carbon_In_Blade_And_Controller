<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserProfile extends Model
{
    protected $fillable = [
        'name',
        'email',
        'birth_date',
        'subscription_expiry'
    ];
    
    protected $casts = [
        'birth_date' => 'date',
        'subscription_expiry' => 'datetime',
    ];
    
    /**
     * Get formatted birth date
     */
    public function getFormattedBirthDateAttribute()
    {
        return Carbon::parse($this->birth_date)->format('F j, Y');
    }
    
    /**
     * Get age from birth date
     */
    public function getAgeAttribute()
    {
        return Carbon::parse($this->birth_date)->age;
    }
    
    /**
     * Get subscription status
     */
    public function getSubscriptionStatusAttribute()
    {
        $expiry = Carbon::parse($this->subscription_expiry);
        
        if ($expiry->isPast()) {
            return 'Expired';
        } elseif ($expiry->isToday()) {
            return 'Expires Today';
        } else {
            return 'Active';
        }
    }
    
    /**
     * Get days until subscription expiry
     */
    public function getDaysUntilExpiryAttribute()
    {
        return Carbon::now()->diffInDays(
            Carbon::parse($this->subscription_expiry),
            false
        );
    }
    
    /**
     * Check if subscription is active
     */
    public function getIsSubscriptionActiveAttribute()
    {
        return Carbon::parse($this->subscription_expiry)->isFuture();
    }
    
    /**
     * Scope for active subscriptions
     */
    public function scopeActiveSubscription($query)
    {
        return $query->where('subscription_expiry', '>', now());
    }
    
    /**
     * Scope for expired subscriptions
     */
    public function scopeExpiredSubscription($query)
    {
        return $query->where('subscription_expiry', '<', now());
    }
    
    /**
     * Scope for birthdays this month
     */
    public function scopeBirthdaysThisMonth($query)
    {
        return $query->whereMonth('birth_date', now()->month);
    }
}