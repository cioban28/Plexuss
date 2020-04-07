<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\UsersPortalEmailEffortLog, App\User, App\EmailLogicHelper;
use App\Http\Controllers\MandrillAutomationController;
use Carbon\Carbon;

class EmailQueueProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $reply_email;
    protected $template_name;
    protected $params;
    protected $email;
    protected $subject;
    protected $user_id;
    protected $ab_test_id;
    protected $email_num;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reply_email, $template_name, $params, $email, $user_id = NULL, $subject = NULL, $ab_test_id = NULL, $email_num = NULL)
    {

        $this->reply_email   = $reply_email;
        $this->template_name = $template_name;
        $this->params        = $params;
        $this->email         = $email; 
        $this->subject       = $subject;
        $this->user_id       = $user_id;
        $this->ab_test_id    = $ab_test_id;
        $this->email_num     = $email_num;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (!isset($this->params)) ? $this->params = array() : NULL;

        if (isset($this->user_id)) {
            $user = User::find($this->user_id);

            $this->params['FNAME']          = ucwords(strtolower($user->fname));
            $this->params['LNAME']          = ucwords(strtolower($user->lname));
            $this->params['USER_IMAGE_URL'] = isset($user->profile_img_loc) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$user->profile_img_loc : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';
        }
        
        $mda = new MandrillAutomationController();
        $mda->newGeneralEmailSend($this->reply_email, $this->template_name, $this->params, $this->email, $this->subject, $this->user_id, $this->ab_test_id);

        $upeel = new UsersPortalEmailEffortLog;
        $arr   = array();
        isset($this->user_id)           ? $arr['user_id'] = $this->user_id : NULL;
        isset($this->template_name)     ? $arr['template_name'] = $this->template_name : NULL;
        isset($this->params['ro_id'])   ? $arr['ro_id'] = $this->params['ro_id'] : NULL;
        isset($this->params['company']) ? $arr['company'] = $this->params['company'] : NULL;
        $arr['params'] = json_encode($this->params);

        $update = $upeel->saveLog($arr);

        $this->updateEmailLogicHelper($this->user_id);
    }

    private function updateEmailLogicHelper($user_id){
        
        if ($this->email_num == 1 || $this->email_num == 2) {
            return "success";
        }

        $attr = array();
        $val  = array();

        $now = Carbon::now();
        $attr['user_id']                    = $user_id;
        $val['user_id']                     = $user_id;

        if ($this->email_num <= 15) {
            $profile_flow_last_template = 1;
            $tmp = EmailLogicHelper::on('bk')->where('user_id', $user_id)
                                             ->select('profile_flow_last_template')
                                             ->first();

            if (isset($tmp->profile_flow_last_template)) {
                $profile_flow_last_template = $tmp->profile_flow_last_template + 1;
            }
            
            $val['profile_flow_last_time_sent'] = $now;
            $val['profile_flow_last_template']  = $profile_flow_last_template;
        
        }else{
            $choose_last_template = 16;
            $tmp = EmailLogicHelper::on('bk')->where('user_id', $user_id)
                                             ->select('choose_last_template')
                                             ->first();

            if (isset($tmp->choose_last_template)) {
                $choose_last_template = $tmp->choose_last_template + 1;
            }
            
            $val['choose_last_time_sent'] = $now;
            $val['choose_last_template']  = $choose_last_template;

        }

        EmailLogicHelper::updateOrCreate($attr, $val);

        return "success";
    }
}
