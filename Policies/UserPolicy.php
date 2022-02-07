<?php

namespace App\Policies;

use App\Models\User;
use http\Env\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user,User $user_model)
    {
        return $user->id === $user_model->id ? Response::allow() : Response::deny();
    }

    public function reset(User $user,$token)
    {
        return $user->token === $token ? Response::allow() : Response::deny();
    }

}
