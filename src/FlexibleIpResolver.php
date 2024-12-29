<?php

namespace wnikk\FlexibleThrottle;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlexibleIpResolver
{
    /**
     * Resolve the identifier for rate limiting.
     *
     * @param Request $request
     * @return string
     */
    public function getId(Request $request)
    {
        return $request->ip();
    }

    /**
     * Resolve the identifier for rate limiting.
     *
     * @param Request $request
     * @return string
     */
    public function resolve(Request $request)
    {
        if (Auth::check()) {
            return Auth::id();
        }

        if ($request->hasSession()) {
            return $request->session()->getId();
        }

        return $request->ip();
    }
}