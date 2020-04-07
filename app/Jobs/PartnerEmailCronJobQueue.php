<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Controllers\UtilityController;

class PartnerEmailCronJobQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rand;
    protected $cron_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($rand, $cron_type)
    {
        $this->rand      = $rand;
        $this->cron_type = $cron_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mac = new UtilityController;
        $ret = $mac->partnerEmailCronJob($this->rand, $this->cron_type);

        return $ret;
    }
}
