<?php

namespace App\Services;

use App\Models\User;

class ActivityLogService
{
    /**
     * Dummy log method.
     * Full implementation will be added later.
     */
    public static function log(
        User    $user,
        string  $event,
        string  $description,
        $project = null,
        array   $properties = []
    ) {
        // Do nothing for now
        return null;
    }
}
