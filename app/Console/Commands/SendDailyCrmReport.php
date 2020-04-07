<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MandrillAutomationController;

class SendDailyCrmReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $report;

    protected $right_now_date;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($report, $right_now_date)
    {
        parent::__construct();

        $this->report = $report;
        $this->right_now_date = $right_now_date;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mac = new MandrillAutomationController;

        $mac->sendSingleDailyCRMReport($this->report, $this->right_now_date);
    }
}
