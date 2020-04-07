<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MandrillAutomationController;

class SendCollegeInquiryReport extends Command
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
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($report)
    {
        parent::__construct();

        $this->report = $report;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mac = new MandrillAutomationController();

        $mac->sendSingleDailyCollegeInquiryReport($this->report);
    }
}
