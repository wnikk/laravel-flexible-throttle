<?php

namespace Wnikk\FlexibleThrottle\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Wnikk\FlexibleThrottle\RateLimiterStats;
use Wnikk\FlexibleThrottle\Facades\FlexibleIpResolverFacade;
use Wnikk\FlexibleThrottle\Helpers\TimeHelper;

/**
 * Class FlexibleThrottle
 *
 * Middleware for advanced rate limiting based on IP, HTTP status codes, and exceptions.
 *
 * @package wnikk\FlexibleThrottle\Middleware
 */
class FlexibleThrottle
{
    /**
     * @var RateLimiterStats
     */
    protected $stats;

    /**
     * @var array
     */
    protected $config;

    /**
     * FlexibleThrottle constructor.
     *
     * @param RateLimiterStats $stats
     */
    public function __construct(RateLimiterStats $stats)
    {
        $this->stats  = $stats;
        $this->config = config('flexible-throttle');
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param int|null $maxAttempts
     * @param int|null $decaySeconds
     * @param int|null $blockDuration
     * @return mixed
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next, $maxAttempts = null, $decaySeconds = null, $blockDuration = null)
    {
        $identifier = FlexibleIpResolverFacade::getId($request);

        if ($this->stats->isLock($identifier))
        {
            $humanTime = TimeHelper::convertTime($this->stats->availableIn($identifier, null));
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Too Many Requests',
                    'retry_after' => $humanTime
                ], Response::HTTP_TOO_MANY_REQUESTS);
            } else {
                abort(
                    Response::HTTP_TOO_MANY_REQUESTS,
                    'Too Many Requests, retry_after '.$humanTime,
                    ['Retry-After' => $humanTime]
                );
            }
        }

        $response = $next($request);

        // +1 request count, if response status code or exception is in the list
        $e = $response->exception??null;
        $statusCode = $response->getStatusCode();
        if ($params = $this->getRuleParams($statusCode, $e?get_class($e):null)) {
            $this->incrementRequestCount($identifier, $params, $maxAttempts, $decaySeconds, $blockDuration);
        }

        return $response;
    }

    /**
     * Increment the request count for the given key.
     *
     * @param string $identifier
     * @param array $params
     * @param int|null $maxAttempts
     * @param int|null $decaySeconds
     * @param int|null $blockDuration
     * @return void
     */
    public function incrementRequestCount($identifier, $params, $maxAttempts, $decaySeconds, $blockDuration)
    {
        $eCode = $params['exception'] ?? $params['status_code'];
        if (
            $this->stats->tooManyAttempts(
                $identifier,
                $eCode,
                $maxAttempts ?? $params['max_attempts']
            )
        ) {
            $this->stats->setLock(
                $identifier,
                $eCode,
                $blockDuration ?? $params['block_duration']
            );
        } else {
            // +1 request count
            $this->stats->hit(
                $identifier,
                $eCode,
                $decaySeconds ?? $params['decay_seconds']
            );
        }
    }


    /**
     * Get default config for status code or exception.
     *
     * @return array
     */
    protected function getDefaultRuleParams()
    {
        return [
            'status_code'    => 0,
            'exception'      => null,
            'max_attempts'   => $this->config['max_attempts'],
            'decay_seconds'  => $this->config['decay_seconds'],
            'block_duration' => $this->config['block_duration']
        ];
    }

    /**
     * Get config for status code or exception.
     *
     * @param int $statusCode
     * @param string|null $exceptionClass
     * @return array|null
     */
    protected function getRuleParams(int $statusCode, $exceptionClass = null)
    {
        $default = $this->getDefaultRuleParams();
        if ($exceptionClass &&
            (isset($this->config["rules"]["exceptions"][$exceptionClass]) ||
                in_array($exceptionClass, $this->config["rules"]["exceptions"]))
        ) {
            return ($this->config["rules"]["exceptions"][$exceptionClass]??[]) +
                ['exception' => $exceptionClass] +
                $default;
        }

        if (isset($this->config["rules"]["status_codes"][$statusCode]) ||
                in_array($statusCode, $this->config["rules"]["status_codes"])
        ) {
            return ($this->config["rules"]["status_codes"][$statusCode]??[]) +
                ['status_code' => $statusCode] +
                $default;
        }

        return null;
    }
}