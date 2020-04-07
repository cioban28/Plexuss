@extends('private.college.master')
@section('collegenav')
    <?php
        $CollegeData = $college_data;
    ?>

    <!-- recruit me MOBILE menu area. This will be review later. -->
    <div class="row hide-for-large-up">
         <div class='only_visible_not_on_scroll'>
        <!-- This is the Recruit Me / Compare buttons on tablet and mobile view -->
        @if($signed_in == 1)
            @if ($isInUserList == 0)
                @if( isset($profile_perc) && $profile_perc < 30 && $completed_signup == 0 )
                    <div class="small-12 medium-6 columns is-redirect" data-cid="{{$CollegeData->id}}">
                @else
                    <div class="small-12 medium-6 columns " data-reveal-id="recruitmeModal" data-reveal-ajax="/ajax/recruiteme/{{$CollegeData->id}}">
                @endif
                        <div class='mob-recruit-btn text-left orange-btn'>
                            <img src="/images/colleges/recruit-me-white.png" alt=""/>Get Recruited!
                        </div>
                    </div>
            @else
                <div class="small-12 medium-6 columns">
                    <div class='mob-recruit-btn-pending text-left'>
                        <img class="mob-already-added-icon" src="/images/colleges/recruitment-btn.png" alt="">&nbsp;&nbsp;Already on my list!
                    </div>
                </div>
            @endif
        @else
            <a href="/signup?requestType=recruitme&collegeId={{$CollegeData->id}}&utm_source=SEO&utm_medium={{$currentPage or ''}}&utm_content={{$CollegeData->id}}&utm_campaign=recruitme">
                <div class="small-12 medium-6 columns ">
                    <div class='mob-recruit-btn text-left orange-btn'>
                        <img src="/images/colleges/recruit-me-white.png" alt=""/>Get Recruited!
                    </div>
                </div>
            </a>
        @endif

        <div class="small-12 medium-6 text-left columns ">
            <a href="{{$CollegeData->paid_app_url or '/college-application'}}">
                <div class='mob-apply-btn'>
                    <img src="/images/colleges/apply-btn.png" alt=""/>Apply Now
                </div>
            </a>
        </div>

            <div class="small-12 medium-6 text-left columns ">
            <a  href="https://plexuss.com/adRedirect?company=edx&utm_source={{$college_slug}}&cid=2&uid={{$user_id}}">
                <div class='mob-vs-btn edx_img'>
                    <img src="/images/colleges/edx.png" alt=""/> Take a free course !
                </div>
            </a>
        </div>
    </div>
    </div>

    <!-- This is the white Header area at the top of the College single view page. -->
    <div itemscope itemtype="http://schema.org/CollegeOrUniversity" class="row">
        <div class="large-12 columns ">
            <div class='row collapse paddingtb-university-name-panel bg-pure-white' data-cid='{{$CollegeData->id}}'>
                <div class="column small-12">
                    <div class="row collapse">
                        <div class="small-12 column text-right">
                            @if(isset($show_international_tab) && $show_international_tab == true)
                            <a class="toggle-international" href="/college/{{ $CollegeData->slug }}/undergrad?showUS=false">
                                <span class="blue-globe-icon"></span>
                                <span class="toggle-int-text">International Student View</span>
                            </a>
                            @endif
                            <div class="plex-college-rank">
                                <span>#{{ $CollegeData->plexuss_ranking or 'na' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="small-4 small-push-4 small-text-center medium-2 medium-push-5 large-2 large-pull-0 columns">
                    @if(isset( $CollegeData->logo_url) && $CollegeData->id != 1785)
                        <a itemprop = "url" href= "/college/{{ $CollegeData->slug  }}/"> <img itemprop="logo" class="schoolHeaderLogo" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$CollegeData->logo_url}}" alt=""/></a>
                    @endif
                </div>


                <div class="small-12 large-10 columns">

                    <div class="row">
                        <div class="column large-12">
                            <div class="row">
                                <h1 itemprop="name" class="large-12 university-name small-text-center large-text-right">
                                    <span class="flag flag-{{ $CollegeData->country_code or ''}}"> </span>&nbsp;{{{$CollegeData->school_name}}}
                                </h1>
                                @if (isset($is_online_school) && $is_online_school == true)
                                <div class="college-online-school-label">
                                    Online School
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="column large-12">
                            <div class="row">
                                <div class="large-12 university-address small-text-center large-text-right">
                                  <span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                                    <span itemprop="streetAddress"> {{{ $CollegeData->address }}}</span>{{{ $CollegeData->address != '' && $CollegeData->address != ' ' ? ',' : '' }}}
                                    <span itemprop="addressLocality"> {{{ $CollegeData->city }}}</span>{{{ $CollegeData->city != '' && $CollegeData->city != ' ' ? ',' : '' }}}
                                    <span itemprop="addressRegion"> {{{ $CollegeData->state }}} </span>
                                    <span itemprop="postalCode"> {{{ $CollegeData->zip }}},</span>
                                    <span itemprop="country"> {{{ $CollegeData->country_name or '' }}}</span>

                                    {{{ $CollegeData->zip != '' && $CollegeData->zip != ' ' ? ' | ' : '' }}}
                                    <span itemprop="telephone">{{{ $CollegeData->general_phone }}}</span>
                                  </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="clearfix-padding"></div> --}}
    <!-- This is the middle college menu where user can select overview , stats, ranking, ect -->
    <div class='row' id="college-middle-menu" >
        <div class="column small-12 hide-for-large-up">
            <div class="row">
                <div class="column small-12 college-dropdown" onclick="collegeMiddleMobileMenuClicked(this);">
                    <div class="row">
                        <div class="column small-12 medium-6 small-text-center medium-text-left menuitem mobileMenuTitle"><span class="icon-overview sprite"></span>{{$pageViewType}}</div>
                        <div class="column small-12 medium-6 small-text-center medium-text-right menuitem">Select another topic <span class='arrow'></span></div>
                    </div>
                    <div class='flex-container dropDownCollegeMenu'>

                <li class="column small-12 medium-6 small-text-center" data-link="stats">
                    <a  href="/college/{{ $CollegeData->slug  }}/stats">
                        <span class="icon-stats sprite"></span>Stats
                    </a>
                </li>
                <li class="column small-12 medium-6 small-text-center" data-link="admissions">
                    <a  href="/college/{{ $CollegeData->slug  }}/admissions">
                        <span class="icon-admissions sprite"></span>Admissions
                    </a>
                </li>
                <li class="column small-12 medium-6 small-text-center" data-link="enrollment">
                    <a href="/college/{{ $CollegeData->slug  }}/enrollment">
                        <span class="icon-enrollment sprite"></span>Enrollment
                    </a>
                </li>
                <li class="column small-12 medium-6 small-text-center" data-link="ranking">
                    <a  href="/college/{{ $CollegeData->slug  }}/ranking">
                        <span class="icon-ranking sprite"></span>Ranking
                    </a>
                </li>
                <li class="column small-12 medium-6 small-text-center" data-link="tuition">
                    <a  href="/college/{{ $CollegeData->slug  }}/tuition">
                        <span class="icon-tuition sprite"></span>Tuition
                    </a>
                </li>
                <li class="column small-12 medium-6 small-text-center"  data-link="financial-aid">
                    <a class="tab-financial-aid" href="/college/{{ $CollegeData->slug  }}/financial-aid">
                        <span class="icon-financial-aid sprite"></span>&nbsp;&nbsp;&nbsp;Financial Aid
                    </a>
                </li>
                <li class="inner-list-menu college-more-icon tab-more" onmouseover="collegeMiddleDesktopMoreClicked();">More<span class='arrow more-dropdown-arrow'></span></li>
                    </div>
                </div>
            </div>
        </div>
        <div class='flex-container dropDownCollegeMenu'>
            <ul class="row bg-ext-black university-attached-menu">
                <li class="inner-list-menu college-stats-more yt-channel" onclick="loadCollegeInfo('overview', '{{$CollegeId}}', this);return false;" data-link="stats" data-ytchannel="{{$college_data->yt_overview_vids or ''}}" data-virtualtour="{{ $college_data->virtualTour_overview or '' }}">
                    <a class="tab-overview" href= "/college/{{ $CollegeData->slug  }}/overview">
                        <span class="icon-overview sprite"></span>Overview
                    </a>
                </li>
                <li class="inner-list-menu menu-dropdown mobile-hide college-stats-more" onclick="loadCollegeInfo('stats', '{{$CollegeId}}', this);return false;" data-link="stats">
                    <a class="tab-stats" href="/college/{{ $CollegeData->slug  }}/stats" onmouseover="collegeMiddleDesktopStatsOver();">
                        <span class="icon-stats sprite"></span>Stats<span class='arrow more-dropdown-arrow stats-arrow'></span>
                    </a>
                </li>
                <li class="inner-list-menu college-stats-more" onclick="loadCollegeInfo('ranking', '{{$CollegeId}}', this);return false;" data-link="ranking">
                    <a class="tab-ranking" href="/college/{{ $CollegeData->slug  }}/ranking">
                        <span class="icon-ranking sprite"></span>Ranking
                    </a>
                </li>

                <li class="inner-list-menu menu-dropdown mobile-hide college-stats-more" onclick="loadCollegeInfo('tuition', '{{$CollegeId}}', this);return false;" data-link="tuition">
                    <a class="tab-tuition" href="/college/{{ $CollegeData->slug  }}/tuition" onmouseover="collegeMiddleDesktopTuitionOver();">
                        <span class="icon-tuition sprite"></span>Tuition<span class='arrow more-dropdown-arrow tuition-arrow'></span>
                    </a>
                </li>

                <li class="small-12 small-text-left  large-2 large-text-center column text-center hide-for-large-up college-stats-more" onclick="loadCollegeInfo('stats', '{{$CollegeId}}', this);return false;" data-link="stats">
                    <a class="tab-stats" href="/college/{{ $CollegeData->slug  }}/stats">
                        <span class="icon-stats sprite"></span>Stats
                    </a>
                </li>

                <li class="small-12 small-text-left  large-2 large-text-center column text-center hide-for-large-up college-stats-more" onclick="loadCollegeInfo('admissions', '{{$CollegeId}}', this);return false;" data-link="admissions">
                    <a class="tab-admissions" href="/college/{{ $CollegeData->slug  }}/admissions">
                        <span class="icon-admissions sprite"></span>Admissions
                    </a>
                </li>

                <li class="small-12 small-text-left  large-2 large-text-center column text-center hide-for-large-up college-stats-more" onclick="loadCollegeInfo('tuition', '{{$CollegeId}}', this);return false;" data-link="tuition">
                    <a class="tuition-tab-tablet" href="/college/{{ $CollegeData->slug  }}/tuition">
                        <span class="icon-tuition sprite"></span>Tuition
                    </a>

                </li>
                <li class="small-12 small-text-left  large-2 large-text-center column text-center college-stats-more" onclick="loadCollegeInfo('news', '{{$CollegeId}}', this);return false;" data-link="news">
                    <a class="tab-news" href="/college/{{ $CollegeData->slug }}/news">
                        <span class="icon-news sprite"></span>News
                    </a>
                </li>

                <li class="small-12 small-text-left  large-2 large-text-center column text-center hide-for-large-up college-stats-more chat-with-btn" onclick="loadCollegeInfo('chat', '{{$CollegeId}}', this);return false;" data-link="stats">
                    <a class="tab-chat" href="/college/{{ $CollegeData->slug  }}/chat">
                        <span class="icon-chat sprite"></span>Chat
                    </a>
                </li>

                <li class="column college-stats-more small-12 hide-for-large-up" onclick="loadCollegeInfo('enrollment','{{$CollegeId}}', this);return false;" data-link="enrollment">
                    <a href="/college/{{ $CollegeData->slug  }}/enrollment">
                        <span class="icon-enrollment sprite"></span>Enrollment
                    </a>
                </li>
                <li class="inner-list-menu menu-dropdown mobile-hide college-stats-more" onclick="loadCollegeInfo('current-students', '{{$CollegeId}}', this);return false;" data-link="stats">
                    <a class="tab-current-student" href="/college/{{ $CollegeData->slug  }}/current-students" onmouseover="collegeMiddleDesktopCurrentStudentOver();">
                        Current Students
                    </a>
                </li>
                <li class="inner-list-menu college-more-icon tab-more" onmouseover="collegeMiddleDesktopMoreClicked();">More<span class='arrow more-dropdown-arrow'></span></li>
            </ul>
        </div>
        <div class="column small-12">
            <ul class="row largeMoreDropDown" onmouseup="closeAllCollegeMenuDropDowns();">
               <li class="column college-stats-more text-left chat-with-btn" onclick="loadCollegeInfo('chat', '{{$CollegeId}}', this);return false;" data-link="stats">
                    <a class="tab-chat" href="/college/{{ $CollegeData->slug  }}/chat">
                        <span class="icon-chat sprite"></span>&nbsp;&nbsp;&nbsp;Chat
                    </a>
                </li>
            </ul>
        </div>
        <!-- current student -->
        <div class="column small-12 mobile-hide">
            <ul class="row largeCurrentStudentDropDown" >
                <li class="column college-stats-more text-left chat-with-btn" onclick="loadCollegeInfo('alumni', '{{$CollegeId}}', this);return false;" data-link="students">
                    <a class="tab-chat" href="/college/{{ $CollegeData->slug  }}/alumni">
                        Alumni
                    </a>
                </li>
            </ul>
        </div>
        <!-- stats drop down menu -->
        <div class="column small-12">
            <ul class="row largeStatsDropDown" >
                <li class="column college-stats-more text-left" onclick="loadCollegeInfo('admissions', '{{$CollegeId}}', this);return false;" data-link="admissions">
                    <a class="tab-admissions" href="/college/{{ $CollegeData->slug  }}/admissions">
                        <span class="icon-admissions sprite"></span>   Admissions
                    </a>
                </li>

                <li class="column college-stats-more text-left" onclick="loadCollegeInfo('enrollment','{{$CollegeId}}', this);return false;" data-link="enrollment">
                    <a class="tab-enrollment" href="/college/{{ $CollegeData->slug  }}/enrollment">
                        <span class="icon-enrollment sprite"></span>&nbsp;&nbsp;&nbsp;Enrollment
                    </a>
                </li>
            </ul>
        </div>

        <!-- tuition drop down menu -->
        <div class="column small-12">
            <ul class="row largeTuitionDropDown" >
                <li class="column college-stats-more text-left" onclick="loadCollegeInfo('financial-aid', '{{$CollegeId}}', this);return false;" data-link="financial-aid">
                    <a class="tab-financial-aid" href="/college/{{ $CollegeData->slug  }}/financial-aid">
                        <span class="icon-financial-aid sprite"></span>&nbsp;&nbsp;&nbsp;Financial Aid
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- This is what pulls in the ajax 'type' info on php. Will be modified by JS by ajax. -->
    <div id="collegeInfoArea" data-collegeID='{{$CollegeId}}'>
        @include('private.college.ajax.' . $pageViewType)
        @if(isset($CollegeData->disclaimer) && !empty($CollegeData->disclaimer))
        <div class="row" style="margin: 0 0 100px;">
            <div class="columns small-12">
                {{$CollegeData->disclaimer or ''}}
            </div>
        </div>
        @endif
    </div>

    <!-- This is the boxes India created! WE will NOT want to use these. AO will handle the JS to ignore these. -->
    <div id='stats' class='collegePanel' data-type ></div>
    <div id='ranking' class="collegePanel row" data-type></div>
    <div id='value' class="collegePanel row" data-type></div>
    <div id='admissions' class="collegePanel row" data-type></div>
    <div id='tuition' class="collegePanel row" data-type></div>
    <div id='notables' class="collegePanel row" data-type></div>
    <div id='financial-aid' class="collegePanel row" data-type></div>
    <div id='campus' class="collegePanel row" data-type></div>
    <div id='athletics' class="collegePanel row" data-type></div>
    <div id='enrollment' class="collegePanel row" data-type></div>
    <div id='programs' class="collegePanel row" data-type></div>
    <!-- Do Not use the above!! -->
@stop



@section('sidebar')

    <!-- college engage box row -->
    <div class='row college-engage-box'>
        <div class="column small-12">

            <div class="row">
                <div class="large-12 column side-bar-news side-bar-1 show-for-medium-up" id='side-bar-1'>

                    <div class="row college-engage-box-row">
                        <div class="column small-12 text-center">
                            <div class="rightbox1-engage-title">Engage</div>
                        </div>
                    </div>


                    <!-- get recruited btn -->
                    @if ($signed_in == 1)

                        @if ($isInUserList == 0)

                            @if( isset($profile_perc) && $profile_perc < 30 && $completed_signup == 0 )
                                <div class="row recruitment-btn college-engage-box-row is-redirect orange-btn" data-cid="{{$CollegeData->id}}">
                            @else
                                <div class="row recruitment-btn college-engage-box-row orange-btn" data-reveal-id="recruitmeModal" data-reveal-ajax="/ajax/recruiteme/{{$CollegeData->id}}">
                            @endif
                                    <div class="small-3 column text-center">
                                        <img src="/images/colleges/recruit-me-white.png" alt="">
                                    </div>
                                    <div class="small-7 column text-left">
                                        Get Recruited!
                                    </div>
                                    <div class='small-2 column'>
                                        <span class='rm-tooltip-mark'><div class="question-mark">?</div></span>
                                        <div class='rm-tooltip-text'>
                                            By clicking on recruit me, you grant access for this college to communicate with you. Manage all of your communication through your Recruitment Portal
                                            <div class='rm-tooltip-triangle'></div>
                                        </div>
                                    </div>
                                </div>
                        @else
                            <div class="row recruitment-btn-pending college-engage-box-row">
                                <div class="small-3 column">
                                    <img src="/images/colleges/recruit-me-white.png" alt="">
                                </div>
                                <div class="small-9 column text-left">Already on my list!</div>
                            </div>
                        @endif

                    @else
                        <!-- redirect to signup of not signed in -->
                        <div class="college-engage-box-row">
                            <a href="/signup?requestType=recruitme&collegeId={{$CollegeData->id or ''}}&utm_source=SEO&utm_medium={{$currentPage or ''}}&utm_content={{$CollegeData->id or ''}}&utm_campaign=recruitme">
                                <div id="get-recruited-btn-single-view" class="row recruitment-btn orange-btn">
                                    <div class="large-3 small-3 column text-center">
                                        <img src="/images/colleges/recruit-me-white.png" alt="">
                                    </div>
                                    <div class="small-7 column btn-rec-title signedout-getrecruited text-left">Get Recruited!</div>
                                    <div class=' small-2 column btn-rec-tooltip signout-recruited-tooltip'>
                                        <span class='rm-tooltip-mark'><div class="question-mark">?</div></span>
                                        <div class='rm-tooltip-text'>
                                            By clicking on recruit me, you grant access for this college to communicate with you. Manage all of your communication through your Recruitment Portal
                                            <div class='rm-tooltip-triangle'>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    <!-- apply now -->
                    <div class="college-engage-box-row">
                        @if (strpos($CollegeData->paid_app_url, 'adRedirect') !== false)

                        <a  href="javascript:void(0)" data-reveal-id="learnmore"
                            id="collegeapplynow"
                            
                            data-source="college_apply_now"
                            data-url="{{$CollegeData->paid_app_url or ''}}"
                            data-slug="{{$college_slug or ''}}"  >
                            <div class="row trigger-apply-btn blue-btn">
                                <div class="small-3 column text-center"><img src="/images/colleges/apply-btn.png" alt="" style="margin-top: 1px;margin-bottom: -4px;padding-right: 6px;padding-left: 9px;height: auto;width: 52px;"></div>
                                <div class="small-9 column text-left">Apply Now</div>
                            </div>
                        </a>

                        @else

                        <a href="/college-application">
                            
                            <div class="row trigger-apply-btn blue-btn">
                                <div class="small-3 column text-center"><img src="/images/colleges/apply-btn.png" alt="" style="margin-top: 1px;margin-bottom: -4px;padding-right: 6px;padding-left: 9px;height: auto;width: 52px;"></div>
                                <div class="small-9 column text-left">Apply Now</div>
                            </div>
                        </a>

                        @endif
<div id="learnmore" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
<div class="fuller_sec">
 <div class="row">
    <div class="column small-12 text-center">
        <img itemprop="logo" class="school-logo-applymodal" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$CollegeData->logo_url}}" alt=""/>
    </div>
    <div class="columns small-12 text-center">
        <span style="color: #4D4D4D; font-size: 16pt;font-weight: bolder;">{{$CollegeData->school_name}} is a part of our network</span>
    </div>
    <div class="column small-12">
        &nbsp;
    </div>
    <div class="columns small-12 text-center" style="margin-bottom: 4em;">
        
        <span style="color: #4D4D4D; font-size: 14pt;">We would like to provide you with more information. You can stay on Plexuss or visit the school site  for more information.</span>
        
    </div>
    <br/><br/>
</div>

 <div class="learn-more-apply">
    <div class="learn_btn">
    <a href="{{$CollegeData->paid_app_url or '/college-application'}}" class="btn btn-learn-more">Learn More</a>
</div>
  <div class="plx_link">
    <a class="close-reveal-modal stay-plexsuss" style="color: #4D4D4D; font-size: 14pt;">Stay on Plexuss</a>
  </div>
  <div class="close_btn">
  <a class="close-reveal-modal closer_sec" aria-label="Close">&#215;</a>
</div>
</div>
</div>
</div>



                       </div>





                    <div class="college-engage-box-row">
                        <a  href="https://plexuss.com/adRedirect?company=edx&utm_source={{$college_slug}}&cid=2&uid={{$user_id}}" target="_blank">
                            <div class="row trigger-apply-btn orange-btn white_back">
                                 <div class="column small-3 text-center edx_img_sec"><img src="/images/colleges/edx.png" alt=""></div>
                                <div class="column small-9 text-center edxcourse">Take a Free Course !</div>
                            </div>
                        </a>
                    </div>
                    <?php
                        // $time = date('G');
                        // $showSkype = false;
                        // if($time >= 9 && $time <= 16){
                        //     $showSkype = true;
                        // }

                    //@if(!$showSkype) hideBtn @endif
                    ?>




                    @if(isset($plexuss_skype_call_chat))
                    <!-- \\\\\\\ skype schools button ////// -->
                    <!-- <div class="college-engage-box-row college-skypeButton ">
                        <div class="row skype-schools-btn">
                            <div class="column small-3 text-center"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skypeicon.png" alt=""></div>
                            <div class="column small-9 text-left"><a style="color:#ffffff;" href="skype:live:premium_156?call">Call</a>/<a style="color:#ffffff;" href="skype:live:premium_156?chat">Chat</a></div>
                        </div>
                    </div> -->
                    @else
                         <!-- \\\\\\\\ chat now button //////// -->
                       <!--  @if( isset($chat['isLive']) && $chat['isLive'] == 1 )
                            <div class="row trigger-chat-btn grey-btn college-chatButton" onclick="Plex.triggerChatBtn();">
                                <div class="column small-3 text-center"><img src="/images/colleges/chat-now_white.png" alt=""></div>
                                <div class="column small-9 text-left">Chat now</div>
                            </div>
                        @else -->
                            <!-- <div class="college-engage-box-row college-chatButton ">
                                @if( isset($CollegeData->in_our_network) && $CollegeData->in_our_network == 1 )
                                    <a href="/portal/messages/{{$CollegeData->id}}/college">
                                @else
                                    <a href="/portal/messages/7916/college?intendedCollegeId={{$CollegeData->id}}">
                                @endif
                                    <div class="row trigger-chat-btn grey-btn">
                                        <div class="column small-3 text-center"><img src="/images/colleges/chat-now_white.png" alt=""></div>
                                        <div class="column small-9 text-left">Send Offline Message</div>
                                    </div>
                                </a>
                            </div> -->
                        <!-- @endif -->
                    @endif

                </div>
            </div>

        </div>
    </div>

    <!-- adsense area -->
    @if(isset($signed_in) && $signed_in == 0)
       <!-- include signup banner -->
       <!-- @include('includes.banner_ad') -->
    @endif

    @if(isset($college_slug) && $college_slug != 'trident-university-international')
        @include('includes.banner_top_right')
    @endif

    <!-- engagement carousel -->
    <div class="row">
        <div class="column small-12">
            <div class="row collapse">
                <div class="large-12 column">
                    @include('private.includes.right_side_get_started')
                </div>
            </div>
        </div>
    </div>

    <!-- invite friends -->
    @if( isset($signed_in) && $signed_in == 1 )
        <div class="row">
            <div class="column small-12">
                @include('private.includes.invite_friends_right_side')
            </div>
        </div>
    @endif

    <?php $shown_affilate_ad = false;  ?>
    <!-- Ad area -->
    @if(isset($rand_num) && $rand_num == 1)
        @if(isset($affiliate_ad) && !empty($affiliate_ad)  && $shown_affilate_ad == false)
            @include('includes.ad_affiliates')
            <?php $shown_affilate_ad = true;  ?>
        @elseif( isset($eddy_found) && $eddy_found && isset($college_slug) && $college_slug != 'trident-university-international')
            <div id="eddyListings"></div>
        @endif
    @else
        @if( isset($eddy_found) && $eddy_found && isset($college_slug) && $college_slug != 'trident-university-international')
            <div id="eddyListings"></div>
        @elseif(isset($affiliate_ad) && !empty($affiliate_ad)  && $shown_affilate_ad == false)
            @include('includes.ad_affiliates')
            <?php $shown_affilate_ad = true;  ?>
        @endif
    @endif

    <!-- plex video -->
    <div class="row">
        <div class="column small-12">
            <div class="row collapse">
                <div class="large-12 column side-bar-1">
                    <img class="rightside-plex-college-vid" data-reveal-id="college-home-ranking-video" src="/images/video-images/college-video.png" alt="Plexuss College Video">
                </div>
            </div>
        </div>
    </div>

    <!-- Ad area -->
    @if(isset($rand_num) && $rand_num == 0)
        @if(isset($affiliate_ad) && !empty($affiliate_ad) && $shown_affilate_ad == false)
            @include('includes.ad_affiliates')
            <?php $shown_affilate_ad = true;  ?>
        @elseif( isset($eddy_found) && $eddy_found && isset($college_slug) && $college_slug != 'trident-university-international' )
            <div id="eddyListings"></div>
        @endif
    @else
        @if( isset($eddy_found) && $eddy_found && isset($college_slug) && $college_slug != 'trident-university-international' )
            <div id="eddyListings"></div>
        @elseif(isset($affiliate_ad) && !empty($affiliate_ad) && $shown_affilate_ad == false)
            @include('includes.ad_affiliates')
            <?php $shown_affilate_ad = true;  ?>
        @endif
    @endif

    <div class="row">
        <div class="column small-12">
            @include('private.includes.right_side_footer')
        </div>
    </div>

@stop
