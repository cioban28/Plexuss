<?php
    $collegeData = $college_data;
    $undergradData = $undergrad_grad_data;
   
    // echo '<pre>';
    // print_r($collegeData->id);
    // echo '</pre>';
    // exit;
?>

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
                            ELS Program Cost
                        </div>

                        <div class="detail-university-grey"><span class="bigger">TUITION</span></div>
                        <div class="detail-university-green-content">$1,850</div>
                        <div class="els-session-c">
                            <div class="els-session">1 session (4-weeks) for each level</div>
                            <a class="els-view" href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/EnglishForAcademicPurposes+-+Sample+Schedule.pdf">
                                Download Cost &amp; Class Schedule
                            </a>
                        </div>
                        <br /><br />
                       
                    </div>

                    <div class="large-6 column yt-vid-stats">
                        <div class="video_owl_carousel owl-carousel owl-theme">
                            <div class="item">
                                <iframe width="100%" height="315" src="https://www.youtube.com/embed/uvh1tjS4bsQ" frameborder="0" allowfullscreen></iframe>
                            </div>
                            @if($collegeData->id == 2197)
                            <div class="item">
                                <iframe width="100%" height="315" src="https://www.youtube.com/embed/zS2uVgM52R0" frameborder="0" allowfullscreen></iframe>
                            </div>
                            @endif

                            @if($collegeData->id == 396)
                            <div class="item">
                                <iframe width="100%" height="315" src="https://www.youtube.com/embed/qWYgAbQivwc" frameborder="0" allowfullscreen></iframe>
                            </div>
                            @endif

                            @if($collegeData->id == 1279)
                            <div class="item">
                            <iframe width="100%" height="315" src="https://www.youtube.com/embed/lELBAgoriGQ" frameborder="0" allowfullscreen></iframe>
                            @endif
                        </div>
                    </div>              

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

<div class="row mt15 ">            
    <div class="column small-12">

        <div class="row els-entry">
            <div class="column small-12">
                <div class="entry-h">ACADEMIC DIRECT-ENTRY PATHWAY PROGRAMS</div>
                <div>For students seeking admission to a college or university, successfully completing level 109 or 112 of the English for Academic Purposes (EAP) program earns you an official certificate recognized by more than 650 colleges and universities, confirming that you have achieved the English language proficiency required for admission</div>
                <a href="/international-students?aid={{$collegeData->aor_id or ''}}&type=uni" class="entry-see">See Partner Universities</a>        
            </div>
        </div>
        
    </div>
</div>

<div class="row mt15">            
            
    <!-- Admissions Box -->
    <div class="column box-div small-12 large-4 clearfix" id="admission_box">
        <div class="bg-ext-black radial-bdr inner-cont">
            
            <div class='university-content-admis-headline1 text-center'>
                Application Checklist
            </div>

            <div class="els-checklist">
                <div>Copy of Passport &nbsp;&nbsp;<a href="" data-reveal-id="els_passport"><u>Sample</u></a></div>
                <div>Copy of Bank Statement &nbsp;&nbsp;<a href="" data-reveal-id="els_bank"><u>Sample</u></a></div>
                <div class="text-center"><a class="els-apply" href="/college-application">Apply Now</a></div>
            </div>


        </div>
    </div>
                   
    <!--Scholarship Information Box-->
    <div class="box-div column small-12 large-4" id="scholarship_box">
        <div class="bg-ext-black radial-bdr inner-cont">
            <div class='university-content-admis-headline1 text-center'>
                Session Start Dates
            </div>

            <div class="row">
                <div class="column small-12 medium-6 text-right els-dates">
                    <div>January 2</div>
                    <div>January 30</div>
                    <div>February 27</div>
                    <div>March 27</div>
                    <div>April 24</div>
                    <div>May 22</div>
                    <div>June 19</div>
                </div>
                <div class="column small-12 medium-6 text-right els-dates">
                    <div>July 17</div>
                    <div>August 14</div>
                    <div>September 11</div>
                    <div>October 9</div>
                    <div>November 6</div>
                    <div>December 4</div>
                </div>
            </div>

        </div>
    </div>
    
    <!--  Additional Notes  -->
    <?php 
        $both  = isset($undergradData['additional_notes']['both']) ? $undergradData['additional_notes']['both'] :null;
        $epp = isset($undergradData['additional_notes']['epp']) ? $undergradData['additional_notes']['epp']: null;
    ?>

    <div class="box-div column small-12 large-4">
        <div class="bg-ext-black radial-bdr inner-cont"  id="additoinal_notes_box">
            <div class='university-content-admis-headline1 text-center'>
                Download Brochures
            </div>

            <div class="programs-links">
                <div><a href="https://www.els.edu/-/media/ELS/ELS-Files/Brochures/English/ELS-Global-Core-Brochure-2017.pdf?la=en"><u>2017 Programs/Location</u></a></div>
                <div><a href="https://www.els.edu/-/media/ELS/ELS-Files/Brochures/English/201718-USA-University-Guide-English.pdf?la=en"><u>2017-18’ USA University Guide English</u></a></div>
                <div><a href="https://www.els.edu/-/media/ELS/ELS-Files/Brochures/English/201718-Community-College-Guide-English.pdf?la=en"><u>2017-18’ Community College Guide English</u></a></div>    
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
            <div class="grades-exams-row sle">
                <div class="row">
                    <?php
                        $level = '112';
                        if( $collegeData->id == 320 || $collegeData->id == 349 ){
                            $level = '109';
                        }
                    ?>
                    <div class="column small-12 large-3"><b>ELS Level {{$level}}</b></div>
                    <div class="column small-12 large-9">Complete Level 109 or 112 of the English for Academic Purposes (EAP) program</div>
                </div>
            </div>

            <div class="grades-exams-row els">
                <div class="els-exam">No need to take TOEFL or IELTS exam to be accepted to over 650 universities &amp; colleges</div>                

                <div>12 Levels: 101 (Beginner) to 112(Masters)</div>
                <a class="els-view" href="" data-reveal-id="els_brochure">View all levels</a>

                <div>Level 109: Accepted by most community colleges</div>
                <a href="/international-students?aid={{$collegeData->aor_id or ''}}&type=cc" class="els-view">View partner colleges</a>

                <div>Level 112: Accepted for undergraduate and graduate level</div>
                <a href="/international-students?aid={{$collegeData->aor_id or ''}}&type=uni" class="els-view">View Partner Universities</a>
            </div>
        </div><!-- end table body -->

    </div><!-- end column -->
</div><!-- end row-->




<!--/////////// visa, financial, academic //////////////-->
<?php 
    $epp      = isset($undergradData['requirements']['epp']) ? $undergradData['requirements']['epp'] : null;
    $both      = isset($undergradData['requirements']['both']) ? $undergradData['requirements']['both'] : null;
?>

<div class="row mt15 clearfix els"  id="visa_fin_acad_sec">
    
    <div class="column small-12 large-6" id="visa-fin-container">

        <!-- visa -->
        <div class="mt15 requirements-title">
            <span>VISA REQUIREMENTS</span>
            <span class="els-tip">
                ?
                <div class="t-arrow"></div>
                <div class="els-tip-container mod">
                    <div class="tip-h">How do I get a student visa?</div>
                    <br />
                    <div>According to US immigration laws, you must enter the United States on a nonimmigrant, student (F-1) visa if you wish to study in our intensive and semi-intensive English programs. ELS Language Centers is authorized under Federal law to enroll nonimmigrant alien students</div>
                    <br />
                    <div>To obtain an F-1 student visa you will need to present to the US Embassy or US Consulate certain documents to demonstrate your intent to study in the United States and, after completion of your studies, return to your home country. The documents you need include an I-20 obtained from ELS, proof of financial certification, a valid passport, and evidence of ties to your country. In addition, a personal interview with a visa officer will be required. We recommend that you make an appointment for your interview at least 60-120 days before the date you intend to begin your studies. More information on how to apply for a student visa, including the required visa application, is available on US Embassy and US Consulate websites, a listing of which may be found at <a href="http://www.usembassy.gov" target="_blank">http://www.usembassy.gov</a></div>
                    <br />
                    <div>Prospective and current F-1 visa students should visit Study in the States <a href="https://studyinthestates.dhs.gov/students" target="_blank">https://studyinthestates.dhs.gov/students</a> to learn about the process and rules for studying in the United States as an international student.</div>
                    <br />
                    <div class="tip-h">How do I get an I-20?</div>
                    <br />
                    <div>When applying to most ELS Language Centers programs, you can indicate on your application that you need an I-20. You must also supply documentation with your application showing proof of financial responsibility. You must have enough available money to pay for one session’s tuition, living expenses and miscellaneous expenses.</div>
                </div>
            </span>
        </div>

        <div class="bg-pure-white v-f-a-content els" id="visa_req_cont">
            <div class="row">
                <div class="column small-7 text-left">
                    I-20 obtained from ELS
                </div>
                <a href="" data-reveal-id="els_visa" class="column small-5 els-learn">Learn More</a>
            </div>
            <div class="row">
                <div class="column small-7 text-left">
                    Copy of Valid Passport
                </div>
                <a href="" data-reveal-id="els_visa" class="column small-5 els-learn">Learn More</a>
            </div>
        </div>
    </div>

    <div class="column small-12 large-6" id="visa-fin-container">
        <!-- financial -->
        <div class="mt15 requirements-title">
            FINANCIAL REQUIREMENTS
            <span class="els-tip">
                ?
                <div class="t-arrow"></div>
                <div class="els-tip-container mod">
                    <div class="tip-h">What is financial certification?</div>
                    <br />
                    <div>Your application must be accompanied by certification that while you are attending ELS Language Centers, sufficient funds are available to meet your combined living and tuition expenses. If you will be accompanied by family members, their living expenses must be covered as part of their student-dependent (F-2) visas. Any one of the following is an acceptable form of certification</div>
                    <br />
                    <ul>
                        <li>A current personal bank statement or an original letter from your bank, in English.</li>
                        <li>Both a letter/affidavit of support from your parents or other source of support stating they will be responsible for your expenses during your stay at ELS Language Centers, and a bank statement (or bank letter) verifying their financial ability to meet your expenses.</li>
                        <li>A letter guaranteeing financial support from your employer, in English.</li>
                        <li>An original scholarship letter from your government or other organization, in English.</li>
                    </ul>
                    <br />
                    <div>Since you will need to present financial documentation to the US Consular Officer when applying for a student visa, we suggest that you make sufficient copies of all financial documentation for both ELS Language Centers and for visa application purposes.</div>
                </div>
            </span>
        </div>

        <div class="bg-pure-white v-f-a-content els" id="fin_req_cont">
            <div class="row">
                <div class="column small-7 text-left">
                    Proof of financial certificiation (bank statement)
                </div>
                <a href="" data-reveal-id="els_financial" class="column small-5 els-learn">Learn More</a>
            </div>
        </div>

    </div>
  
</div>

<div style="margin: 0 0 400px;"></div>

<div id="els_passport" class="reveal-modal text-center" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div class="text-right"><a class="close-reveal-modal" aria-label="Close">&#215;</a></div>
    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/sample_passport.jpg">
</div>

<div id="els_bank" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div class="text-right">
        <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>
    <object width="100%" height="500" type="application/pdf" data="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/Sample-Bank-Letter.pdf?#zoom=85&scrollbar=0&toolbar=0&navpanes=0">
        <p>The example document PDF could not be displayed due to unsupported browser. Please upgrade your browser or use Google Chrome or FireFox.</p>
    </object>
</div>

<div id="els_brochure" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div class="text-right"><a class="close-reveal-modal" aria-label="Close">&#215;</a></div>
    <object width="100%" height="500" type="application/pdf" data="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/els_brochure.pdf?#zoom=85&scrollbar=0&toolbar=0&navpanes=0">
        <p>The example document PDF could not be displayed due to unsupported browser. Please upgrade your browser or use Google Chrome or FireFox.</p>
    </object>
</div>

<div id="els_financial" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div class="text-right"><a class="close-reveal-modal" aria-label="Close">&#215;</a></div>
    <div class="els-tip-container">
        <div class="tip-h">What is financial certification?</div>
        <br />
        <div>Your application must be accompanied by certification that while you are attending ELS Language Centers, sufficient funds are available to meet your combined living and tuition expenses. If you will be accompanied by family members, their living expenses must be covered as part of their student-dependent (F-2) visas. Any one of the following is an acceptable form of certification</div>
        <br />
        <ul>
            <li>A current personal bank statement or an original letter from your bank, in English.</li>
            <li>Both a letter/affidavit of support from your parents or other source of support stating they will be responsible for your expenses during your stay at ELS Language Centers, and a bank statement (or bank letter) verifying their financial ability to meet your expenses.</li>
            <li>A letter guaranteeing financial support from your employer, in English.</li>
            <li>An original scholarship letter from your government or other organization, in English.</li>
        </ul>
        <br />
        <div>Since you will need to present financial documentation to the US Consular Officer when applying for a student visa, we suggest that you make sufficient copies of all financial documentation for both ELS Language Centers and for visa application purposes.</div>
    </div>
</div>

<div id="els_visa" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div class="text-right"><a class="close-reveal-modal" aria-label="Close">&#215;</a></div>
    <div class="els-tip-container">
        <div class="tip-h">How do I get a student visa?</div>
        <br />
        <div>According to US immigration laws, you must enter the United States on a nonimmigrant, student (F-1) visa if you wish to study in our intensive and semi-intensive English programs. ELS Language Centers is authorized under Federal law to enroll nonimmigrant alien students</div>
        <br />
        <div>To obtain an F-1 student visa you will need to present to the US Embassy or US Consulate certain documents to demonstrate your intent to study in the United States and, after completion of your studies, return to your home country. The documents you need include an I-20 obtained from ELS, proof of financial certification, a valid passport, and evidence of ties to your country. In addition, a personal interview with a visa officer will be required. We recommend that you make an appointment for your interview at least 60-120 days before the date you intend to begin your studies. More information on how to apply for a student visa, including the required visa application, is available on US Embassy and US Consulate websites, a listing of which may be found at <a href="http://www.usembassy.gov" target="_blank">http://www.usembassy.gov</a></div>
        <br />
        <div>Prospective and current F-1 visa students should visit Study in the States <a href="https://studyinthestates.dhs.gov/students" target="_blank">https://studyinthestates.dhs.gov/students</a> to learn about the process and rules for studying in the United States as an international student.</div>
        <br />
        <div class="tip-h">How do I get an I-20?</div>
        <br />
        <div>When applying to most ELS Language Centers programs, you can indicate on your application that you need an I-20. You must also supply documentation with your application showing proof of financial responsibility. You must have enough available money to pay for one session’s tuition, living expenses and miscellaneous expenses.</div>
    </div>
</div>
