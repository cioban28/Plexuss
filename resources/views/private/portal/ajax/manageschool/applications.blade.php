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
            <div class="portalMainTitle left">APPLICATIONS</div>
            <div class="portalSubTitle left">Apply to schools or manage your current applications</div>
            <div class="show-tutorial right">
                <div class="tutorial-icon"></div>
                <div>SHOW TUTORIAL</div>
            </div>
        </div>
    </div>


    <!-- top your list menu -->
    <div class="portal_header_nav clearfix show-for-medium-up" id="move-to-trash-button-div">
        <div class="left action compare" onclick="portalCompareSchools();">
            <div class="p-icon compare"></div>
            <div class="action-name">COMPARE SCHOOLS</div>
        </div>
    </div>

    @if (empty($colleges))
        <div class="no-apps-bg"></div>
    @endif

    <!-- content area -->
    <div class="row portal_header-mid collapse">

        <div class="small-12 column portal-content-right-side " id="content-list-div">
            <!-- data table header -->
            <div class='row theader'>
                <div class='small-6 medium-4 column islist'>
                    <input type="checkbox" id="select-all-schools">
                    <div><label for="select-all-schools">Schools</label></div>
                </div>
                <div class='small-1 column text-center hide-for-small-only'>Rank</div>
                <div class='small-2 column text-center hide-for-small-only'>
                    <div style="position: relative; display: inline-block;">
                        ENGLISH PROGRAM
                        <div class="appl-tip">
                            ?
                            <div class="appl-tipper">
                                <div><b><u>English Program</u></b></div>
                                Estimated annual cost for English Programs
                            </div>
                        </div>

                    </div>
                </div>
                <div class='small-2 column text-center'>
                    <div style="position: relative; display: inline-block;">
                        UNDERGRADUATE
                        <div class="appl-tip">
                            ?
                            <div class="appl-tipper">
                                <div><b><u>Undergraduate</u></b></div>
                                Estimated annual cost for an Undergraduate degree
                            </div>
                        </div>
                    </div>
                </div>
                <div class='small-1 column text-center hide-for-small-only'>
                    <div style="position: relative; display: inline-block;">
                        GRADUATE
                        <div class="appl-tip">
                            ?
                            <div class="appl-tipper">
                                <div><b><u>Graduate</u></b></div>
                                Estimated annual cost for a Graduate degree
                            </div>
                        </div>
                    </div>
                </div>
                <div class='small-3 medium-2 column text-center'>STATUS</div>
            </div>

            @if (empty($colleges))
                <div class="no-apps-yet">
                    <div class="msg">You qualify to apply to 1 university and guess what, we’ve waived your fee!</div>
                    <div class='button' onclick="window.location.href='/college-application';">Apply</div>
                </div>
            @else
                <div class="list-items stylish-scrollbar">

                        <!-- This will be a loop! -->
                        @foreach ($colleges as $colleges)
                            <div class="item school">
                                <div class='row collapse innerwrapper'>

                                    <div class='small-6 medium-4 column'>
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
                                                        <img class='schoollogo' src="{{ $colleges['logo_url'] }}">
                                                    </a>
                                                @else
                                                    &nbsp;
                                                @endif
                                            </div>


                                            <div class="small-10 medium-9 large-8 column">
                                                <div class="school-deets">
                                                    <div class="schoolname">
														<span class="flag flag-{{ $colleges['country_code'] }}"></span>
                                                        <a href="/college/{{$colleges['slug']}}">
                                                            {{ $colleges['school_name'] }}
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <div class='schooladdress'>
                                                            {{ $colleges['city'] }}, {{ $colleges['state'] }}
                                                        </div>
                                                        <div id="quick-facts-in-your-list-button" class='quickfacts' onClick="showUserrecruitInfo( this, {{$colleges['college_id']}})">
                                                            Why I should apply<span class='smallArrow'></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class='small-1 column text-center hide-for-small-only'>
                                        <div class='ranking'>#{{ $colleges['rank'] or 'N/A' }}</div>
                                    </div>

                                    <div class="small-2 column text-center hide-for-small-only">
                                        <div class="appl-values">{{$colleges['epp_column_cost'] > 0 ? '$'.number_format($colleges['epp_column_cost'], 0) : 'N/A'}}</div>
                                    </div>

                                    <div class='small-2 column text-center'>
                                        <div class="appl-values">{{$colleges['undergrad_column_cost'] > 0 ? '$'.number_format($colleges['undergrad_column_cost'], 0) : 'N/A'}}</div>
                                    </div>

                                    <div class="small-1 column text-center hide-for-small-only">
                                        <div class="appl-values">{{$colleges['grad_column_cost'] > 0 ? '$'.number_format($colleges['grad_column_cost'], 0) : 'N/A'}}</div>
                                    </div>

                                    <div class='small-3 medium-2 column text-center'>
                                        <div class="appl-status @if($colleges['submitted'] == 1) submitted @endif">{{$colleges['submitted_msg'] or ''}}</div>
                                    </div>

                                </div>

                                <div class='schooldropdown why small-12 column' style="display:none;">
                                    @if( isset($colleges['why_recommended']) && !empty($colleges['why_recommended']) )
                                        <div class="why-reco msg"><b>You are receiving this recommendation because</b></div>
                                    @endif

                                    <ul class="why-reco list">
                                        @foreach($colleges['why_recommended'] as $why)
                                            <li>{{$why}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                        <!-- END of loop! -->
                </div>
            @endif
        </div>
    </div>



    </div>




</div>

<!-- footer menu in mobile view -->
<!-- footer menu in mobile view -->
