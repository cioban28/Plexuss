<?php

 // dd(get_defined_vars());

?>

<div id="manage-school-div" class='row collapse'>
    <div class="column small-12">
        <div class="row pos-rel"><!-- header menu in mobile view -->
        	<div class="show-for-small-only">
                <div class="row pt10">
                	<div class="small-12 column c-black fs13 text-center">TRASH</div>
                </div>
            </div>
          <!-- header menu in mobile view -->
            <div class="portal-section-head clearfix show-for-medium-up">
                <div class="portalMainTitle left">TRASH</div>
                <div class="portalSubTitle left">Restore schools and Scholarships</div>
                <div class="show-tutorial right">
                    <div class="tutorial-icon"></div>
                    <div>SHOW TUTORIAL</div>
                </div>
            </div>
        </div>

        <div class="row portal_header_nav show-for-medium-up">
            <div class="left action" onclick="restoreSchool()">
                <div class="p-icon restore"></div>
                <div class="action-name">RESTORE</div>
            </div>
        </div>

        <div class="row portal_header-mid collapse">

                <div class="small-12 column portal-content-right-side " id="content-list-div">
                <!-- data table header -->
                <div class='row theader'>
                    <div class='small-9 medium-9 column istrash'>
                        <input type="checkbox" id="select-all-schools">
                        <div><label for="select-all-schools">Schools and Scholarships</label></div>
                    </div>
                    <!-- <div class='small-3 medium-2 column text-center'>Rank</div> -->
                    <div class='medium-3 column text-center show-for-medium-up'>RESTORE</div>
                </div>
                <div class="list-items stylish-scrollbar">

                    @if (empty($colleges))
                        <div class="row">
                            <div class='small-10 small-centered column text-center noschoolmsg'>
                                Trash is empty.
                            </div>
                        </div>
                    @else
                        <!-- This will be a loop! -->
                        @foreach ($colleges as $colleges)
                            <div class='row item school collapse'>

                                <div class='small-12 column'>
                                    <div class='row collapse innerwrapper'>



                                        <div class='small-9 medium-9 column'>
                                            <div class='row collapse'>
                                                <div class='small-1 column text-center'>
                                                    {{ Form::checkbox('schoolID', $colleges['college_id']  , false, array(
                                                    'class'=>'select-school-chkbx',
                                                    'data-type' => $colleges["type"],
                                                    'data-info' => '{"slug" : "'.$colleges["slug"].'" , "id" : "'. $colleges['college_id'] .'"}'
                                                    )) }}
                                                </div>

                                                <!-- https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$colleges['logo_url']}} -->
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
                                                    <div class='row collapse'>
                                                        <div class='schoolname small-12 column'>
															<span class="flag flag-{{ $colleges['country_code'] }}"></span>
                                                            <a href="/college/{{ $colleges['slug'] }}">{{ $colleges['school_name'] }}</a>
                                                        </div>
                                                        <div class='schooladdress small-6 column'>
                                                            {{ $colleges['city'] }}, {{ $colleges['state'] }}
                                                        </div>
                                                        <div class='small-6 column quickfacts' onClick="getUserCollegeInfo( this, {{$colleges['college_id']}})">
                                                            quick facts<span class='smallArrow'></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class='small-3 medium-2 column text-center'>
                                            <div class='ranking'>#{{ $colleges['rank'] }}</div>

                                        </div> -->

                                        <div class='small-9 medium-3 column text-center end'>
                                            <div class='row collapse'>
                                                <!--
                                                <div class='small-6 column hide-for-medium-up55' onClick="getUserCollegeInfo( this, {{$colleges['college_id']}})" style="display:none;">
                                                    <div class='recruitme-buttons no'>Quick facts</div>
                                                </div>

                                                <div class='small-8 small-centered column hide-for-medium-up' onClick="restoreSchool( {{ $colleges['college_id'] }} );">
                                                    <div class='recruitme-buttons yes'>Restore</div>
                                                </div>
                                                -->

                                                <div class='small-9 text-center medium-12 column'>
                                                    <div class='item-recover-button' onClick="restoreSchool( {{ $colleges['college_id'] }}, '{{$colleges['type']}}' );">
                                                        <img src="/images/portal/recover_icon.png">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>


                                    </div>
                                </div>

                                <div class='schooldropdown small-12 column' style="display:none;">
                                    HIDDEN Dropdown
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
<!-- footer menu in mobile view -->
@include('private.portal.ajax.includes.manageschoolMobilefooter')
<!-- footer menu in mobile view -->
