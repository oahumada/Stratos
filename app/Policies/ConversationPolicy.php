<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Determine if user can list conversations.
     */
    public function viewAny(User $user): bool
    {
        return (bool) $user->organization_id;
    }

    /**
     * Determine if user can view a specific conversation.
     * User must be in same organization AND either:
     * - Be a participant, OR
     * - Be admin/approver
     */
    public function view(User $user, Conversation $conversation): bool
    {
        // Multi-tenant check
        if (($user->organization_id ?? null) !== ($conversation->organization_id ?? null)) {
            return false;
        }

        // Admin/approver always can view
        if ($this->isAdmin($user)) {
            return true;
        }

        // Check if user (via people relationship) is a participant
        if ($user->people) {
            return $conversation->isParticipant($user->people->id);
        }

        return false;
    }

    /**
     * Determine if user can create conversations in their organization.
     */
    public function create(User $user): bool
    {
        return (bool) $user->organization_id && (bool) $user->people;
    }

    /**
     * Determine if user can send messages in a conversation.
     */
    public function sendMessage(User $user, Conversation $conversation): bool
    {
        // Multi-tenant check
        if (($user->organization_id ?? null) !== ($conversation->organization_id ?? null)) {
            return false;
        }

        if (! $user->people) {
            // Try to load the relationship if it's not already loaded
            $user->load('people');
            if (! $user->people) {
                return false;
            }
        }

        // Check if participant and can_send
        $participant = $conversation->participants()
            ->where('people_id', $user->people->id)
            ->first();

        if (! $participant) {
            return false;
        }

        return $participant->can_send && ! $participant->left_at;
    }

    /**
     * Determine if user can update a conversation (e.g., title, description).
     * Only creator or admin can update.
     */
    public function update(User $user, Conversation $conversation): bool
    {
        // Multi-tenant check
        if (($user->organization_id ?? null) !== ($conversation->organization_id ?? null)) {
            return false;
        }

        // Creator or admin
        return $user->id === $conversation->created_by || $this->isAdmin($user);
    }

    /**
     * Determine if user can delete/archive a conversation.
     * Only creator or admin can delete.
     */
    public function delete(User $user, Conversation $conversation): bool
    {
        // Multi-tenant check
        if (($user->organization_id ?? null) !== ($conversation->organization_id ?? null)) {
            return false;
        }

        // Creator or admin
        return $user->id === $conversation->created_by || $this->isAdmin($user);
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
