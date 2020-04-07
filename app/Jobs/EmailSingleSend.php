<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Controllers\MandrillAutomationController;

class EmailSingleSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reply_email;
    protected $template_name;
    protected $params;
    protected $email;
    protected $subject;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reply_email, $template_name, $params, $email, $subject = NULL)
    {
        $this->reply_email   = $reply_email;
        $this->template_name = $template_name;
        $this->params        = $params;
        $this->email         = $email; 
        $this->subject       = $subject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mda = new MandrillAutomationController();
        $mda->generalEmailSend($this->reply_email, $this->template_name, $this->params, $this->email, $this->subject);
    }
}
