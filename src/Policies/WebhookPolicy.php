<?php

namespace StarEditions\WebhookEvent\Policies;

use StarEditions\WebhookEvent\Models\Webhook;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebhookPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny($user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \StarEditions\WebhookEvent\Models\Webhook  $webhook
     * @return mixed
     */
    public function view($user, Webhook $webhook)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create($user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \StarEditions\WebhookEvent\Models\Webhook  $webhook
     * @return mixed
     */
    public function update($user, Webhook $webhook)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \StarEditions\WebhookEvent\Models\Webhook  $webhook
     * @return mixed
     */
    public function delete($user, Webhook $webhook)
    {
        return true;
    }
}
