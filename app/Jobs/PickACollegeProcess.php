<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Controllers\GetStartedController;
use Illuminate\Support\Facades\Cache;

class PickACollegeProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Cache::forget(env('ENVIRONMENT') .'_getGetStartedThreeCollegesPins_'. $this->user_id);

        $gsc = new GetStartedController;
        $ret = $gsc->getGetStartedThreeCollegesPins($this->user_id, true);

        Cache::put(env('ENVIRONMENT') .'_getGetStartedThreeCollegesPins_'. $this->user_id, $ret, 240);
    }
}
