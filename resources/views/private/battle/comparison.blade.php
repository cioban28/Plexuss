<!doctype html>
<html class="no-js" lang="en">
    <head>
        @include('private.headers.header')
    </head>
    <body id="{{$currentPage}}">
        @include('private.includes.topnav')
        
        <div class="content-wrapper">

          <div class="row collapse comp-c-wrapper">

           <div class="row">
                <div class='columns small-12 text-center'>
                    <div class="center-college-nav-ranking mt30">
                    @include('private.college.collegeNav')
                    </div>
                </div>
            </div>

           <!-- left Div-->
              <div class="small-12 large-12 column">
                <div class="row">

                  <div class="column small-12">
                    <div class="battle-heading pl20 pt15 hide-for-small">
                        <img  src="/images/colleges/compare/battle.png" class="text-center" style="width:40px; height:32px;" alt="">
                        <span class="fs20 c-white"><span class="f-bold">BATTLE</span> SCHOOLS</span>
                        <span class="fs12 c-white f-bold">COMPARE THE TOP STATS OF ANY SCHOOLS</span>
                    </div>
                    <div class="bck-fff battle-mid-content owlTitleColumn">
                        <div class="row">
                            <div class="small-12 medium-3 column no-padding text-center valign-middle">
                                <!-- // sticky header div-->
                                <div class='comapreSchooltitleArea'>
                                    <a href="#" data-reveal-id="selectSchoolPopup">
                                        <div class="orange-btn mt10">Add new school</div>
                                    </a>
                                </div>
                                <!-- // sticky header div-->

                                <!--Heading div for small-->
                                <div class="row pt10 show-for-small text-center">
                                    <span class="battlefont c-black">BATTLE SCHOOLS</span><br>
                                    <span class="fs12 c-black">COMPARE THE TOP STATS OF ANY SCHOOLS</span><br>

                                     <a href="#" data-reveal-id="selectSchoolPopup">
                                            <div class="orange-btn mt20" style="font-size:14px; display:inline-block">Add Schools to compare</div>
                                    </a>
                                </div>
                                <!--Heading div for small-->

                                <div class="border-right-gray row hide-for-small addSchool-btn-txt">
                                    <div class='column small-12 text-center'>
                                        <a href="#" data-reveal-id="selectSchoolPopup">
                                            <div class="orange-btn mt10">Add new school</div>
                                        </a>
                                        <?php if(isset($addschool) && $addschool==1){$visible='hidden';} else { $visible='visible';}?>
                                        <div class="c79 f-bold fs18 pt10" id="added-string" style="visibility:{{ $visible }} ">You haven’t <br>added any <br> schools<br></div>
                                        <!--<div class="fs12 f-normal c79 pt40">*Hover over Titles for <br> explanation</div>-->
                                    </div>
                                </div>

                               <div class="college-info-title hide-for-small">
                                    <div class="odd-div title-text br-white">RANKING</div>
                                    <div class="title-text br-white">ACCEPTANCE <br> RATE</div>
                                    <div class="odd-div title-text br-white">TUITION <br><span class="fs10">(avg. in-state)</span></div>
                                    <div class="title-text br-white">TUITION <br><span class="fs10">(avg. out-state)</span></div>
                                    <div class="odd-div title-text br-white">TOTAL EXPENSE <br><span class="fs10">(on campus)</span> </div>
                                    <div class="title-text br-white">STUDENT BODY <br><span class="fs10">(on campus)</span></div>
                                    <div class="odd-div title-text br-white">APPLICATION <br> DEADLINE <br><span class="fs10">(undergraduate)</span></div>
                                    <div class="title-text br-white">APPLICATION FEE</div>
                                    <div class="odd-div title-text br-white">SECTOR OF <br> INSTITUTION</div>
                                    <div class="title-text br-white">CALENDAR <br> SYSTEM</div>
                                    <div class="odd-div title-text br-white">RELIGIOUS <br>AFFILIATION</div>
                                    <div class="title-text br-white">CAMPUS SETTING</div>
                                    <div class="odd-div title-text br-white">ENDOWMENT</div>
                               </div>
                            </div>

                           <div class="small-12 medium-9 column no-padding">
                                <div id="owl-compare" class="owl-compare owl-carousel mb5">
                                    <!-- Add a for each loop.  I would allow it to make a min of 4 columns to fill up the OWL slider. -->
                                    <!--  use {{ $student_body_1 or ''}} so it wont throw errors -->
                                    @include('private.battle.comparisonColumn')
                                    <!-- end of for each loop -->
                               </div>
                           </div>

                       </div>
                    </div>
                  </div>
                </div>

                <!-- create acct engagment -->
                <div class="row small-collapse medium-uncollapse show-for-medium-only">
                  <div class="column small-12">
                    @if( isset($signed_in) && $signed_in == 0 )
                      @include('private.includes.right_side_createAcct_comparison')
                    @endif
                  </div>
                </div>
                            

              </div>
            <!-- left Div-->

           <!-- Right Div-->
           <?php
              //  <div class="large-3 column show-for-large-up">
              //   <div class="row">
              //     <div class="column small-12 column">
              //       <!-- RIGHT SIDE FOOTER (HELP, CONTACT, ABOUT, ETC.) HERE! -->
              //       @if( isset($signed_in) && $signed_in == 0 )
              //         @include('private.includes.right_side_createAcct_comparison')
              //       @endif

              //     </div>
                  
              //     <div class="column small-12 column"> 
              //       @if( isset($signed_in) && $signed_in == 1 )
              //         @include('private.includes.invite_friends_right_side')
              //       @endif

              //        <!-- adsense area -->
              //       @include('private.includes.adsense-235x280')

              //       @include('private.includes.right_side_get_started')
              //       @include('private.includes.right_side_footer')
              //     </div>
              //   </div>

              // </div>
            ?>
           <!--  Right Div  -->
          </div>


          <?php //@include('includes.smartInteractiveColumn') ?>

        </div>

<!-- ADD SCHOOL POPUP -->
<div id="selectSchoolPopup" class="reveal-modal radius10 small" data-reveal>
  <div class="row">
    <div class="column small-12 small-text-right">
      <a class="close-reveal-modal c-black help_helpful_videos_close_icon">&#215;</a>
    </div>
  </div>
<div class="pos-rel model-inner-div">
    <div class="fs24 text-center pt25"><span class="f-bold">BATTLE</span> SCHOOLS</div>
    <div class="pt25 text-center"><img src="/images/colleges/compare/compare.png" title="" alt="" /></div>

       <div class="row collapse pl30 pt25">
          <div class="small-10 column">
              <input type="text" name="addschool" id="addschool" placeholder="Start typing college name" class="add-school-txt" onKeyPress="comparisionAutocomplete('addschool','college_slug');">
              <input type="hidden" name="college_slug" id="college_slug" value="">
            </div>
            <div class="small-2 column add-btn cursor hide-for-small"> <img id="compare-add-btn-go-dsktop" src="/images/colleges/compare/add-btn.jpg" title="" alt="" onClick="Addcollege(0,2)"/></div>
            <div class="small-2 column add-btn cursor show-for-small"> <img src="/images/colleges/compare/add-btn.jpg" title="" alt="" onClick="Addcollege(0,1)" /></div>
        </div>
</div>

</div>

<!--ADD SCHOOL POPUP  -->
    @include('private.includes.backToTop')
    @include('private.footers.footer')
    <div style="background:#FFF">

    </div>

    <script type="text/javascript">

        $(document).ready(function(){

          if(  $('#added-string').is(':visible')  ){
              console.log('the text is visible!');
          }else{
              console.log('not visible'); 
          }

        });

    </script>

    </body>
</html>
