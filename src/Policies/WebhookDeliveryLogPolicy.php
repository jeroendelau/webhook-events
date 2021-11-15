<?php

namespace StarEditions\WebhookEvent\Policies;

use StarEditions\WebhookEvent\Models\WebhookDeliveryLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebhookDeliveryLogPolicy
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
     * @param  \StarEditions\WebhookEvent\Models\WebhookDeliveryLog  $category
     * @return mixed
     */
    public function view($user, WebhookDeliveryLog $webhookDeliveryLog)
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
     * @param  \StarEditions\WebhookEvent\Models\WebhookDeliveryLog  $category
     * @return mixed
     */
    public function update($user, WebhookDeliveryLog $webhookDeliveryLog)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \StarEditions\WebhookEvent\Models\WebhookDeliveryLog  $category
     * @return mixed
     */
    public function delete($user, WebhookDeliveryLog $webhookDeliveryLog)
    {
        return true;
    }
}
