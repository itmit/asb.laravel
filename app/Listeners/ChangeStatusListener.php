<?php

namespace App\Listeners;

use App\Events\ChangeStatus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeStatusListener
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
     * @param  ChangeStatus  $event
     * @return void
     */
    public function handle(ChangeStatus $event)
    {
        return 'SERGAY';
    }

    public function failed(ChangeStatus $event, $exception)
    {
        return 'fail';
    }
}
