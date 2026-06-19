<?php

namespace App\Services;

use App\Models\User;

class ActivityLogService
{
    /**
     * Dummy log method for Commit 2. 
     * Full implementation will be added in Commit 6.
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
