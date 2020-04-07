
<div id="manage-school-div" class='row collapse'>
    <div class="column small-12">
        <div class="row pos-rel">
           <!-- header menu in mobile view -->
        	<div class="show-for-small-only">
                <div class="row pt10">
                	<div class="small-12 column c-black fs13 text-center">RECOMMENDED SCHOOLS</div>
                </div>
            </div>
            <!-- header menu in mobile view -->
            <div class="portal-section-head clearfix show-for-medium-up">
                <div class="portalMainTitle left">SCHOOLS RECOMMENDED BY PLEXUSS</div>
                <div class="portalSubTitle left">Say “yes” to add to Your List, or “no” to remove</div>
                <div class="show-tutorial right">
                    <div class="tutorial-icon"></div>
                    <div>SHOW TUTORIAL</div>
                </div>
            </div>
        </div>

        <div class="portal_header_nav clearfix show-for-medium-up">
            <div class="left action" onclick="portalCompareSchools();">
                <div class="p-icon compare"></div>
                <div class="action-name">COMPARE SCHOOLS</div>
            </div>
        </div>

        <div class="row portal_header-mid collapse">
            <!-- left menu -->
            <div class="small-12 column portal-content-right-side " id="content-list-div">
                <!-- data table header -->
                <div class='row theader collapse'>
                    <div class='small-9 medium-6 column '>
                        <input type="checkbox" id="select-all-schools">
                        <div><label for="select-all-schools">Schools</label></div>
                    </div>
                    <div class='small-3 medium-2 column text-center'>RANK</div>
                    <div class='show-for-medium-up medium-3 end column text-center'>WANT TO BE RECRUITED</div>
                </div>
                <div class="list-items stylish-scrollbar">
                    @if (empty($colleges))
                        <div class="row">
                            <div class='small-10 small-centered column text-center noschoolmsg'>
                                You won't find meaningless recommendations here.</br>
                                We need to know what schools you are already interested in for us to recommend schools to you.
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class='small-10 small-centered column text-center'>
                                <div class='button' onClick="console.log('Monkey1');addSchoolPopup();">Add colleges to my list</div>
                            </div>
                        </div>
                    @else
                        <!-- This will be a loop! -->
                        @foreach ($colleges as $college)
                            <div class='row item school collapse'>
                                <div class='small-12 column'>
                                    <div class='row collapse innerwrapper'>
                                        <div class='small-9 medium-6 column'>
                                            <div class='row collapse'>
                                                <div class='small-1 column text-center'>
                                                    {{ Form::checkbox('schoolID', $college['slug']  , false, array(
                                                    'class'=>'select-school-chkbx',
                                                    'data-info' => '{"slug" : "'.$college["slug"].'" , "id" : "'. $college['college_id'] .'"}'
                                                    )) }}
                                                </div>

                                                <div class="small-2 medium-2 large-2 column text-center show-for-medium-up">
                                                    @if (isset($college['logo_url']))
                                                        @if(isset($college['slug']))
                                                        <a href="/college/{{ $college['slug'] }}">
                                                            <img class='schoollogo' src="{{ $college['logo_url'] }}">
                                                        </a>
                                                        @else
                                                        <span>
                                                            <img class='schoollogo' src="{{ $college['logo_url'] }}">
                                                        </span>
                                                        @endif
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </div>


                                                <div class="small-10 medium-8 large-8 column">

                                                    <div class='row collapse'>
                                                        <div class='schoolname small-12 column'>
															 <span class="flag flag-{{ $college['country_code'] }}"></span>
                                                            <a href="/college/{{ $college['slug'] }}">{{ $college['school_name'] }}</a>
                                                        </div>
                                                        <div class='schooladdress small-12 column'>
                                                            {{ $college['city'] }}, {{ $college['state'] }}
                                                        </div>
                                                        @if(!isset($college['type']) || $college['type'] != "scholarship")
                                                        <div class='small-12 column quickfacts recommended' onClick="showUserrecruitInfo( this, {{$college['college_id']}})">
                                                            Why we recommended this school?<span class='smallArrow'></span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='small-3 medium-2 column text-center'>
                                            <div class='ranking'>#{{ $college['rank'] }}</div>
                                        </div>
                                        <div class='small-12  medium-3 column end'>
                                            <div class="row ">
                                                @if(isset($college['type']) && $college['type'] == "scholarship")
                                                    <div class='small-6 medium-6 column text-center'>
                                                        <div class='recruitme-buttons yes' onClick="addUserScholarship( {{ $college['college_id'] }} );">YES</div>
                                                    </div>
                                                @elseif ( isset($college['ro_detail']) && isset($college['ro_detail']['ro_id']) )
                                                    <div class='small-6 medium-6 column text-center'>
                                                        <div class='revenue-organizaton-button recruitme-buttons {{$college['ro_detail']['type']}}' data-college="{{ json_encode($college) }}">
                                                            @if ($college['ro_detail']['type'] == 'post')
                                                                YES
                                                            @else
                                                                LEARN MORE
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class='small-6 medium-6 column text-center'>
                                                        <div class='recruitme-buttons yes' data-reveal-id="recruitmeModal" data-reveal-ajax="/ajax/recruiteme/{{ $college['college_id'] }}">YES</div>
                                                    </div>
                                                @endif
                                                @if(isset($college['type']) && $college['type'] == "scholarship")
                                                <div class='small-6 medium-6 column text-center'>
                                                    <div class='recruitme-buttons no' onClick="trashSpecificScholarship( {{ $college['college_id'] }} );">NO</div>
                                                </div>
                                                @else
                                                <div class='small-6 medium-6 column text-center'>
                                                    <div class='recruitme-buttons no' onClick="trashSchool( {{ $college['college_id'] }} );">NO</div>
                                                </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class='schooldropdown small-12 column' style='display:none;'>
                                    @if (isset($college['recommend_based_on_college_name']))
                                        <div class='row'>
                                            <div class='small-12 large-6 column recruitinfo'>
                                                You are receiving this recommendation because you chose {{$college['recommend_based_on_college_name']}} on {{$college['date_added']}} .
                                            </div>
                                            <div class='small-12 large-6 column'>
                                                <div class='row collapse recruitleftbox'>
                                                    <div class='small-12 column recruitschooltitle'>{{$college['school_name']}} has:</div>
                                                    <div class='small-12 column'>
                                                        <ol type="a">
                                                            @if (isset($college['is_higher_rank_recommend'] ))
                                                                @if($college['is_higher_rank_recommend'] ==1)
                                                                    <li>A higher rank</li>
                                                                @endif
                                                            @endif
                                                            @if (isset($college['is_major_recommend']))
                                                                <li>They offer your degree and major</li>
                                                            @elseif (isset($college['is_department_recommend']))
                                                                <li>They offer your degree and majors in the same department</li>
                                                            @endif
                                                            @if (isset($college['is_lower_tuition_recommend'] ))
                                                                @if($college['is_lower_tuition_recommend'] ==1)
                                                                    <li>Lower Tuition</li>
                                                                 @endif
                                                            @endif
                                                            @if (isset($college['is_top_75_percentile_recommend'] ))
                                                                @if($college['is_top_75_percentile_recommend'] ==1)
                                                                    <li> Your score put you in the top 75% percentile of their past year’s enrollment class</li>
                                                                @endif
                                                            @endif
                                                        </ol>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class='row'>
                                            <div class='small-12 large-6 column recruitinfo'>
                                                You are receiving this recommendation because you are in the 75th percentile of the students who enrolled at this college in the past year.
                                            </div>
                                            <div class='small-12 large-6 column'>
                                                <div class='row collapse recruitleftbox'>
                                                    <div class='small-12 column'>
                                                        In order to get better recommendations you can also choose the schools that you are interested in attending.
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
             @include('private.includes.ajax_loader')
             <!-- footer menu in mobile view -->
            <!-- footer menu in mobile view -->
        </div>


    </div>
</div>

@if(isset($latest_recruit) && !empty($latest_recruit))
<!-- Recommendation Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          Hi {{$fname}},
          <a type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </a>
        </h5>
      </div>
      <div class="modal-body" id="modal-content">
          <div class='description'>

            <span id="uni_name" style="font-weight: 600">{{$latest_recruit['college_name']}}</span>
            has been added to your list of colleges. Learn more about other recommendations by clicking 'Why we recommended this school' tab.
          </div>
        <div class='desktop-gif'>
            <img src="{{asset('images/portal/desktop-gif.gif')}}">
        </div>
        <div class='mobile-gif'>
            <img src="{{asset('images/portal/mobile-gif.gif')}}">
        </div>
          <button id="got-it" class="got-it">Ok, got it!</button>
          <a href="#" class="dont-show">Got it! Don't show it again</a>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" type="text/css" href="{{asset('css/portal-recommended-modal/portal_recommended_modal.css')}}">
<script type="text/javascript">
    // Get the modal
    $(document).ready(function(e){
      var modal = document.getElementById('myModal');
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];
        // When the user clicks on <span> (x), close the modal
        var gotIt = document.getElementById('got-it');
        gotIt.onclick = function(){
          modal.style.display = "none";
        }
        span.onclick = function() {
            modal.style.display = "none";
        }
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
      }
      if({{$latest_recruit['show_modal']}})
      {
        modal.style.display = "block";
      }
    });

    $('.got-it').on('click', function(){
        var modal = document.getElementById('myModal');
        modal.style.display = "none";
    });

    $('.dont-show').on('click', function(){
        var modal = document.getElementById('myModal');
        modal.style.display = "none";
        $.ajax({
            type: "POST",
            url: '/ajax/portal/dont_show_modal/' + {{$user_id}},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
                if(data == 'success'){
                    console.log('Won\'t show this pop up again ');
                }
            }
        })
    });
</script>
@endif
