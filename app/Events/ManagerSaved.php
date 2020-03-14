<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Manager;

class ManagerSaved
{
    use Dispatchable, SerializesModels;

    public $manager;


    /**
     * Create a new event instance.
     *
     * @param  Manager $manager
     * @return void
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }
}
