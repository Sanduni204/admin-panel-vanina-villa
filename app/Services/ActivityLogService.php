<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function log(
        User $user,
        string $action,
        array $context = [],
        ?string $entityType = null,
        ?int $entityId = null,
        ?Request $request = null
    ): void {
        AdminActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $context['old'] ?? null,
            'new_values' => $context['new'] ?? $context ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
