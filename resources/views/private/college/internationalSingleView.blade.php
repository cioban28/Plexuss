@extends('private.college.master')
@section('collegenav')
    <?php
        $CollegeData = $college_data;
        $undergrad_grad_data = $undergrad_grad_data;

        $aor_id = null;
        isset($CollegeData->aor_id) ? $aor_id = $CollegeData->aor_id : null;
        // dd($undergrad_grad_data['define_program']);
        // echo '<pre>';
        // print_r($CollegeData->aor_id);
        // echo '</pre>';
        // exit;
    ?>
    {{ Form::hidden('college_aor_id', $aor_id, array('id' => 'college_aor_id')) }}
    <!-- recruit me MOBILE menu area. This will be review later. -->
    <div class="row hide-for-large-up">

        <!-- This is the Recruit Me / Compare buttons on tablet and mobile view -->
        @if($signed_in == 1)
            @if ($isInUserList == 0)
                @if( isset($profile_perc) && $profile_perc < 30 )
                    <div class="small-12 medium-6 columns is-redirect" data-cid="{{$CollegeData->id}}">
                @else
                    <div class="small-12 medium-6 columns " data-reveal-id="recruitmeModal" data-reveal-ajax="/ajax/recruiteme/{{$CollegeData->id}}">
                @endif
                        <div class='mob-recruit-btn text-left grey-btn'>
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
                    <div class='mob-recruit-btn text-left grey-btn'>
                        <img src="/images/colleges/recruit-me-white.png" alt=""/>Get Recruited!
                    </div>
                </div>
            </a>
        @endif

        <div class="small-12 medium-6 text-left columns ">
            <a href="{{ '/comparison?UrlSlugs=' . $CollegeData->slug  }}">
                <div class='mob-vs-btn'>
                    <img src="/images/colleges/compare-schools-gloves_white.png" alt=""/>Compare Schools
                </div>
            </a>
        </div>
        
    </div>


    
    <!-- This is the white Header area at the top of the College single view page. -->
    <div itemscope itemtype="http://schema.org/CollegeOrUniversity" class="row">
        <div class="large-12 columns">
            <div class='row collapse paddingtb-university-name-panel bg-pure-white'>
            
                <div class="column small-12">
                    <div class="row collapse">

                        <div class="small-12 column text-right">
                            
                            <a class="toggle-international" href="/college/{{ $CollegeData->slug }}?showUS=true">
                                <span class="flag flag-us"></span>
                                <span class="toggle-int-text">U.S. Student View</span>
                            </a>
                            
                            <div class="plex-college-rank">
                                <span>#{{ $CollegeData->news['plexuss'] or 'na' }}</span>
                            </div>

                        </div>
                    </div>
                </div>
                
                <div class="small-4 small-push-4 small-text-center medium-2 medium-push-5 large-2 large-pull-0 columns">
                    @if(isset( $CollegeData->logo_url))
                        <a itemprop = "url" href= "/college/{{ $CollegeData->slug  }}/"> <img itemprop="logo" class="schoolHeaderLogo" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$CollegeData->logo_url}}" alt=""/></a> 
                    @endif
                </div>
                
                
                <div class="small-12 large-10 columns">

                    <div class="row">
                        <div class="column large-12">
                            <div class="row">
                                <h1 itemprop="name" class="large-12 university-name small-text-center large-text-right">
                                    {{{$CollegeData->school_name}}}
                                </h1>
                                @if (isset($is_online_school) && $is_online_school == true)
                                <div class="college-online-school-label">
                                    Online
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
                                    <span class="flag flag-{{ $CollegeData->country_code or ''}}"> </span>
                                    {{{ $CollegeData->zip != '' && $CollegeData->zip != ' ' ? ' | ' : '' }}} 
                                    @if (!isset($is_online_school) || $is_online_school == false)
                                        <span itemprop="telephone">{{{ $CollegeData->general_phone }}}</span>
                                    @endif
                                  </span>
                                </div>

                                @if( isset($CollegeData->aor_id) && $CollegeData->aor_id == 5 )
                                    <div class="large-12 university-address small-text-center large-text-right els-accepts">
                                        <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/els_logo.png" style="width: 50px;" alt="ELS Image">
                                        <span>This University accepts ELS English Certificate of Completion</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix-padding"></div>
    <!-- This is the middle college menu where user can select overview , stats, ranking, ect -->
    <div class='row' id="college-middle-menu" >
        <div class="column small-12 medium-small-topnav">
            <div class="row">
                <div class="column small-12 college-dropdown university-attached-menu" onclick="topnavInternational();">
                    <div class="row">
                        <div class="column small-12 medium-6 small-text-center medium-text-left menuitem mobileMenuTitle"><span class="icon-overview sprite"></span>Overview</div>
                        <div class="column small-12 medium-6 small-text-center medium-text-right menuitem">Select another topic <span class='arrow'></span>
                        </div>
                    </div>
                        
                </div>
            </div>
        </div>
        <div class='column small-12'>
            <div id="int_topNav_container">
                <ul class="int-topnav clearfix">
                    <li class="overview-tab" onclick="loadCollegeInfo('overview', '{{$CollegeId}}', this);return false;" data-link="stats" data-ytchannel="{{$college_data->yt_overview_vids or ''}}" data-virtualtour="{{ $college_data->virtualTour_overview or '' }}">
                        <div class="topnav-li-container">
                        <a class="tab-overview" href= "/college/{{ $CollegeData->slug  }}/overview">
                            <span class="icon-overview international-sprite sprite"></span>Overview
                        </a>
                        </div>
                    </li>


                    <!-- college requirements -->
                   
                        

                        <!-- list item on nav bar -->

                        <!-- just undergrad program -->
                        @if( isset($undergrad_grad_data['define_program']) && 
                        $undergrad_grad_data['define_program']['epp'] == false &&
                        $undergrad_grad_data['define_program']['undergrad'] == true && 
                        $undergrad_grad_data['define_program']['grad'] == false)
                        <li class="undergradGradepp-tab undergrad-tab text-left" onclick="loadCollegeInfo('undergrad', '{{$CollegeId}}', this);return false;" data-link="undergrad">
                            <div class="topnav-li-container">
                                <a class="tab-undergrad tab-undergradengP">
                                    <span class="icon-undergrad-m icon-undergrad-menu international-sprite sprite"></span>
                                    <span id="undergrad-grad-text">Undergraduate Requirements</span>
                                  
                                </a>
                            </div>
                        
                        
                        <!-- just grad program  -->
                        @elseif( isset($undergrad_grad_data['define_program']) && 
                        $undergrad_grad_data['define_program']['epp'] == false &&
                        $undergrad_grad_data['define_program']['undergrad'] == false && 
                        $undergrad_grad_data['define_program']['grad'] == true)
                          <li class="undergradGradepp-tab undergrad-tab text-left" onclick="loadCollegeInfo('grad', '{{$CollegeId}}', this);return false;" data-link="grad">
                            <div class="topnav-li-container">
                                <a class="tab-grad tab-undergradengP">
                                    <span class="icon-undergrad-m icon-undergrad-menu international-sprite sprite"></span>
                                    <span id="undergrad-grad-text">Graduate Requirements</span>
                                    
                                </a>
                            </div>
                            
                        <!-- both grad and undergrad, no English pathway   -->
                        @elseif( isset($undergrad_grad_data['define_program']) && 
                        $undergrad_grad_data['define_program']['epp'] == false &&
                        $undergrad_grad_data['define_program']['undergrad'] == true && 
                        $undergrad_grad_data['define_program']['grad'] == true)
                         <li class="undergradGradepp-tab grad-tab"  onclick="undergradGradDropdown()">
                            <div class="topnav-li-container">
                                    <a class="tab-undergrad tab-undergradengP">
                                        <span class="icon-undergrad-m icon-undergrad-menu international-sprite sprite"></span>
                                        <span id="undergrad-grad-text">Undergraduate Requirements</span>
                                        <span  class='arrow more-dropdown-arrow'></span>
                                    </a>
                            </div>
                            <ul id="underGrad-dropdown">
                                <li class="undergrad-tab text-left" onclick="loadCollegeInfo('undergrad', '{{$CollegeId}}', this);return false;" data-link="undergrad">
                                    <a class="tab-undergrad" href="/college/{{ $CollegeData->slug  }}/undergrad">
                                        <span class="icon-undergrad international-sprite sprite"></span>
                                        <span id="menu-title">Undergraduate Requirements</span>
                                    </a>

                                </li>
                                <li class=" grad-tab text-left" onclick="loadCollegeInfo('grad', '{{$CollegeId}}', this);return false;" data-link="grad">
                                    <a class="tab-grad" href="/college/{{ $CollegeData->slug  }}/grad">
                                        <span class="icon-grad international-sprite sprite"></span>
                                        Graduate Requirements
                                    </a>
                                </li>
                            </ul>

                         <!-- just epp  -->
                        @elseif( isset($undergrad_grad_data['define_program']) && 
                        $undergrad_grad_data['define_program']['epp'] == true &&
                        $undergrad_grad_data['define_program']['undergrad'] == false && 
                        $undergrad_grad_data['define_program']['grad'] == false)
                        <li class="undergradGradepp-tab undergrad-tab text-left" onclick="loadCollegeInfo('epp', '{{$CollegeId}}', this);return false;" data-link="epp">
                            <div class="topnav-li-container">
                                <a class="tab-engP tab-undergradengP">
                                    <span class="icon-engP icon-undergrad-m  icon-undergrad-menu international-sprite sprite"></span>
                                    @if(isset($CollegeData->aor_id) && $CollegeData->aor_id === 5)
                                    <span id="undergrad-grad-text">ELS English Program</span>
                                    @else
                                    <span id="undergrad-grad-text">English Program</span>
                                    @endif
                                </a>
                            </div>

                        <!-- epp and undergrad      -->
                        @elseif( isset($undergrad_grad_data['define_program']) && 
                        $undergrad_grad_data['define_program']['epp'] == true &&
                        $undergrad_grad_data['define_program']['undergrad'] == true && 
                        $undergrad_grad_data['define_program']['grad'] == false)
                        <li class="undergradGradepp-tab grad-tab"  onclick="undergradGradDropdown()">
                            <div class="topnav-li-container">
                                    <a class="tab-undergrad tab-undergradengP">
                                        <span class="icon-undergrad-m icon-undergrad-menu international-sprite sprite"></span>
                                        <span id="undergrad-grad-text">Undergraduate Requirements</span>
                                        <span  class='arrow more-dropdown-arrow'></span>
                                    </a>
                            </div>

                            <ul id="underGrad-dropdown">
                                <li class="engP-tab text-left" onclick="loadCollegeInfo('epp', '{{$CollegeId}}', this);return false;" data-link="epp">
                                    <a class="tab-engP" href="/college/{{ $CollegeData->slug  }}/epp">
                                        <span class="icon-engP icon-undergrad-m  icon-undergrad-menu international-sprite sprite"></span>
                                        @if(isset($CollegeData->aor_id) && $CollegeData->aor_id == 5)
                                        <span id="undergrad-grad-text">ELS English Program</span>
                                        @else
                                        <span id="undergrad-grad-text">English Program</span>
                                        @endif
                                    </a>

                                </li>
                                <li class="undergrad-tab text-left" onclick="loadCollegeInfo('undergrad', '{{$CollegeId}}', this);return false;" data-link="undergrad">
                                    <a class="tab-undergrad" href="/college/{{ $CollegeData->slug  }}/undergrad">
                                        <span class="icon-undergrad international-sprite sprite"></span>
                                        <span id="menu-title">Undergraduate Requirements</span>
                                    </a>

                                </li>
                            </ul>


                        <!-- epp and grad     -->
                        @elseif( isset($undergrad_grad_data['define_program']) && 
                        $undergrad_grad_data['define_program']['epp'] == true &&
                        $undergrad_grad_data['define_program']['undergrad'] == false && 
                        $undergrad_grad_data['define_program']['grad'] == true)
                         <li class="undergradGradepp-tab grad-tab"  onclick="undergradGradDropdown()">
                            <div class="topnav-li-container">
                                    <a class="tab-undergrad tab-undergradengP">
                                        <span class="icon-undergrad-m icon-undergrad-menu international-sprite sprite"></span>
                                        <span id="undergrad-grad-text">Graduate Requirements</span>
                                        <span  class='arrow more-dropdown-arrow'></span>
                                    </a>
                            </div>

                            <ul id="underGrad-dropdown">
                                <li class="engP-tab text-left" onclick="loadCollegeInfo('epp', '{{$CollegeId}}', this);return false;" data-link="epp">
                                    <a class="tab-engP" href="/college/{{ $CollegeData->slug  }}/epp">
                                        <span class="icon-engP icon-undergrad-m  icon-undergrad-menu international-sprite sprite"></span>
                                        @if(isset($CollegeData->aor_id) && $CollegeData->aor_id == 5)
                                        <span id="undergrad-grad-text">ELS English Program</span>
                                        @else
                                        <span id="undergrad-grad-text">English Program</span>
                                        @endif
                                    </a>

                                </li>
                                <li class="grad-tab text-left" onclick="loadCollegeInfo('grad', '{{$CollegeId}}', this);return false;" data-link="grad">
                                    <a class="tab-grad" href="/college/{{ $CollegeData->slug  }}/grad">
                                        <span class="icon-grad international-sprite sprite"></span>
                                        Graduate Requirements
                                    </a>
                                </li>
                            </ul>   

                        <!-- all , undergrad/grad/English Pathway -->
                        @else
                        <li class="undergradGradepp-tab grad-tab"  onclick="undergradGradDropdown()">
                            <div class="topnav-li-container">
                                    <a class="tab-undergrad tab-undergradengP">
                                        <span class="icon-undergrad-m icon-undergrad-menu international-sprite sprite"></span>
                                        <span id="undergrad-grad-text">Undergraduate Requirements</span>
                                        <span  class='arrow more-dropdown-arrow'></span>
                                    </a>
                            </div>

                            <ul id="underGrad-dropdown">
                                <li class="engP-tab text-left" onclick="loadCollegeInfo('epp', '{{$CollegeId}}', this);return false;" data-link="epp">
                                    <a class="tab-engP" href="/college/{{ $CollegeData->slug  }}/epp">
                                        <span class="icon-engP icon-undergrad-m  icon-undergrad-menu international-sprite sprite"></span>
                                        @if(isset($CollegeData->aor_id) && $CollegeData->aor_id == 5)
                                        <span id="undergrad-grad-text">ELS English Program</span>
                                        @else
                                        <span id="undergrad-grad-text">English Program</span>
                                        @endif
                                    </a>

                                </li>
                                <li class="undergrad-tab text-left" onclick="loadCollegeInfo('undergrad', '{{$CollegeId}}', this);return false;" data-link="undergrad">
                                    <a class="tab-undergrad" href="/college/{{ $CollegeData->slug  }}/undergrad">
                                        <span class="icon-undergrad international-sprite sprite"></span>
                                        <span id="menu-title">Undergraduate Requirements</span>
                                    </a>

                                </li>
                                <li class="grad-tab text-left" onclick="loadCollegeInfo('grad', '{{$CollegeId}}', this);return false;" data-link="grad">
                                    <a class="tab-grad" href="/college/{{ $CollegeData->slug  }}/grad">
                                        <span class="icon-grad international-sprite sprite"></span>
                                        Graduate Requirements
                                    </a>
                                </li>
                            </ul>

                        @endif    
                    </li>
                    

                    <li class="ranking-tab" onclick="loadCollegeInfo('ranking', '{{$CollegeId}}', this);return false;" data-link="ranking">
                        <div class="topnav-li-container">
                        <a class="tab-ranking" href="/college/{{ $CollegeData->slug  }}/ranking">
                            <span class="icon-ranking international-sprite sprite"></span>Ranking
                        </a>
                        </div>
                    </li>
                    <li class="news-tab" onclick="loadCollegeInfo('news', '{{$CollegeId}}', this);return false;" data-link="news">
                        <div class="topnav-li-container">
                        <a class="tab-news" href="/college/{{ $CollegeData->slug }}/news">
                            <span class="icon-news international-sprite sprite"></span>News
                        </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

    </div>



<?php 
    // dd($pageViewType);
?>
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



                    <!-- ///////// recruit me button ////////////  -->
                    @if ($signed_in == 1)

                        @if ($isInUserList == 0)
                        
                            @if( isset($profile_perc) && $profile_perc < 30 )
                                <div class="row recruitment-btn college-engage-box-row is-redirect grey-btn" data-cid="{{$CollegeData->id}}">
                            @else
                                <div class="row recruitment-btn college-engage-box-row grey-btn" data-reveal-id="recruitmeModal" data-reveal-ajax="/ajax/recruiteme/{{$CollegeData->id}}">
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
                                <div id="get-recruited-btn-single-view" class="row recruitment-btn grey-btn">
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



                    <!-- \\\\\\\ compare schools button ////// -->
                   <!--  <div class="college-engage-box-row">
                        <a href="{{ '/comparison?UrlSlugs=' . $CollegeData->slug  }}">
                            <div id="compareSchoolsBtnSingleViewColPg" class="row compare-schools-btn">
                                <div class="small-3 column text-center"><img src="/images/colleges/compare-schools-gloves_white.png" alt=""></div>
                                <div class="small-9 column text-left">Compare Schools</div>
                            </div>
                        </a>
                    </div> -->




                    <?php
                        // $time = date('G');
                        // $showSkype = false;
                        // if($time >= 9 && $time <= 16){
                        //     $showSkype = true;
                        // }
                      // @if(!$showSkype) hideBtn @endif
                    ?>
                  


                   
                    @if(isset($plexuss_skype_call_chat))
                    <!-- \\\\\\\ skype schools button ////// -->
                    <!-- shown anytime between 9am-5pm -->
                    <div class="college-engage-box-row college-skypeButton">
                        <div class="row skype-schools-btn">
                            <div class="column small-3 text-center"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skypeicon.png" alt=""></div>
                            <div class="column small-9 text-left"><a style="color:#ffffff;" href="skype:live:premium_156?call">Call</a>/<a style="color:#ffffff;" href="skype:live:premium_156?chat">Chat</a></div> 
                        </div>
                    </div>
                    @else
                        <!-- \\\\\\\\ chat now button //////// -->
                        <!-- on anytime not between 9a-5pm -->
                        @if( isset($chat['isLive']) && $chat['isLive'] == 1 )
                            <div class="row trigger-chat-btn grey-btn college-chatButton" onclick="Plex.triggerChatBtn();">
                                <div class="column small-3 text-center"><img src="/images/colleges/chat-now_white.png" alt=""></div>
                                <div class="column small-9 text-left">Chat now</div>
                            </div>
                        @else
                            <div class="college-engage-box-row college-chatButton">
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
                            </div>
                        @endif
                    @endif
                  


                    <!-- Apply now -->
                    @if(isset($CollegeData->paid_app_url))
                    <div id="collegeapplynow" class="college-engage-box-row">
                        <a href="/college-application" target="_blank">
                            <div class="row trigger-apply-btn  orange-btn">
                                <div class="column small-12 text-center">Apply Now</div>
                            </div>
                        </a>
                    </div>
                    @endif
                    <div id="hidden-this-college-slug" class="hide" data-slug="{{$college_slug or ''}}"></div>
                </div>
            </div>

        </div>
    </div>

    <!-- Ad Area -->
    <!--
    if(isset($college_data->aor_id) && $college_data->aor_id == 5)
        include('includes.els_ad')
    elseif( isset($eddy_found) && $eddy_found )
        <div id="eddyListings"></div>
    else
        include('private.includes.adsense-307x280')
    endif 
     
    -->

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

        <!-- adsense area -->
        @include('private.includes.adsense-307x280')
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
    <!-- adsense area -->
    @include('private.includes.adsense-307x280')
    <!-- RIGHT SIDE FOOTER (HELP, CONTACT, ABOUT ETC.) HERE! -->

    <!-- Ad area -->
    @if(isset($affiliate_ad) && !empty($affiliate_ad))
        @include('includes.ad_affiliates')
    @endif
    <div class="row">
        <div class="column small-12">
            @include('private.includes.right_side_footer')
        </div>
    </div>

@stop
