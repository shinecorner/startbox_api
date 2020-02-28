<?php

namespace App\Policies;

use App\Models\Nogo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NogoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the nogo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nogo  $nogo
     * @return mixed
     */
    public function view(User $user, Nogo $nogo)
    {
        return true;
    }

    /**
     * Determine whether the user can create nogos.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the nogo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nogo  $nogo
     * @return mixed
     */
    public function update(User $user, Nogo $nogo)
    {
        return $user->isProvider() && $user->profileable_id === $nogo->provider_id;
    }

    /**
     * Determine whether the user can delete the nogo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nogo  $nogo
     * @return mixed
     */
    public function delete(User $user, Nogo $nogo)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the nogo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nogo  $nogo
     * @return mixed
     */
    public function restore(User $user, Nogo $nogo)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the nogo.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nogo  $nogo
     * @return mixed
     */
    public function forceDelete(User $user, Nogo $nogo)
    {
        return false;
    }
}
