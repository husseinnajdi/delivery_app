<?php
namespace App\Services;

use App\Models\User;
use App\Models\activity_log;

class ActivityLog
{
    public function log(int $user_id, string $action, string $description, string $ip_address): void
    {
        activity_log::create([
            'user_id' => $user_id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ip_address,
        ]);
    }
}
