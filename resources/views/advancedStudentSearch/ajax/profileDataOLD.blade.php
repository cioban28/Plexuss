<?php //dd($searchResults); 
$k = $searchResults[0];
// dd($k['college_info']);

//dd($data);
?>
        

        <!-- upload modal -->
        <div id="upload_prescreen_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
            <div class="text-right"><a class="close-reveal-modal" aria-label="Close">&#215;</a></div>   
            {{Form::open(array('id'=>'upload_prescreen_form'))}}
                {{ Form::hidden('postType', 'prescreenupload', array()); }}
                {{ Form::hidden('docType', 'prescreen', array('class'=>'doctype')); }}
                @if(isset($uid))
                {{ Form::hidden('user_id', $uid, array()); }}
                @endif

                <div class="row">
                    <div class="column fixed-col">
                        <img src="/images/transcript-img.png" alt="upload to plexuss">
                    </div>
                    <div class="column small-8 end">
                        <div class="update-title">Upload files</div>
                        {{Form::file('prescreen_upload')}}
                    </div>
                </div>

                <div class="row">
                    <div class="column small-12 text-right">
                        <div><span class="cancel-upload-btn close-reveal-modal cancel-upload-p-btn">Cancel</span> <span class="upload-files-btn">{{Form::submit('upload')}}</span></div>    
                    </div>
                </div>

            {{Form::close()}}
        </div>

        <div class="row outer-row">
            <div class="column small-4 medium-2 large-1 small-centered medium-uncentered">
                <div class="profile-img" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/{{$k['profile_img_loc'] or 'default.png'}},(default)]" style='background-image:url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/{{$k["profile_img_loc"] or "default.png"}}")'></div>
            </div>
            <div class="column small-12 medium-10 large-11 small-only-text-center">
                <div class="p-name">{{$k['address']}}</div>
                <div class="p-grad-yr">College Grad: <b>{{$k['grad_year'] or 'N/A'}}</b></div>
            </div>
            <!--<div class="column small-12 medium-3 large-2 small-only-text-center">
                <a href="">View Full Profile</a>
            </div>-->
        </div>

        <div class="row outer-row">
            <div class="column small-12 medium-6">
                <div class="clearfix custom-row">
                    <div class="p-sat left"><b>SAT: </b>{{$k['sat_score'] or 'N/A'}}</div>
                    <div class="p-act left"><b>ACT: </b>{{$k['act_composite'] or 'N/A'}}</div>
                    <div class="p-toefl left"><b>TOEFL: </b>{{$k['toefl_total'] or 'N/A'}}</div>
                    <div class="p-ielts left"><b>IELTS: </b>{{$k['ielts_total'] or 'N/A'}}</div>
                </div>

                <div class="custom-row p-financials-for-yr-1"><b>Financials for First Year: </b>{{$k['financial_firstyr_affordibility'] or 'N/A'}}</div>   

                <div class="custom-row">
                    <div><b>Objective:</b></div>
                    <div><q>{{$k['objective'] or 'N/A'}}</q></div>
                </div>

                <div class="row collapse custom-row">
                    <div class="column small-12 large-12 interestedin">
                        <div><b>Program(s) Interested In:</b></div>
                        <div>{{$k['major'] or 'N/A'}}</div>
                    </div>
                </div>
            </div>

            <div class="column small-12 medium-6">
                @if(Session::has('handshake_power'))
                <div class="row">
                    <div class="column small-12">
                        <div><b><a href="{{$loginas or ''}}" target="_blank">Login As</a></b></div>
                    </div>
                    <div class="column small-12">
                        <div><b>User id</b></div>
                        <div>{{$uid or ''}}</div>
                    </div>
                    @if(isset($k['fb_id']) && $k['fb_id'] != '')
                    <div class="column small-12">
                        <div><a target="_blank" href="https://www.facebook.com/{{$k['fb_id'] or ''}}">FB</a></div>
                    </div>
                    @endif
                    @if(isset($k['skype_id']) && $k['skype_id'] != 'N/A')
                    <div class="column small-12">
                        <div><b>Skype id</b></div>
                        <div><a href="skype:{{$k['skype_id']}}?chat">
                            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon.png" alt=""/>
                        {{$k['skype_id']}}
                        </a></div>
                    </div>
                    @endif
                    <div class="column small-12">
                        <div><b>Email</b></div>
                        <div><a href="mailto:{{$k['userEmail'] or ''}}">{{$k['userEmail'] or ''}}</a></div>
                    </div>
                    <div class="column small-12">
                        <div><b>Address</b></div>
                        <div>{{$k['userAddress'] or ''}}, {{$k['userCity'] or ''}}, {{$k['userState'] or ''}}, {{$k['userZip'] or ''}}</div>
                    </div>
                    <div class="column small-12">
                        <div><b>Phone (YOU CAN ONLY CALL IF THE PHONE HAS + IN IT)</b></div>
                        <div><a href="callto://{{str_replace(' ', '', trim($k['userPhone']))}}">{{$k['userPhone'] or ''}}</a></div>
                    </div>
                    <div class="column small-12">
                        -------------------------------
                    </div>
                </div>
                @endif
                <div class="uploaded-docs-container clearfix right-custom-row">
                    <div><b>Uploads:</b></div>
                    @foreach($k['upload_docs'] as $x)
                        <div class="single-doc left">
                            <div class="doc-icon {{$x['doc_type']}} left"></div>
                            <div class="doc-name left">
                                <div>@if($x['doc_type']== 'transcript') Transcript @elseif($x['doc_type']== 'toefl') TOEFL @elseif($x['doc_type']== 'ielts') IELTS @elseif($x['doc_type']== 'financial') Financial Docs @elseif($x['doc_type']== 'resume') Resume/Portfolio @elseif($x['doc_type']== 'prescreen_interview') Plexuss Interview @elseif($x['doc_type']== 'essay') Essay @elseif($x['doc_type']== 'passport') Passport @elseif($x['doc_type']== 'other') Other @endif</div>
                                <div class="options"><a class="view-doc" data-type="Transcript" onClick="openTranscriptPreview(this);" data-transcript-name="{{$x['transcript_name']}}">View</a> | <a href="{{$x['path']}}">Download</a></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    @endforeach

                    @if(!$k['transcript'] &&
                        !$k['toefl'] &&
                        !$k['ielts'] &&
                        !$k['financial'] &&
                        !$k['prescreen_interview'] &&
                        !$k['essay'] &&
                        !$k['passport'] &&
                        !$k['other'] &&
                        !$k['resume'])
                        <div>Has not uploaded any documents</div>   
                    @endif

                </div>
            
                <!-- upload Prescreen button-->
                <button id="upload_prescreen_btn" class="upload-prescreen-btn" data-doc-type="prescreen">Upload PreScreen</button>
            
            </div>
            @if(Session::has('handshake_power'))
            <div class="column small-12 medium-8">

            <div class="column small-12">
                -------------------------------
            </div>
            <div class="column small-12" style="margin-bottom: 0.4em;">
                <span><b>Colleges can get match to</b></span>
                <div class="add-college-matches-btn closed-color">
                    <span class="college-add-btn">&#65291;&nbsp;ADD</span>
                    <span class="college-close-btn">&ndash;&nbsp;&nbsp;CLOSE</span>
                </div>
            </div>
            <input id="student_info" type="hidden" value="{{ $uid }}">

            <!-- MODAL for adding a Matched College -->
            <div class="column small-12 add_college_modal">
                <div class="add-modal-container">

                    <!--///// Search and search results screen /////-->
                    <div class="s1">

                        <!-- input for search -->
                        <div class="college-search-container ui-widget"> 
                            <input class="collegeSearch ui-autocomplete-input" type="text" value="search for a college..."/>
                        </div>
                        <div class="collegeResults">
                            <!-- college search results go in here -->
                        </div>
                    </div>


                    <!--///// Select a portal screen /////-->
                    <div class="s2">
                        
                        <div class="college-head"> 
                        <!-- college selected goes up here-->
                         </div>

                        <!-- portal selection form -->
                        <div class="big-gray-title">Select portal</div>
                            <form>  
                                <fieldset>
                                    <div class="portals-radio-container">
                                        <!-- for each portal radio buttons go here-->
                                    </div>
                                </fieldset>

                                <div class="add-back-btn">Back</div>
                                <button class="add-college-btn" type="submit">Add School</button>   
                            </form>
                    </div>

                    
                </div>
            </div>

            @if(isset($matched_colleges) && !empty($matched_colleges))

            <div class="matched_colleges_container">
            @foreach($matched_colleges as $key)
            <div class="column small-12" style="margin-bottom: 0.4em;">
                <div>
                    <div class="row">
                        <div class="column small-2"><div class="recruit-student-btn text-center" onclick="Plex.studentSearch.addStudentManual('PreScreened', {{$uid}} , {{$key['college_id'] or -1}} , {{$key['aor_id'] or -1}}, {{$key['org_portal_id'] or -1}}, {{$key['aor_portal_id'] or -1}}, '{{$key['school_name'] or ''}}');">Screen</div></div>

                        <div class="column small-2"><div class="addpending-student-btn text-center" onclick="Plex.studentSearch.addStudentManual('Pending', {{$uid}} , {{$key['college_id'] or -1}} , {{$key['aor_id'] or -1}}, {{$key['org_portal_id'] or -1}}, {{$key['aor_portal_id'] or -1}}, '{{$key['school_name'] or ''}}');">Pending</div></div>

                        <div class="column small-2"><div class="addhandshake-student-btn text-center" onclick="Plex.studentSearch.addStudentManual('HandShake', {{$uid}} , {{$key['college_id'] or -1}} , {{$key['aor_id'] or -1}}, {{$key['org_portal_id'] or -1}}, {{$key['aor_portal_id'] or -1}}, '{{$key['school_name'] or ''}}');">HandShake</div></div>

                        <div class="column small-6">
                         {{$key['school_name'] or ''}} @if(isset($key['contract_type']))<b>Contract</b>@endif {{$key['contract_type'] or ''}} @if(isset($key['client_type']))<b>Client</b>@endif {{$key['client_type'] or ''}} @if(isset($key['org_portal_name']))<b>Portal name</b>@endif {{$key['org_portal_name'] or ''}} @if(isset($key['aor_portal_name']))<b>AOR Portal name</b>@endif {{$key['aor_portal_name'] or ''}}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            </div>

            <div class="column small-12">
                -------------------------------
            </div>
            @endif
            </div>
            <div class="column small-12 medium-4">
                <div class="row">
                    <div class="column small-12 student-profile-headers personal-notes">
                        Notes <small>(For your personal notes)</small>
                    </div>
                    
                    <div class="column small-12 textarea-col">
                        <textarea class="notes-textarea" name="studentNotes" cols="30" rows="10" data-studentid="{{$uid}}">{{$note or ''}}</textarea>
                        <div class="column small-12 last-saved-note-time">
                            @if( isset($note) && !empty($note) )
                            Last Saved: <span class="note-time-updated">{{$note_updated_at or '--:--'}}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        @if( isset($k['competitor_colleges']) && !empty($k['competitor_colleges']) )
        <div class="row outer-row collapse requested-by">
            <div class="column small-12">
                <b>This student has also requested to be recruited by: </b>
            </div>
            @if(isset($k['college_info']) && !empty($k['college_info']))
            <div class="column small-12 medium-3 your-college">
                <div class="text-center head">YOUR COLLEGE</div>
                <div class="college-item text-center">
                    <div class="college-img" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$k['college_info']['logo'] or 'default-missing-college-logo.png'}}, (default)]" style='background-image:url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$k["college_info"]["logo"]}}")'></div>
                    <div class="college-name">
                        <a href="/college/{{$k['college_info']['slug']}}" target="_blank">
                            <span data-tooltip aria-haspopup="true" class="has-tip" title="{{$k['college_info']['name']}}">
                                {{$k['college_info']['name']}}
                            </span>
                        </a>
                    </div>
                    <div class="college-views">
                        @if($k['college_info']['page_views'] == 0)
                            --
                        @else
                            {{$k['college_info']['page_views']}} views
                        @endif
                    </div>   
                </div>
            </div>
            @endif
            <div class="column small-12 @if(isset($k['college_info']) && !empty($k['college_info'])) {{'medium-9'}} @else {{'medium-12'}} @endif your-competitor">
                <div class="owl-left owl-arrow"><div></div></div>
                <div class="text-center head">YOUR COMPETITORS</div>
                <div class="student-search-owl owl-carousel owl-theme" data-isset="0">
                    @foreach($k['competitor_colleges'] as $l)
                        <div class="item text-center">
                            <div class="college-img" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$l['logo'] or 'default-missing-college-logo.png'}}, (default)]" style='background-image:url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$l["logo"]}}")'></div>
                            <div class="college-name">
                                <a href="/college/{{$l['slug']}}" target="_blank">
                                    <span data-tooltip aria-haspopup="true" class="has-tip" title="{{$l['name']}}">
                                        {{$l['name']}}
                                    </span>
                                </a>
                            </div>
                            <div class="college-views">
                                @if($l['page_views'] == 0)
                                    --
                                @else
                                    {{$l['page_views']}} views
                                @endif

                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="owl-right owl-arrow"><div></div></div>
            </div>
        </div>
        @endif    



    </div>

    <script type="text/javascript">
        $(document).foundation('interchange', 'reflow');
        Plex.studentSearch.initNewOwl();

    </script>