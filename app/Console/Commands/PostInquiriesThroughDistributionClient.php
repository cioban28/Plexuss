<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\DistributionController;

class PostInquiriesThroughDistributionClient extends Command
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

    protected $ro_id;
    protected $college_id;
    protected $user_id;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($ro_id, $college_id, $user_id)
    {
        parent::__construct();

        $this->ro_id      = $ro_id;
        $this->college_id = $college_id;
        $this->user_id    = $user_id;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dc = new DistributionController();
        $dc->postInquiriesWithQueue($this->ro_id, $this->college_id, $this->user_id);
    }
}
