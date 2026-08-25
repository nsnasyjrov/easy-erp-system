<?php

namespace App\Policies;

use App\Enums\RoleCode;
use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    public function before(User $user, string $abilities): ?bool
    {
        return $user->role?->code === RoleCode::Admin? true : null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role?->code === RoleCode::Manager;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        if($user->role?->code === RoleCode::Manager && $user->responsible_manager_id === $user->id) {
            return True;
        }

        /**
         * Надо будет в любом случае расширять - будет сценарий того, что пользователь заведен для клиента, являющегося
         * Individual, для Employee видимость нужных ему клиентов.
         */

        return False;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role?->code === RoleCode::Manager;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        if($user->role?->code === RoleCode::Manager && $user->responsible_manager_id === $user->id) {
            return True;
        }

        return False;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return false;
    }

    public function assignResponsibleManager(User $user, Client $client): bool
    {
        return False;
    }
}
