<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\InviteController;

class SendInviteEmail extends Command
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

    protected $data;

    protected $person;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($data, $person)
    {
        parent::__construct();

        $this->data = $data;
        $this->person = $person;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        InviteController::sendSingleReferralInvite($this->data, $this->person);
    }
}
