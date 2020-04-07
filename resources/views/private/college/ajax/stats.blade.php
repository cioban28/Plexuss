<?php
    $collegeData = $college_data;
    // dd($collegeData);
    //dd( get_defined_vars() );
?>
<!--///// social buttons div of holding \\\\\-->
<div id="share_div_of_holding"
	data-share_params='{
		"page_title":"{{ $collegeData->page_title }}",
		"image_prefix":"{{ $collegeData->share_image_path }}",
		"image_name":"{{ $collegeData->share_image }}"
	}'
></div>
<!--\\\\\ social buttons div of holding /////-->

<div class='row' style="border: solid 0px #ff0000;">
    <div class='column small-12'>
        <div style='display:block;'>
            <!--<div class="row university-stats-content show-for-small-only">
                <div class="small-12 columns no-padding">
                    <img src="/images/colleges/stats-top-content.jpg" style="border-radius:10px">
                </div>
            </div>-->
            
            <div class="row university-stats-content">
                <div class="large-12 columns no-padding bg-ext-black radial-bdr">
                    <div class="large-6 columns no-padding college-rank-divide">
                        <div class="row">
                            <div class='small-12 column university_content_admis_deadline'>
                                STATS
                            </div>
                        </div>
                        <div class="detail-university-grey"><span>ADMISSION DEADLINE</span></div>
                        <div class="detail-university-green-content">{{ $collegeData->deadline or '' }}</div>
                        <div class="detail-university-grey">ACCEPTANCE RATE</div>
                        <div class="detail-university-green-content">
                            @if (isset($collegeData->acceptance_rate))
                                {{$collegeData->acceptance_rate}}% ACCEPTED
                            @else
                               N/A
                            @endif
                        </div>
                        <div class="detail-university-grey">STUDENT BODY SIZE</div>
                        <div class="large-12 columns detail-university-green-content stats-student-body-size-height">
                            <div class="large-6 small-6 columns bdr-dot-right text-left">
                            @if(isset($collegeData->student_body_total))
                                {{ number_format($collegeData->student_body_total) }}<br><span class="font-12">TOTAL</span>
                            @else
                                N/A<br><span class="font-12">TOTAL</span>
                            @endif
                            </div>
                            <div class="large-6 small-6 columns text-center">
                            @if(isset($collegeData->undergrad_enroll_1112))
                                {{ number_format($collegeData->undergrad_enroll_1112) }}<br><span class="font-12">UNDERGRAD</span>
                            @else
                                N/A<br><span class="font-12">UNDERGRAD</span>
                            @endif
                            </div>
                        </div>
                        <!-- Commenting out tuition stat for now because the value returns only 0 for all schools which is not correct
                        <div class="large-12 columns detail-university-grey">TUITION</div>
                        <div class="large-12 column detail-university-green-content text-left">
                            {{ '$' . number_format($collegeData->tuition_fees_1213)}}
                        </div>-->
                    </div>
                    @if( isset($collegeData->youtube_stats_videos) && count($collegeData->youtube_stats_videos) > 0 )
                    <div class="large-6 column yt-vid-stats">
                        @foreach( $collegeData->youtube_stats_videos as $vid )
                        <iframe width="100%" height="280" src="https://www.youtube.com/embed/{{$vid['video_id']}}" style="border:none;" allowfullscreen></iframe>
                        @endforeach
                    </div>
                    @else
                    <div class="large-6 columns no-padding hide-for-small-only">
                        <img src="/images/colleges/default-college-page-photo_3.jpg" class="default-coll-stats-img" alt="">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<div class="custom-row">
    <div class="row mt15">
        <div id="container-box" class="js-masonry">
            
            <!-- Graduation Doughnut Box -->
            @if(!isset($collegeData->hide_graduation_rate_4_year))
            <div class="box-div column small-12 medium-6" id="graduation-doughnut">
                <div class="">
                    <div class="bg-pure-white box-graduation-degree radial-bdr row collapse">
                        <div class="box-top-content-image column small-12"><img src="/images/colleges/box-1-top-content-img.png" alt=""></div>
                        <div class="text-center box-1-title-head">4 - Year<br><span>Graduation Rate</span></div>
                        <div class="text-center box-1-chart-donut">
                            <div style="width:100%; text-align:center;">
                                <input class="doughnut_grad_box" value="{{$collegeData->graduation_rate_4_year}}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!--General Information Box-->
            <div class="box-div column small-12 medium-6">
                <div class="">
                    <div class="bg-box-2 radial-bdr">
                        <div class="box-2-header">GENERAL INFORMATION</div>
                        <div class="box-2-content">
                            <div class="fs16"><span>Type :</span><br>{{$collegeData->school_sector}}</div>
                            <div class="fs16"><span>Campus setting:</span><br>{{{$collegeData->institution_size}}}</div>
                            <div class="fs16"><span>Campus housing:</span><br>{{{$collegeData->campus_housing}}}</div>
                            <div class="fs16"><span>Religious Affiliation:</span><br>{{{$collegeData->religious_affiliation}}}</div>
                            <div class="fs16"><span>Academic Calendar:</span><br>{{{$collegeData->calendar_system}}}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!--General Links Box  _______ hiding general links box on stats page
            <div class="box-div column small-12 medium-6">
                <div class="bg-box-3">

                    <div class="box-2-header">
                        GENERAL LINKS
                    </div>

                    <div class="box-3-content">

                        <div class="row">
                            @if(isset($collegeData->school_url) && strlen($collegeData->school_url) > 1 )
                            <div class="column small-12">
                                <a class="col-overview-link-hover" href="{{$collegeData->school_url}}" target="_blank"> > Website</a>
                            </div>
                            @endif

                            @if(isset($collegeData->admission_url) && strlen($collegeData->admission_url) > 1)
                            <div class="column small-12">
                                <a class="col-overview-link-hover" href="{{$collegeData->admission_url}}" target="_blank"> > Admissions</a>
                            </div>
                            @endif

                            @if(isset($collegeData->application_url) && strlen($collegeData->application_url) > 1)
                            <div class="column small-12">
                                <a class="col-overview-link-hover" href="{{$collegeData->application_url}}" target="_blank"> > Apply Online</a>
                            </div>
                            @endif

                            @if(isset($collegeData->financial_aid_url) && strlen($collegeData->financial_aid_url) > 1)
                            <div class="column small-12">
                                <a class="col-overview-link-hover" href="{{$collegeData->financial_aid_url}}" target="_blank"> > Financial Aid</a>
                            </div>
                            @endif

                            @if(isset($collegeData->calculator_url) && strlen($collegeData->calculator_url) > 1)
                            <div class="column small-12">
                                <a class="col-overview-link-hover" href="{{$collegeData->calculator_url}}" target="_blank"> > Net Price Calculator</a>
                            </div>
                            @endif

                            @if(isset($collegeData->mission_url) && strlen($collegeData->mission_url) > 1)
                            <div class="column small-12">
                                <a class="col-overview-link-hover" href="{{$collegeData->mission_url}}" target="_blank"> > Mission Statement</a>
                            </div>
                            @endif
                        </div>

                    </div>

                </div>
            </div>-->
            
        <!--  Incoming Average Freshmen GPA  -->
          @if(isset($collegeData->average_freshman_gpa))
           <div class="box-div column small-12 medium-6" id="incFresh-Ave-GPA">
                       <div class="row">
                           <div class="small-12 columns pin-stats-aveGPA-header text-center pin-stats-aveGPA">Average Incoming Freshmen GPA</div>
                       </div>
                       <div class="row">
                           <div class="small-12 columns pin-stats-aveGPA-header text-center pin-stats-aveGPA-body-text">{{$collegeData->average_freshman_gpa or "" }}</div>
                       </div>
           </div>
           @endif
            <!-- Average SAT Score 25th percentile Box -->
            @if(!isset($collegeData->hide_sat25percentile))
            <div class="box-div column small-12 medium-6" id="avg-sat25-percentile">
                <div class="">
                    <div class="margin10bottom" style="background-color:#a0db39;min-height: 354px;">
                        <div class="titleAvgBox">25th PERCENTILE<br /><br /><span>SAT SCORE</span></div>
                        <br />

                        <div class="text-center box-1-chart-donut row">
                            <br />
                            <div style="width:100%; text-align:center; height: 160px;">
                                <input class="sat-doughnut-box" value="{{$collegeData->sat25percentile or "" }}">
                            </div>
                            <div class="small-10 small-centered columns pos-arc-values">
                                <div class="row">
                                    <div class="column small-6">600</div>
                                    <div class="column small-6">2400</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endif

            
            <!-- Average SAT Score 75th percentile Box -->
            @if(!isset($collegeData->hide_sat75percentile))
            <div class="box-div column small-12 medium-6" id="avg-sat75-percentile">
                <div class="">
                    <div class="margin10bottom" style="background-color:#a0db39;min-height: 354px;">
                        <div class="titleAvgBox">75th PERCENTILE<br /><br /><span>SAT SCORE</span></div>
                        <br />

                        <div class="text-center box-1-chart-donut row">
                            <br />
                            <div style="width:100%; text-align:center; height: 160px;">
                                <input class="sat-doughnut-box" value="{{$collegeData->sat75percentile or "" }}">
                            </div>
                            <div class="small-10 small-centered columns pos-arc-values">
                                <div class="row">
                                    <div class="small-6 columns">600</div>
                                    <div class="small-6 columns">2400</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endif

            <!-- Average SAT Scores Graph -->
            @if(!isset($collegeData->hide_sat_percent))
            <div class="box-div column small-12 medium-6" id="SATScores-graph">
                <div class="">
                    <div class="min-h-300" style="background-color:#a0db39">
                        <div class="text-right share-btn-height"></div>
                        <div class="graph-image">
                            <div class="vertical-graph-cover">
                                <div class="vertical-graph" style="height:{{{ 100 - $collegeData->sat_percent }}}%"></div>
                            </div>
                            
                        </div>
                        <div class="graph-image">
                            <strong class="graph-per-fs text-white">{{$collegeData->sat_percent or "" }}%</strong>
                            <br>
                            <strong class="sub-per-fs text-white">SUBMITING</strong>
                            <br>
                            <strong class="sub-per-fs">SAT SCORES</strong>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            

            <!-- Average ACT Score 25th percentile Box -->
            @if(!isset($collegeData->hide_act_composite_25))
            <div class="box-div column small-12 medium-6" id="avg-act25-percentile">
                <div class="">
                    <div class="margin10bottom" style="background-color:#168f3a;min-height: 354px;">
                        <div class="titleAvgBox">25th PERCENTILE<br /><br /><span>ACT SCORE</span></div>
                        <br />
                        <div class="text-center box-1-chart-donut row">
                            <br />
                            <div style="width:100%; text-align:center; height: 160px;">
                                <input class="act-doughnut-box" value="{{$collegeData->act_composite_25 or "" }}">
                            </div>
                            <div class="small-10 small-centered columns pos-arc-values">
                                <div class="row">
                                    <div class="small-6 columns">1</div>
                                    <div class="small-6 columns">36</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Average ACT Score 75th percentile Box -->
            @if(!isset($collegeData->hide_act_composite_75))
            <div class="box-div column small-12 medium-6" id="avg-act75-percentile">
                <div class="">
                    <div class="margin10bottom" style="background-color:#168f3a;min-height: 354px;">
                        <div class="titleAvgBox">75th PERCENTILE<br /><br /><span>ACT SCORE</span></div>
                        <br />
                        <div class="text-center box-1-chart-donut row">
                            <br />
                            <div style="width:100%; text-align:center; height: 160px;">
                                <input class="act-doughnut-box" value="{{$collegeData->act_composite_75 or "" }}">
                            </div>
                            <div class="small-10 small-centered columns pos-arc-values">
                                <div class="row">
                                    <div class="small-6 columns">1</div>
                                    <div class="small-6 columns">36</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Average ACT Scores Graph -->
            @if(!isset($collegeData->hide_act_percent))
            <div class="box-div column small-12 medium-6" id="ACTScores-graph">
                <div class="">
                    <div class="radial-bdr margin10bottom min-h-300 ml5" style="background-color:#168f3a;">
                        <div class="text-right share-btn-height"></div>
                        <div class="graph-image">
                            <div class="vertical-graph-cover">
                                <div class="vertical-graph" style="height:{{ 100 - $collegeData->act_percent }}%"></div>
                            </div>
                        </div>
                        <div class="graph-image">
                            <strong class="graph-per-fs text-white">{{$collegeData->act_percent or "" }}%</strong>
                            <br>
                            <strong class="sub-per-fs text-white">SUBMITING</strong>
                            <br>
                            <strong class="sub-per-fs">ACT SCORES</strong>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Student to Faculty Ratio Box -->
            @if(!isset($collegeData->hide_student_faculty_ratio))
            <div class="box-div column small-12 medium-6">
                <div class="">
                    <div class="row2-bg-box-3 radial-bdr margin10bottom">
                        <div class="box-3-content no-margin">
                            <img src="/images/colleges/row-2-image-3.png" style="width:100%; border-radius: 5px 5px 0 0;" alt="">
                            <p class="text-center student-ratio-box-head">
                                <strong>STUDENT</strong>
                                <span>TO</span>
                                <strong class="text-green">FACULTY</strong>
                                <br>
                                <span class="font-22">RATIO</span>
                            </p>
                            <div class="student-ratio-div">
                                @for($i=1;$i<=$collegeData->student_faculty_ratio;$i++)
                                    <img src="/images/colleges/student-ratio.png" alt=""/>
                                @endfor
                            </div>
                            <div class="faculty-highlight"><img src="/images/colleges/teacher-ratio.png" alt=""></div>
                            <br>
                            <p class="green-ratio-title text-center">{{$collegeData->student_faculty_ratio}} : 1</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Endowment Box -->
            <div class="box-div column small-12 medium-12 large-6">
                <div class="">
                    <div class="bg-pure-white radial-bdr text-black margin10bottom">
                        <div class="graph-image padding20">
                            <strong class="font-18">COLLEGE TOTAL</strong>
                            <br /><br>
                            <strong class="font-26">ENDOWMENT</strong>
                            <br><br>
                            <strong class="font-30 text-green">
                                @if($collegeData->totalEndowment=="N/A")
                                {{ $collegeData->totalEndowment or "" }}
                                @else
                                ${{ number_format($collegeData->totalEndowment) }}
                                @endif
                            </strong>
                        </div>
                        <div><img src="/images/colleges/icon-hand.png" alt=""></div>
                        <br>
                    </div>
                </div>
            </div>
            
            <!-- Accreditation Box -->
            <div class="box-div column small-12 medium-6">
                <div class="">
                    <div class="bg-box-3" style="background:#004358">
                        <div class="box-2-header">ACCREDITATION</div>
                        <div class="box-3-content">
                            <div class="bold-font">AGENCY</div>
                            <div>
                            @if($collegeData->accred_agency)
                                {{$collegeData->accred_agency}}
                            @else
                                N/A
                            @endif
                            </div>
                            <div class="bold-font">PERIODS OF ACCREDITATION</div>
                            <div>
                                @if($collegeData->accred_period)
                                    {{$collegeData->accred_period}}
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="bold-font">STATUS</div>
                            <div>
                                @if($collegeData->accred_status)
                                    {{$collegeData->accred_status}}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Awards Offered Box -->
            <div class="box-div column small-12 medium-6">
                <div class="">
                    <div class="bg-box-3" style="background:#05ced3">
                        <div class="box-2-header">AWARDS OFFERED</div>
                        <div class="box-3-content">
                            <div><span>Bachelor’s degree : {{$collegeData->bachelors_degree}}</span></div>
                            <div><span>Master’s degree : {{$collegeData->masters_degree}}</span></div>
                            <div><span>Post - Master’s certificate : {{$collegeData->post_masters_degree}}</span></div>
                            <div><span>Doctor’s degree - Research/scholarship : {{$collegeData->doctors_degree_research}}</span></div>
                            <div><span>Doctor’s degree - Professional practice : {{$collegeData->doctors_degree_professional}}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ROTC Box -->
            @if(!isset($collegeData->hide_rotc))
            <div class="box-div column small-12 medium-6">
                <div class="">

                    <div class="bg-army-block radial-bdr text-black margin20bottom">
                        <div class="box-2-header">ROTC</div>
                        <div class="margin20top">
                            <div class="large-12 columns no-padding margin20bottom">

                                <div class="large-4 small-2 columns no-padding">
                                    @if($collegeData->rotc_army=="Implied no")
                                        <!-- Need Cross or wrong Icon for this -->
                                        <img src="/images/colleges/empty-big.png" alt="">
                                    @elseif($collegeData->rotc_army=="Yes")
                                        <img src="/images/colleges/correct-big.png" alt="">
                                    @elseif($collegeData->rotc_army=="Not applicable")
                                        <!-- Need N/A Icon for this -->
                                        <img src="/images/colleges/empty-big.png" alt="">
                                    @endif
                                </div>

                                <div class="large-8 small-8 bg-ext-black columns rotc-content-title">ARMY</div>

                            </div>

                            <div class="large-12 columns no-padding margin20bottom">
                                <div class="large-4 small-2 columns no-padding">
                                    @if($collegeData->rotc_navy=="Implied no")
                                        <!-- Need Cross or wrong Icon for this -->
                                        <img src="/images/colleges/empty-big.png" alt="">
                                    @elseif($collegeData->rotc_navy=="Yes")
                                        <img src="/images/colleges/correct-big.png" alt="">
                                    @elseif($collegeData->rotc_navy=="Not applicable")
                                        <!-- Need N/A Icon for this -->
                                        <img src="/images/colleges/empty-big.png" alt="">
                                    @endif
                                </div>

                                <div class="large-8 small-8 bg-ext-black columns rotc-content-title">NAVY</div>

                            </div>

                            <div class="large-12 columns no-padding margin20bottom">
                                <div class="large-4 small-2 columns no-padding">
                                    @if($collegeData->rotc_air=="Implied no")
                                        <!-- Need Cross or wrong Icon for this -->
                                        <img src="/images/colleges/empty-big.png" alt="">
                                    @elseif($collegeData->rotc_air=="Yes")
                                        <img src="/images/colleges/correct-big.png" alt="">
                                    @elseif($collegeData->rotc_air=="Not applicable")
                                        <!-- Need N/A Icon for this -->
                                        <img src="/images/colleges/empty-big.png" alt="">
                                    @endif
                                </div>
                                <div class="large-8 small-8 bg-ext-black columns rotc-content-title">AIR FORCE</div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            @endif
        </div>
    </div>
</div>
