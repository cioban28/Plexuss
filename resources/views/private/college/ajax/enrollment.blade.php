<?php 
    $collegeData = $college_data;
    //dd($collegeData->youtube_enrollment_videos);
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
        	<div class="row university-stats-content">
                <div class="large-12 columns no-padding bg-ext-black radial-bdr">
                    <div class="large-6 columns no-padding college-rank-divide coll-enrollment-box-height">
                        <div class="university_content_admis_deadline pl10 pb10 pt10 coll-enrollment-box-header">UNDERGRADUATE ENROLLMENT</div>
                        <div class="detail-university-grey">TOTAL ENROLLMENT</div>
                        <div class="detail-university-green-content">
                            @if (isset($collegeData->undergrad_total))
                                {{ number_format($collegeData->undergrad_total )}}
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="detail-university-grey">TRANSFER ENROLLMENT</div>
                        <div class="detail-university-green-content">
                            @if (isset($collegeData->undergrad_transfers_total))
                                {{ number_format($collegeData->undergrad_transfers_total )}}
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="detail-university-grey">ATTENDANCE STATUS</div>
                        <div class="large-12 columns detail-university-green-content">
                            <div class="large-6 small-6 columns bdr-dot-right no-padding text-left coll-enroll-attendance">
                            @if (isset($collegeData->undergrad_full_time_total))
                                {{ number_format($collegeData->undergrad_full_time_total )}}
                            @else
                                N/A
                            @endif<br><span class="font-12">FULL-TIME</span></div>
                            <div class="large-6 small-6 columns text-center coll-enroll-attendance">
                            @if (isset($collegeData->undergrad_part_time_total))
                                {{ number_format($collegeData->undergrad_part_time_total )}}
                            @else
                                N/A
                            @endif<br><span class="font-12">PART-TIME</span></div>
                        </div>
                    </div>
                    @if( isset($collegeData->youtube_enrollment_videos) && count($collegeData->youtube_enrollment_videos) > 0 )
                     <div class="large-6 column yt-vid-enrollment">
                        @foreach( $collegeData->youtube_enrollment_videos as $vid )
                        <iframe width="100%" height="280" src="https://www.youtube.com/embed/{{$vid['video_id']}}" style="border:none;" allowfullscreen></iframe>
                        @endforeach
                    </div>
                    @else
        			<div class="large-6 columns no-padding">
        				<img class="coll-enroll-tempImg" src="/images/colleges/stats-top-content.jpg" alt="">
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if( isset($collegeData->undergrad_men_total) && isset($collegeData->undergrad_women_total) )
        <div class="row pos-relative">
        	<!-- Undergraduate Ethnic Page commented because of error mentioned in mail, review required -->
        	<div class="custom-two-col-box bg-pure-white small-12 medium-7">
            	<div id="grad-ethnicBox">
                    <div class="custom-two-col-box-head" style="background-image:url(/images/colleges/ug-ethnic-image.png);background-repeat:no-repeat;height: 98px; background-size: cover;">
                        <span class="font-18">Undergrad</span><br />Race/Ethnicity
                    </div>
                    <div class="enrollment-twobox-content">
                        <div class="row mb20">
                            <!--
                            <div class="small-5 columns small-text-center text-right no-padding mt10">
                                <ul class="ethnic-ul">
                                    <li>AMERICAN INDIAN OR ALASK NATIVE</li>
                                    <li style="color:#05CCD2">ASIAN</li>
                                    <li style="color:#04A5AC">BLACK OR AFRICAN AMERICAN</li>
                                    <li style="color:#004358">HISPANIC / LATINO</li>
                                    <li>NATIVE HAWAIIN /<br /> OTHER PACIFIC ISLANDER</li>
                                    <li style="color:#9FD939">WHITE</li>
                                    <li style="color:#1DB151">2 OR MORE RACES</li>
                                    <li>RACE / ETHNICITY UNKNOWN</li>
                                    <li style="color:#148D39">NON-RESIDENT-ALIEN</li>
                                </ul>
                            </div>-->
                            <div class="small-12 columns">
                                <ul class="ethnic-bar-graph-ul">
                                    <div class="text-left enrollment-raceEthnicity-labels">AMERICAN INDIAN OR ALASK NATIVE</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->aianfinalPercent }}%;background:#000"></div>{{ $collegeData->aianfinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-asian">ASIAN</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->asianfinalPercent }}%;background:#05CCD2"></div>{{ $collegeData->asianfinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-afriAmerican">BLACK OR AFRICAN AMERICAN</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{  $collegeData->bkaafinalPercent }}%;background:#04A5AC"></div>{{ $collegeData->bkaafinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-hispanic">HISPANIC / LATINO</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->hispfinalPercent }}%;background:#004358"></div>{{ $collegeData->hispfinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels">NATIVE HAWAIIN / OTHER PACIFIC ISLANDER</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->nhpifinalPercent }}%;background:#000000"></div>{{ $collegeData->nhpifinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-white">WHITE</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->whitefinalPercent }}%;background:#9FD939"></div>{{ $collegeData->whitefinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-twoOrMore">2 OR MORE RACES</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->twomorefinalPercent }}%;background:#1DB151"></div>{{ $collegeData->twomorefinalPercent}}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels">RACE / ETHNICITY UNKNOWN</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->unknownfinalPercent }}%;background:#000000"></div>{{ $collegeData->unknownfinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-alien">NON-RESIDENT-ALIEN</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->alienfinalPercent }}%;background:#148D39"></div>{{ $collegeData->alienfinalPercent }}%
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<!-- Hidden for now because pie is an image
            <div class="custom-one-col-box">
            	<div id="enroll-nutbox">
                     <div class="undergrad-distEducation-headText" style="background:#000">
                        <span class="font-14">UNDERGRAD</span><br />DISTANCE EDUCATION
                    </div>
                    <div class="dist-subTitle">Enrolled in distance education: {{ $collegeData->distance_pct }}%</div>
                    <div class="text-center"><img src="/images/colleges/enrollment-graph.png" /></div>
                    <div class="dist-subTitle">Not in any distance education: {{ $collegeData->distance_none_pct }}%</div>
                </div>
            </div>
			-->
            <!--
            <div class="custom-two-col-box bg-blue-box">
            	<div id="side-three-nutbox">
                    <div class="row col-in-line">
                        <div class="large-3 small-3 columns no-padding"><img src="/images/colleges/ug-state-enrollment.png" /></div>
                        <div class="large-6 small-5 columns"><img src="/images/colleges/ug-enrollment-graphimg.png" /></div>
                        <div class="large-3 small-4 column enroll-box-text no-padding">
                            <div class="text-white">{{-- $collegeData->foreign_pct --}}% Foreign</div>
                            <div class="text-gold">10% Out-of-state</div>
                            <div class="text-black">72% In-state</div>
                        </div>
                    </div>
                </div>
            </div>
            -->
            
            <div class="small-12 medium-5 column gender-square">
                <div class="large-12 small-12 columns no-padding mt10">
                    <div id="undergad-comparison-box">
                        <div class="row">
                            <div class="large-6 small-6 column bg-men-side text-center">
                                <img src="/images/colleges/men-figure-compare.png" alt=""/>
                                <div class="comparison-content"><span class="fs36">{{ $collegeData->undergrad_men_total }}</span><br />Admitted</div>
                            </div>
                            <div class="large-6 small-6 column  bg-women-side text-center">
                                <img src="/images/colleges/women-figure-compare.png" alt=""/>
                                <div class="comparison-content"><span class="fs36">{{ $collegeData->undergrad_women_total }}</span><br />Admitted</div>
                            </div>
                        </div>
                        <p class="comparison-title">
                            <span class="font-14">UNDERGRAD</span><br />STUDENT GENDER
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="row pos-relative">
            <!-- Undergraduate Ethnic Page commented because of error mentioned in mail, review required -->
            <div class="custom-two-col-box bg-pure-white small-12">
                <div id="grad-ethnicBox">
                    <!--duplicate id grad-ethnicBox found-->
                    <div class="custom-two-col-box-head" style="background-image:url(/images/colleges/ug-ethnic-image.png);background-repeat:no-repeat;height: 98px; background-size: cover;">
                        <span class="font-18">Undergrad</span><br />Race/Ethnicity
                    </div>
                    <div class="enrollment-twobox-content">
                        <div class="row mb20">
                            <!--
                            <div class="small-5 columns small-text-center text-right no-padding mt10">
                                <ul class="ethnic-ul">
                                    <li>AMERICAN INDIAN OR ALASK NATIVE</li>
                                    <li style="color:#05CCD2">ASIAN</li>
                                    <li style="color:#04A5AC">BLACK OR AFRICAN AMERICAN</li>
                                    <li style="color:#004358">HISPANIC / LATINO</li>
                                    <li>NATIVE HAWAIIN /<br /> OTHER PACIFIC ISLANDER</li>
                                    <li style="color:#9FD939">WHITE</li>
                                    <li style="color:#1DB151">2 OR MORE RACES</li>
                                    <li>RACE / ETHNICITY UNKNOWN</li>
                                    <li style="color:#148D39">NON-RESIDENT-ALIEN</li>
                                </ul>
                            </div>-->
                            <div class="small-12 columns">
                                <ul class="ethnic-bar-graph-ul">
                                    <div class="text-left enrollment-raceEthnicity-labels">AMERICAN INDIAN OR ALASK NATIVE</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->aianfinalPercent }}%;background:#000"></div>{{ $collegeData->aianfinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-asian">ASIAN</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->asianfinalPercent }}%;background:#05CCD2"></div>{{ $collegeData->asianfinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-afriAmerican">BLACK OR AFRICAN AMERICAN</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{  $collegeData->bkaafinalPercent }}%;background:#04A5AC"></div>{{ $collegeData->bkaafinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-hispanic">HISPANIC / LATINO</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->hispfinalPercent }}%;background:#004358"></div>{{ $collegeData->hispfinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels">NATIVE HAWAIIN / OTHER PACIFIC ISLANDER</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->nhpifinalPercent }}%;background:#000000"></div>{{ $collegeData->nhpifinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-white">WHITE</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->whitefinalPercent }}%;background:#9FD939"></div>{{ $collegeData->whitefinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-twoOrMore">2 OR MORE RACES</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->twomorefinalPercent }}%;background:#1DB151"></div>{{ $collegeData->twomorefinalPercent}}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels">RACE / ETHNICITY UNKNOWN</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->unknownfinalPercent }}%;background:#000000"></div>{{ $collegeData->unknownfinalPercent }}%
                                    </li>
                                    <div class="text-left enrollment-raceEthnicity-labels race-alien">NON-RESIDENT-ALIEN</div>
                                    <li class="horizontal-graph-cover">
                                        <div class="horizontal-graph" style="width:{{ $collegeData->alienfinalPercent }}%;background:#148D39"></div>{{ $collegeData->alienfinalPercent }}%
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
			<!--
            <div class="custom-one-col-box">
            	<div id="enroll-student-nutbox">
                    <div class="undergrad-distEducation-headText" style="background:#000">
                        <span class="font-14">UNDERGRAD</span><br />STUDENT AGE
                    </div>
                    <div class="dist-subTitle">25 and over: {{ $collegeData->age_25_over_pct  }}%</div>
                    <div class="text-center"><img src="/images/colleges/enrollment-graph.png" /></div>
                    <div class="dist-subTitle">24 and under: {{ $collegeData->age_24_under_pct  }}%</div>
                </div>
            </div>    
			-->
        </div>
    </div>
</div>
