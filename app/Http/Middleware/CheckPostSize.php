<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\PostTooLargeException;

class CheckPostSize
{
    public function handle($request, Closure $next)
    {
        $max = ini_get('post_max_size');

        if ($request->server('CONTENT_LENGTH') > $this->convertToBytes($max)) {
            throw new PostTooLargeException('The uploaded file is too large.');
        }

        return $next($request);
    }

    private function convertToBytes($size)
    {
        $units = ['B', 'K', 'M', 'G', 'T', 'P'];
        $size = strtoupper($size);
        $unit = preg_replace('/[^BKMGTPE]/', '', $size);
        $value = (int) preg_replace('/[^0-9]/', '', $size);

        return $value * pow(1024, array_search($unit, $units));
    }
}
