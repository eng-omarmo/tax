<?php

if (!function_exists('getStatusClass')) {
    function getStatusClass($metricType): string
    {
        return match ($metricType) {
            'payment' => 'bg-gradient-start-2',
            'outstanding' => 'bg-gradient-start-4',
            'collection' => 'bg-gradient-start-3',
            default => 'bg-gradient-start-1',
        };
    }
}
