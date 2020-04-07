    <div class="row">
        <div class='column small-12 small-center text-left'>
            <div class="row">
                <div class="column small-12">
                    <span class="icon-arrow account_setting fs22 mobile-fs20 pl40 " style="background-position: 0px -25px;">Account Settings</span>
                </div>
            </div>
        </div>
    </div>
    <div class="bdr-b-dot mt10 mr10 show-for-small-only"></div>
    
    <div class='row' align="center">
        <div class="column small-12 small-center">
            <div class="row">
                <div class="column small-12">
                    <p class="c79 fs16 mt15 show-for-medium-up text-left">Change your Password<br />
                        <span class="c79 fs12 mobile-pr25" style="display:block">Your password should contain 8-13 letters and numbers, starts with a letter and contains at least one number
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    

    <div class='row'>
        <div class="column small-12" align="center">
            <div class="row">
                <div class="column small-12">
                    <p class="c-black fs18 mt15 show-for-small-only">Change your Password<br /><span class="c79 fs12 mobile-pr25 mt10" style="display:block">Your password should contain 8-13 letters and numbers, Starts with a letter and Contains at least one number</span></p>
                </div>
            </div>
        </div>
    </div>
    
    
    
    
   {{ Form::open(array('url' => "setting/accountSetting/",'method' => 'POST', 'name'=>'accountSettingsChangePass','id' => 'accountSettingsChangePass','data-abide'=>'ajax')); }}

        <div class="row">
            <div class="columns small-12">
                <div class='row'>
                    <div class="large-3 medium-5 small-5 columns c79 fs12 f-bold">Current Password</div>
                    <div class="large-9 medium-7 small-7 columns c79 mobile-pl20">{{ Form::password('old_pass', array('id'=>'old_pass','placeholder'=>'Old Password', 'class'=>'pwd-txt mobile-p','required', 'pattern' => 'passwordpattern','style'=>'height:32px !important;padding-left:10px !important;')) }}
                    <small class="error">Please enter current password.</small>
                    </div>
                </div>
            </div>
        </div>        
        <div class="row" >
            <div class="columns medium-12">
                <div class='row'>
                    <div class="large-3 medium-5 small-5 columns c79 fs12 f-bold">New Password</div>
                    <div class="large-9 medium-7 small-7 columns c79 mobile-pl20">{{ Form::password('new_pass', array('id'=>'new_pass','placeholder'=>'New Password', 'class'=>'pwd-txt mobile-p','required', 'pattern' => 'passwordpattern','style'=>'height:32px !important;padding-left:10px !important;')) }}
                    <small class="error passError">*Please enter a valid password with these requirements:<br/>
                    <ul style="margin:0px !important;">
                    <li style="margin:0px !important;">8-13 letters and numbers </li>
                    <li style="margin:0px !important;">Starts with a letter</li>
                    <li style="margin:0px !important;">Contains at least one number</li>
                    </ul>
                    </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" >
            <div class="columns medium-12">
                <div class='row'>
                    <div class="large-3 medium-5 small-5 columns c79 fs12 f-bold">Verify Password</div>
                    <div class="large-9 medium-7 small-7 columns c79 mobile-pl20">{{ Form::password('verify_pass', array('id'=>'verify_pass','placeholder'=>'Verify Password', 'class'=>'pwd-txt mobile-p','required', 'pattern' => 'passwordpattern','data-equalto'=>'new_pass','style'=>'height:32px !important;padding-left:10px !important;')) }}
                    <small class="error">Passwords must match.</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="column small-12 small-text-center">
                <div class="row">
                    <div class="small-12 column small-text-center medium-text-right">
                       {{ Form::submit('Save New Password', array('class'=>'button org-btn','style'=>'padding:0px 14px;'))}} 
                    </div>
                </div>
            </div>
        </div>
    {{Form::close()}}