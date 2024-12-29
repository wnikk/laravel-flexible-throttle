<?php

namespace Wnikk\FlexibleThrottle;

use Illuminate\Support\Facades\RateLimiter;

class RateLimiterStats
{
    /**
     * Resolve the request signature for rate limiting.
     *
     * @param string $identifier
     * @param string $eCode
     * @return string
     */
    protected function resolveRequestSignature($identifier, $eCode = null)
    {
        return 'ft-'.md5($identifier . '|' . $eCode);
    }

    /**
     * Block the given key for the specified duration.
     *
     * @param string $identifier
     * @param string $key
     * @param int $duration
     * @return void
     */
    public function setLock($identifier, $eCode, int $duration)
    {
        $key = $this->resolveRequestSignature($identifier, $eCode);
        RateLimiter::clear($identifier);
        RateLimiter::clear($key);
        RateLimiter::hit($identifier, $duration);
    }

    /**
     * Check is block given identifier.
     *
     * @param string $identifier
     * @return bool
     */
    public function isLock($identifier)
    {
        return $this->tooManyAttempts(
            $identifier,
            null,
            1
        );
    }

    /**
     * Check if the given key has too many attempts.
     *
     * @param string $identifier
     * @param string $key
     * @param int $maxAttempts
     * @return bool
     */
    public function tooManyAttempts($identifier, $eCode, $maxAttempts)
    {
        if (RateLimiter::tooManyAttempts($identifier, 1)){
            return true;
        }
        if ($eCode) {
            $key = $this->resolveRequestSignature($identifier, $eCode);
            return RateLimiter::tooManyAttempts($key, $maxAttempts);
        }
        return false;
    }

    /**
     * Get the number of seconds until the key is available again.
     *
     * @param string $identifier
     * @param string $eCode
     * @return int
     */
    public function availableIn($identifier, $eCode = null)
    {
        if ($eCode) {
            $key = $this->resolveRequestSignature($identifier, $eCode);
            RateLimiter::availableIn($key);
        }

        return RateLimiter::availableIn($identifier);
    }

    /**
     * Record a hit for the given key.
     *
     * @param string $identifier
     * @param string $eCode
     * @param int $decaySeconds
     * @return void
     */
    public function hit($identifier, $eCode, int $decaySeconds)
    {
        $key = $this->resolveRequestSignature($identifier, $eCode);

        RateLimiter::hit($key, $decaySeconds);
    }
}