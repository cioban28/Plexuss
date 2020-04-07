<?php

namespace App\Listeners;

use App\Events\UserSignedup;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Storage;

class UserSignedupListener
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
     * @param  UserSignedup  $event
     * @return void
     */
    public function handle(UserSignedup $event)
    {
        //
        Storage::put('loginactivity.txt', json_encode($event));
    }
}
