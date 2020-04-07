<?php
    $collegeData = $college_data;
    $undergradData = $undergrad_grad_data;
//dd($data);

    // Case for when grad page is loaded through url
    $revenue_programs = isset($collegeData->news['revenue_programs']) ? $collegeData->news['revenue_programs'] : null;

    // Case for when the grad page is loaded through ajax
    if (!isset($revenue_programs) && isset($collegeData->revenue_programs)) {
        $revenue_programs = $collegeData->revenue_programs;
    }
?>

<!--///// social buttons div of holding \\\\\-->

<!--\\\\\ social buttons div of holding /////-->



<!-- ////////////////// International Cost and Video /////////// -->
<?php 

   $header = isset($undergradData['header_info']['undergrad']) ? $undergradData['header_info']['undergrad'] : null;
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
                      

                        @if(isset($header['undergrad_total_yearly_cost']))
                        <div class="detail-university-grey"><span class="bigger">TOTAL YEARLY COSTS</span></div>
                        <div class="detail-university-green-content">

                                ${{number_format($header['undergrad_total_yearly_cost'])}} 
                        </div>
                        <br /><br />
                        @endif

                        @if(isset($header['undergrad_tuition']))
                        <div class="detail-university-grey">TUITION</div>
                        <div class="detail-university-green-content">
                            
                                ${{number_format($header['undergrad_tuition'])}}    
                        </div>
                        @endif


                         @if(isset($header['undergrad_room_board']))
                        <div class="detail-university-grey">ROOM &amp; BOARD</div>
                        <div class="detail-university-green-content">
                           
                                ${{number_format($header['undergrad_room_board'])}} 
                        </div>
                        @endif


                        @if(isset($header['undergrad_book_supplies']))
                        <div class="detail-university-grey">BOOKS &amp; SUPPLIES</div>
                        <div class="detail-university-green-content">
                            
                                ${{number_format($header['undergrad_book_supplies'])}} 
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
    $admission   = isset($undergradData['admission_info']['undergrad']) ? $undergradData['admission_info']['undergrad'] : null;
    $scholarship = isset($undergradData['scholarship_info']['undergrad']) ? $undergradData['scholarship_info']['undergrad'] : null;
?>
<div class="row mt15">            
            
            <!-- Admissions Box -->
            <div class="column box-div small-12 large-4 clearfix" id="admission_box">
                <div class="bg-ext-black radial-bdr inner-cont">
                    
                    <div class='university-content-admis-head2'>
                        ADMISSIONS
                    </div>

                    @if(isset($admission['undergrad_application_deadline']))
                    <div class="detail-university-grey">APPLICATION DEADLINE</div>
                    <div class="detail-university-green-content">
                        @if( $admission['undergrad_application_deadline'] == 'rolling_admissions' )
                            {{ 'Rolling Admissions' }}
                        @else
                            {{$admission['undergrad_application_deadline']}}
                        @endif
                        
                    </div>
                    @endif
                    <br/>

                     @if($admission['undergrad_admissions_available'])
                    <div class="detail-university-grey">CONDITIONAL ADMISSIONS AVAILABLE</div>
                    <div class="detail-university-green-content">
                        {{ucfirst($admission['undergrad_admissions_available'])}}
                    </div>
                    <br/><br/>  
                    @endif


                    @if(isset($admission['undergrad_application_fee']))
                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions">APPLICATION FEE</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            ${{number_format($admission['undergrad_application_fee'])}}
                        </div>
                    </div>
                    @endif

                    @if(isset($admission['undergrad_num_of_applicants']))
                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions"># OF APPLICANTS</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            {{number_format($admission['undergrad_num_of_applicants'])}}
                        </div>
                    </div>
                    @endif

                    @if(isset($admission['undergrad_num_of_admitted']))
                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions"># ADMITTED</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            {{number_format($admission['undergrad_num_of_admitted'])}}
                        </div>
                    </div>
                                        

                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions">% ADMITTED</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            @if($admission['undergrad_num_of_applicants'] == 0)
                                N/A
                            @else
                                {{ ceil( 100* ($admission['undergrad_num_of_admitted']/ $admission['undergrad_num_of_applicants']) )  }}%
                            @endif
                        </div>
                    </div>
                    @endif


                    @if(isset($admission['undergrad_num_of_admitted_enrolled']))
                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions"># ADMITTED & ENROLLED</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            {{number_format($admission['undergrad_num_of_admitted_enrolled'])}}
                        </div>
                    </div>

                    <div class="row">
                        <div class="column small-6 detail-university-grey left-col-addmissions">% ADMITTED & ENROLLED</div>
                        <div class="column small-6 detail-university-green-content right-col-addmissions">
                            @if($admission['undergrad_num_of_applicants'] == 0)
                                N/A
                            @else
                                {{ ceil( 100* ($admission['undergrad_num_of_admitted_enrolled']/ $admission['undergrad_num_of_applicants']) ) }}%
                            @endif
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


                    @if(isset($scholarship['undergrad_scholarship_available']))
                    <div class="detail-university-grey">Scholarship(s) Available</div>
                    <div class="detail-university-green-content">
                        {{ucfirst($scholarship['undergrad_scholarship_available'])}}
                    </div>
                    @endif


                    @if(isset($scholarship['undergrad_scholarship_student_received_aid']))
                     <div class="row">
                        <div class="column small-7 detail-university-grey left-col-addmissions">Students who recieved aid</div>
                        <div class="column small-5 detail-university-green-content right-col-addmissions">
                            {{$scholarship['undergrad_scholarship_student_received_aid']}}%
                        </div>
                    </div>
                    @endif


                    @if(isset($scholarship['undergrad_scholarship_avg_financial_aid_given']))
                    <div class="row">
                        <div class="column small-7 detail-university-grey left-col-addmissions">Avg. Financial aid given</div>
                        <div class="column small-5 detail-university-green-content right-col-addmissions">
                            ${{number_format($scholarship['undergrad_scholarship_avg_financial_aid_given'])}}
                        </div>
                    </div>
                    @endif


                    @if(isset($scholarship['undergrad_scholarship_requirments']))
                        <div class="university-content-admis-head3">SCHOLARSHIP REQUIREMENTS</div>
                        <div class="detail-university-green-content-smaller">
                            {{$scholarship['undergrad_scholarship_requirments']}}
                        </div>
                    @endif <!-- end if for scholarship requirments -->

                    @if(isset($scholarship['undergrad_scholarship_gpa']))
                        <div class="university-content-admis-head5">GPA</div>
                        <div class="detail-university-green-content">
                            {{$scholarship['undergrad_scholarship_gpa']}}
                        </div>
                    @endif

                    @if(isset($scholarship['undergrad_scholarship_link']))
                        <div class="university-content-admis-head5">LINK FOR MORE INFO</div>
                        <div class="detail-university-little-link">
                            <a href="{{$scholarship['undergrad_scholarship_link']}}" target="_blank" >
                            {{$scholarship['undergrad_scholarship_link']}}
                            </a>
                        </div> 
                    @endif <!-- end if for links for more info -->

                </div>
            </div>
            
           
            
            <!--  Additional Notes  -->
            <?php 
                $both      = isset($undergradData['additional_notes']['both']) ? $undergradData['additional_notes']['both'] :null;
                $undergrad = isset($undergradData['additional_notes']['undergrad']) ? $undergradData['additional_notes']['undergrad']: null;
            ?>
            <div class="box-div column small-12 large-4">
                <div class="bg-pure-white radial-bdr inner-cont"  id="additoinal_notes_box">
                 <div class='university-content-admis-headline-black'>
                        ADDITIONAL NOTES
                    </div>

                    <div class="additionalNotes-cont">
                        @if(isset($both))
                            @foreach($both as $key)
                                {!! $key['content'] or '' !!}
                            @endforeach
                        @endif

                        @if(isset($undergrad))
                            @foreach($undergrad as $key)
                                {!! $key['content'] or '' !!}
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>                    
</div>



<!--/////////// Grades and Exam Requirements Table ///////-->
<?php 
    $grades = isset($undergradData['grade_exams']['undergrad']) ? $undergradData['grade_exams']['undergrad'] : null;
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
                        @if(isset($grades['undergrad_grade_gpa_min']))
                            {{$grades['undergrad_grade_gpa_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 sub-title-col">
                         @if(isset($grades['undergrad_grade_gpa_avg']))
                            {{$grades['undergrad_grade_gpa_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
           

            @if (isset($grades['undergrad_grade_toefl_min']) || isset($grades['undergrad_grade_toefl_avg']) || isset($grades['undergrad_grade_ielts_min']) || isset($grades['undergrad_grade_ielts_avg']))

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
                        @if(isset($grades['undergrad_grade_toefl_min']))
                            {{$grades['undergrad_grade_toefl_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['undergrad_grade_toefl_avg']))
                            {{$grades['undergrad_grade_toefl_avg'] or ''}}
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
                        @if(isset($grades['undergrad_grade_ielts_min']))
                            {{$grades['undergrad_grade_ielts_min']  or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['undergrad_grade_ielts_avg']))
                            {{$grades['undergrad_grade_ielts_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if (isset($grades['undergrad_grade_act_composite_min']) || isset($grades['undergrad_grade_act_composite_avg']) || isset($grades['undergrad_grade_act_math_min']) || isset($grades['undergrad_grade_act_composite_avg']) || isset($grades['undergrad_grade_act_english_min']) || isset($grades['undergrad_grade_act_english_avg']))

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
                        @if(isset($grades['undergrad_grade_act_composite_min']))
                            {{$grades['undergrad_grade_act_composite_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['undergrad_grade_act_composite_avg']))
                            {{$grades['undergrad_grade_act_composite_avg'] or ''}}
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
                        @if(isset($grades['undergrad_grade_act_math_min']))
                            {{$grades['undergrad_grade_act_math_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['undergrad_grade_act_composite_avg']))
                            {{$grades['undergrad_grade_act_composite_avg'] or ''}}
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
                        @if(isset($grades['undergrad_grade_act_english_min']))
                            {{$grades['undergrad_grade_act_english_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['undergrad_grade_act_english_avg']))
                            {{$grades['undergrad_grade_act_english_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if (isset($grades['undergrad_grade_sat_math_min']) || isset($grades['undergrad_grade_sat_math_avg']) || isset($grades['undergrad_grade_sat_reading_min']) || isset($grades['undergrad_grade_sat_reading_avg']))
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
                        @if(isset($grades['undergrad_grade_sat_math_min']))
                            {{$grades['undergrad_grade_sat_math_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['undergrad_grade_sat_math_avg']))
                            {{$grades['undergrad_grade_sat_math_avg'] or ''}}
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
                        @if(isset($grades['undergrad_grade_sat_reading_min']))
                            {{$grades['undergrad_grade_sat_reading_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['undergrad_grade_sat_reading_avg']))
                            {{$grades['undergrad_grade_sat_reading_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if (isset($grades['undergrad_grade_gre_writing_min']) || isset($grades['undergrad_grade_gre_writing_avg']) || isset($grades['undergrad_grade_gre_verbal_min']) || isset($grades['undergrad_grade_gre_verbal_avg']) || isset($grades['undergrad_grade_gre_quant_min']) || isset($grades['undergrad_grade_gre_quant_avg']))
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
                        @if(isset($grades['undergrad_grade_gre_writing_min']))
                            {{$grades['undergrad_grade_gre_writing_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['undergrad_grade_gre_writing_avg']))
                            {{$grades['undergrad_grade_gre_writing_avg'] or ''}}
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
                        @if(isset($grades['undergrad_grade_gre_verbal_min']))
                            {{$grades['undergrad_grade_gre_verbal_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['undergrad_grade_gre_verbal_avg']))
                            {{$grades['undergrad_grade_gre_verbal_avg'] or ''}}
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
                        @if(isset($grades['undergrad_grade_gre_quant_min']))
                            {{$grades['undergrad_grade_gre_quant_min'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                    <div class="column small-3 large-3 ger-avg-col">
                        @if(isset($grades['undergrad_grade_gre_quant_avg']))
                            {{$grades['undergrad_grade_gre_quant_avg'] or ''}}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div><!-- end table body -->
    </div><!-- end column -->
</div><!-- end row-->



<!--////////////// Majors and Degrees for Revenue Programs //////////////////-->
<?php 
    $masters_degrees = !empty($revenue_programs) ? array_filter($revenue_programs, function($program) { return $program['degree_type'] == 4; }) : [];

    $bachelors_degrees = !empty($revenue_programs) ? array_filter($revenue_programs, function($program) { return $program['degree_type'] == 3; }) : [];

?>

@if (!empty($revenue_programs))
<div class="row mt15" id="major_degree_offered">
    <div class="column small-12">
        <div class="university-content-admis-headline1 int-alumni-title-img" id="major_degree_offered_title">
            MAJORS &amp; DEGREES OFFERED
        </div>
        <div class="row bg-pure-white" id="major_degree_offered_cont">
            <div class="revenue_programs">
                @if (!empty($bachelors_degrees))
                    <div>
                        <div class="degree_program_type">Bachelors Degrees</div>
                        <div class='major_names_container'>
                            @foreach ($bachelors_degrees as $degree)
                                <div class='mt3'>{{$degree['program_name']}}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (!empty($masters_degrees))
                    <div>
                        <div class="degree_program_type">Masters Degrees</div>
                        <div class='major_names_container'>
                            @foreach ($masters_degrees as $degree)
                                <div>{{$degree['program_name']}}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<?php

?>
@if (!empty($revenue_programs))
<div class="row mt15" id="revenue_programs_details" data-revenue_programs='{{json_encode($revenue_programs)}}'>
    <div class="column small-12">
        <div class="row bg-pure-white">
            <div class="revenue_programs_select_container">
                <select class='revenue_program_name_toggle'>
                    @foreach($revenue_programs as $key => $program)
                        <option value="{{$program['program_name']}}" @if($key === 0) selected @endif>{{$program['program_name']}}</option>
                    @endforeach
                </select>
            </div>

            <div class='revenue_programs_details_container'>
                @if (isset($revenue_programs[0]))
                    <?php
                    $selling_points = !empty($revenue_programs[0]['selling_points']) ? $revenue_programs[0]['selling_points'] : [];
                    ?>
                    @foreach ($selling_points as $point) 
                        <p>{{$point}}</p>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!--////////////// Majors and Degrees //////////////////-->
<?php 
    $majors_degrees = isset($undergradData['majors_degrees']['grad']) ? $undergradData['majors_degrees']['grad'] : null;
    $majors_degrees_cnt = count($majors_degrees);
    $cnt = 0;
?>
@if(isset($majors_degrees))
<div class="row mt15" id="major_degree_offered">
    <div class="column small-12">
        <div class="university-content-admis-headline1 int-alumni-title-img" id="major_degree_offered_title">
            MAJORS &amp; DEGREES OFFERED
        </div>

        <!-- half of Degrees/Majors offered list goes into one column, the other half into the next
        devvs will perform calc -->
        <div class="row bg-pure-white" id="major_degree_offered_cont">
            @foreach($majors_degrees as $key => $value)
            <?php 
                $count = count($value);
            ?>
            
                <div class="row">
                    <div class="detail-university-green-content w50p">{{$key}}</div>

                    @if($count > 5)
                        <div class="column small-12 medium-6">     

                            <ul>
                                @for($i = 0; $i < ceil($count/2); $i++)
                                    <li>{{$value[$i]}}</li>
                                @endfor

                            </ul>
                        </div>

                        <div class="column small-12 medium-6">     

                            <ul>
                                 @for($i =  ceil($count/2); $i < $count; $i++)
                                    <li>{{$value[$i]}}</li>
                                @endfor
                            </ul>
                        </div>
                    @else
                          <div class="column small-12 medium-6">     

                                <ul>
                                    @foreach($value as $li)
                                        <li>{{$li}}</li>
                                    @endforeach
                                </ul>
                            </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!--/////////// visa, financial, academic //////////////-->
<?php 
    $undergrad      = isset($undergradData['requirements']['undergrad']) ? $undergradData['requirements']['undergrad'] : null;
    $both      = isset($undergradData['requirements']['both']) ? $undergradData['requirements']['both'] : null;
?>

@if( isset($undergrad) || isset($both) )
<div class="row mt15 clearfix"  id="visa_fin_acad_sec">
    
    <div class="column small-12 large-6" id="visa-fin-container">

        <!-- visa -->
        @if(isset($both['visa']) || isset($undergrad['visa']))
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

            @if(isset($undergrad['visa']))
            @foreach($undergrad['visa'] as $key)
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
        @if(isset($both['financial']) || isset($undergrad['financial']))
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

            @if(isset($undergrad['financial']))
            @foreach($undergrad['financial'] as $key)
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
    @if(isset($both['academic']) || isset($undergrad['academic']))
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

            @if(isset($undergrad['academic']))
            @foreach($undergrad['academic'] as $key)
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
    @endif
</div>
@endif



<!--////////////// Majors and Degrees //////////////////-->
<?php 
    $majors_degrees = isset($undergradData['majors_degrees']['undergrad']) ? $undergradData['majors_degrees']['undergrad'] : null;
    $majors_degrees_cnt = count($majors_degrees);
    $cnt = 0;
?>
@if(isset($majors_degrees))
<div class="row mt15" id="major_degree_offered">
    <div class="column small-12">
        <div class="university-content-admis-headline1 int-alumni-title-img" id="major_degree_offered_title">
            MAJORS &amp; DEGREES OFFERED
        </div>

         <!-- half of Degrees/Majors offered list goes into one column, the other half into the next
        devvs will perform calc -->
        <div class="row bg-pure-white" id="major_degree_offered_cont">
            @foreach($majors_degrees as $key => $value)
            <?php 
                $count = count($value);
                $numkeys = count($key);
            ?>
            

                <div class="row">
                    <div class="detail-university-green-content w50p">{{$key}}</div>

                    @if($count > 5)
                        <div class="column small-12 medium-6">     

                            <ul>
                                @for($i = 0; $i < ceil($count/2); $i++)
                                    <li>{{$value[$i]}}</li>
                                @endfor

                            </ul>
                        </div>

                        <div class="column small-12 medium-6">     

                            <ul>
                                 @for($i =  ceil($count/2); $i < $count; $i++)
                                    <li>{{$value[$i]}}</li>
                                @endfor
                            </ul>
                        </div>
                    @else
                          <div class="column small-12 medium-6">     

                                <ul>
                                    @foreach($value as $li)
                                        <li>{{$li}}</li>
                                    @endforeach
                                </ul>
                            </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

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


