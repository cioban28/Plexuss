<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ResetPasswordController;

class PasswordResetQueue extends Command
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

    protected $email;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        parent::__construct();

        $this->email = $email;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $rpc = new ResetPasswordController;
        $rpc->postRemind($this->email);
    }
}
