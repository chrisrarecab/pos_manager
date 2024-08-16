<?php

namespace App\Policies;

use App\Models\User;
use App\Models\superadmin_tool_flags;
use Illuminate\Auth\Access\Response;

class SuperadminToolFlagsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, superadmin_tool_flags $superadminToolFlags): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, superadmin_tool_flags $superadminToolFlags): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, superadmin_tool_flags $superadminToolFlags): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, superadmin_tool_flags $superadminToolFlags): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, superadmin_tool_flags $superadminToolFlags): bool
    {
        //
    }
}
