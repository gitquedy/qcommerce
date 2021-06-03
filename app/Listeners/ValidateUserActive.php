<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Validated;
use Illuminate\Validation\ValidationException;

class ValidateUserActive
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Validated $event)
    {
        if ($event->user->status) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => ['This account is no longer active. Please contact your shop owner.'],
        ]);
    }
}
