<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogAppTopCategoryRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Логируем запрос
        Log::channel('app_top_category')->info('Request received', [
            'ip' => $request->ip(),
            'date' => $request->input('date'),
            'response_code' => $response->getStatusCode(),
            'user_agent' => $request->userAgent(),
        ]);
        
        return $response;
    }
}