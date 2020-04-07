<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\UtilityController;

class PostAutoPortalEmail extends Command
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

    protected $key;
    protected $template_name;
    protected $ro_name;
    protected $this_ro;
    protected $forced_college_id;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($key, $template_name, $ro_name, $this_ro, $forced_college_id)
    {
        parent::__construct();

        $this->key               = $key;
        $this->template_name     = $template_name;
        $this->ro_name           = $ro_name;
        $this->this_ro           = $this_ro;
        $this->forced_college_id = $forced_college_id;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $uc = new UtilityController();
        $uc->autoPortalEmailQueuePart($this->key, $this->template_name, $this->ro_name, $this->this_ro, $this->forced_college_id);
    }
}
