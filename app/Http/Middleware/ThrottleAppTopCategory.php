<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter as FacadesRateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleAppTopCategory
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'app_top_category_limit:' . $request->ip();
        
        // Устанавливаем лимит: 5 запросов в минуту
        if (FacadesRateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status_code' => 429,
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        }
        
        FacadesRateLimiter::hit($key, 60); // 60 секунд (1 минута)
        
        return $next($request);
    }
}