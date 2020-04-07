<?php
    $collegeData = $college_data;
    $undergradData = $undergrad_grad_data;
   

    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';
    // exit;
?>

<!--///// social buttons div of holding \\\\\-->

<!--\\\\\ social buttons div of holding /////-->



<!-- ////////////////// International Cost and Video /////////// -->
<?php 

   $header = isset($undergradData['header_info']['epp']) ? $undergradData['header_info']['epp'] : null;
    $videos = isset($undergradData['video_testimonials']) ? $undergradData['video_testimonials'] : null;  

?>
<div class='row' style="border: solid 0px #ff0000;">
    <div class='column small-12'>
        <div style='display:block;'>
            
            <div class="row university-stats-content">
                <div class="large-12 columns no-padding bg-ext-black radial-bdr">
                    <div class="large-6 columns college-rank-divide">
                       
                        <div class='small-12 column university-content-admis-headline1'>
                            ANNUAL INTERNATIONAL COSTS
                        </div>
                      

                        @if(isset($header['epp_total_yearly_cost']))
                        <div class="detail-university-grey"><span class="bigger">TOTAL YEARLY COSTS</span></div>
                        <div class="detail-university-green-content">

                                ${{number_format($header['epp_total_yearly_cost'])}} 
                        </div>
                        <br /><br />
                        @endif

                        @if(isset($header['epp_tuition']))
                        <div class="detail-university-grey">TUITION</div>
                        <div class="detail-university-green-content">
                            
                                ${{number_format($header['epp_tuition'])}}    
                        </div>
                        @endif


                         @if(isset($header['epp_room_board']))
                        <div class="detail-university-grey">ROOM &amp; BOARD</div>
                        <div class="detail-university-green-content">
                           
                                ${{number_format($header['epp_room_board'])}} 
                        </div>
                        @endif


                        @if(isset($header['epp_book_supplies']))
                        <div class="detail-university-grey">BOOKS &amp; SUPPLIES</div>
                        <div class="detail-university-green-content">
                            
                                ${{number_format($header['epp_book_supplies'])}} 
                        </div>
                        @endif
                       
                    </div>
                    @if( isset($videos) && count($videos) > 0 )
                    <div class="large-6 column yt-vid-stats">
                        <div class="video_owl_carousel owl-carousel owl-theme">
                            @foreach( $videos as $vid )
                            <div class="item">
                                {!! $vid['embed'] or '' !!}
                                
                                <div class="vid-caption">
                                    {!! $vid['title'] or '' !!}
                                </div>
                            </div>
                            @endforeach
                        </div>                           

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



<!--//////////// Admissions, Scholarship, Notes //////////-->
<?php 
    $admission   = isset($undergradData['admission_info']['epp']) ? $undergradData['admission_info']['epp'] : null;
    $scholarship = isset($undergradData['scholarship_info']['epp']) ? $undergradData['scholarship_info']['epp'] : null;
?>
<div class="row mt15">            
            
            <!-- Admissions Box -->
            <div class="column box-div small-12 large-4 clearfix" id="admission_box">
                <div class="bg-ext-black radial-bdr inner-cont">
                    
                    <div class='university-content-admis-head2'>
                        ADMISSIONS
                    </div>

                    @if(isset($admission['epp_application_deadline']))
                    <div class="detail-university-grey">APPLICATION DEADLINE</div>
                    <div class="detail-university-green-content">
                        @if( $admission['epp_application_deadline'] == 'rolling_admissions' )
                            {{ 'Rolling Admissions' }}
                        @else
                            {{$admission['epp_application_deadline']}}
                        @endif
                        
                    </div>
                    @endif
                    <br/>

                     @if($admission['epp_admissions_available'])
                    <div class="detail-university-grey">CONDITIONAL ADMISSIONS AVAILABLE</div>
                    <div class="detail-university-green-content">
                        {{ucfirst($admission['epp_admissions_available'])}}
                    </div>
                    <br/><br/>  
                    @endif


                    @if(isset($admission['epp_application_fee']))
                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions">APPLICATION FEE</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            ${{number_format($admission['epp_application_fee'])}}
                        </div>
                    </div>
                    @endif

                    @if(isset($admission['epp_num_of_applicants']))
                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions"># OF APPLICANTS</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            {{number_format($admission['epp_num_of_applicants'])}}
                        </div>
                    </div>
                    @endif

                    @if(isset($admission['epp_num_of_admitted']))
                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions"># ADMITTED</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            {{number_format($admission['epp_num_of_admitted'])}}
                        </div>
                    </div>
                                        

                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions">% ADMITTED</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            {{ ceil( 100* ($admission['epp_num_of_admitted']/ $admission['epp_num_of_applicants']) ) }}%
                        </div>
                    </div>
                    @endif


                    @if(isset($admission['epp_num_of_admitted_enrolled']))
                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions"># ADMITTED & ENROLLED</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            {{number_format($admission['epp_num_of_admitted_enrolled'])}}
                        </div>
                    </div>

                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions">% ADMITTED & ENROLLED</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            {{ ceil( 100* ($admission['epp_num_of_admitted_enrolled']/ $admission['epp_num_of_applicants']) ) }}%
                        </div>
                    </div>
                    @endif
                </div>
            </div>
                       
            

            <!--Scholarship Information Box-->
            <div class="box-div column small-12 large-4" id="scholarship_box">
                <div class="bg-ext-black radial-bdr inner-cont">
                    <div class='university-content-admis-headline1'>
                        SCHOLARSHIP INFO
                    </div>


                    @if(isset($scholarship['epp_scholarship_available']))
                    <div class="detail-university-grey">Scholarship(s) Available</div>
                    <div class="detail-university-green-content">
                        {{ucfirst($scholarship['epp_scholarship_available'])}}
                    </div>
                    @endif


                    @if(isset($scholarship['epp_scholarship_student_received_aid']))
                     <div class="row">
                        <div class="column small-7 detail-university-grey left-col-addmissions">Students who recieved aid</div>
                        <div class="column small-5 detail-university-green-content right-col-addmissions">
                            {{$scholarship['epp_scholarship_student_received_aid']}}%
                        </div>
                    </div>
                    @endif


                    @if(isset($scholarship['epp_scholarship_avg_financial_aid_given']))
                    <div class="row">
                        <div class="column small-7 detail-university-grey left-col-addmissions">Avg. Financial aid given</div>
                        <div class="column small-5 detail-university-green-content right-col-addmissions">
                            ${{number_format($scholarship['epp_scholarship_avg_financial_aid_given'])}}
                        </div>
                    </div>
                    @endif


                    @if(isset($scholarship['epp_scholarship_requirments']))
                        <div class="university-content-admis-head3">SCHOLARSHIP REQUIREMENTS</div>
                        <div class="detail-university-green-content-smaller">
                            {{$scholarship['epp_scholarship_requirments']}}
                        </div>
                    @endif <!-- end if for scholarship requirments -->

                    @if(isset($scholarship['epp_scholarship_gpa']))
                        <div class="university-content-admis-head5">GPA</div>
                        <div class="detail-university-green-content">
                            {{$scholarship['epp_scholarship_gpa']}}
                        </div>
                    @endif

                    @if(isset($scholarship['epp_scholarship_link']))
                        <div class="university-content-admis-head5">LINK FOR MORE INFO</div>
                        <div class="detail-university-little-link">
                            <a href="{{$scholarship['epp_scholarship_link']}}" target="_blank" >
                            {{$scholarship['epp_scholarship_link']}}
                            </a>
                        </div> 
                    @endif <!-- end if for links for more info -->

                </div>
            </div>
            
           
            
            <!--  Additional Notes  -->
            <?php 
                $both  = isset($undergradData['additional_notes']['both']) ? $undergradData['additional_notes']['both'] :null;
                $epp = isset($undergradData['additional_notes']['epp']) ? $undergradData['additional_notes']['epp']: null;
            ?>
            <div class="box-div column small-12 large-4">
                <div class="bg-pure-white radial-bdr inner-cont"  id="additoinal_notes_box">
                 <div class='university-content-admis-headline-black'>
                        ADDITIONAL NOTES
                    </div>

                    <div class="additionalNotes-cont">
                       <!--  <div class=" detail-university-grey-c mb20">ENGLISH PATHWAY PROGRAM COST<br> BY SEMESTER</div> -->

<!-- 
                        <div class="detail-university-grey-c mb10">1st Semester -  </div>
                        <div class="detail-university-grey-c mb10">2nd Semester -  </div>
                        <div class="detail-university-grey-c mb20">3rd Semester -  </div>
 -->
                        @if(isset($both))
                            @foreach($both as $key)
                                {!! $key['content'] or '' !!}
                            @endforeach
                        @elseif(isset($epp))
                            @foreach($epp as $key)
                                {!! $key['content'] or '' !!}
                            @endforeach
                        @endif

                        
                    </div>
                </div>
            </div>                    
</div>



<!--/////////// Grades and Exam Requirements Table ///////-->
<?php 
    $grades = isset($undergradData['grade_exams']['epp']) ? $undergradData['grade_exams']['epp'] : null;
?>
<div class="row">    
    <div class="column small-12" id="grades_exams_requirement_table">
        <div class="university-content-admis-headline-black int-alumni-title-img"  id="grades_exams_requirement_title"> 
            GRADE &amp; EXAM REQUIREMENTS
        </div>

        <div id="grades_exams_table_body">
            <div class="grades-exams-row">
                <div class="row">
                    <div class="column small-6 large-6">&nbsp;</div>
                    <div class="column small-3 large-3 title-3">MINIMUM</div>
                    <div class="column small-3 large-3 title-3">AVERAGE</div>
                </div>


                <div class="row" id="gpa_row">
                    <div class="column small-6 large-6 ger-name-col">
                        <span class="sub-title-col">GPA</span>
                        <a href="#" class="gpa-convert">How do I convert my GPA?</a>
                    </div>
                    <div class="column small-3 large-3 sub-title-col">
                        @if(isset($grades['epp_grade_gpa_min']))
                            {{$grades['epp_grade_gpa_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 sub-title-col">
                         @if(isset($grades['epp_grade_gpa_avg']))
                            {{$grades['epp_grade_gpa_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
           


            <div class="grades-exams-row" id="english_row">
                <div class="row">
                    <div class="column small-12 large-12 ger-name-col sub-title-col">
                        English Language Proficiency
                    </div>
                </div>
                <div class="row">
                    <div class="column small-6 large-6 title-3">
                        TOEFL
                    </div>
                    <div class="column small-3 large-3 ger-min-col">
                        @if(isset($grades['epp_grade_toefl_min']))
                            {{$grades['epp_grade_toefl_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['epp_grade_toefl_avg']))
                            {{$grades['epp_grade_toefl_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="column small-6 large-6 title-3">
                        IELTS
                    </div>
                    <div class="column small-3 large-3 ger-min-col">
                        @if(isset($grades['epp_grade_ielts_min']))
                            {{$grades['epp_grade_ielts_min']  or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['epp_grade_ielts_avg']))
                            {{$grades['epp_grade_ielts_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
           
            <div class="grades-exams-row" id="act_row">
                <div class="row">
                    <div class="column small-12 large-12 ger-name-col sub-title-col">
                        ACT
                    </div>
                </div>
                <div class="row">
                    <div class="column small-6 large-6 title-3">
                        Composite
                    </div>
                    <div class="column small-3 large-3 ger-min-col">
                        @if(isset($grades['epp_grade_act_composite_min']))
                            {{$grades['epp_grade_act_composite_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['epp_grade_act_composite_avg']))
                            {{$grades['epp_grade_act_composite_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="column small-6 large-6 title-3">
                        Math
                    </div>
                    <div class="column small-3 large-3 ger-min-col">
                        @if(isset($grades['epp_grade_act_math_min']))
                            {{$grades['epp_grade_act_math_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['epp_grade_act_composite_avg']))
                            {{$grades['epp_grade_act_composite_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="column small-6 large-6 title-3">
                        English
                    </div>
                    <div class="column small-3 large-3 ger-min-col">
                        @if(isset($grades['epp_grade_act_english_min']))
                            {{$grades['epp_grade_act_english_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['epp_grade_act_english_avg']))
                            {{$grades['epp_grade_act_english_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>

            <div class="grades-exams-row" id="sat_row">
                <div class="row">
                    <div class="column small-12 large-12 ger-name-col sub-title-col">
                        SAT
                    </div>
                </div>

                <div class="row">
                    <div class="column small-6 large-6 title-3">
                        Math
                    </div>
                    <div class="column small-3 large-3 ger-min-col">
                        @if(isset($grades['epp_grade_sat_math_min']))
                            {{$grades['epp_grade_sat_math_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['epp_grade_sat_math_avg']))
                            {{$grades['epp_grade_sat_math_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="column small-6 large-6 title-3">
                        Reading
                    </div>
                    <div class="column small-3 large-3 ger-min-col">
                        @if(isset($grades['epp_grade_sat_reading_min']))
                            {{$grades['epp_grade_sat_reading_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['epp_grade_sat_reading_avg']))
                            {{$grades['epp_grade_sat_reading_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>

            <div class="grades-exams-row" id="gre_row">
                <div class="row">
                    <div class="column small-12 large-12 ger-name-col sub-title-col">
                        GRE
                    </div>
                </div>
                <div class="row">
                    <div class="column small-6 large-6 title-3">
                        Writing
                    </div>
                    <div class="column small-3 large-3 ger-min-col">
                        @if(isset($grades['epp_grade_gre_writing_min']))
                            {{$grades['epp_grade_gre_writing_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['epp_grade_gre_writing_avg']))
                            {{$grades['epp_grade_gre_writing_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="column small-6 large-6 title-3">
                        Verbal
                    </div>
                    <div class="column small-3 large-3 ger-min-col">
                        @if(isset($grades['epp_grade_gre_verbal_min']))
                            {{$grades['epp_grade_gre_verbal_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['epp_grade_gre_verbal_avg']))
                            {{$grades['epp_grade_gre_verbal_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="column small-6 large-6 title-3">
                        Quantitive
                    </div>
                    <div class="column small-3 large-3 ger-min-col">
                        @if(isset($grades['epp_grade_gre_quant_min']))
                            {{$grades['epp_grade_gre_quant_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['epp_grade_gre_quant_avg']))
                            {{$grades['epp_grade_gre_quant_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div><!-- end table body -->
    </div><!-- end column -->
</div><!-- end row-->




<!--/////////// visa, financial, academic //////////////-->
<?php 
    $epp      = isset($undergradData['requirements']['epp']) ? $undergradData['requirements']['epp'] : null;
    $both      = isset($undergradData['requirements']['both']) ? $undergradData['requirements']['both'] : null;
?>


<div class="row mt15 clearfix"  id="visa_fin_acad_sec">
    
    <div class="column small-12 large-6" id="visa-fin-container">

        <!-- visa -->
        @if(isset($both['visa']) || isset($epp['visa']))
        <div class="row mt15 requirements-title">VISA REQUIREMENTS</div>
        <div class="bg-pure-white v-f-a-content" id="visa_req_cont">
            @if(isset($both['visa']))
            @foreach($both['visa'] as $key)
            <div class="row">
                <div class="column @if(isset($key['attachment_url']) && !empty($key['attachment_url'])) small-7 @else small-12 @endif text-left">
                    {{$key['title'] or ''}}
                    <div class="download-desc">{{$key['description'] or ''}}</div>
                </div>
                @if(isset($key['attachment_url']) && !empty($key['attachment_url']))
                <a target="_blank" href="{{$key['attachment_url'] or ''}}" class="column small-5 v-f-a-right downloads-link-orange">Download</a>
                @endif
            </div>
            @endforeach
            @endif

            @if(isset($epp['visa']))
            @foreach($epp['visa'] as $key)
            <div class="row">
                <div class="column @if(isset($key['attachment_url']) && !empty($key['attachment_url'])) small-7 @else small-12 @endif text-left">
                    {{$key['title'] or ''}}
                    <div class="download-desc">{{$key['description'] or ''}}</div>
                </div>
                @if(isset($key['attachment_url']) && !empty($key['attachment_url']))
                <a target="_blank" href="{{$key['attachment_url'] or ''}}" class="column small-5 v-f-a-right downloads-link-orange">Download</a>
                @endif
            </div>
            @endforeach
            @endif
        </div>
        @endif

        <!-- financial -->
        @if(isset($both['financial']) || isset($epp['financial']))
        <div class="row mt15 requirements-title">FINANCIAL REQUIREMENTS</div>
        <div class="bg-pure-white v-f-a-content" id="fin_req_cont">
            @if(isset($both['financial']))
            @foreach($both['financial'] as $key)
            <div class="row">
                <div class="column @if(isset($key['attachment_url']) && !empty($key['attachment_url'])) small-7 @else small-12 @endif text-left">
                    {{$key['title'] or ''}}
                    <div class="download-desc">{{$key['description'] or ''}}</div>
                </div>
                @if(isset($key['attachment_url']) && !empty($key['attachment_url']))
                <a target="_blank" href="{{$key['attachment_url'] or ''}}" class="column small-5 v-f-a-right downloads-link-orange">Download</a>
                @endif
            </div>
            @endforeach
            @endif

            @if(isset($epp['financial']))
            @foreach($epp['financial'] as $key)
            <div class="row">
                <div class="column @if(isset($key['attachment_url']) && !empty($key['attachment_url'])) small-7 @else small-12 @endif text-left">
                    {{$key['title'] or ''}}
                    <div class="download-desc">{{$key['description'] or ''}}</div>
                </div>
                @if(isset($key['attachment_url']) && !empty($key['attachment_url']))
                <a target="_blank" href="{{$key['attachment_url'] or ''}}" class="column small-5 v-f-a-right downloads-link-orange">Download</a>
                @endif
            </div>
            @endforeach
            @endif
        </div>
        @endif

    </div>

    <!-- academic -->
   
    <div class="column small-12 large-6 mt15" id="academic_col">   
        <div class="row requirements-title">ACADEMIC REQUIREMENTS</div>
        <div class="bg-pure-white v-f-a-content" id="int_academic_cont">
            @if(isset($both['academic']))
            @foreach($both['academic'] as $key)
            <div class="row">
                <div class="column @if(isset($key['attachment_url']) && !empty($key['attachment_url'])) small-7 @else small-12 @endif text-left">
                    {{$key['title'] or ''}}
                    <div class="download-desc">{{$key['description'] or ''}}</div>
                </div>
                @if(isset($key['attachment_url']) && !empty($key['attachment_url']))
                <a target="_blank" href="{{$key['attachment_url'] or ''}}" class="column small-5 v-f-a-right downloads-link-orange">Download</a>
                @endif
            </div>
            @endforeach
            @endif

            @if(isset($epp['academic']))
            @foreach($epp['academic'] as $key)
            <div class="row">
                <div class="column @if(isset($key['attachment_url']) && !empty($key['attachment_url'])) small-7 @else small-12 @endif text-left">
                    {{$key['title'] or ''}}
                    <div class="download-desc">{{$key['description'] or ''}}</div>
                </div>
                @if(isset($key['attachment_url']) && !empty($key['attachment_url']))
                <a target="_blank" href="{{$key['attachment_url'] or ''}}" class="column small-5 v-f-a-right downloads-link-orange">Download</a>
                @endif
            </div>
            @endforeach
            @endif

        </div>
    </div>
  
</div>




<!--////////////// Majors and Degrees -- next release see if EPP has specific courses //////////////////-->
<?php 
    $majors_degrees = isset($undergradData['majors_degrees']['epp']) ? $undergradData['majors_degrees']['epp'] : null;
    $majors_degrees_cnt = count($majors_degrees);
    $cnt = 0;
?>


<!--/////////////// International Alumni ///////////////-->
<?php 
    $alums = isset($undergradData['alums']) ? $undergradData['alums'] : null;
?>
@if(isset($alums) && !empty($alums))
<div class="row mt15">
    <div class="column">
        <div class="row university-content-admis-headline1 int-alumni-title-img" id="int_alumni_title">
            INTERNATIONAL ALUMNI
        </div>
        <div class=" row owl-int-alumni-carousel owl-carousel owl-theme bg-pure-white" id="int_alumni_cont">
            @foreach($alums as $key)    
            <div class="item">
                <div class="int-alumni-cont">
                    <div class="int-alumni-pic" style="background-image: url('{{$key["photo_url"]}}')"></div>
                    <div class="int-alumni-lgreen">{{$key['alumni_name'] or ''}}</div>
                    @if(isset($key['school_name']))
                    <div class="">{{$key['school_name'] or ''}} '{{$key['grad_year_abbr'] or ''}}</div>
                    @endif
                    @if(isset($key['department']))
                    <div>{{$key['department'] or ''}}</div>
                    @endif
                    <div>{{$key['location'] or ''}}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif


<!-- just for vsiually appealing and pleasant padding at the end -->
<!-- and to give some space for back to top button -->
<div class="mt15"> &nbsp;</div>


