<?php
    if (isset($college_data)) {
        $collegeData = $college_data;
    }
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
            <div class="large-5 columns no-padding college-rank-divide pb20">
                <div class="large-12 columns adm-first-contentLeftHead">ADMISSIONS <span class="col-admiss-undergrad-sm">(undergraduate)</span></div>
                <div class="large-12 columns mt15 mb10 coll-admission-undergrad-info-box">
                	<div class="adm-topleft-ques small-7 columns no-padding">
                    	APPLICATION DEADLINE
                    </div>
                    <div class="adm-topleft-ans small-5 columns {{$collegeData->rollingadmissions or ''}}">
						{{ $college_data->deadline or ""}}
                    </div>
                </div>

                <!--<div class="large-12 columns adm-blackbdr"></div>-->
                
                <div class="large-12 columns mt10 mb10 coll-admission-undergrad-info-box">
                	<div class="adm-topleft-ques small-7 columns no-padding">
                    	# OF APPLICANTS
                    </div>
                    <div class="adm-topleft-ans small-5 columns">
                        @if (isset($college_data->applicants_total))
                                {{number_format($college_data->applicants_total)}}
                            @else
                               N/A
                        @endif
                    </div>
                </div>
                <!--<div class="large-12 columns adm-blackbdr"></div>-->
                
                <div class="large-12 columns mt10 mb10 coll-admission-undergrad-info-box">
                	<div class="row">
						<div class='small-12 column no-padding'>
							<div class='row'>
								<div class="adm-topleft-ques small-7 columns no-padding">
									# ADMITTED
								</div>
								<div class="adm-topleft-ans small-5 columns">
									@if (isset($college_data->admissions_total))
                               	 		{{number_format($college_data->admissions_total)}}
                           		    @else
                               			N/A
                        			@endif
								</div>
							</div>
							<div class="row">
								<div class="adm-topleft-ques small-7 columns no-padding">
									% ADMITTED
								</div>
								<div class="adm-topleft-ans small-5 columns">
									@if (isset($college_data->percentadmitted) && $college_data->percentadmitted!=0)
                               	 		{{$college_data->percentadmitted}}%
                           		    @else
                               			N/A
                        			@endif
								</div>
							</div>
						</div>
                    </div>
                </div>
                <div class="large-12 columns adm-blackbdr"></div>
                
                <div class="large-12 columns mt10 mb10 coll-admission-undergrad-info-box">
                	<div class="row">
						<div class='small-12 column no-padding'>
							<div class='row'>
								<div class="adm-topleft-ques small-7 columns no-padding">
									# ADMITTED &amp; ENROLLED
								</div>
								<div class="adm-topleft-ans small-5 columns">

									@if (isset($college_data->enrolled_total))
                               	 		{{number_format($college_data->enrolled_total)}}
                           		    @else
                               			N/A
                        			@endif
								</div>
							</div>
							<div class="row col-admiss-percentAdmit">
								<div class="adm-topleft-ques small-7 columns no-padding">
									% ADMITTED &amp; ENROLLED
								</div>
								<div class="adm-topleft-ans small-5 columns">
									@if (isset($college_data->per_adm_enrolled) &&  $college_data->per_adm_enrolled!=0)
                               	 		{{$college_data->per_adm_enrolled}}%
                           		    @else
                               			N/A
                        			@endif
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
            
            @if( isset($college_data->youtube_admissions_videos) && count($college_data->youtube_admissions_videos) > 0 )
            <div class="large-7 column yt-vid-admissions">
                @foreach( $college_data->youtube_admissions_videos as $vid )
                <iframe width="100%" height="280" src="https://www.youtube.com/embed/{{$vid['video_id']}}" style="border:none;" allowfullscreen></iframe>
                @endforeach
            </div>
            @else
            <div class="large-7 columns no-padding">
                <img class="coll-enroll-tempImg" src="/images/colleges/stats-top-content.jpg" alt="">
            </div>
            @endif
        </div>
    </div>
</div></div></div>
<div class="custom-row col-admiss-cust-row">
    <!--First Box-->
    <div class="large-4 column">
        <!-- Application Info Box -->
        <div class="large-12 columns no-padding">
            <div class="row" id="application-info-box">
				<div class="adm-InfoBox" style="background:#FFFFFF">
				    <div class="adm-infobox-title">APPLICATION INFO</div>
				    <div class="adm-infobox-ques">OPEN ADMISSIONS:</div>
				    <div class="adm-infobox-ans">{{$college_data->open_admissions or 'NA'}}</div>
				    
				    <div class="adm-infobox-ques">COMMON APPLICATION:</div>
				    <div class="adm-infobox-ans">{{ isset($college_data->common_app) && $college_data->common_app != null ? $college_data->common_app : 'No'}}</div>
				    
				    <div class="adm-infobox-ques">APPLICATION FEE:</div>
				    <div class="adm-infobox-ans">${{$college_data->application_fee_undergrad or 'NA'}}</div>
				    
				    <div class="adm-infobox-weblink">
				    	@if( isset($college_data->application_url) && $college_data->application_url != ' ' )
				    	<div class="row">
				    		<div class="column small-12">
				    			<a href="{{$college_data->application_url or '#'}}" class="admission_app_link" target="_blank">
									> APPLICATION LINK
				    			</a>
				    		</div>
				    	</div>
				    	@endif
				    </div>
				    
				</div>
            </div>
        </div>
        <br />
        <!-- if undergrad comparison has no value, don't show box -->
		@if( isset($college_data->admissions_men) && isset($college_data->admissions_women) )
        <div class="large-12 small-12 columns no-padding mt10">
			<!-- undergad-comparison-box -->
        	<div id="undergad-comparison-box">
				<div class="row">
				    <div class="large-6 small-6 column bg-men-side text-center">
				        <img src="/images/colleges/men-figure-compare.png" alt=""/>
				        <div class="comparison-content"><span class="fs36">{{ number_format($college_data->admissions_men) }}</span><br />Admitted</div>
				    </div>
				    <div class="large-6 small-6 column  bg-women-side text-center">
				        <img src="/images/colleges/women-figure-compare.png" alt=""/>
				        <div class="comparison-content"><span class="fs36">{{number_format($college_data->admissions_women) }}</span><br />Admitted</div>
				    </div>
				</div>
				<p class="comparison-title">
				<span class="font-14">UNDERGRAD</span><br />STUDENT GENDER
				</p>
        	</div>
        </div>
        @endif
    </div>
    <!--Second Box -->
	<div class="large-8 columns no-mob-padding mob-top10-margin">
		<div class="large-12 small-12 columns no-padding">
			<!--salary-box-admissions -->
			<div class="row" id="salary-box-admissions">
				<div class="avg-salary-pop-degree pos-relative no-padding">
				    <div class="salarybox-headerImage">
				        <img src="/images/colleges/calendar-top-image.png" alt=""/>
				    </div>
				    <div class="avg-salary-title p10 fs12" style="background:#000000">
				        <div class="large-4 small-4 columns">TEST</div>
				        <div class="large-4 small-4 columns">25TH PERCENTILE</div>
				        <div class="large-4 small-4 columns">75TH PERCENTILE</div>
				    </div>
				    <div class="row" style="background-color:#004358">
				        <div class="large-12 columns salary-structure-list" style="background:#00394C">
				            <div class="large-4 small-4 columns salary-content-text-value">SAT CRITICAL READING</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->sat_read_25 or 'NA'}}</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->sat_read_75 or 'NA'}}</div>
				        </div>
				        <div class="large-12 columns salary-structure-list">
				            <div class="large-4 small-4 columns salary-content-text-value">SAT MATH</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->sat_math_25 or 'NA'}}</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->sat_math_75 or 'NA'}}</div>
				        </div>
				        <div class="large-12 columns salary-structure-list" style="background:#00394C">
				            <div class="large-4 small-4 columns salary-content-text-value">SAT WRITING</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->sat_write_25 or 'NA'}}</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->sat_write_75 or 'NA'}}</div>
				        </div>
				        <div class="large-12 columns salary-structure-list">
				            <div class="large-4 small-4 columns salary-content-text-value">ACT COMPOSITE</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->act_composite_25 or 'NA'}}</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->act_composite_75 or 'NA'}}</div>
				        </div>
				        <div class="large-12 columns salary-structure-list" style="background:#00394C">
				            <div class="large-4 small-4 columns salary-content-text-value">ACT ENGLISH</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->act_english_25 or 'NA'}}</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->act_english_75 or 'NA'}}</div>
				        </div>
				        <div class="large-12 columns salary-structure-list" >
				            <div class="large-4 small-4 columns salary-content-text-value">ACT MATH</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->act_math_25 or 'NA'}}</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->act_math_75 or 'NA'}}</div>
				        </div>
				        <div class="large-12 columns salary-structure-list" style="background:#00394C">
				            <div class="large-4 small-4 columns salary-content-text-value">ACT WRITING</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->act_write_25 or 'NA'}}</div>
				            <div class="large-4 small-4 columns salary-content-text-value">{{$college_data->act_write_75 or 'NA'}}</div>
				        </div>
				    </div>
				</div>
			</div>
		</div>
        <div class="large-12 small-12 columns no-padding mt10">
        	<!-- admission-consider-box -->
			<div class="row" id="admission-consider-box">
				<div class="avg-salary-pop-degree pos-relative no-padding">
				    <div class="avg-salary-title p10" style="background:#000000">WHAT IS CONSIDERED FOR ADMISSION?</div>
				    <div class="row" style="background-color:#049AA2">
				        <div class="large-12 columns salary-structure-list" style="background:#04A6AE">
				        	<div class="large-9 small-6 columns salary-content-text-value">SECONDARY SCHOOL GPA</div>
				        	<div class="large-3 small-6 columns salary-content-text-value">'{{$college_data->secondary_school_gpa or 'NA'}}'</div>
				        </div>
				        <div class="large-12 columns salary-structure-list">
				        	<div class="large-9 small-6 columns salary-content-text-value">RECORD - COMPLETION OF A COLLEGE-PREP PROGRAM</div>
				        	<div class="large-3 small-6 columns salary-content-text-value">'{{$college_data->secondary_school_record or 'NA'}}'</div>
				        </div>
				        <div class="large-12 columns salary-structure-list" style="background:#04A6AE">
				        	<div class="large-9 small-6 columns salary-content-text-value">PORTFOLIO</div>
				        	<div class="large-3 small-6 columns salary-content-text-value">--</div>
				        </div>
				        <div class="large-12 columns salary-structure-list">
				        	<div class="large-9 small-6 columns salary-content-text-value">ADMISSION TEST SCORES (SAT/ACT)</div>
				        	<div class="large-3 small-6 columns salary-content-text-value">'{{$college_data->admission_test_scores or 'NA'}}'</div>
				        	</div>
				        <div class="large-12 columns salary-structure-list" style="background:#04A6AE">
				        	<div class="large-9 small-6 columns salary-content-text-value">TOEFL (Test of English as a Foreign language)</div>
				        	<div class="large-3 small-6 columns salary-content-text-value">'{{$college_data->admission_test_scores or 'NA'}}'</div>
				        </div>
				        <div class="large-12 columns salary-structure-list">
				        	<div class="large-9 small-6 columns salary-content-text-value">RECOMMENDATIONS</div>
				        	<div class="large-3 small-6 columns salary-content-text-value">'{{$college_data->recommendations or 'NA'}}'</div>
				        </div>
				    </div>
				</div>
			</div>
		</div>
	</div>
</div>
