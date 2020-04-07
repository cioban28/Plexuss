@extends('private.setting.master')
<?php
    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';
    // exit();
    // dd($data);
?>
@section('sidebar')

    <div class="row plex-settings-sidebar collapse">
        <div class="column small-12">

            <div class="row sidebar-settings-title">
                <div class="column small-11 small-centered">
                    Settings
                </div>
            </div>

            <ul class="side-nav settings-sidebar-ul">
                @if( isset($active_tab) && empty($active_tab) )
                <li class="active">
                @else
                <li>
                @endif
                    <a href="#plex-acctsettings" class="settings-menu-item">
                        <div class="row">
                            <div class="column medium-3 large-2">
                                <img class="settings-menu-icon" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/setting.png">
                                <img class="settings-menu-icon activated" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/setting-white.jpg">
                            </div>
                            <div class="column medium-9 large-10">
                                Change Password
                            </div>
                        </div>
                    </a>
                </li>

                @if( !isset($is_organization) || $is_organization == 0 )
                    @if( isset($active_tab) && $active_tab == 'email' )
                    <li class="active">
                    @else
                    <li>
                    @endif
                        <a href="#plex-emailNotifications" class="settings-menu-item">
                            <div class="row">
                                <div class="column medium-3 large-2">
                                    <img class="settings-menu-icon" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/email.png" style="margin-top: 4px;">
                                    <img class="settings-menu-icon activated" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/email-white.png" style="margin-top: 4px;">
                                </div>
                                <div class="column medium-9 large-10">
                                    Email Notifications
                                </div>
                            </div>
                        </a>
                    </li>

                    @if( isset($active_tab) && $active_tab == 'text' )
                    <li class="active">
                    @else
                    <li>
                    @endif
                        <a href="#plex-textNotifications" class="settings-menu-item">
                            <div class="row">
                                <div class="column medium-3 large-2">
                                    <img class="settings-menu-icon" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/text.png" style="margin-left: 4px;">
                                    <img class="settings-menu-icon activated" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/text-white.png" style="margin-left: 4px;">
                                </div>
                                <div class="column medium-9 large-10">
                                    Text Notifications
                                </div>
                            </div>
                        </a>
                    </li>

                    @if( isset($active_tab) && $active_tab == 'invite' )
                    <li class="active">
                    @else
                    <li>
                    @endif
                        <a href="#plex-invitefriends" class="settings-menu-item">
                            <div class="row">
                                <div class="column medium-3 large-2">
                                    <img class="settings-menu-icon" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/invite-friends-invitation-icon_black.png">
                                    <img class="settings-menu-icon activated" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/invite-friends-invitation-icon_whitepng.png">
                                </div>
                                <div class="column medium-9 large-10">
                                    Invite Friends
                                </div>
                            </div>
                        </a>
                    </li>
                @endif

                <!--
                @if( $super_admin == 1)
                <li @if( isset($active_tab) && $active_tab == 'manageusers' ) class="active" @endif >
                    <a href="#plex-manageusers" class="settings-menu-item">
                        <div class="row">
                            <div class="column medium-3 large-2">
                                <img class="settings-menu-icon" src="/images/setting/manager-users.png">
                                <img class="settings-menu-icon activated" src="/images/setting/manager-users-white.png">
                            </div>
                            <div class="column medium-9 large-10">
                                Manage Users
                            </div>
                        </div>
                    </a>
                </li>
                @endif
                -->

                @if(isset($is_aor) && $is_aor == 0)
                @if( isset($active_tab) && $active_tab == 'billing' )
                <li class="active">
                @else
                <li>
                @endif
                    @if( isset($is_organization) && $is_organization == 1 )
                    <a class="settings-menu-item" href="#plex-billing">
                    @else
                    <a class="settings-menu-item" href="#plex-student-billing">
                    @endif
                        <div class="row">
                            <div class="column medium-3 large-2">
                                <img class="settings-menu-icon" src="/images/setting/manager-users.png">
                                <img class="settings-menu-icon activated" src="/images/setting/manager-users-white.png">
                            </div>
                            <div class="column medium-9 large-10">
                                Billing
                            </div>
                        </div>
                    </a>
                </li>
                @endif
                @if(isset($is_gdpr) && $is_gdpr == true)
                @if( isset($active_tab) && empty($active_tab) == 'data_preferences' )
                <li class="active">
                @else
                <li>
                @endif
                    <a href="#plex-data_preferences" class="settings-menu-item">
                        <div class="row">
                            <div class="column medium-3 large-2">
                                <img class="settings-menu-icon" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/setting.png">
                                <img class="settings-menu-icon activated" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/setting-white.jpg">
                            </div>
                            <div class="column medium-9 large-10">
                                Data Preferences
                            </div>
                        </div>
                    </a>
                </li>
                @endif
            </ul>

        </div>
    </div>

@stop

@section('content')
<?php
    $hide_chg_pass = '';
    $hide_invite = '';
    $choose_contacts_subsection = '';
    $find_contacts_subsection = '';

    $arr = array();
    $arr['setting_section']['invite'] = 'hide-this-section';
    $arr['setting_section']['setting'] = 'hide-this-section';
    $arr['setting_section']['manageusers'] = 'hide-this-section';
    $arr['setting_section']['billing'] = 'hide-this-section';
    $arr['setting_section']['email'] = 'hide-this-section';
    $arr['setting_section']['text'] = 'hide-this-section';
    $arr['setting_section']['data_preferences'] = 'hide-this-section';

    //hide, show depending on what top nav settings tab was clicked
    if( isset($active_tab) ){
       $arr['setting_section'][$active_tab] = '';
    }

    //hide invite sub section depending on if user has allowed gmail(etc) to access contacts and has returned to settings with data
    if( isset($contactList) && !empty($contactList) ){
        $find_contacts_subsection = 'hide-sub-section';
    }else{
        $choose_contacts_subsection = 'hide-sub-section';
    }
?>

    <!-- \\\\\\\\\\\\\\\ acct settings section - start //////////////// -->
    <div class="row collapse plex-acct-settings-section settings-section {{$arr['setting_section']['setting']}}" id="plex-acctsettings">
        <div class="column small-12">

            <!-- account settings section title -->
            <!--<div class="row">
                <div class="column small-11 small-centered">
                    <div class="row change-pass-section-title" data-equalizer>
                        <div class="column small-2 medium-1 icon-arrow" data-equalizer-watch></div>
                        <div class="column small-10 medium-11" data-equalizer-watch>Account Settings</div>
                    </div>
                </div>
            </div>-->

            <div class="row password-change-form">
                <div class="column small-11 small-centered">

                    <!-- account settings section instructions -->
                    <div class="row change-pass-title-descrip-row">
                        <div class="column small-12">Change password</div>
                        <br />
                        <div class="column small-12">Your password should contain 8-13 letters and numbers, starts with a letter and contains at least one number</div>
                    </div>
					@if($success_msg!='')<small style="color:green" class="success">{{ $success_msg }}.</small>@endif
					@if($error_msg!='')<small class="error">{{$error_msg}}</small>@endif
					<!-- account settings section password change form -->
                   {{ Form::open(array('url' => "settings",'method' => 'POST', 'name'=>'accountSettingsChangePass','id' => 'accountSettingsChangePass')) }}
				     {{ Form::hidden('action',"change_pass")}}
				    <!-- old password -->
                    <div class='row'>
                        <div class="column small-12 medium-3">
                            {{Form::label('old_pass', 'Old Password')}}
                        </div>

                        <div class="column small-12 medium-9">
                            {{ Form::password('old_pass', array('id'=>'old_pass','placeholder'=>'', 'class'=>'','required')) }}
                            <small class="error" style="display:none;">Please enter current password.</small>
                        </div>
                    </div>

                    <!-- new password -->
                    <div class='row'>
                        <div class="column small-12 medium-3">
                            {{Form::label('new_pass', 'New Password')}}
                        </div>

                        <div class="column small-12 medium-9">
                            {{ Form::password('new_pass', array('id'=>'new_pass','placeholder'=>'', 'class'=>'','required', 'pattern' => '^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}')) }}
                            <small class="error passError" style="display:none;">*Please enter a valid password with these requirements:<br/>
                                <ul>
                                    <li>8-13 letters and numbers</li>
                                    <li>Starts with a letter</li>
                                    <li>Contains at least one number</li>
                                </ul>
                            </small>
                        </div>
                    </div>

                    <!-- verify password -->
                    <div class='row'>
                        <div class="column small-12 medium-3">
                            {{Form::label('verify_pass', 'Verify Password')}}
                        </div>

                        <div class="column small-12 medium-9">
                            {{ Form::password('verify_pass', array('id'=>'verify_pass','placeholder'=>'', 'class'=>'','required', 'pattern' => '^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}','data-equalto'=>'new_pass')) }}
                            @if($pass_not_match!='')<small class="error">{{$pass_not_match}}</small>@endif
                        </div>
                    </div>

                    <!-- submit new password -->
                    <div class="row">
                        <div class="column small-12 small-text-center medium-6 medium-offset-6 medium-text-right large-4 large-offset-8">
                           {{ Form::submit('Save New Password', array('class'=>'button submit_new_pass_btn'))}}
                        </div>
                    </div>
                    {{Form::close()}}

                    <!-- deactivate your account -->
                    <div class="row">
                        <div class="column small-12 medium-12 large-12 text-right">
                            <a href="#" data-reveal-id="deactivate-user" class="deactivate-account">Delete my account. We'll be sad to see you go!</a>
                        </div>
                    </div>

                    <div id="deactivate-user" data-reveal class="reveal-modal row" aria-hidden="true" role="dialog" aria-labelledby="">
                        <div class="column large-12 text-right">
                            <a class="close-reveal-modal" aria-label="Close">&#215;</a>
                        </div>
                        <div class="column large-3">
                            <img src="/images/setting/sad-to-see-you-go.png">
                        </div>
                        <div class="column large-9">
                            <div class="row">
                                <span class="deactivate-acct">We are sad to see you go</span>
                            </div>
                            <div class="row">
                                <span class="deactivate-suggestion">Before you go, what could we be doing better?</span>
                            </div>
                            <div class="row">
                                {{Form::textarea('deactivate_suggestion', '', array('class' => 'deactivate-suggestion-textarea', 'size' => '30x5'))}}
                            </div>
                            <div class="row">
                                <span class="backup-acct">Please note that this will permanently delete your account and all data associated with it will be lost. If you wish to return you will have to create a new one. </span>
                            </div>
                            <div class="row">
                                <a href="#" class="button deactivate-btn" data-reveal-id="delete-user-acct">Delete my Account</a>
                                <a class="close-reveal-modal button secondary" aria-label="Close">Cancel</a>
                            </div>
                        </div>
                        <div id="delete-user-acct" data-reveal class="reveal-modal row text-center" aria-hidden="true" role="dialog">
                            {{Form::open(
                                array('action' => 'AjaxController@deleteUserAccount',
                                    'method' => 'POST',
                                    'data-abide')
                            )}}
                            {{Form::hidden('deactivate_suggestion', null)}}
                            <div class="row">Are you sure you want to delete your account?</div>
                            <div class="row delete-action">
                                {{Form::submit('Delete my Account', array('class' => 'button deactivate-btn'))}}
                                <a class="close-reveal-modal button secondary" aria-label="Close">Cancel</a>
                            </div>
                            {{Form::close() }}
                        </div>
                    </div>

                </div>
            </div>


        </div>
    </div>
    <!-- ///////////////////// acct settings section - end \\\\\\\\\\\\\\\\\\\\\\\\ -->



    <!-- \\\\\\\\\\\\\\\\\\\\ invite friends section - start //////////////////////// -->
    <div class="row collapse plex-invite-friends-section settings-section {{$arr['setting_section']['invite']}}" id="plex-invitefriends">
        <div class="column small-12">

            <!-- invite friends section title row -->
            <div class="row">
                <div class="column small-11 small-centered">
                    <div class="row collapse invite-friends-title">
                        <div class="column small-1 medium-1">
                            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/invite-friends-invitation-icon_black.png" alt="">
                        </div>
                        <div class="column small-11 medium-5">Invite Friends</div>
                        <div class="column medium-6 text-right show-for-medium-up">
                            <div class="manage-imported-contacts-link">Manage imported contacts</div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- invite friends opening - start -->
            <div class="row collapse invite-friends-opening invite-sub-section {{$find_contacts_subsection}}">
                <div class="column small-11 small-centered">

                    <!-- invite friends description row -->
                    <div class="row help-friends-msg-row">
                        <div class="column small-12 text-center medium-2">
                            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/invite-friends-figures-giving.gif" alt="">
                        </div>
                        <div class="column small-12 small-text-center medium-text-left medium-10 invite-friends-msg">Help your friends find colleges</div>
                    </div>

                    <!-- \\\\\\ invite contact from social networks row ////// -->
                    <div class="row collapse invite-friends-options-container">
                        <div class="column small-12">

                            <div class="row">

                                <!-- invite option box - start -->
                                <a href="/googleInvite" class="invite-from-options">
                                    <div class="column small-6 medium-3">
                                        <div class="row invite-from-box">
                                            <div class="column small-11 small-centered text-center">
                                                <div class="gmail" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/import-contacts-sprite-sheet_gray.png, (default)]"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="column small-12 invite-from-name text-center">Gmail</div>
                                        </div>
                                    </div>
                                </a>
                                <!-- invite option box - end -->

                                <!-- invite option box - start
                                <a href="/yahooInvite" class="invite-from-options">
                                    <div class="column small-6 medium-3">
                                        <div class="row invite-from-box">
                                            <div class="column small-11 small-centered text-center">
                                                <div class="yahoo" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/import-contacts-sprite-sheet_gray.png, (default)]"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="column small-12 invite-from-name text-center">Yahoo</div>
                                        </div>
                                    </div>
                                </a>-->
                                <!-- invite option box - end -->

                                <!-- invite option box - start -->
                                <a href="/microsoftInvite" class="invite-from-options" data-connect-with="invite-from-contact-list">
                                    <div class="column small-6 medium-3">
                                        <div class="row invite-from-box">
                                            <div class="column small-11 small-centered text-center">
                                                <div class="outlook" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/import-contacts-sprite-sheet_gray.png, (default)]"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="column small-12 invite-from-name text-center">Outlook</div>
                                        </div>
                                    </div>
                                </a>
                                <!-- invite option box - end -->

                                <!-- invite option box - start -->
                                <a href="/microsoftInvite" class="invite-from-options">
                                    <div class="column small-6 medium-3 end">
                                        <div class="row invite-from-box">
                                            <div class="column small-11 small-centered text-center">
                                                <div class="hotmail" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/import-contacts-sprite-sheet_gray.png, (default)]"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="column small-12 invite-from-name text-center">Hotmail</div>
                                        </div>
                                    </div>
                                </a>
                                <!-- invite option box - end -->
                            </div>

                        </div>
                    </div>

                    <!-- submit individual email row -->
                    {{Form::open(array('url' => '','method' => 'POST','data-abide'))}}
                    <div class="row send-email-add-row">
                        <div class="column small-12">
                            {{Form::label('id', 'Send to their email...', array('class'=>'invite-friends-msg'))}}
                        </div>
                    </div>

                    <div class="row individual-email-submission-row">
                        <div class="column small-12">
                            <div class="row collapse">
                                <div class="column small-9 medium-10">
                                    {{Form::text('email', null, array('placeholder'=>'Add friends\' email addresses', 'class'=>'individual-email-value', 'pattern'=>'email'))}}
                                    <small class="error">Invalid email</small>
                                </div>
                                <div class="column small-3 medium-2">
                                    <div class="button postfix invite-submit-email-btn">Send</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{Form::close()}}

                    <!-- ajax loader -->
                    <div id="submit-individual-invite-ajax-loader" class="row text-center">
                        <div class="column small-12">
                            <svg width="70" height="20">
                                <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
                                    <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                                    <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                                    <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                                    <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                                </rect>
                                <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
                                    <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                                    <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                                    <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                                    <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                                </rect>
                                <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
                                    <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                                    <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                                    <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                                    <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                                </rect>
                            </svg>
                        </div>
                    </div>

                    <!-- preview invite row -->
                    <div class="row help-friends-msg-row">
                        <div class="column small-12 medium-4 small-centered text-center">
                            <div class="preview-invite-hover-arrow"></div>
                            <span class="preview-invite">
                                Preview invite
                            </span>
                        </div>
                    </div>

                    <!-- button to display page of all contacts the user has already imported -->
                    <div class="row hide-for-medium-up mobile-manage-contacts-link">
                        <div class="column small-12 text-center">
                            <div class="manage-imported-contacts-link">Manage imported contacts</div>
                        </div>
                    </div>

                    <!-- \\\\\\\\\\\\\\\\ invite friends email modal - start ////////////////// -->
                    <div id="email-invite-preview-modal" class="row invite-preview-modal">
                        <div class="column small-12">

                            <div class="row collapse invite-preview-modal-inner">
                                <div class="column small-12">

                                    <div class="row">
                                        <div class="column small-12 text-center preview-logo">
                                            <img src="/images/plexuss_logo.png" alt="">
                                        </div>
                                    </div>

                                    <div class="row collapse email-invite-plex-nav-row">
                                        <div class="column small-2 large-offset-1 text-center">Home</div>
                                        <div class="column small-2 text-center">Profile</div>
                                        <div class="column small-2 text-center">Portal</div>
                                        <div class="column small-4 medium-3 large-2 text-center">Find Colleges</div>
                                        <div class="column small-2 text-center end">News</div>
                                    </div>

                                    <div class="row">
                                        <div class="column small-12 text-center been-invited-image">
                                            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/email-image-youre-invited.png" alt="">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="column small-12 text-center try-plex-invite-msg">
                                            You have been invited to try Plexuss
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="column small-10 medium-4 small-centered text-center check-it-out-btn">
                                            Check it out!
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- \\\\\\\\\\\\\\\\ invite friends email modal - start ////////////////// -->

                </div>
            </div>
            <!-- invite friends opening - end -->



            <!-- invite friends manage contacts - start -->
            <div class="row collapse invite-sub-section {{$choose_contacts_subsection}}" id="invite-from-contact-list">
                <div class="column small-12">

                    <div class="row collapse">
                        <div class="column small-11 small-centered">

                            <!-- section title -->
                            <div class="row invite-via-title-link">
                                <div class="column small-12">
                                    <a href="#" class="back-to-previous-section"><span style="font-size: 30px;">&#8249;</span> Invite from Contacts</a>
                                </div>
                            </div>



                                {{Form::open(array('url' => '','method' => 'POST','data-abide'))}}
                                <!-- directions -->
                                <div class="row">
                                    <div class="column small-12 choose-contacts-description">
                                        Choose the contacts you would like to invite to Plexuss
                                    </div>
                                </div>

                                <!-- select all checkbox -->
                                <div class="row select-all-users-checkbox-row">
                                    <div class="column small-8 medium-6">
                                        {{Form::checkbox('name', 'value', false, array('id' => 'select_all_from_fb', 'class'=>'select_all_invites_checkbox invite-chkbox'))}}
                                        {!! Form::label('select_all_from_fb', 'Select All') !!}
                                        <span class="contact-list-count"> &nbsp;</span>
                                    </div>
                                    <div class="column small-4 medium-6 small-text-right contacts-currently-selected">
                                        <span class="selected-counter">0</span> selected
                                    </div>
                                </div>

                                <!-- ajax loader -->
                                <div id="manage-contacts-ajax-loader" class="row text-center">
                                    <div class="column small-12">
                                        <svg width="70" height="20">
                                            <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
                                                <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                                                <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                                                <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                                                <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                                            </rect>
                                            <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
                                                <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                                                <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                                                <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                                                <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                                            </rect>
                                            <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
                                                <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                                                <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                                                <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                                                <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                                            </rect>
                                        </svg>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="column small-12">

                                        <div class="row invite-friends-list-row">
                                            <div class="column small-12 invite-friends-list-container-col">

                                                <!-- foreach contact, display name and email as a checkbox item -->
                                                @if( isset($contactList) && !empty($contactList) )
                                                @foreach( $contactList as $contact )
                                                <div class="row invite-user-choice-row">
                                                    <div class="column small-12 invite-user-choice">

                                                        <div class="row">
                                                            <div class="column small-1">
                                                                {{Form::checkbox('name', 'value', false, array('id' => $contact['invite_email'], 'class'=>'invite-friend-option-checkbox invite-chkbox', 'data-contacts-name'=>$contact['invite_name']))}}
                                                            </div>
                                                            <div class="column small-11">
                                                                <label for="{{$contact['invite_email']}}">

                                                                    @if( empty($contact['invite_name']) )
                                                                        <span class="invite-name">(No name)</span>
                                                                    @else
                                                                        <span class="invite-name">{{$contact['invite_name']}}</span>
                                                                    @endif

                                                                    &nbsp;&nbsp;&nbsp;&nbsp;

                                                                    @if( !empty($contact['invite_email']) )
                                                                        <span>{{$contact['invite_email']}}</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                @endforeach
                                                @else
                                                    <div class="row no-contacts-yet-msg-row text-center">
                                                        <div class="column small-12 no-contacts-yet-msg">
                                                            You haven't imported contacts yet!
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- send invite error message -->
                                <div class="row send-invites-error-msg">
                                    <div class="column small-12 text-right">
                                        Please choose at least one contact from the list to send invites
                                    </div>
                                </div>

                                <!-- send invite error message -->
                                <div class="row mailing-invites-error-msg">
                                    <div class="column small-12 text-right">
                                        Oops! Something went wrong mailing the invites. One or some of these contacts may have already received an invite.
                                    </div>
                                </div>

                                <!-- send invites submit button -->
                                <div class="row send-invites-btn-row">
                                    <div class="column small-12 medium-5 medium-offset-7 large-3 large-offset-9 text-center send-invites-btn">
                                        Send invites
                                    </div>
                                </div>
                                {{Form::close()}}





                            <div class="list-of-contacts-container-ajax"></div>

                        </div>
                    </div>

                </div>
            </div>
            <!-- invite friends connect with gmail - end -->

        </div>
    </div>
    <!-- ////////////////////////// invite friends section - end \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ -->

    <!-- \\\\\\\\\\\\\\\\\\\\ Manage Users section - start //////////////////////// -->
    <div class="row collapse plex-manage-users-section settings-section {{$arr['setting_section']['manageusers']}}" id="plex-manageusers">
        <div class="column large-12">

            <!-- manage users section title row -->
            <div class="row">
                <div class="column small-11 small-centered">
                    <div class="row collapse manage-users-title">
                        <div class="column small-11 medium-5">Manage Users</div>
                        <div class="column medium-2 text-right show-for-medium-up">
                            <div class="manage-portals-link">Manage Portals</div>
                        </div>
                    </div>
                </div>
            </div>
            @if(isset($users))
            <div class="row users-container">
                @foreach($users as $user)
               
                <div class="column small-6 medium-4 large-3 text-center users-acct"
                data-user='{ "user_id" : {{$user["user_id"]}}, "fname" : "{{$user["fname"]}}" , "lname" : "{{$user["lname"]}}" , "profile_img_loc" : "{{$user["profile_img_loc"]}}", "super_admin" : "{{$user["super_admin"]}}" }'>
                    <div class="users-bg">
                        @if(isset($user['super_admin']) && $user['super_admin'] == 1)
                            <img class="left" src="/images/setting/super-admin.png">
                        @endif
                        @if(isset($user['profile_img_loc']))
                            <img class="users-profile-img" src="{{$user['profile_img_loc']}}"/>
                            <img class="right hide" src="/images/setting/cogwheel-small.png" style="z-index: 2; top: -123px; position: relative;"/>
                        @else
                            <img class="right hide" src="/images/setting/cogwheel-small.png"/>
                        @endif

                    </div>
                    <div class="row users-name">
                        <div>{{$user['fname']. ' '. $user['lname']}}</div>

                        @if(isset($user['portal_info']) && !empty($user['portal_info']))
                        <div class="row users-access">
                            <?php $cnt = 0; ?>
                            @foreach($user['portal_info'] as $pi)
                                <?php $cnt += 1; ?>
                                @if($cnt >= 3)
                                <div class="extra" data-portal-info="{{$pi->name}}">{{$pi->name}}</div>
                                @else
                                <div data-portal-info="{{$pi->name}}">{{$pi->name}}</div>
                                @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                <div class="column small-6 medium-4 large-3 text-center users-acct">
                    <div class="users-bg">
                    </div>
                    <div class="row users-name">
                        <div class="row users-access">
                            <div>Add Users</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        {{ Form::open( array('data-abide' => 'ajax', 'id' => 'user-information-update' ) ) }}
        {{ Form::hidden('targetted_user_id', null, array('class' => 'user_information_user_id')) }}
        <div class="column large-12 modify-users">
            <div class="row modify-title">
                Modify Users
            </div>
            <div class="row edit-title">
                Edit user information below
            </div>

            <div class="row user-img">

            </div>

            <div class="row user-img-upload">
                <a class="button updateTargetProfilePic">
                    Upload Photo
                </a>
            </div>

            <div class="row user-info">
                <div class="column large-12">
                    <div class="row">
                        <div class="column large-3">
                            First Name
                        </div>
                        <div class="column large-5">
                            {{ Form::text('fname', null, array('placeholder' => 'First Name' ,'class' => 'fname')) }}
                        </div>
                        <div class="column large-4">
                        </div>
                    </div>
                </div>
                <div class="column large-12">
                    <div class="row">
                        <div class="column large-3">
                            Last Name
                        </div>
                        <div class="column large-5">
                            {{ Form::text('lname', null, array('placeholder' => 'Last Name' ,'class' => 'lname')) }}
                        </div>
                        <div class="column large-4">
                        </div>
                    </div>
                </div>
                <div class="column large-12">
                    <div class="row">
                        <div class="column large-3">
                            Portals
                        </div>
                        <div class="column large-5 portal-collections">

                        </div>
                        <div class="column large-4">
                        </div>
                    </div>
                </div>

                <div class="column large-12 updated-user-access">
                        <div class="row">
                            {{Form::radio('users-access', 'Admin', null , array('id' => 'updated-admin') )}}
                            {{Form::label('updated-admin', 'Admin')}}
                            <span class="mytooltip" title="">?</span>
                        </div>
                        <div class="row">
                            {{Form::radio('users-access', 'User', null, array('id' => 'updated-user') )}}
                            {{Form::label('updated-user', 'User')}}
                            <span class="mytooltip" title="">?</span>
                        </div>
                </div>

            </div>

            <div class="row portal-decision">
                <div class="column large-3">
                    <a href="#" class="button secondary deleteUser" data-reveal-id="confirmDelUser">
                        Delete User
                    </a>
                </div>
                <div class="column large-offset-2 large-2">
                    <a class="button secondary" onClick="Plex.settings.cancerlSetting($(this));">Cancel</a>
                </div>
                <div class="column large-3 save-settings">
                    <a class="button" onClick="Plex.settings.saveSetting($(this));">Save Settings</a>
                </div>
                <div class="column large-4">
                </div>
            </div>

            <div class='reveal-modal small' id="confirmDelUser" data-reveal aria-hidden="true" role="dialog">
                <div class="row">
                    <div class="column">
                        <a class="close-reveal-modal right" aria-label="Close">&#215;</a>
                    </div>
                </div>
                <div class="row">
                    Do you really want to delete this user?
                </div>

                <div class="row">
                    <div class="column small-6 close-reveal-modal">
                        <div class="button secondary">
                            Cancel
                        </div>
                    </div>
                    <div class="column small-6" onClick="Plex.settings.delUserFromOrg($('.portal-decision'));">
                        <div class="button">
                            confirm delete
                        </div>
                    </div>
                </div>

            </div>

        </div>
        {{ Form::close() }}
        {{ Form::open( array('data-abide' => 'ajax', 'id' => 'user-information-update' ) ) }}
        <div class="column large-12 add-users-step1">
            <div class="row">
                <div class="column large-4 add-users-step1-title">
                    Add Users
                </div>
                <div class="column small-12 large-2 right">
                    Step 1 of 2
                </div>
            </div>
            <div class="row add-users-memo">
                Add people to your college using their email addresses, use commas to separate mutiple addresses.
            </div>

            <div class="row add-users-name">
                {{Form::text('users_name', '', array('placeholder' => "Ex: newname@mycompany.com, newname@mycompany.com", 'class' => "field", 'data-input' => '') ) }}
            </div>

            <div class="row users-access">
                <div class="column large-12 left">
                    Assign roles:
                </div>
                <div class="column large-12 left">
                    {{Form::radio('users-access', 'Admin', null , array('id' => 'users-access-admin') )}}
                    {{Form::label('users-access-admin', 'Admin')}}
                    <span class="mytooltip" title="">?</span>

                    <!-- data-tooltip-special aria-haspopup="true" class="has-tip tip-right" data-width="1200" title="" -->
                </div>
                <div class="column large-12 left">
                    {{Form::radio('users-access', 'User', null , array('id' => 'users-access-user') )}}
                    {{Form::label('users-access-user', 'User')}}
                    <span class="mytooltip" title="">?</span>
                </div>
                <div class="column small-12 large-12">
                    <a class="button" id="moveToNextStep">Next</a>
                </div>
            </div>
        </div>

        <div class="column large-12 add-users-step2" >
            <div class="row">
                <div class="column large-4 add-users-step2-title">
                    Assign Portal
                </div>
                <div class="column large-2 right">
                    Step 2 of 2
                </div>
            </div>
            <div class="row add-users-memo">
                To assign Pages to ninjasauce@sauce.com, select the pages and choose a role. You can choose a default role, then select all the pages they will work on.
            </div>

            <div class="row users-access">
                <div class="column large-12 left">
                    Portal:
                </div>

                @if(isset($active_portals) && !empty($active_portals))
                @foreach($active_portals as $portal)
                <div class="column large-12 left">

                    {{Form::checkbox('portal_name_'.$portal->hashedid, $portal->name, null , array('id' => $portal->hashedid) )}}
                    {{Form::label($portal->hashedid, $portal->name)}}
                </div>
                @endforeach
                @endif

                <div class="column large12 user-added-confirm">
                    <a class="button" onClick="Plex.settings.userAddComfirm($(this));">Finish!</a>
                </div>
            </div>
        </div>

        {{Form::close()}}

        <div class="column large-12 add-users-finalstep">
            <div class="row user-added">
                Users added!
            </div>
            <div class="row users-list">

            </div>
            <div class="row successful">
                Have successfully been added and will receive an email with a link to access their portals.
            </div>

            <div class="row ok-btn">
                <a class="column large-offset-4 small-4 large-3 button">OK</a>
            </div>
        </div>

        <!--//////////////////// PROFILE PICTURE MODAL \\\\\\\\\\\\\\\\\\\\-->
        <div class='reveal-modal small' id="updateProfilePic" data-reveal>
            <div class='row'>
                <div class='small-12 column close_x'>
                    <img src="/images/close-x.png" class="close-reveal-modal" alt=""></img>
                </div>
            </div>
            {{ Form::open(array('id' => 'uploadProfilePictureForm', 'enctype' => 'multipart/form-data', 'name' => 'uploadPhotoForm', 'data-abide' => 'ajax' )) }}
            {{ Form::hidden('targetted_user_id', null, array('class' => 'user_information_user_id')) }}
            <div class="row">
                <div class="large-10 column">
                    <!--////////// Profile picture form \\\\\\\\\\-->
                    <div class='row'>
                        <div class='small-12 column'>
                            {{ Form::label( 'profile_picture', 'Upload a Profile Picture', array( 'class' => 'upload-title' ) ) }}
                        </div>
                    </div>
                    <div class='row'>
                        <div class='small-12 column'>
                            {{ Form::file('profile_picture', array( 'required', 'pattern' => 'file_types' )) }}
                            <small class='error'>Accepted formats: .jpg, png or gif</small>
                        </div>
                    </div>
                    <!--\\\\\\\\\\ Profile picture form //////////-->
                </div>
            </div>
            <div class="row" id="profile_image_row">
                <div class="small-6 column close-reveal-modal">
                    <div class='button secondary btn-cancel'>
                        Cancel
                    </div>
                </div>
                <div class="small-6 column">
                    {{ Form::submit('Save', array('class' => 'button btn-Save')) }}
                </div>
            </div>
        {{ Form::close() }}
        </div>
<!--\\\\\\\\\\\\\\\\\\\\ PROFILE PICTURE MODAL ////////////////////-->

    </div>
    <!-- ////////////////////////// Manage Users section - end \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ -->

    <!-- \\\\\\\\\\\\\\\\\\\\\\\\\\ Manage Portals section begin /////////////////////// -->
    <div class="row collapse plex-manage-portals-section settings-section" id="plex-manage-portals">
        <div class="column small-12">
            <div class="row">
                <div class="column small-11 small-centered">
                    <div class="row collapse manage-portals-title">
                        <div class="column small-11 medium-5">Manage Portals</div>
                        <div class="column medium-2 right show-for-medium-up">
                            <div class="manage-users-link">Manage Users</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row active-portal-begins">
                <div class="column large-4 portal-title">Active Portal(s)</div>
            </div>

            @if(isset($active_portals) && empty($active_portals) )
                <div class="row portal-container hide" data-hashedid="" data-portalname="">
                    <div class="column large-4 portal-name">
                        <div class="portal-name-shown"></div>
                        <div class="type-new-name hide">
                            <input type='submit' value='save' style='float:right;'>
                            <div style='overflow: hidden; padding-right: .5em;'>
                                <input type='text' value='' />
                            </div>
                        </div>
                        <div class="error" style="font-size:10px; color:#000; display: none">
                            *Length of name should be less than 30
                        </div>
                    </div>
                    <div class="column large-2 rename-portal">
                        <a href="">Rename</a>
                    </div>
                    <div class="column large-3 edit-add-portal">
                        <a href="">Edit/Add Users</a>
                    </div>
                    <div class="column large-2 deactive-portal">
                        <a href="#" class="deactive" data-reveal-id="deactive-portal-modal">Deactivate</a>
                    </div>
                    <div class="column large-1 target-icon hide"></div>

                    <div class="column large-12 emailList">
                        <div class="row email-list">
                        </div>

                        <div class="row add-users">
                            <div class="column large-2 right">
                                {{Form::submit('Add Users', array('class' => 'float-right') )}}
                            </div>
                            <div class="column large-10 users-access-list">
                                {{Form::text('users_name', '', array('placeholder' => "Ex: newname@mycompany.com, newname@mycompany.com", 'class' => "field", 'data-input' => ''))}}
                            </div>
                            <div class="column large-12 check-access">
                                {{Form::radio('users-access', 'Admin', null , array('id' => 'users-access-admin1') )}}
                                {{Form::label('users-access-admin1', 'Admin')}}
                                <span class="mytooltip" title="">?
                                </span>
                            </div>
                            <div class="column large-12 check-access">
                                {{Form::radio('users-access', 'User', null , array('id' => 'users-access-user1') )}}
                                {{Form::label('users-access-user1', 'User')}}
                                <span class="mytooltip" title="">?
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

            @elseif(isset($active_portals) && !empty($active_portals) )

                @foreach($active_portals as $portal)
                @if(isset($portal->hashedid))
                <div class="row portal-container" data-hashedid="{{$portal->hashedid}}" data-portalname="{{$portal->name or ''}}">
                    <div class="column large-4 portal-name">
                        <div class="portal-name-shown">
                            {{$portal->name or ''}}
                        </div>
                        <div class="type-new-name hide">
                            <input type='submit' value='save' style='float:right;'>
                            <div style='overflow: hidden; padding-right: .5em;'>
                                <input type='text' value='{{$portal->name or ''}}' />
                            </div>
                        </div>
                        <div class="error" style="font-size:10px; color:#000; display: none">
                            *Length of name should be less than 30
                        </div>
                    </div>

                    <div class="column large-2 rename-portal">
                        <a href="#">Rename</a>
                    </div>
                    <div class="column large-3 edit-add-portal">
                        <a href="#">Edit/Add Users</a>
                    </div>
                    <div class="column large-2 deactive-portal">
                        <a href="#" class="deactive" data-reveal-id="deactive-portal-modal">
                            @if(isset($portal->active) && $portal->active == 1)
                            Deactivate
                            @else
                            Activate
                            @endif
                        </a>
                    </div>
                    <div class="column large-1 target-icon hide"></div>


                    <div class="column large-12 emailList">
                        <div class="row email-list">

                            <span class="user-access-tooltip email-addr superadmin hide" title="<input type='checkbox' checked/><span>&nbsp;Admin</span><br/><input type='checkbox'/><span>&nbsp;User</span>" data-hasheduserid="">
                            </span>

                            <span class="user-access-tooltip email-addr user hide" title="<input type='checkbox'/><span>&nbsp;Admin</span><br/><input type='checkbox' checked/><span>&nbsp;User</span>" data-hasheduserid="">
                            </span>

                            @foreach($portal->users as $user)
                         
                            @if(isset($user['email']))
                            <span class="user-access-tooltip email-addr" title="<input type='checkbox' @if($user['super_admin'] == 1) checked @endif/><span>&nbsp;Admin</span><br/><input type='checkbox' @if($user['super_admin'] == 0) checked @endif /><span>&nbsp;User</span>" data-hasheduserid="{{Crypt::encrypt($user['user_id'])}}">
                                {{$user['email'] or ''}}<a href="#" class="close">&nbsp;&times;</a>
                            </span>
                            @endif
                            @endforeach

                        </div>

                        <div class="row add-users">
                            <div class="column large-2 right">
                                {{Form::submit('Add Users', array('class' => 'float-right') )}}
                            </div>

                            <div class="column large-10 users-access-list">
                                {{Form::text('users_name', '', array('placeholder' => "Ex: newname@mycompany.com, newname@mycompany.com", 'class' => "field", 'data-input' => ''))}}
                            </div>

                            <div class="column large-12 check-access">
                                {{Form::radio('users-access', 'Admin', null , array('id' => 'users-access-admin_'.$portal->name) )}}
                                {{Form::label('users-access-admin_'.$portal->name, 'Admin')}}
                                <span class="mytooltip" title="">?
                                </span>
                            </div>
                            <div class="column large-12 check-access">
                                {{Form::radio('users-access', 'User', null , array('id' => 'users-access-user_'.$portal->name) )}}
                                {{Form::label('users-access-user_'.$portal->name, 'User')}}
                                <span class="mytooltip" title="">?
                                </span>
                            </div>
                        </div>
                    </div>


                    <div class="column large-12 target-criteria">
                        <div class="row active-target-criteria">
                            Active Targeting Criteria
                        </div>
                        <div class="row">
                            <div class="column large-3">
                                Location:
                            </div>
                            <div class="column large-9">
                                <span class="label">+ United States</span>
                                <span class="label">- Atigua</span>
                            </div>
                        </div>

                    </div>


                </div>
                @endif
                @endforeach
            @endif

            <div id="deactive-portal-modal" class="reveal-modal" data-reveal aria-labelledby="" aria-hidden="true" role="dialog" >
                <div class="row">
                    <div class="column large-12 text-center question-deactive">

                    </div>
                    <div class="row cancel-portal secondary radius" data-hashedid="">
                        <a class="column large-4 button large-offset-1 secondary close-reveal-modal" aria-label="Close">Cancel</a>
                        <a style="background-color: rgb(242, 123, 36);" class="column large-4 large-offset-2 button" onClick="Plex.settings.activateDeactivatePort($(this));">Deactivate</a>
                    </div>
                </div>
            </div>

            <div class="row new-portal">
                <div class="column large-12">
                    <span>Create new portal</span>
                </div>

                <div class="column large-12 new-portal-shown">
                    <input type="submit" value="save" style="float:right"/>
                    <div style='overflow: hidden; padding-right: .5em;'>
                        <input type="text" placeholder="New Portal Name"/>
                    </div>
                </div>
            </div>

            <div class="row deactive-portal-begins">
                <div class="column large-4 portal-title">Deactived Portal(s)</div>
            </div>

            @if(isset($deactive_portals) && empty($deactive_portals))
                <div class="row deactived-portal-container hide" data-hashedid="" data-portalname="">
                    <div class="column large-4 portal-name">
                        <div class="portal-name-shown"></div>
                        <div class="type-new-name hide">
                            <input type='submit' value='save' style='float:right;'>
                            <div style='overflow: hidden; padding-right: .5em;'>
                                <input type='text' value='' />
                            </div>
                        </div>
                        <div class="error" style="font-size:10px; color:#000; display: none">
                            *Length of name should be less than 30
                        </div>
                    </div>
                    <div class="column large-2 rename-portal">
                        <a href="">Rename</a>
                    </div>
                    <div class="column large-3 edit-add-portal">
                        <a href="">Edit/Add Users</a>
                    </div>
                    <div class="column large-2 deactive-portal">
                        <a href="#" class="deactive" data-reveal-id="active-portal-modal">Activate</a>
                    </div>
                    <div class="column large-1 target-icon hide"></div>

                    <div class="column large-12 emailList">
                        <div class="row email-list">
                        </div>

                        <div class="row add-users">
                            <div class="column large-2 right">
                                {{Form::submit('Add Users', array('class' => 'float-right') )}}
                            </div>
                            <div class="column large-10 users-access-list">
                                {{Form::text('users_name', '', array('placeholder' => "Ex: newname@mycompany.com, newname@mycompany.com", 'class' => "field", 'data-input' => ''))}}
                            </div>
                            <div class="column large-12 check-access">
                                {{Form::radio('users-access', 'Admin', null , array('id' => 'users-access-admin2') )}}
                                {{Form::label('users-access-admin2', 'Admin')}}
                                <span class="mytooltip" title="">?
                                </span>
                            </div>
                            <div class="column large-12 check-access">
                                {{Form::radio('users-access', 'User', null , array('id' => 'users-access-user2') )}}
                                {{Form::label('users-access-user2', 'User')}}
                                <span class="mytooltip" title="">?
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            @elseif(isset($deactive_portals) && !empty($deactive_portals))
                @foreach($deactive_portals as $portal)
                @if(isset($portal->hashedid))
                <div class="row deactived-portal-container" data-hashedid="{{$portal->hashedid}}" data-portalname="{{$portal->name}}">
                    <div class="column large-4 portal-name">
                        <div class="portal-name-shown">
                            {{$portal->name or ''}}
                        </div>
                        <div class="type-new-name hide">
                            <input type='submit' value='save' style='float:right;'>
                            <div style='overflow: hidden; padding-right: .5em;'>
                                <input type='text' value='{{$portal->name or ''}}' />
                            </div>
                        </div>
                        <div class="error" style="font-size:10px; color:#000; display: none">
                            *Length of name should be less than 30
                        </div>
                    </div>

                    <div class="column large-2 rename-portal">
                        <a href="#">Rename</a>
                    </div>
                    <div class="column large-3 edit-add-portal">
                        <a href="#">Edit/Add Users</a>
                    </div>
                    <div class="column large-2 deactive-portal">
                        <a href="#" class="deactive" data-reveal-id="active-portal-modal">
                            @if($portal->active == 1)
                            Deactivate
                            @else
                            Activate
                            @endif
                        </a>
                    </div>
                    <div class="column large-1 target-icon hide"></div>


                    <div class="column large-12 emailList">
                        <div class="row email-list">

                            <span class="user-access-tooltip email-addr superadmin hide" title="<input type='checkbox' checked/><span>&nbsp;Admin</span><br/><input type='checkbox'/><span>&nbsp;User</span>" data-hasheduserid="">
                            </span>

                            <span class="user-access-tooltip email-addr user hide" title="<input type='checkbox'/><span>&nbsp;Admin</span><br/><input type='checkbox' checked/><span>&nbsp;User</span>" data-hasheduserid="">
                            </span>

                            @foreach($portal->users as $user)
                          
                            @if(isset($user['email']))
                            <span class="user-access-tooltip email-addr" title="<input type='checkbox' @if($user['super_admin'] == 1) checked @endif/><span>&nbsp;Admin</span><br/><input type='checkbox' @if($user['super_admin'] == 0) checked @endif /><span>&nbsp;User</span>" data-hasheduserid="{{Crypt::encrypt($user['user_id'])}}">
                                {{$user['email'] or ''}}<a href="#" class="close">&nbsp;&times;</a>
                            </span>
                            @endif
                            @endforeach

                        </div>

                        <div class="row add-users">
                            <div class="column large-2 right">
                                {{Form::submit('Add Users', array('class' => 'float-right') )}}
                            </div>
                            <div class="column large-10 users-access-list">
                                {{Form::text('users_name', '', array('placeholder' => "Ex: newname@mycompany.com, newname@mycompany.com", 'class' => "field", 'data-input' => ''))}}
                            </div>
                            <div class="column large-12 check-access">
                                {{Form::radio('users-access', 'Admin', null , array('id' => 'users-access-admin_'.$portal->name) )}}
                                {{Form::label('users-access-admin_'.$portal->name, 'Admin')}}
                                <span class="mytooltip" title="">?
                                </span>
                            </div>
                            <div class="column large-12 check-access">
                                {{Form::radio('users-access', 'User', null , array('id' => 'users-access-user_'.$portal->name) )}}
                                {{Form::label('users-access-user_'.$portal->name, 'User')}}
                                <span class="mytooltip" title="">?
                                </span>
                            </div>
                        </div>
                    </div>


                    <div class="column large-12 target-criteria">
                        <div class="row active-target-criteria">
                            Active Targeting Criteria
                        </div>
                        <div class="row">
                            <div class="column large-3">
                                Location:
                            </div>
                            <div class="column large-9">
                                <span class="label">+ United States</span>
                                <span class="label">- Atigua</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            @endif

            <div id="active-portal-modal" class="reveal-modal" data-reveal aria-labelledby="" aria-hidden="true" role="dialog" >
                <div class="row">
                    <div class="column large-12 text-center question-deactive">

                    </div>
                    <div class="row cancel-portal secondary radius" data-hashedid="">
                        <a class="column large-4 button large-offset-1 secondary close-reveal-modal" aria-label="Close">Cancel</a>
                        <a style="background-color: rgb(242, 123, 36);" class="column large-4 large-offset-2 button" onClick="Plex.settings.activateDeactivatePort($(this));">Activate</a>
                    </div>
                </div>
            </div>



        </div>
    </div>
    <!-- ///////////////////////// Manage Portals section end \\\\\\\\\\\\\\\\\\\\\\\\\\ -->

    @if( isset($is_organization) && $is_organization == 1 )
    <!-- \\\\\\\\\\\\\\\\\\\\\\\\\\ Manage Billing section begin ///////////////////////////// -->
    <div class="row collapse plex-manage-billing-section settings-section {{$arr['setting_section']['billing']}}" id="plex-billing">
        <nav class="top-bar row text-right" data-topbar role="navigation">
            <section class="top-bar-section">
                <ul class="right">
                    <li class="billing-tab billing">Billing</li>
                    <li class="billing-tab invoices">Invoice</li>
                    <li class="billing-tab pricing">Pricing</li>
                    <li class="billing-tab plans">Plans</li>
                </ul>
            </section>
        </nav>

        <div class="column large-12 plex-billing-price-section">
            <div class="row billing-container">
                <div class="column small-12 payment-info-title">
                    Pricing
                </div>
            </div>
        </div>

        <div class="column large-12 plex-billing-default-section">

            <!-- removed old code already -->
            <div class="row billing-container">
                <div class="column small-12 payment-info-title">
                    Billing
                </div>

                <div class="row billing-summary">
                    <div class="column small-3 brief-review">{{$textmsg_tier or ''}}</div>
                    @if(isset($textmsg_tier) && $textmsg_tier == 'free')
                        <div class="column small-9 brief-review text-right">
                            Used <span class="cur-text-msg">{{ (isset($num_of_free_texts)? 500 - $num_of_free_texts : 0 )}}</span> / {{ isset($num_of_free_texts)? $num_of_free_texts : 0 }}
                        </div>
                        <?php
                            $perc = 0;
                            if(isset($num_of_free_texts)) {
                                $perc = (500 - $num_of_free_texts)/5;
                            }
                        ?>
                        <div class="column small-12 end progress @if($perc!=100) success @else alert @endif round">
                            <span class="meter" style="width: {{$perc.'%'}}"></span>
                        </div>

                    @elseif(isset($textmsg_tier) && $textmsg_tier == 'flat_fee' && $flat_fee_sub_tier != 'plan-4')
                        <?php
                            $num_of_planned_texts = 0;
                            switch ($flat_fee_sub_tier) {
                                case 'plan-1':
                                    $num_of_planned_texts = 1000;
                                    break;
                                case 'plan-2':
                                    $num_of_planned_texts = 10000;
                                    break;
                                case 'plan-3':
                                    $num_of_planned_texts = 100000;
                                    break;
                                default:
                                    break;
                            }
                        ?>
                        <div class="column small-9 brief-review text-right">
                            Used <span class="cur-text-msg">{{ (isset($num_of_eligble_texts)? $num_of_planned_texts - $num_of_eligble_texts : 0 )}}</span> / {{ isset($num_of_eligble_texts)? $num_of_eligble_texts : 0 }}
                        </div>
                        <?php
                            $perc = 0;
                            if(isset($num_of_eligble_texts) && $num_of_planned_texts != 0) {
                                $perc = ($num_of_planned_texts - $num_of_eligble_texts) * 100 / $num_of_planned_texts;
                            }
                        ?>
                        <div class="column small-12 end progress @if($perc!=100) success @else alert @endif round">
                            <span class="meter" style="width: {{$perc.'%'}}"></span>
                        </div>

                    @elseif(isset($textmsg_tier) && $textmsg_tier == 'flat_fee' && $flat_fee_sub_tier == 'plan-4')
                        <div class="column small-9 brief-review text-right">
                            Unlimited
                        </div>
                        <div class="column small-12 end progress success round">
                            <span class="meter" style="width: 1%"></span>
                        </div>
                    @endif
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Current Plan</th>
                            <th>Included Texts</th>
                            <th>Billing Cycle</th>
                            <th>Plan Expires</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$textmsg_tier or 'N/A'}}</td>

                            @if(isset($textmsg_tier) && $textmsg_tier == 'free')
                            <td>{{$num_of_free_texts or '0'}} text messages left</td>
                            @elseif(isset($textmsg_tier) && $textmsg_tier == 'flat_fee' && $flat_fee_sub_tier != 'plan-4')
                            <td>{{$num_of_eligble_texts or '0'}} text messages left</td>
                            @elseif(isset($textmsg_tier) && $textmsg_tier == 'flat_fee' && $flat_fee_sub_tier == 'plan-4')
                            <td>Unlimited text messages</td>
                            @else
                            <td>unknown</td>
                            @endif

                            @if(isset($textmsg_tier) && $textmsg_tier == 'free')
                            <td>Free Trial @ $0/month</td>
                            @elseif(isset($textmsg_tier) && $textmsg_tier == 'pay_as_you_go')
                            <td></td>
                            @elseif(isset($textmsg_tier) && $textmsg_tier == 'flat_fee')
                            <td><?php
                                switch ($flat_fee_sub_tier) {
                                    case 'plan-1':
                                        echo '$40.00/month';
                                        break;
                                    case 'plan-2':
                                        echo '$300.00/month';
                                        break;
                                    case 'plan-3':
                                        echo '$2,000.00/month';
                                        break;
                                    case 'plan-4':
                                        echo '$3,000.00/month';
                                        break;
                                    default:
                                        echo '';
                                        break;
                                }
                            ?></td>
                            @else
                            <td>unknown</td>
                            @endif
                            <td>{{$textmsg_expires_date or 'unknown'}}</td>
                            <td><div class="edit-payment-btn hide-for-small-only">Edit Payment Info</div></td>
                        </tr>
                    </tbody>
                </table>

                <div class="row">
                    <div class="column small-12 medium-offset-2 medium-10 large-offset-1 large-5">
                        <button class="change-plan-btn">Change Plan</button>
                    </div>
                    <div class="column small-12 medium-offset-2 medium-10 large-offset-0 large-offset-1 large-5 end @if(isset($textmsg_tier) && $textmsg_tier == 'free') hide @endif">
                        {{Form::checkbox('auto_renew', 'yes', isset($auto_renew) && $auto_renew == 1? true : false, array('class' => 'autorenew', 'id' => 'auto_renew'))}}
                        {{Form::label('auto_renew', 'Auto renew (renews every month)')}}
                    </div>
                </div>
            </div>

        </div>

        <div class="column large-12 plex-billing-plan-section billing-bg">
            <div class="row billing-plan-container" id="change-plan-modal">
                <div class="column large-12 text-center payment-selection">
                    Please select your payment plan
                </div>
                <!--<div class="column large-3 payment-option-1">
                    <nav class="title-area text-center" data-topbar role="navigation">
                        <ul class="">
                            <li>
                                Free Trial
                            </li>
                        </ul>
                    </nav>
                    <ul class="">
                        <li>
                            <div class="bold-font text-center">500</div>
                            <div class="small text-center">Free Texts</div>
                        </li>
                        <li>
                            <div class="bold-font text-center">500</div>
                            <div class="small text-center">Free Incoming</div>
                        </li>
                        <li>
                            <div class="indicate text-center">
                                Free dedicated <br/>
                                number for 30 days.
                            </div>
                        </li>
                        <li>
                            <div class="break-line"></div>
                        </li>
                        <li>
                            <div class="indicate text-center">
                                Limited to U.S, Canada, and <br/> certain countries.
                            </div>
                            <div class="indicate text-center">No Credit card required.</div>
                        </li>
                        <li class="text-center">
                            <a href="#" class="button select-plan-btn @if(isset($textmsg_tier) && $textmsg_tier == 'free') disabled @endif" data-tier="free">Select Plan</a>
                        </li>
                    </ul>
                </div>-->
                <div class="column large-offset-2 large-4 payment-option-2">
                    <nav class="title-area text-center" data-topbar role="navigation">
                        <ul class="">
                            <li>
                                Pay as you go
                            </li>
                        </ul>
                    </nav>
                    <table class="plan-offered">
                        <thead>
                            <td># of Texts</td>
                            <td>SMS</td>
                            <td>MMS</td>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1 - 1,000</td>
                                <td>5 cents</td>
                                <td>6 cents</td>
                            </tr>
                            <tr>
                                <td>1,001 - 10,000</td>
                                <td>4 cents</td>
                                <td>5 cents</td>
                            </tr>
                            <tr>
                                <td>10,001 - 100,000</td>
                                <td>3 cents</td>
                                <td>4 cents</td>
                            </tr>
                            <tr>
                                <td>Unlimited</td>
                                <td>2 cents</td>
                                <td>3 cents</td>
                            </tr>
                        </tbody>
                    </table>
                    <ul class="">
                        <li>
                            <div class="break-line"></div>
                        </li>
                        <li>
                            <div class="indicate text-center">
                                Dedicated local number<br/>
                                $60/year
                            </div>
                            <div class="indicate text-center">
                                Dedicated toll free number $120/year
                            </div>
                        </li>
                        <li class="text-center">
                            <a href="#" class="button select-plan-btn" data-tier="pay_as_you_go">Select Plan</a>
                        </li>
                        <li>
                            <div class="indicate text-center">*You will be charged for receiving texts</div>
                        </li>
                    </ul>
                </div>
                <div class="column large-offset-1 large-4 payment-option-3 end">
                    <nav class="title-area text-center" data-topbar role="navigation">
                        <ul class="">
                            <li>
                                Flat Fee
                            </li>
                        </ul>
                    </nav>
                    <table class="plan-offered">
                        <thead>
                            <td># of Texts</td>
                            <td>Monthly Fee</td>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1 - 1,000</td>
                                <td>$40</td>
                            </tr>
                            <tr>
                                <td>1,001 - 10,000</td>
                                <td>$300</td>
                            </tr>
                            <tr>
                                <td>10,001 - 100,000</td>
                                <td>$2,000</td>
                            </tr>
                            <tr>
                                <td>Unlimited</td>
                                <td>$3,000</td>
                            </tr>
                        </tbody>
                    </table>
                    <ul class="">
                        <li>
                            <div class="break-line"></div>
                        </li>
                        <li>
                            <div class="indicate text-center">
                                Dedicated local number<br/>
                                $60/year
                            </div>
                            <div class="indicate text-center">
                                Dedicated toll free number $120/year
                            </div>
                        </li>
                        <li class="text-center">
                            <a href="#" class="button select-plan-btn" data-tier="flat_fee">Select Plan</a>
                        </li>
                        <li>
                            <div class="indicate text-center">*You will be charged for receiving texts</div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="blank text-center">
                <a class="back-payment-default">Back</a>
            </div>
        </div>

        <div class="column large-12 end plex-textmsg-plan">
            <div class="row plan-title">
                <span class="active">1. Choose your plan</span> /
                <span>2. Choose your Phone Number</span> /
                <span>3. Check Out</span>
            </div>
            <div class="row plan-to-choose">
                <span>How many texts are you going to send?</span>
            </div>
            <div class="row plex-textmsg-plan-container">

            </div>
            <div class="row plex-textmsg-forward-step2">
                <a href="#" class="button disabled">Continue</a>
            </div>
        </div>

        <div class="column large-12 end plex-textmsg-search-phone">
            <div class="row plan-title">
                <span>1. Choose your plan</span> /
                <span class="active">2. Choose your Phone Number</span> /
                <span>3. Check Out</span>
            </div>

            @if(!isset($purchased_phone) || empty($purchased_phone))
                @include('groupMessaging.searchTollFreePhoneNumber')
            @else
                @if(isset($textmsg_tier) && $textmsg_tier == 'flat_fee' && $current_time->gt($textmsg_expires_date) )
                    @include('groupMessaging.searchTollFreePhoneNumber')
                @else
                    <div class="row notify">You have purchased a phone number already! <br> The number is <strong>{{$purchased_phone}}</strong></div>
                    <div class="row plex-textmsg-forward-step3">
                        <a href="#" class="button">Continue</a>
                    </div>
                @endif
            @endif

        </div>

        <div class="column large-12 end billing-info">
            <div class="row plan-title">
                <span>1. Choose your plan</span> /
                <span>2. Choose your Phone Number</span> /
                <span class="active">3. Check Out</span>
            </div>

            <div class="row back-to-plans">
                <div class="back-to-plans-btn column small-12"><small>Back to plans</small>
                </div>
            </div>

            <div class="row collapse payment-row">
                <div class="column large-5">
                    <span class="payment-title">Name and Address : </span>
                    <ul>
                        <li>{{Form::text('bname', isset($paymentInfo->business_name) ? $paymentInfo->business_name : null, array('placeholder' => 'Business Name', 'class' => 'business-name'))}}</li>
                        <li>{{Form::text('cname', isset($paymentInfo->name) ? $paymentInfo->name : null, array('placeholder' => 'Contact Name', 'class' => 'contact-name'))}}</li>
                        <li>{{Form::text('address', isset($paymentInfo->address_line1) ? $paymentInfo->address_line1 : null, array('placeholder' => 'Street Address', 'class' => 'street-address'))}}</li>
                        <li>{{Form::text('apt', isset($paymentInfo->apt) && $paymentInfo->apt != 'N/A' ? $paymentInfo->apt : null, array('placeholder' => 'Apt/Suite', 'class' => 'apt'))}}</li>
                        <li>{{Form::text('city', isset($paymentInfo->address_city) ? $paymentInfo->address_city : null, array('placeholder' => 'City', 'class' => 'city'))}}</li>
                        <li>
                            {{Form::text('state', isset($paymentInfo->address_state) ? $paymentInfo->address_state : null, array('placeholder' => 'State', 'class' => 'state'))}}
                            {{Form::text('zipcode', isset($paymentInfo->zip_code) ? $paymentInfo->zip_code : null, array('placeholder' => 'zipcode', 'class' => 'zipcode'))}}
                        </li>
                        <li class="countries-list clearfix">
                            {{Form::select('country', $countries, isset($paymentInfo->address_country) ? $paymentInfo->address_country : null, array('class' => 'left'))}}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row collapse">
                <div class="column large-5">
                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/creditcards_accepted.png" class="card-providers">
                </div>
            </div>

            <div class="row collapse payment-row">
                <div class="column large-5">
                    <span class="payment-title">Credit or Debit Card</span>
                    <div class="row">{{Form::text('cardnumber', isset($paymentInfo->last4) ? '************'.$paymentInfo->last4 : null, array('placeholder' => 'Card Number', 'class' => 'card-number', 'data-stripe'=>"number"))}}</div>
                    <div class="row exp-row clearfix">
                        <div class="payment-exp-month left">
                            <?php
                                $month = '';
                                if( isset($paymentInfo->exp_month) ){
                                    if( strlen($paymentInfo->exp_month) == 1 ){
                                        $month = '0'.$paymentInfo->exp_month;
                                    }else{
                                        $month = $paymentInfo->exp_month;
                                    }
                                }
                            ?>
                            <select name="month" id="" class="card-expiry-month" data-stripe="exp_month" value="{{$month}}">
                                <option value="" disabled @if(!$month) selected @endif>MM</option>
                                @for( $i = 1; $i < 13; $i++ )
                                    <option value="{{$i < 10 ? '0'.$i : $i}}" @if( $i < 10 && '0'.$i == $month) selected @elseif( $i == $month ) selected @endif>{{$i < 10 ? '0'.$i : $i}}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="separator left"> / </div>

                        <?php
                            $yr = '';
                            if( isset($paymentInfo->exp_year) ){
                                $yr = substr($paymentInfo->exp_year, 2);
                            }
                        ?>

                        <div class="payment-exp-year left">
                            <select name="year" id="" class="card-expiry-year" data-stripe="exp_year">
                                <option value="" disabled @if(!$yr) selected @endif>YY</option>
                                @for($k = 0; $k < 20; $k++)
                                    <?php $date = date('Y'); $date += $k; ?>
                                    <option value="{{substr($date, 2)}}" @if( substr($date, 2) == $yr) selected @endif>{{substr($date, 2)}}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="payment-notice right text-right">
                            <span data-tooltip aria-haspopup="true" class="has-tip cvc-explaination" title="<b>CVC</b><div>The Card Security Code is located on the back of MasterCard, Visa and Discover credit or debit cards and is typically a separate group of 3 digits to the right of the signature strip.</div><br /><img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/creditcard.png' alt='CVC image' />">?</span>
                        </div>

                        <div class="payment-exp-cvc right">
                            {{Form::number('cvc', null, array('placeholder' => 'CVC', 'class' => 'card-cvc', 'data-stripe'=>"cvc", 'min' => '0', 'max' => '999', 'maxlength' => '3'))}}
                        </div>
                    </div>
                    <div class="row">{{Form::text('phone', isset($paymentInfo->phone) ? $paymentInfo->phone : null, array('placeholder' => 'Phone', 'class' => 'card-phone'))}}</div>
                </div>
            </div>

            <div class="row collapse">
                <div class="column large-5">
                    {{Form::checkbox('agreement-chk', '' ,null, array('id' => 'agreement-check'))}}
                    <label for="agreement-check" class="agreement-confirm">Yes, I agree to the above <a href="/terms-of-service" target="_blank">terms and conditions</a></label>
                </div>
            </div>

            <div class="billing-err">
                <div><small>Make sure card number has the correct number of digits and no letters.</small></div>
                <div><small>CVC cannot contain letters and must be 3 digits.</small></div>
                <div><small>Read and agree to the terms and conditions.</small></div>
            </div>

            <div class="callback-err"><small></small></div>

            <div class="row payment-row collapse">
                <div class="column large-5 text-center">
                    {{Form::submit('Next', array('class' => 'submit show-checkout-after', 'id' => 'save-creditcard-info'))}}
                    <span class="payment-errors"></span>
                </div>
            </div>

            <div class="row collapse">
                <div class="column large-2">
                    <div class="poweredby-top text-center">
                        <div class="poweredbySSL"></div>
                    </div>
                </div>
                <div class="column large-2 end">
                    <div class="poweredby-top text-center">
                        <div class="poweredbyStripe"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="billing-checkout">
            <div class="billing-header">Order Summary</div>
            <div class="summary-head clearfix">
                <div class="left">Item</div>
                <div class="right">Cost</div>
            </div>
            <hr />
            <div class="summary-list">
                <div class="summary-item">
                    <div class="item-descrip txt-phone-plan clearfix">
                        <div class="left"><b>Purchased Phone Number</b></div>
                        <div class="right">One time fee</div>
                    </div>
                    <div class="item-package txt-phone-plan clearfix">
                        <div class="left">{{$purchased_phone or 'N/A'}}</div>
                        <div class="right"><b>$60.00</b></div>
                    </div>
                </div>
            </div>
            <div class="summary-list">
                <div class="summary-item">
                    <div class="item-descrip txt-msg-plan clearfix">
                        <div class="left"><b>Premium</b></div>
                        <div class="right">Monthly fee</div>
                    </div>
                    <div class="item-package txt-msg-plan clearfix">
                        <div class="left">Plexuss Premium Membership</div>
                        <div class="right"><b>$100.00</b></div>
                    </div>
                </div>
            </div>
            <hr />
            <div class="summary-total clearfix">
                <div class="left"><b>Grand total</b></div>
                <div class="right"><b>$100.00</b></div>
            </div>

            {{Form::submit('Complete Order', array('class' => 'submit', 'id' => 'complete-purchase'))}}

            <div class="inline-container">
                <div class="ssl-top text-center">
                    <div class="ssl"></div>
                </div>

                <div class="poweredby-top text-center">
                    <div class="poweredby"></div>
                </div>
            </div>

        </div>

        <div class="billing-done text-center">
            <div class="thankyou">Thank you for your order!</div>
            <br />
            <div class="msg">A confirmation email has been sent to <u><span class="billing-email"></span></u> for your record. <br/>This order will also be viewable in your Invoice History.</div>
            <a href="/settings/billing">{{Form::submit('Get Started', array('class' => 'submit', 'id' => 'get-started-w-plex'))}}</a>
        </div>

        <div class="column small-12 billing-history billing-section history">
            <!-- history injected here -->
        </div>

    </div>
    <!-- ////////////////////////// Manage Billing section end \\\\\\\\\\\\\\\\\\\\\\\\\\ -->
    @else
    <!-- \\\\\\\\\\\\\\\\\\\\\\\\\\ Manage Billing for students section begin ///////////////////////////// -->
    <div class="row plex-manage-student-billing-section settings-section {{$arr['setting_section']['billing']}}" id="plex-student-billing">
        <div class="column large-12 plex-billing-default-section" data-is-premium="{{$premium_user_level_1 or 0}}">

            <div class="row collapse payment-info-title">
                <div class="column small-6 billing-header">
                    Invoice History
                </div>
                
            </div>

            <div class="row collapse billing-container">  
                    
                <div class="column small-12 billing-history billing-section history">
                     <!-- history injected here -->
                     @if(isset($paymentInfo) && $paymentInfo != null)
                        @foreach($data['paymentInfo'] as $invoice)
                            <div class="invoice invoice-header clearfix">
                                <div class="order-no left">Order No.</div>
                                <div class="billing-date left">Billing Date</div>
                                <div class="billing-descrip left">Description</div>
                                <div class="billing-amount left">Amount</div>
                            </div>
                            <div class="invoice invoice-item clearfix">
                                <div class="order-no left">{{invoice.invoice_id or ''}}</div>
                                <div class="billing-date left">{{invoice.created_at or ''}}</div>
                                <div class="billing-descrip left">{{invoice.level or ''}}</div>
                                <div class="billing-amount left">${{invoice.amount or '0'}}</div>
                                <div class="billing-descrip-sm clearfix">{{invoice.level or ''}}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="invoice invoice-header clearfix">
                            <div class="order-no left">Order No.</div>
                            <div class="billing-date left">Billing Date</div>
                            <div class="billing-descrip left">Description</div>
                            <div class="billing-amount left">Amount</div>
                        </div>

                        <div class="no-billing-history mt50">No Invoice History</div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    <!-- ////////////////////////// Manage Billing for students section end \\\\\\\\\\\\\\\\\\\\\\\\\\ -->
    @endif

    <div class="row collapse plex-email-notifications-section settings-section {{$arr['setting_section']['email']}} notification-container" id="plex-emailNotifications">
        <div class="column small-12">

            <div></div>
            <div class="title">Email Notifications</div>

            <div class="direc">Email me notifications on...</div>

            <form>
                <input type="hidden" name="type" value="email">

                <div class="notif-row">
                    <div class="row notif-opt-container">
                        <div class="column small-8 name">
                            School Notifications
                        </div>
                        <div class="column small-2 small-text-left large-text-center option">
                            <div class="switch round">
                                <input id="switch1" type="checkbox" name="all_school_notifications" class="toggle-all" @if(isset($setting_notification['email']['all_school_notifications'])) checked="checked" @endif>
                                <label for="switch1"></label>
                            </div>
                        </div>
                        <div id="school-notifications" class="column small-2 details notif-details text-center">
                            <div>Details</div>
                            <div class="arrow"></div>
                        </div>
                    </div>

                    <div id="school-notifications-dropdown" class="notif-dropdown" style="@if(isset($setting_notification['email']['all_school_notifications'])) display: none; @else display: block; @endif">
                        <div class="row notif-opt-container">
                            <div class="column small-10 name">
                                A school wants to recruit you
                            </div>

                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch2" type="checkbox" name="wants_to_recruit_you" @if(!isset($setting_notification['email']['wants_to_recruit_you'])) checked="checked" @endif>
                                    <label for="switch2"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row notif-opt-container">
                            <div class="column small-10 name">
                                Schools viewed your Profile
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch3" type="checkbox" name="viewed_your_profile" @if(!isset($setting_notification['email']['viewed_your_profile'])) checked="checked" @endif>
                                    <label for="switch3"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row notif-opt-container">
                            <div class="column small-10 name">
                                A school has sent you a message
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch4" type="checkbox" name="sent_you_a_message" @if(!isset($setting_notification['email']['sent_you_a_message'])) checked="checked" @endif>
                                    <label for="switch4"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row notif-opt-container">
                            <div class="column small-10 name">
                                Handshakes with colleges
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch5" type="checkbox" name="handshakes_with_colleges" @if(!isset($setting_notification['email']['handshakes_with_colleges'])) checked="checked" @endif>
                                    <label for="switch5"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="notif-row">
                    <div class="row notif-opt-container">
                        <div class="column small-8 name">
                            Plexuss Notifications
                        </div>
                        <div class="column small-2 small-text-left large-text-center option">
                            <div class="switch round">
                                <input id="switch6" type="checkbox" name="all_plexuss_notifications" class="toggle-all" @if(isset($setting_notification['email']['all_plexuss_notifications'])) checked="checked" @endif>
                                <label for="switch6"></label>
                            </div>
                        </div>
                        <div id="plex-notifications" class="column small-2 details notif-details text-center">
                            <div>Details</div>
                            <div class="arrow"></div>
                        </div>
                    </div>

                    <div id="plex-notifications-dropdown" class="notif-dropdown" style="@if(isset($setting_notification['email']['all_plexuss_notifications'])) display: none; @else display: block; @endif">
                        <div class="row notif-opt-container">
                            <div class="column small-10 name">
                                College Stats Updates
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch7" type="checkbox" name="college_stats_updates" @if(!isset($setting_notification['email']['college_stats_updates'])) checked="checked" @endif>
                                    <label for="switch7"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row notif-opt-container">
                            <div class="column small-10 name">
                                College News Updates
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch8" type="checkbox" name="college_news_updates" @if(!isset($setting_notification['email']['college_news_updates'])) checked="checked" @endif>
                                    <label for="switch8"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="button radius save-notifications-btn">Save settings</button>
                </div>

            </form>

        </div>
    </div>

    <div class="row collapse plex-text-notifications-section settings-section {{$arr['setting_section']['text']}} notification-container" id="plex-textNotifications">
        <div class="column small-12">
            <div id="back-to-text-notifications">
                <div>&#x02039;</div>
                <div>Back to Notifications</div>
            </div>
            <div class="title">Text Notifications</div>

            <div class="" id="no-phone-get-phone-info" style="@if( isset($txt_opt_in) && $txt_opt_in == 1 ) display: none; @endif">

                <div class="enter-phone-form">
                    <br />
                    <form>
                        <div class="row collapse">
                            <div class="column small-12 medium-10 large-7">
                                Activating a mobile number allows Plexuss to send text messages to your phone so you can receive notifications.
                            </div>
                        </div>
                        <br>
                        <div class="row collapse">
                            <div class="small-12 medium-10 large-7 columns">
                                <div class="row collapse">
                                    <div class="small-3 columns">
                                        <label for="phone-field" class="inline">Phone</label>
                                    </div>
                                    <div class="small-9 columns" style="relative">
                                        <input type="text" id="phone-field" name="phone" placeholder="Enter phone number..." required value="{{$phone_without_calling_code or ''}}">
                                        <input type="hidden" id="formatted_hidden_phone" name="formatted_phone" value="{{$phone_without_calling_code or ''}}">
                                        <div id="dialing-codes-toggler" class="dialing-codes-btn">
                                            <span class="selected-code">+<span id="calling_code">{{$calling_code or ''}}</span></span>
                                            <div class="arrow"></div>
                                        </div>
                                        <div id="dialing-codes-dropdown" class="dialing-codes-container">
                                            @foreach( $callingCodes as $code )
                                                <div class="codes" data-code="{{$code['country_phone_code']}}">
                                                    <div class="flag flag-{{strtolower($code['country_code'])}}"></div>
                                                    <div class="country-name">{{$code['country_name'] or ''}} (+{{$code['country_phone_code'] or ''}})</div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="phone-err"><small>This phone number is not valid. Make sure your country code is correct.</small></div>
                                        <div>
                                            <input id="terms-agree" name="txt_opt_in" type="checkbox" checked="checked">
                                            <label for="terms-agree">I agree to the <a href="/terms-of-service">Terms of Service</a></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row collapse phone-form-err">
                            <div class="column small-12 medium-10 large-7 text-right">
                                <small>Must agree to the Terms and Conditions in order to continue.</small>
                            </div>
                        </div>

                        <div class="row collapse">
                            <div class="column small-12 medium-10 large-7 text-right">
                                <button class="button radius to-confirmation-section-btn">Next</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="enter-confirmation-code">

                    <form>

                        <div class="row">
                            <div class="column small-12 text-center confo">

                                <div class="title">We've sent you a SMS code to</div>

                                <div class="edit-phone-wrapper">
                                    <div class="edit-phone" style="position: relative">
                                        <input type="text" id="edited-phone-field" name="phone" placeholder="Enter phone number..." required>
                                        <input type="hidden" id="edited_formatted_hidden_phone" name="formatted_phone" value="">
                                        <div id="edited-dialing-codes-toggler" class="dialing-codes-btn edited">
                                            <span class="selected-code">+<span id="edited_calling_code">{{$calling_code or ''}}</span></span>
                                            <div class="arrow"></div>
                                        </div>
                                        <div id="edited-dialing-codes-dropdown" class="dialing-codes-container edited">
                                            @foreach( $callingCodes as $code )
                                                <div class="codes" data-code="{{$code['country_phone_code']}}">
                                                    <div class="flag flag-{{strtolower($code['country_code'])}}"></div>
                                                    <div class="country-name">{{$code['country_name'] or ''}} (+{{$code['country_phone_code'] or ''}})</div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="save-edited-wrapper"><button class="save-edited-phone-btn">Save</button></div>
                                        <div class="edit-phone-err"><small>This phone number is not valid. Make sure your country code is correct.</small></div>
                                    </div>
                                    <div id="entered_phone_num" class="number">+1 1212121212</div>
                                </div>

                                <div class="update">
                                    <div class="update-phone">Update</div>
                                </div>

                                <div class="direc">To complete your phone number verification, please enter your 4 digit code below.</div>
                                <div class="input-wrapper">
                                    <input id="_code" type="text" maxLength="4" name="code" placeholder="----" required />
                                </div>

                                <div class="code-err">The code provided is either incorrect or has expired. Click <u>Resend</u> below to receive a new code.</div>

                                <div class="update">
                                    <div class="resend-code-btn">Resend</div>
                                </div>

                                <div class="resend-err">There was an error re-sending to the provided phone number. Check to make sure your phone number and country code are correct.</div>

                                <div class="reached-limit-err"></div>

                                <div class="actions">
                                    <div class="update"><div class="back-to-phone-enter-btn">Go back</div></div>
                                    <div><button class="button radius confirm-code-btn">Confirm</button></div>
                                </div>

                            </div>
                        </div>

                    </form>

                </div>

            </div>

            <div id="has-phone-to-setup-notifications" style="@if( isset($txt_opt_in) && $txt_opt_in != 1 ) display: none; @endif">

                <div>Mobile Number</div>

                <div class="current-phone-view">
                    @if( isset($verified_phone) && $verified_phone == 1 )
                        <div class="verified">
                            <span>&#10003;</span>
                            <div class="tip">Your phone number is verified</div>
                        </div>
                    @endif
                    <div class="phone">{{$phone or ''}}</div>
                    <div class="edit"></div>
                    <div class="remove">x</div>
                </div>

                <div class="direc">Text me notifications on...</div>

                <form>
                    <input type="hidden" name="type" value="text">

                    <div class="notif-row">
                        <div class="row notif-opt-container">
                            <div class="column small-8 name">
                                School Notifications
                            </div>
                            <div class="column small-2 small-text-left large-text-center option">
                                <div class="switch round">
                                    <input id="text-switch1" type="checkbox" name="all_school_notifications" class="toggle-all" @if(isset($setting_notification['text']['all_school_notifications'])) checked="checked" @endif>
                                    <label for="text-switch1"></label>
                                </div>
                            </div>
                            <div id="text-school-notifications" class="column small-2 details notif-details text-center">
                                <div>Details</div>
                                <div class="arrow"></div>
                            </div>
                        </div>

                        <div id="text-school-notifications-dropdown" class="notif-dropdown" style="@if(isset($setting_notification['text']['all_school_notifications'])) display: none; @else display: block; @endif">
                            <div class="row notif-opt-container">
                                <div class="column small-10 name">
                                    A school wants to recruit you
                                </div>
                                <div class="column small-2 text-right option">
                                    <div class="switch round">
                                        <input id="text-switch2" type="checkbox" name="wants_to_recruit_you" @if(!isset($setting_notification['text']['wants_to_recruit_you'])) checked="checked" @endif>
                                        <label for="text-switch2"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row notif-opt-container">
                                <div class="column small-10 name">
                                    Schools viewed your Profile
                                </div>
                                <div class="column small-2 text-right option">
                                    <div class="switch round">
                                        <input id="text-switch3" type="checkbox" name="viewed_your_profile" @if(!isset($setting_notification['text']['viewed_your_profile'])) checked="checked" @endif>
                                        <label for="text-switch3"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row notif-opt-container">
                                <div class="column small-10 name">
                                    A school has sent you a message
                                </div>
                                <div class="column small-2 text-right option">
                                    <div class="switch round">
                                        <input id="text-switch4" type="checkbox" name="sent_you_a_message" @if(!isset($setting_notification['text']['sent_you_a_message'])) checked="checked" @endif>
                                        <label for="text-switch4"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row notif-opt-container">
                                <div class="column small-10 name">
                                    Handshakes with colleges
                                </div>
                                <div class="column small-2 text-right option">
                                    <div class="switch round">
                                        <input id="text-switch5" type="checkbox" name="handshakes_with_colleges" @if(!isset($setting_notification['text']['handshakes_with_colleges'])) checked="checked" @endif>
                                        <label for="text-switch5"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="notif-row">
                        <div class="row notif-opt-container">
                            <div class="column small-8 name">
                                Plexuss Notifications
                            </div>
                            <div class="column small-2 small-text-left large-text-center option">
                                <div class="switch round">
                                    <input id="text-switch6" type="checkbox" name="all_plexuss_notifications" class="toggle-all" @if(isset($setting_notification['text']['all_plexuss_notifications'])) checked="checked" @endif>
                                    <label for="text-switch6"></label>
                                </div>
                            </div>
                            <div id="text-plex-notifications" class="column small-2 details notif-details text-center">
                                <div>Details</div>
                                <div class="arrow"></div>
                            </div>
                        </div>

                        <div id="text-plex-notifications-dropdown" class="notif-dropdown" style="@if(isset($setting_notification['text']['all_plexuss_notifications'])) display: none; @else display: block; @endif">
                            <div class="row notif-opt-container">
                                <div class="column small-10 name">
                                    College Stats Updates
                                </div>
                                <div class="column small-2 text-right option">
                                    <div class="switch round">
                                        <input id="text-switch7" type="checkbox" name="college_stats_updates" @if(!isset($setting_notification['text']['college_stats_updates'])) checked="checked" @endif>
                                        <label for="text-switch7"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row notif-opt-container">
                                <div class="column small-10 name">
                                    College News Updates
                                </div>
                                <div class="column small-2 text-right option">
                                    <div class="switch round">
                                        <input id="text-switch8" type="checkbox" name="college_news_updates" @if(!isset($setting_notification['text']['college_news_updates'])) checked="checked" @endif>
                                        <label for="text-switch8"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button class="button radius save-notifications-btn">Save settings</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- \\\\\\\\\\\\\\\\\\\\\\\\\\ Data Preferences section begin ///////////////////////////// -->
    <div class="row collapse plex-data_preferences-section settings-section {{$arr['setting_section']['data_preferences'] or ''}} notification-container" id="plex-data_preferences">
        <div class="column small-12">

            <div></div>
            <div class="title">Data Preferences</div>

            <form>
                <input type="hidden" name="type" value="data_preferences">

                <div class="notif-row">
                    <div class="row notif-opt-container">
                        <div class="column small-8 name">
                            I wish to receive information from universities, plexuss media partners and other 3rd party opportunities
                        </div>
                        <div class="column small-2 small-text-left large-text-center option">
                            <div class="switch round">
                                <input id="switch_data_preferences" type="checkbox" name="data_preferences" @if((isset($setting_notification['data_preferences']['all']) && $setting_notification['data_preferences']['all'] == 'true') || !isset($setting_notification['data_preferences']['all']))  checked="checked"  @endif class="toggle-all">
                                <label for="switch_data_preferences"></label>
                            </div>
                        </div>
                        <div id="data_preferences-notifications" class="column small-2 details notif-details text-center">
                            <div>Details</div>
                            <div class="arrow"></div>
                        </div>
                    </div>

                    <div id="data_preferences-notifications-dropdown" class="notif-dropdown" style="@if(isset($setting_notification['email']['all_plexuss_notifications'])) display: none; @else display: block; @endif">
                        <div class="row notif-opt-container">
                            <div class="column small-3 name">
                                LCCA
                            </div>
                            <div class='small-7 column' style="padding-top:0.8em; ">
                                <span class='setting-rm-tooltip-mark'><div class="question-mark">?</div></span>
                                <div class='setting-rm-tooltip-text'>
                                    LCCA is part of the Global University Systems group of companies (<a target="_blank" href="https://www.gus-group.com/en/">GUS Group</a>). By agreeing to be contacted by this school, you agree to receive information about exciting offers, newsletters, events, scholarships, and bursaries from GUS Group institutions that are relevant to the courses you've indicated interest in. Additionally, the information you provide will be processed in accordance with <a target="_blank" href="https://www.gisma.com/privacy-policy">GUS Group's Privacy Policy</a>. <br/>

                                    If you change your mind at any time and no longer want to receive offers, newsletters, events, scholarships, or bursaries from GUS Group, you can withdraw your consent by contacting +44 (0)20 3535 1155 using the Unsubscribe link on any of future GUS group emails. 
                                    <div class='setting-rm-tooltip-triangle'></div>
                                </div>
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch_lcca" type="checkbox" name="lcca" @if(!isset($setting_notification['data_preferences']['lcca']) || (isset($setting_notification['data_preferences']['lcca']) && $setting_notification['data_preferences']['lcca'] == 'true')) checked="checked" @endif>
                                    <label for="switch_lcca"></label>
                                </div>
                            </div>
                        </div>

                        <div class="row notif-opt-container">
                            <div class="column small-3 name">
                                St Patrick’s
                            </div>
                            <div class='small-7 column' style="padding-top:0.8em; ">
                                <span class='setting-rm-tooltip-mark'><div class="question-mark">?</div></span>
                                <div class='setting-rm-tooltip-text'>
                                    St Patrick’s is part of the Global University Systems group of companies (<a target="_blank" href="https://www.gus-group.com/en/">GUS Group</a>). By agreeing to be contacted by this school, you agree to receive information about exciting offers, newsletters, events, scholarships, and bursaries from GUS Group institutions that are relevant to the courses you've indicated interest in. Additionally, the information you provide will be processed in accordance with <a target="_blank" href="https://www.gisma.com/privacy-policy">GUS Group's Privacy Policy</a>. <br/>

                                    If you change your mind at any time and no longer want to receive offers, newsletters, events, scholarships, or bursaries from GUS Group, you can withdraw your consent by contacting + 020 7287 6664 using the Unsubscribe link on any of future GUS group emails. 
                                    <div class='setting-rm-tooltip-triangle'></div>
                                </div>
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch_st_patrick" type="checkbox" name="st_patrick" @if(!isset($setting_notification['data_preferences']['st_patrick']) || (isset($setting_notification['data_preferences']['st_patrick']) && $setting_notification['data_preferences']['st_patrick'] == 'true')) checked="checked" @endif>
                                    <label for="switch_st_patrick"></label>
                                </div>
                            </div>
                        </div>

                        <div class="row notif-opt-container">
                            <div class="column small-3 name">
                                LBSF
                            </div>
                            <div class='small-7 column' style="padding-top:0.8em; ">
                                <span class='setting-rm-tooltip-mark'><div class="question-mark">?</div></span>
                                <div class='setting-rm-tooltip-text'>
                                    LBSF is part of the Global University Systems group of companies (<a target="_blank" href="https://www.gus-group.com/en/">GUS Group</a>). By agreeing to be contacted by this school, you agree to receive information about exciting offers, newsletters, events, scholarships, and bursaries from GUS Group institutions that are relevant to the courses you've indicated interest in. Additionally, the information you provide will be processed in accordance with <a target="_blank" href="https://www.gisma.com/privacy-policy">GUS Group's Privacy Policy</a>. <br/>

                                    If you change your mind at any time and no longer want to receive offers, newsletters, events, scholarships, or bursaries from GUS Group, you can withdraw your consent by contacting +44 (0) 20 3535 1122 using the Unsubscribe link on any of future GUS group emails. 
                                    <div class='setting-rm-tooltip-triangle'></div>
                                </div>
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch_lbsf" type="checkbox" name="lbsf" @if(!isset($setting_notification['data_preferences']['lbsf']) || (isset($setting_notification['data_preferences']['lbsf']) && $setting_notification['data_preferences']['lbsf'] == 'true')) checked="checked" @endif>
                                    <label for="switch_lbsf"></label>
                                </div>
                            </div>
                        </div>

                        <div class="row notif-opt-container">
                            <div class="column small-3 name">
                                GISMA
                            </div>
                            <div class='small-7 column' style="padding-top:0.8em; ">
                                <span class='setting-rm-tooltip-mark'><div class="question-mark">?</div></span>
                                <div class='setting-rm-tooltip-text'>
                                    GISMA is part of the Global University Systems group of companies (<a target="_blank" href="https://www.gus-group.com/en/">GUS Group</a>). By agreeing to be contacted by this school, you agree to receive information about exciting offers, newsletters, events, scholarships, and bursaries from GUS Group institutions that are relevant to the courses you've indicated interest in. Additionally, the information you provide will be processed in accordance with <a target="_blank" href="https://www.gisma.com/privacy-policy">GUS Group's Privacy Policy</a>. <br/>

                                    If you change your mind at any time and no longer want to receive offers, newsletters, events, scholarships, or bursaries from GUS Group, you can withdraw your consent by contacting +49 (0) 30 58 58 409-50 using the Unsubscribe link on any of future GUS group emails. 
                                    <div class='setting-rm-tooltip-triangle'></div>
                                </div>
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch_gisma" type="checkbox" name="gisma" @if(!isset($setting_notification['data_preferences']['gisma']) || (isset($setting_notification['data_preferences']['gisma']) && $setting_notification['data_preferences']['gisma'] == 'true')) checked="checked" @endif>
                                    <label for="switch_gisma"></label>
                                </div>
                            </div>
                        </div>

                        <div class="row notif-opt-container">
                            <div class="column small-3 name">
                                Arden University Limited
                            </div>
                            <div class='small-7 column' style="padding-top:0.8em; ">
                                <span class='setting-rm-tooltip-mark'><div class="question-mark">?</div></span>
                                <div class='setting-rm-tooltip-text'>
                                    Arden University Limited is part of the Global University Systems group of companies (<a target="_blank" href="https://www.gus-group.com/en/">GUS Group</a>). By agreeing to be contacted by this school, you agree to receive information about exciting offers, newsletters, events, scholarships, and bursaries from GUS Group institutions that are relevant to the courses you've indicated interest in. Additionally, the information you provide will be processed in accordance with <a target="_blank" href="https://www.gisma.com/privacy-policy">GUS Group's Privacy Policy</a>. <br/>

                                    If you change your mind at any time and no longer want to receive offers, newsletters, events, scholarships, or bursaries from GUS Group, you can withdraw your consent by contacting 0808 115 3326 using the Unsubscribe link on any of future GUS group emails. 
                                    <div class='setting-rm-tooltip-triangle'></div>
                                </div>
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch_aul" type="checkbox" name="aul" @if(!isset($setting_notification['data_preferences']['aul']) || (isset($setting_notification['data_preferences']['aul']) && $setting_notification['data_preferences']['aul'] == 'true')) checked="checked" @endif>
                                    <label for="switch_aul"></label>
                                </div>
                            </div>
                        </div>

                        <div class="row notif-opt-container">
                            <div class="column small-3 name">
                                Berlin School of Business and Innovation
                            </div>
                            <div class='small-7 column' style="padding-top:0.8em; ">
                                <span class='setting-rm-tooltip-mark'><div class="question-mark">?</div></span>
                                <div class='setting-rm-tooltip-text'>
                                    Berlin School of Business and Innovation is part of the Global University Systems group of companies (<a target="_blank" href="https://www.gus-group.com/en/">GUS Group</a>). By agreeing to be contacted by this school, you agree to receive information about exciting offers, newsletters, events, scholarships, and bursaries from GUS Group institutions that are relevant to the courses you've indicated interest in. Additionally, the information you provide will be processed in accordance with <a target="_blank" href="https://www.gisma.com/privacy-policy">GUS Group's Privacy Policy</a>. <br/>

                                    If you change your mind at any time and no longer want to receive offers, newsletters, events, scholarships, or bursaries from GUS Group, you can withdraw your consent by contacting +49 3058 584 0959 using the Unsubscribe link on any of future GUS group emails. 
                                    <div class='setting-rm-tooltip-triangle'></div>
                                </div>
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch_bsbi" type="checkbox" name="bsbi" @if(!isset($setting_notification['data_preferences']['bsbi']) || (isset($setting_notification['data_preferences']['bsbi']) && $setting_notification['data_preferences']['bsbi'] == 'true')) checked="checked" @endif>
                                    <label for="switch_bsbi"></label>
                                </div>
                            </div>
                        </div>

                        <div class="row notif-opt-container">
                            <div class="column small-3 name">
                                TSOM
                            </div>
                            <div class='small-7 column' style="padding-top:0.8em; ">
                                <span class='setting-rm-tooltip-mark'><div class="question-mark">?</div></span>
                                <div class='setting-rm-tooltip-text'>
                                    TSOM is part of the Global University Systems group of companies (<a target="_blank" href="https://www.gus-group.com/en/">GUS Group</a>). By agreeing to be contacted by this school, you agree to receive information about exciting offers, newsletters, events, scholarships, and bursaries from GUS Group institutions that are relevant to the courses you've indicated interest in. Additionally, the information you provide will be processed in accordance with <a target="_blank" href="https://www.gisma.com/privacy-policy">GUS Group's Privacy Policy</a>. <br/>

                                    If you change your mind at any time and no longer want to receive offers, newsletters, events, scholarships, or bursaries from GUS Group, you can withdraw your consent by contacting +0014168002204 using the Unsubscribe link on any of future GUS group emails. 
                                    <div class='setting-rm-tooltip-triangle'></div>
                                </div>
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch_tsom" type="checkbox" name="tsom" @if(!isset($setting_notification['data_preferences']['tsom']) || (isset($setting_notification['data_preferences']['tsom']) && $setting_notification['data_preferences']['tsom'] == 'true')) checked="checked" @endif>
                                    <label for="switch_tsom"></label>
                                </div>
                            </div>
                        </div>

                        <div class="row notif-opt-container">
                            <div class="column small-3 name">
                                TLG
                            </div>
                            <div class='small-7 column' style="padding-top:0.8em; ">
                                <span class='setting-rm-tooltip-mark'><div class="question-mark">?</div></span>
                                <div class='setting-rm-tooltip-text'>
                                    TLG is part of the Global University Systems group of companies (<a target="_blank" href="https://www.gus-group.com/en/">GUS Group</a>). By agreeing to be contacted by this school, you agree to receive information about exciting offers, newsletters, events, scholarships, and bursaries from GUS Group institutions that are relevant to the courses you've indicated interest in. Additionally, the information you provide will be processed in accordance with <a target="_blank" href="https://www.gisma.com/privacy-policy">GUS Group's Privacy Policy</a>. <br/>

                                    If you change your mind at any time and no longer want to receive offers, newsletters, events, scholarships, or bursaries from GUS Group, you can withdraw your consent by contacting +44 (0) 203 435 4569 using the Unsubscribe link on any of future GUS group emails. 
                                    <div class='setting-rm-tooltip-triangle'></div>
                                </div>
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch_tlg" type="checkbox" name="tlg" @if(!isset($setting_notification['data_preferences']['tlg']) || (isset($setting_notification['data_preferences']['tlg']) && $setting_notification['data_preferences']['tlg'] == 'true')) checked="checked" @endif>
                                    <label for="switch_tlg"></label>
                                </div>
                            </div>
                        </div>

                        <div class="row notif-opt-container">
                            <div class="column small-3 name">
                                U-Law
                            </div>
                            <div class='small-7 column' style="padding-top:0.8em; ">
                                <span class='setting-rm-tooltip-mark'><div class="question-mark">?</div></span>
                                <div class='setting-rm-tooltip-text'>
                                    U-Law is part of the Global University Systems group of companies (<a target="_blank" href="https://www.gus-group.com/en/">GUS Group</a>). By agreeing to be contacted by this school, you agree to receive information about exciting offers, newsletters, events, scholarships, and bursaries from GUS Group institutions that are relevant to the courses you've indicated interest in. Additionally, the information you provide will be processed in accordance with <a target="_blank" href="https://www.gisma.com/privacy-policy">GUS Group's Privacy Policy</a>. <br/>

                                    If you change your mind at any time and no longer want to receive offers, newsletters, events, scholarships, or bursaries from GUS Group, you can withdraw your consent by contacting +44 1483 216308 using the Unsubscribe link on any of future GUS group emails. 
                                    <div class='setting-rm-tooltip-triangle'></div>
                                </div>
                            </div>
                            <div class="column small-2 text-right option">
                                <div class="switch round">
                                    <input id="switch_ulaw" type="checkbox" name="ulaw" @if(!isset($setting_notification['data_preferences']['ulaw']) || (isset($setting_notification['data_preferences']['ulaw']) && $setting_notification['data_preferences']['ulaw'] == 'true')) checked="checked" @endif>
                                    <label for="switch_ulaw"></label>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="text-right">
                    <button class="button radius save-data_preferences-btn">Save settings</button>
                </div>

            </form>

        </div>
    </div>
    <!-- ////////////////////////// Data Preferences section end \\\\\\\\\\\\\\\\\\\\\\\\\\ -->


    <div id="phone-removal-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
        <div class="text-right close-reveal-modal">x</div>

        <div class="question">Are you sure you want to delete your phone number?</div>

        <div class="btn-grp text-center">
            <div class="button radius close-reveal-modal">Cancel</div>
            <div class="button radius" id="yes-delete-my-phone-btn">Yes</div>
        </div>
    </div>
@stop
