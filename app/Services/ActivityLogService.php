<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;

class ActivityLogService
{
    public static function log(
        User    $user,
        string  $event,
        string  $description,
        ?Project $project = null,
        array   $properties = []
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'    => $user->id,
            'project_id' => $project?->id,
            'event'      => $event,
            'description'=> $description,
            'properties' => $properties ?: null,
        ]);
    }
}
