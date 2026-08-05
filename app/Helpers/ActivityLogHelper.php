<?php

declare(strict_types=1);

use App\Models\ActivityLog;

if (! function_exists('activity_log')) {
    function activity_log(array $data): void
    {
        ActivityLog::create(array_merge([
            'ip_address' => request()->ip(),
        ], $data));
    }
}
