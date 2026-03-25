<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    /**
     * Determine if user can list messages (typically via conversation).
     */
    public function viewAny(User $user): bool
    {
        return (bool) $user->organization_id;
    }

    /**
     * Determine if user can view a specific message.
     * User must be in same organization AND be a participant in the conversation.
     */
    public function view(User $user, Message $message): bool
    {
        // Multi-tenant check
        if (($user->organization_id ?? null) !== ($message->organization_id ?? null)) {
            return false;
        }

        if (! $user->people_id) {
            return false;
        }

        // Check if participant in the conversation
        return $message->conversation->isParticipant($user->people_id);
    }

    /**
     * Message create is typically handled via sendMessage() on ConversationPolicy.
     * But we keep this for consistency.
     */
    public function create(User $user): bool
    {
        return (bool) $user->organization_id && (bool) $user->people_id;
    }

    /**
     * Determine if user can update (edit) a message.
     * Only sender can update (if allowed by business rules).
     * For now, we may not allow editing; this is a policy hook.
     */
    public function update(User $user, Message $message): bool
    {
        // Multi-tenant check
        if (($user->organization_id ?? null) !== ($message->organization_id ?? null)) {
            return false;
        }

        // Only sender can update
        return $user->people_id === $message->people_id;
    }

    /**
     * Determine if user can delete a message.
     * Sender or admin can delete.
     */
    public function delete(User $user, Message $message): bool
    {
        // Multi-tenant check
        if (($user->organization_id ?? null) !== ($message->organization_id ?? null)) {
            return false;
        }

        // Sender or admin
        return $user->people_id === $message->people_id || $this->isAdmin($user);
    }

    /**
     * Helper: Check if user is admin/approver.
     */
    private function isAdmin(User $user): bool
    {
        $role = $user->role ?? null;

        if (in_array($role, ['admin', 'approver', 'owner', 'superadmin'], true)) {
            return true;
        }

        if (property_exists($user, 'is_admin') && (bool) $user->is_admin) {
            return true;
        }

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('approver') || $user->hasRole('admin');
        }

        return false;
    }
}
