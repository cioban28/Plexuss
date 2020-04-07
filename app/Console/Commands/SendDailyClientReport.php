<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MandrillAutomationController;

class SendDailyClientReport extends Command
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

    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mac = new MandrillAutomationController;

        $mac->sendSingleDailyClientReport($this->client);
    }
}
