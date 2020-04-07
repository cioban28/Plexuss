<div id="manage-school-div" class='row collapse'>
    <div class="column small-12">



        <div class="row pos-rel">
           <!-- header menu in mobile view -->
            <div class="show-for-small-only">
                <div class="row pt10">
                    <div class="small-12 column c-black fs13 text-center">COLLEGES RECRUITED YOU</div>
                </div>
            </div>
            <!-- header menu in mobile view -->
            <div class="portal-section-head show-for-medium-up clearfix">
                <div class="portalMainTitle left">SCHOOLS THAT WANT TO RECRUIT YOU</div>
                <div class="portalSubTitle left">Say “yes” to add to Your List, or “no” to remove</div>
                <div class="show-tutorial right">
                    <div class="tutorial-icon"></div>
                    <div>SHOW TUTORIAL</div>
                </div>
            </div>
        </div>

        <div class="clearfix portal_header_nav show-for-medium-up">
            <div class="left action" onclick="portalCompareSchools();">
                <div class="p-icon compare"></div>
                <div class="action-name">COMPARE SCHOOLS</div>
            </div>
        </div>





        <div class="row portal_header-mid collapse">
            <!-- left menu -->
            <div class="small-12 column portal-content-right-side " id="content-list-div">
                <!-- data table header -->
                <div class='row theader'>
                    <div class='small-9 medium-6 column'>
                        <input type="checkbox" id="select-all-schools">
                        <div><label for="select-all-schools">Schools</label></div>
                    </div>
                    <div class='small-3 medium-2 column text-center'>RANK</div>
                    <div class='show-for-medium-up medium-4 column text-center'>WANT TO BE RECRUITED</div>
                </div>
                <div class="list-items stylish-scrollbar">
                    @if (empty($colleges))
                        <div class="row noschool-msg-row">
                            <div class="small-9 column noschoolmsg">
                                No colleges here yet, but no worries! Let's make sure
                                you've done everything you can to be ready:
                                <br>
                                <span>
                                    <br/>1. Make sure your profile status is at least green
                                    <br>2. Develop your profile more to attract colleges
                                    <br>3. Try reaching out to the colleges you like first
                                </span>
                                <br />
                                <div class = "small-10">
                                    <div class='button' onClick="addSchoolPopup();">Add colleges to my list</div>
                                </div>
                            </div>
                            <div class="small-3 column text-right show-for-medium-up noschoolimg">
                                <img class="" src="/images/portal/waiting.png">
                            </div>

                        </div>
                    @else
                        <!-- This will be a loop! -->
                        @foreach ($colleges as $colleges)
                            <div class='row item school collapse'>
                                <div class='small-12 column'>
                                    <div class='row collapse innerwrapper'>
                                        <div class='small-9 medium-6 column'>
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
                                                <div class='row collapse'>

                                                    <div class='schoolname small-12 column'>
														<span class="flag flag-{{ $colleges['country_code'] }}"></span>
                                                        <a href="/college/{{ $colleges['slug'] }}">{{ $colleges['school_name'] }}</a>
                                                    </div>
                                                    <div class='schooladdress small-6 column'>
                                                        {{ $colleges['city'] }}, {{ $colleges['state'] }}
                                                    </div>
                                                    <div id="quick-facts-in-your-list-button" class='small-6 column quickfacts' onClick="getUserCollegeInfo( this, {{$colleges['college_id']}})" >
                                                        quick facts<span class='smallArrow'></span>
                                                    </div>
                                                </div>

                                            </div>
                                            </div>
                                        </div>
                                        <div class='small-3 medium-2 column text-center'>
                                            <div class='ranking'>#{{ $colleges['rank'] }}</div>
                                        </div>
                                        <div class='small-12  medium-4 column'>
                                            <div class="row ">
                                                @if ( isset($colleges['ro_detail']) && isset($colleges['ro_detail']['ro_id']) )
                                                    <div class='small-6 medium-6 column text-center'>
                                                        <div class='revenue-organizaton-button recruitme-buttons {{$colleges['ro_detail']['type']}}' data-college="{{ json_encode($colleges) }}">
                                                            @if ($colleges['ro_detail']['type'] == 'post')
                                                                YES
                                                            @else
                                                                LEARN MORE
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class='small-6 medium-6 column text-center'>
                                                        <div class='recruitme-buttons yes' data-reveal-id="recruitmeModal" data-reveal-ajax="/ajax/recruiteme/{{ $colleges['college_id'] }}">YES</div>
                                                    </div>
                                                @endif
                                                <div class='small-6 medium-6 column text-center'>
                                                    <div class='recruitme-buttons no' onClick="trashSchool( {{ $colleges['college_id'] }} );">NO</div>
                                                </div>
                                            </div>
                                        </div>
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
                <div id="partner-redirect-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
                    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
                    <h4>You will be redirected to one of our partner's website</h4>
                    <div data-url="" class='partner-redirect-continue-button'>Continue</div>
                </div>
            </div>
             <!-- footer menu in mobile view -->
             @include('private.includes.ajax_loader')

            <!-- footer menu in mobile view -->
        </div>


    </div>
</div>
