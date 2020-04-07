<div id="manage-school-div" class='row collapse'>

        <div class="column small-12">



    <div class="row pos-rel">
        <!-- header menu in mobile view -->
        <div class="show-for-small-only">
            <div class="row pt15">
                <div class="small-12 column c-black fs14 text-center">YOUR COLLEGE LIST</div>
            </div>
        </div>
        <!-- End header menu in mobile view -->
        <div class="portal-section-head clearfix show-for-medium-up">
            <div class="portalMainTitle left">YOUR LIST</div>
            <div class="portalSubTitle left">Manage the schools you have requested to be recruited by</div>
            <div class="show-tutorial right">
                <div class="tutorial-icon"></div>
                <div>SHOW TUTORIAL</div>
            </div>
        </div>
    </div>


    <!-- top your list menu -->
    <div class="portal_header_nav clearfix show-for-medium-up" id="move-to-trash-button-div">
        @if( isset($profile_percent) && $profile_percent < 30 && $completed_signup == 0 )
        <div class="left action addschools" onclick="window.location.href='/college-application';">
        @else
        <div class="left action addschools" onclick="addSchoolPopup();">
        @endif
            <div class="p-icon add"></div>
            <div class="action-name">ADD SCHOOLS</div>
        </div>
        <div id="trash" class="left action trash" onclick="trashSchool();">
            <div class="p-icon trash"></div>
            <div class="action-name">MOVE TO TRASH</div>
        </div>
        <div id="compare" class="left action compare" onclick="portalCompareSchools();">
            <div class="p-icon compare"></div>
            <div class="action-name">COMPARE SCHOOLS</div>
        </div>
    </div>



    <!-- content area -->
    <div class="row portal_header-mid collapse">

        <div class="small-12 column portal-content-right-side " id="content-list-div">
            <!-- data table header -->
            <div class='row theader'>
                <div class='small-6 medium-5 column islist'>
                    <input type="checkbox" id="select-all-schools">
                    <div><label for="select-all-schools">Schools</label></div>
                </div>
                <div class='small-1 column text-center hide-for-small-only'>Rank</div>
                <div class='small-2 column text-center hide-for-small-only'>Handshakes</div>
                <div class='small-1 column text-center'>Applied</div>
                <div class='small-1 column text-center hide-for-small-only'>Plexuss Member</div>
                <div class='small-3 medium-2 column text-center'>Message</div>
            </div>

            <div class="list-items stylish-scrollbar">

                @if (empty($colleges))
                    <div class="row">
                        <div class='small-10 small-centered column text-center noschoolmsg'>
                            You have not added any schools to your list. Add any colleges that you are interested in being recruited by.
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class='small-10 small-centered column text-center'>
                            @if( isset($profile_percent) && $profile_percent < 30 && $completed_signup == 0 )
                                <div class='button' onclick="window.location.href='/college-application';">Add colleges to my list</div>
                            @else
                                <div class='button' onclick="addSchoolPopup();">Add colleges to my list</div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- This will be a loop! -->
                    @foreach ($colleges as $colleges)
                        <div class="item school">
                            <div class='row collapse innerwrapper'>

                                <div class='small-6 medium-5 column'>
                                    <div class='row collapse'>
                                        <div class='small-1 column text-center'>
                                            {{ Form::checkbox('schoolID', $colleges['slug']  , false, array(
                                            'class'=>'select-school-chkbx',
                                            'data-info' => '{"slug" : "'.$colleges["slug"].'" , "id" : "'. $colleges['college_id'] .'"}'
                                            )) }}
                                        </div>

                                        <div class="small-2 medium-2 large-2 column text-center show-for-medium-up">
                                            @if (isset($colleges['logo_url']))
                                                <a href="/college/{{ $colleges['slug'] }}">
                                                    <img class='schoollogo' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{ $colleges['logo_url'] }}">
                                                </a>
                                            @else
                                                &nbsp;
                                            @endif
                                        </div>


                                        <div class="small-10 medium-9 large-8 column">
                                            <div class="school-deets">
                                                <div class="schoolname">
                                                    <a href="/college/{{$colleges['slug']}}">
                                                        <span class="flag flag-{{ $colleges['country_code'] }}"></span>
                                                        {{ $colleges['school_name'] }}
                                                    </a>
                                                </div>
                                                <div>
                                                    <div class='schooladdress'>
                                                        {{ $colleges['city'] }}, {{ $colleges['state'] }}
                                                    </div>
                                                    <div id="quick-facts-in-your-list-button" class='quickfacts' onClick="getUserCollegeInfo( this, {{$colleges['college_id']}})" >
                                                        quick facts<span class='smallArrow'></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class='small-1 column text-center hide-for-small-only'>
                                    <div class='ranking'>#{{ $colleges['rank'] }}</div>
                                </div>

                                <div class="small-2 column text-center hide-for-small-only">
                                    @if ( $colleges['hand_shake'] )
                                        <div class="p-icon handshake"></div>
                                    @else
                                        <div>&nbsp;</div>
                                    @endif
                                </div>

                                <div class='small-1 column text-center'>
                                    @if( $colleges['user_applied'] == 1 )
                                        <div class="applied-star">&#9733;</div>
                                    @else
                                        <div>&nbsp;</div>
                                    @endif
                                </div>

                                <div class="small-1 column text-center hide-for-small-only">
                                    @if( isset($colleges['in_our_network']) && $colleges['in_our_network'] == '1' )
                                    <div class="is-member"></div>
                                    @else
                                        <div>&nbsp;</div>
                                    @endif
                                </div>

                                <div class='small-3 medium-2 column text-center'>
                                    @if ( $colleges['hand_shake'] )
                                        <div onclick="loadPortalTabs('messages', {{ $colleges['college_id'] }});" class="messageIcon msg-col"></div>
                                    @else
                                        <div class="msg-col">N/A</div>
                                    @endif
                                </div>

                            </div>

                            <div class='schooldropdown small-12 column' style="display:none;">
                                <!-- hidden drop down -->
                            </div>
                        </div>
                    @endforeach
                    <!-- END of loop! -->
                @endif
            </div>
        </div>
    </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("#trash").css('display','none');
        $("#compare").css('display','none');
        $("#select-all-schools").on("change", function(){
            if($(this).is(':checked'))
            {
                $("#trash").css('display','block');
                $("#compare").css('display','block');
            }
            else{
                $("#trash").css('display','none');
                $("#compare").css('display','none');
            }
        });
    });

    $('.select-school-chkbx').click(function () {
       var check = $('.list-items').find('input[type=checkbox]:checked').length;
       if(check)
        {
            $("#trash").css('display','block');
            $("#compare").css('display','block');
        }
        else{
            $("#trash").css('display','none');
            $("#compare").css('display','none');
        }
    });
</script>

<!-- footer menu in mobile view -->
<!-- footer menu in mobile view -->
