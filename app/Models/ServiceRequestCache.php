<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ServiceRequestCache extends Model
{
    use HasFactory;

    protected $table = 'service_request_cache';

    protected $fillable = [
        'cache_key',
        'service_request_id',
        'technician_id',
        'cache_type',
        'cache_data',
        'expires_at'
    ];

    protected $casts = [
        'cache_data' => 'array',
        'expires_at' => 'datetime'
    ];

    /**
     * Scope to get non-expired cache entries
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', Carbon::now());
        });
    }

    /**
     * Scope to get cache by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('cache_type', $type);
    }

    /**
     * Scope to get cache for service request
     */
    public function scopeForServiceRequest($query, $serviceRequestId)
    {
        return $query->where('service_request_id', $serviceRequestId);
    }

    /**
     * Scope to get cache for technician
     */
    public function scopeForTechnician($query, $technicianId)
    {
        return $query->where('technician_id', $technicianId);
    }

    /**
     * Check if cache entry is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->isPast();
    }

    /**
     * Get cache value if not expired
     */
    public function getValue()
    {
        if ($this->isExpired()) {
            return null;
        }
        
        return $this->cache_data;
    }

    /**
     * Set cache with expiration
     */
    public static function setCache(string $key, $data, $expiresInMinutes = null, array $attributes = []): self
    {
        $expiresAt = $expiresInMinutes ? Carbon::now()->addMinutes($expiresInMinutes) : null;
        
        return self::updateOrCreate(
            ['cache_key' => $key],
            array_merge($attributes, [
                'cache_data' => $data,
                'expires_at' => $expiresAt
            ])
        );
    }

    /**
     * Get cache by key
     */
    public static function getCache(string $key)
    {
        $cache = self::where('cache_key', $key)->notExpired()->first();
        
        return $cache ? $cache->getValue() : null;
    }

    /**
     * Clear expired cache entries
     */
    public static function clearExpired(): int
    {
        return self::where('expires_at', '<', Carbon::now())->delete();
    }

    /**
     * Clear cache by type
     */
    public static function clearByType(string $type): int
    {
        return self::where('cache_type', $type)->delete();
    }
}
