<?php 

// dd(get_defined_vars());

?>


<!doctype html>
<html class="no-js" lang="en">
    <head>
        @include('private.headers.header')
    </head>
    <body id="{{$currentPage}}">
        <!--ssss -->
        @include('private.includes.topnav')
        

     <div class="row collapse college-page-toplevel-container" style="position:relative;">
        

                <!-- Left Side Part -->
                <div class="column small-12  large-9 ">
                    <!--generate the right panel-->
                        
    <!-- recruit me MOBILE menu area. This will be review later. -->
    <div class="row hide-for-large-up">
         <div class="only_visible_not_on_scroll">
   
        
        <div class="small-12 medium-6 text-left columns ">
            <a href="javascript:void(0)" data-reveal-id="learnmore">
                <div class="mob-apply-btn">
                   Apply Now
                </div>
            </a>



        </div>

    </div>
    </div>

    <!-- This is the white Header area at the top of the College single view page. -->
    <div itemscope="" itemtype="http://schema.org/CollegeOrUniversity" class="row">
        <div class="large-12 columns ">
            <div class="row collapse paddingtb-university-name-panel bg-pure-white" data-cid="240">
               
          <img src= "/images/topbanners/schlorshipBanner.png" />

            </div>
            <div class="text_sec">
                <h1>Next College Student Athlete</h1>
            </div>
        </div>
    </div>

    
  

    <!-- This is what pulls in the ajax 'type' info on php. Will be modified by JS by ajax. -->
    <div id="collegeInfoArea" data-collegeid="240">
 







<!-- survival guide -->
<div class="row">
    </div>
<div class="clearfix-padding"></div>
<!-- Hiding this for now! Back end will need to fix! --> 


<div class="row">
    <div class="column small-12">
        <div class="row overview-text-area">
            <div class="column heading_sec">
                <h2>Description</h2>
                <p>NCSA is the world’s largest and most successful collegiate athletic recruiting network. NCSA’s 750 teammates 
leverage exclusive data, proprietary matching algorithms and personal relationships built over nearly two decades 
as the industry leader to connect tens of thousands of college-bound student-athletes to more than 35,000 college 
coaches nationwide across 34 sports every year</p>

                <h2>Eligibility Requirement</h2>
                <p> Our commitment is to helping all student-athletes find their best college fit, and every year we donate our time 
and services to qualified athletes based on financial need and to all eligible military veterans. You can learn more 
about NCSA at www.ncsasports.org</p>
               

            <div class="providedby"><span class="powered">Powered By</span> <img src="/images/ncsa.jpg" /></div>


                                
            </div>
        </div>
    </div>
</div>


<div class="clearfix-padding"></div>


            </div>

    <!-- This is the boxes India created! WE will NOT want to use these. AO will handle the JS to ignore these. -->
    <div id="stats" class="collegePanel" data-type=""></div>
    <div id="ranking" class="collegePanel row" data-type=""></div>
    <div id="value" class="collegePanel row" data-type=""></div>
    <div id="admissions" class="collegePanel row" data-type=""></div>
    <div id="tuition" class="collegePanel row" data-type=""></div>
    <div id="notables" class="collegePanel row" data-type=""></div>
    <div id="financial-aid" class="collegePanel row" data-type=""></div>
    <div id="campus" class="collegePanel row" data-type=""></div>
    <div id="athletics" class="collegePanel row" data-type=""></div>
    <div id="enrollment" class="collegePanel row" data-type=""></div>
    <div id="programs" class="collegePanel row" data-type=""></div>
    <!-- Do Not use the above!! -->
                </div>
                <!-- End Left Side Part  -->

                <!-- Right Side Part -->
                <div class="column small-3 large-3 show-for-large-up">
                    
    <!-- college engage box row -->
    <div class="row college-engage-box">
        <div class="column small-12">

            <div class="row">
                <div class="large-12 column side-bar-news side-bar-1 show-for-medium-up" id="side-bar-1">

                    <div class="row college-engage-box-row">
                        <div class="column small-12 text-center">
                            <div class="rightbox1-engage-title">Engage</div>
                        </div>
                    </div>


                    <!-- get recruited btn -->
                                         
                       
                    
                    <!-- apply now -->
                    <div class="college-engage-box-row">
                        <a href="javascript:void(0)" data-reveal-id="learnmore"  id="collegeapplynow" target="_blank">
                            <div class="row trigger-apply-btn blue-btn">
                                
                                <div class="text-left Text_center">Apply </div>
                            </div>
                        </a>

 <div id="learnmore" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">


<div class="fuller_sec">
    <h1>what sport do you play</h1>
   
<select name="college"><option selected>Select</option><option >Select1</option></select>


</div>
<div class="next_btn_sec">
    <button type="button">Next</button>
    </div>


 <div class="close_btn">
  <a class="close-reveal-modal closer_sec" aria-label="Close">&#215;</a>
</div>
</div>


                    </div>

                
                    
                </div>
            </div>

        </div>
    </div>

      

<div class="row" id="plexussBannerAd" style="
    margin-bottom: 1em;
    margin-left: 0.2em;
">
    <div class="columns small-12 text-center">
        <a href="https://plexuss.com/college-application" target="_blank"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ad_copies/plexussadnew.jpg"> </a>
    </div>
</div>


  



                        




                </div>
                <!-- End Right Side Part -->
        </div>


    @include('private.footers.footer')  
    </body>

</html>
