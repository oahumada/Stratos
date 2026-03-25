<?php

namespace App\Listeners;

use App\Events\OperationCompleted;
use App\Models\User;
use App\Notifications\OperationCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendOperationCompletedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OperationCompleted $event): void
    {
        $opData = $event->operation;
        $orgId = $event->organizationId;

        $admins = User::where('organization_id', $orgId)
            ->where('role', 'admin')
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new OperationCompletedNotification($opData));
        }
    }
}
