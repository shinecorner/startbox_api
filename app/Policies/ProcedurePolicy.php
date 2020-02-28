<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Procedure;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcedurePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the procedure.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Procedure  $procedure
     * @return mixed
     */
    public function view(User $user, Procedure $procedure)
    {
        return true;
    }

    /**
     * Determine whether the user can create procedures.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the procedure.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Procedure  $procedure
     * @return mixed
     */
    public function update(User $user, Procedure $procedure)
    {
        return $user->isProvider() && $user->profileable_id === $procedure->provider_id;
    }

    /**
     * Determine whether the user can delete the procedure.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Procedure  $procedure
     * @return mixed
     */
    public function delete(User $user, Procedure $procedure)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the procedure.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Procedure  $procedure
     * @return mixed
     */
    public function restore(User $user, Procedure $procedure)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the procedure.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Procedure  $procedure
     * @return mixed
     */
    public function forceDelete(User $user, Procedure $procedure)
    {
        return true;
    }
}
