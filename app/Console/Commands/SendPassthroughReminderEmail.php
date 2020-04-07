<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MandrillAutomationController;

class SendPassthroughReminderEmail extends Command
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

    protected $person;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($person)
    {
        parent::__construct();
        $this->person = $person;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mac = new MandrillAutomationController;

        $mac->sendSinglePassthroughReminder($this->person);
    }
}
