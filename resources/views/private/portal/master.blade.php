<!doctype html>
<html class="no-js" lang="en">
    <head>
        @include('private.headers.header')
        @include('includes.facebook_event_tracking')
        <style>
            .toggle-on{ text-indent:10px !important;}.toggle-off{text-indent:15px !important; }
        </style>
    </head>
    <body id="{{$currentPage}}">
        @include('private.includes.topnav')     
        
        @if( (isset($webinar_is_live) && $webinar_is_live == true) || (isset($is_any_school_live) && $is_any_school_live == true) || (isset($country_id) && $country_id != 1 && !isset($is_organization)) )
        <div class="portal-window chat-bar-visible">
        @else
        <div class="portal-window">
        @endif
            @if( isset($is_aor) && $is_aor == 1) 
                <?php $aor = 'yes'; ?>
            @else 
                <?php $aor = 'no'; ?>
            @endif

            <div id="portal-nav-window" class="hide-for-small-only" data-is-aor="{{$aor}}" data-userid="{{$user_id}}" data-fname="{{$userinfo['fname']}}">
                <!-- react component here -->
            </div>

            <div id="portal-user-window" class="sic-opened">
                <!-- Black tabs for all the portal section -->
                <div id="black-tabs" class="row collapse portal-tab show-for-small-only">
                    <div class="column small-7 medium-2">
                        <div class='schoolTab portalTab' onClick="loadPortalTabs('portal');" >Manage Schools</div>
                    </div>
                    <div class="column small-1 end">
                        <div class='messageTab portalTab @if( isset($is_aor) && $is_aor == 1) {{ "hide" }} @endif'  onClick="loadPortalTabs('messages');">Messages</div>
                    </div>
                </div><!-- END Black tabs for all the portal section -->
                      
                <div id="portalListwrapper">
                    <!-- Will be filled with AJAX during page load. -->
                </div>
            </div>


            <!-- SIC -->
            @include('includes.smartInteractiveColumn')

        </div><!-- END Black tabs for all the portal section -->

        <!-- temp socketio stuff -->
        <style type="text/css">
            #tempdiv{
                position: fixed;
                background: #000;
                color: #fff;
                z-index: 10000;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                max-width: 350px;
                margin: auto 0 auto auto;
            }

            #chatwindow{
                height: 200px;
                border: thin solid white;
                overflow-y: auto;
                padding: 10px;
            }
            #ellipsis{
                padding: 10px 0;    
                position: absolute;
                bottom: 100%;
                left: 5px;
                display: none;
            }
            #ellipsis > .ellipse{ 
                position: relative;
                display: inline-block; 
                border-radius: 15px;
                background: #ddd;
                color: #797979;
                padding: 0 10px;
                vertical-align: middle;
            }
            #ellipsis > .ellipse > .ellips-dot{
                display: inline-block;
                width: 4px;
                height: 4px;
                border-radius: 100%;
                background: #797979;
                animation: doTheWave 1.2s linear infinite;
            }

            #ellipsis > .ellipse > .ellips-dot:nth-child(2) { animation-delay: -1.0s; }
            #ellipsis > .ellipse > .ellips-dot:nth-child(3) { animation-delay: -0.8s; }

            #ellipsis > span{
                font-size: 12px;
                display: inline-block;
                padding: 0 0 0 5px;
                color: #797979;
            }

            .mine{
                text-align: right;
                color: green;
            }
            .yours{
                text-align: left;
                color: #eee;
            }
            .singlethread{
                border: 1px solid #fff;
                cursor: pointer;
                margin: 0 0 5px;
                color: #797979;
                font-size: 14;
            }
            .singlethread:hover, .singlethread.active{
                background: #049AC5;
                color: #fff;
            }
            #powerbtn{
                border: 1px solid #fff;
                border-radius: 3px;
                text-align: center;
                width: 200px;
                padding: 5px 0;
                margin: 5px 0;
                cursor: pointer; 
            }

            @keyframes doTheWave {
                0%, 60%, 100% { transform: initial; }
                30% { transform: translateY(-7px); }
            }
            @-webkit-keyframes doTheWave {
                0%, 60%, 100% { transform: initial; }
                30% { transform: translateY(-7px); }
            }
        </style>
        <!-- temp socketio stuff -->
     

        <!-- Portal Setting Popup  -->
        <div id="portalSettingModel" class="reveal-modal medium" data-reveal>   
            <div class="pos-rel model-inner-div">
                <div align="center" class="msgloader pt40 d-none pos-abs" style="left:360px; top:200px; z-index:1000"><img src="/images/AjaxLoader.gif"></div> 
                <div class="row mb20">
                        <span class="icon-arrow account_setting fs22 pl40 mobile-fs20" style="background-position:10px -307px; margin-left:-15px;">Portal Settings</span>
                </div>
                
                <div class="row">
                    <div class="small-4 column"><img src="/images/portal/spam.png"></div>   
                    <div class="small-8 column pt60">
                        <span class="fs36 f-bold">Plexuss is Anti-Spam!</span><br>
                        <span class="fs14 f-normal c79">Keep your Portal Spam-Free and choose the types of schools you’d like to hear from </span>
                    </div>  
                </div>

                <div class="row">
                    <span class="fs18 c79 c-black">I only want to hear from these types of schools:</span>                          
                    
                    <div class="mt20 mb20"> 
                        <form name="setting_form" class="setting_form">      
                        <div class="row mb20">         
                            <div class="small-12 medium-6 column no-padding ">
                                <div class="row">
                                    <div class="small-3  column no-padding">
                                            
                                            
                                            <?php if($userinfo['allow_private']==1){ $checked='checked="checked"';} else{$checked='';}?>                         
                                           
                                             <div class="switchcustom round tiny">
                                            <input type="checkbox" name="allow_private" id="allow_private" class="toggle" <?php echo $checked?>>
                                            <label for="allow_private"></label>
                                            </div>
                                    </div>
                                    <div class="small-9  column no-padding fs12">
                                        <span class="c-black">Private</span><span class="c79">(examples:  Columbia University)</span>                        
                                    </div>
                                </div>    
                            </div>
                        
                            <div class="small-12 medium-6 column no-padding m-mt10">
                                <div class="row">
                                    <div class="small-3 column no-padding">
                                            <?php if($userinfo['allow_4_year']==1){ $checked='checked="checked"';} else{$checked='';}?> 
                                         
                                            <div class="switchcustom round tiny">
                                            <input type="checkbox" name="allow_4_year" id="allow_4_year" class="toggle" <?php echo $checked?>>
                                            <label for="allow_4_year"></label>
                                            </div>
                                         
                                    </div>
                                    <div class="small-9 column no-padding fs12">
                                        <span class="c-black">4- Year</span>
                                    </div>
                                </div>    
                            </div>
                        </div>
                        
                        <div class="row mb20">         
                            <div class="small-12 medium-6 column no-padding">
                                <div class="row">
                                    <div class="small-3 column no-padding">
                                         <?php if($userinfo['allow_non_traditional']==1){ $checked='checked="checked"';} else{$checked='';}?> 
                                        <div class="switchcustom round tiny">
                                        <input type="checkbox" name="allow_non_traditional" id="allow_non_traditional" class="toggle" <?php echo $checked?>>
                                        <label for="allow_non_traditional"></label>
                                        </div>
                                         
                                    </div>
                                    <div class="small-9 column no-padding fs12">
                                        <span class="c-black"> Non-traditional</span><span class="c79">(examples: Berkeley college)</span>                       
                                    </div>
                                </div>    
                            </div>
                        
                            <div class="small-12 medium-6 column no-padding m-mt10">
                                <div class="row">
                                    <div class="small-3 column no-padding">
                                        <?php if($userinfo['only_ranked']==1){ $checked='checked="checked"';} else{$checked='';}?> 
                                    
                                         <div class="switchcustom round tiny">
                                        <input type="checkbox" name="only_ranked" id="only_ranked" class="toggle" <?php echo $checked?>>
                                        <label for="only_ranked"></label>
                                        </div>
                                    </div>
                                    <div class="small-9 column no-padding fs12">
                                        <span class="c-black">Only Ranked Schools <img src="/images/nav-icons/question-mark.png"></span>
                                    </div>
                                </div>    
                            </div>
                        </div>
                        
                        <div class="row">         
                            <div class="small-12 medium-6 column no-padding">
                                <div class="row">
                                    <div class="small-3 column no-padding">
                                         <?php if($userinfo['allow_public']=='1'){ $checked='checked="checked"';} else{$checked='';}?> 
                                        <div class="switchcustom round tiny">
                                        <input type="checkbox" name="allow_public" id="allow_public" class="toggle" <?php echo $checked?>>
                                        <label for="allow_public"></label>
                                        </div>
                                    </div>
                                    <div class="small-9 column no-padding fs12">
                                        <span class="c-black">Public</span><span class="c79">(examples: University of Michigan)</span>
                                    </div>
                                </div>    
                            </div>
                        
                            <div class="small-12 medium-6 column no-padding m-mt10">
                                <div class="row">
                                    <div class="small-3 column no-padding">
                                         <?php if($userinfo['allow_2_year']=='1'){ $checked='checked="checked"';} else{$checked='';}?> 
                                          <div class="switchcustom round tiny">
                                        <input type="checkbox" name="allow_2_year" id="allow_2_year" class="toggle" <?php echo $checked?>>
                                        <label for="allow_2_year"></label>
                                        </div>
                                    </div>
                                    <div class="small-9 column no-padding fs12">
                                        <span class="c-black">2- Year</span>
                                    </div>
                                </div>
                            
                                </div>    
                            </div>       
                        </form> 
                    </div>
                    
                    <span class="c79 fs12">You can also access and manage these settings through the <img src="/images/setting_gray.png"> settings in your Portal.</span> 
                    <div class="mb20 mt20">
                    <div class="c79 fs14" align="right"><span onClick="$('#portalSettingModel').foundation('reveal', 'close');">Cancel</span> 
                    <input type="button" value="Save" class="org-btn ml10" onClick="settingStatus('<?php echo $userinfo['id']?>');"/>
                    </div>
                    
                    </div>    
                </div>
            </div>    
            <a class="close-reveal-modal c-black">&#215;</a>
        </div>
        <!-- Portal Setting Popup  -->

        <!--  Plexuss Network Notification Popup  -->
        <div id="PlexussNotificationPopup" class="reveal-modal medium" data-reveal> <!--Notification Popup-->  </div>
        <!--  Plexuss Network Notification Popup  -->
        


        <!-- Add School Model -->
        <div id="portalAddSchoolModel" class="reveal-modal medium" data-reveal>
            <!-- close icon row -->
            <div class="row">
                <div class="column small-12 small-text-right">
                    <a class="close-reveal-modal c-black help_helpful_videos_close_icon">&#215;</a>
                </div>
            </div>
            
            <div class="row mb10 mt10">
                <div class='column small-12 fs30 f-normal'>
                    Add a schools to your list
                </div>
            </div>

            <div class="row mb20">
                <div class='column small-12 fs20 portal-addSchool-mod'>
                    Which college do you want to be recruited by?
                </div>
            </div>

            <div class="row mb30">
                <div class="small-12 column">
            
                    <form name="addschool_form" id="addschool_form">
                        <input type="text" name="addschool" id="addschool_1" placeholder="Start typing college name" >
                        <input type="hidden" name="college_id" id="college_id_1" value="" class="addschool-txt">
                        <input type="hidden" name="rowcountvar" id="rowcountvar" value="1">
                    </form>

                    <!-- <input type="text" name="addschool" placeholder="Start typing college name">-->
                </div>
                <!--
                <div class="small-5 medium-2 column pt10">
                    <input type="button" class="gray-btn f-bold" value="+ another school" style="font-size:11px; font-weight:bold;" onclick="addRow('dataTable')">
                </div>
                -->
            </div>
            

            <!-- cancel/save row -->
            <div class='row'>
                <div class="column small-12 large-push-6 large-6">

                    <div class="row">
                        <div class='column small-6 close-reveal-modal text-center'>
                            <div class="button btn-cancel">Cancel</div>
                        </div>
                        <div class='column small-6'>
                            <input id="addTomyList" type="button" value="Add to my list" class="button btn-save" data-reveal-ajax="/ajax/recruiteme/4480" data-reveal-id="recruitmeModal" style="cursor:pointer" />
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- end of add schools modal -->

        <!-- File attachment modal - include the view attchment modal -->
        @include('includes.fileAttachmentModal')

        <div class="joyride-layer" data-ride="{{$didJoyride or 0}}"></div>

        @include('private.includes.backToTop')
        @include('private.footers.footer')
        <script type="text/javascript">       
            $(document).ready(function(e) {
                //console.log("I am loading " + '{{ $section or ""}}');
                loadPortalTabs('{{ $section or ""}}', '{{ $school_id or ""}}');
            });
        </script>
    </body>
</html>
