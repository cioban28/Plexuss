<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\EmailSuppressionList;

class AddToEmailSuppressionList extends Command
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

    protected $attr;
    protected $val;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($attr, $val)
    {
        parent::__construct();

        $this->attr = $attr;
        $this->val  = $val;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        EmailSuppressionList::updateOrCreate($this->attr, $this->val);
    }
}
