<!-- Recruit me modal -->
<div id="recruitmeModal" data-options="" class="reveal-modal medium" data-reveal>
	{{ Form::open(array('id'=>"recruitmePlsModal", 'name' => 'interested_reason','class' =>'no-padding', 'data-abide'=>'ajax', 'url' => '/ajax/recruiteme/'.$schoolId)) }}
	{{ Form::hidden( 'type', 'recruitMeModal')}}
	{{ Form::hidden( 'on-page', '', array('id' => 'page_identifier') ) }}
	{{ Form::hidden( 'source', '', array('id' => 'source_identifier') ) }}

    @if(isset($aorSchool))
        {{ Form::hidden('aorSchool', $aorSchool)}}
    @endif

	<div class="pos-rel modal-inner-div userInfoNotify @if(isset($showProfileInfo) && $showProfileInfo == 'showProfileModal') @else hide @endif">
        <div class='row'>
            <div class="column small-1">
            	<!--<a class="close-reveal-modal closeX">&#215;</a>-->
            </div>
            <div class="incomplete-contact-info-title column small-11 end text-left">
                {{$school_name or 'This University'}} needs a little more information before recruiting you
            </div>
            <div class="column small-offset-1 small-10 end incomplete-contact-info">
                <div class="row collapse">
                    <div class="column small-3">
                        <label class="inline" for="phoneinput">Phone Number</label>
                    </div>
                    <div class="column small-9 end" style="position: relative;">
                        {{ Form::text('phone', $phone, array( 'id' => 'phoneinput-with-code-2' ,'placeholder' =>'(000)000-0000','required', 'pattern' => 'phoneinput'))}}
                        {{ Form::hidden('area_code', '', array('class' => 'area_code')) }}
                        <div class="flag-code">
                            <div class="code-val">
                                @foreach( $countriesAreaCode as $code )
                                    @if( $code['country_id'] == $country_id )
                                        +{{trim($code['country_phone_code'])}}
                                    @endif
                                @endforeach
                            </div> &#9662;
                        </div>
                        <div class="twilio-err"><small>Please enter a valid phone</small></div>
                        <div id="phone-code-list">
                            <ul>
                                @foreach( $countriesAreaCode as $code )
                                    <li data-phone-code="{{$code['country_phone_code']}}">
                                        <div class="flag flag-{{strtolower($code['country_code'])}}"></div>
                                        <div class="country-name-code">{{$code['country_name']}} (+{{$code['country_phone_code']}})</div>
                                    </li>   
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="column small-3">&nbsp;</div>
                    <div class="column small-9 end" style="padding-bottom: 1.7em;">
                        {{Form::checkbox('txt_opt_in', 'acceptTextMsgFromShcool', (isset($txt_opt_in) && $txt_opt_in == -1)? false : true , array('id' => 'txt_opt_in'))}}
                        <label for="txt_opt_in" class="inline">I consent to receive text message from Plexuss and universities on the Plexuss network. <a href="/text-privacy-policy" target="_blank">Privacy Policy</a></label>
                    </div>
                </div>
                <div class="row collapse">
                    <div class="column small-3">
                        <label class="inline" for="addressinput">Address</label>
                    </div>
                    <div class="column small-9 end">
                        {{ Form::text('address', $address, array( 'id' => 'addressinput', 'placeholder' =>'Address', 'required', 'pattern' => 'address'))}}
                        <small class='error'>Please enter a valid address</small>
                    </div>
                </div>
                <div class="row collapse">
                    <div class="column small-3">
                        <label class="inline" for="addressinput">City</label>
                    </div>
                    <div class="column small-9 end">
                        {{ Form::text('city', $city, array( 'id' => 'cityinput' ,'placeholder' =>'City','required', 'pattern' => 'city'))}}
                        <small class='error'>Please enter a valid city</small>
                    </div>
                </div>
                <div class="row collapse">
                    <div class='small-12 large-3 column'>
                        <label class="inline" for="stateinput">State/Province</label>
                    </div>
                    @if( isset($is_intl_student) && (int)$is_intl_student == 1 )
                    <div class='small-12 large-4 column'>
                        {{ Form::text('state', $state, array( 'id' => 'stateinput' ,'placeholder' =>'state','pattern'=>'state', 'maxlength' => '2' ) )}}
                    </div>
                    @else
                    <div class='small-12 large-4 column text-center'>
                        {{ Form::text('state', $state, array( 'id' => 'stateinput' ,'placeholder' =>'state','pattern'=>'state', 'required', 'maxlength' => '2' ) )}}
                        <small class='error'>Please enter a valid state/province</small>
                    </div>
                    @endif
                    <div class='small-12 large-2 column text-center'>
                        <label class="inline" for="zipinput">Zip code</label>
                    </div>
                    <div class='small-12 large-3 end column'>
                        @if(isset($zipRequired) && $zipRequired)
                        {{ Form::text('zip', $zip, array( 'id' => 'zipinput' ,'placeholder' =>'Zipcode','pattern'=>'zip', 'required','maxlength' => '10'))}}
                        <small class='error'>Please enter a valid zip</small>
                        @else
                        {{ Form::text('zip', $zip, array( 'id' => 'zipinput' ,'placeholder' =>'Zipcode','pattern'=>'zip', 'maxlength' => '10'))}}
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="column small-12 text-center">
                        <button class="button check-info-btn">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<div class="pos-rel model-inner-div regularRecruitme @if(isset($showProfileInfo) && $showProfileInfo == 'showProfileModal') hide @endif">
	        <div class='row'>
	        	<div class="column small-1">
		        	<!--<a class="close-reveal-modal closeX">&#215;</a>-->
		        </div>
	            <div class="recruitTitle column small-11 end text-center">
	                {{$school_name or ''}} wants to know why you’re interested
	            </div>
	        </div>
	        <div class="row"> 
	            <div class="column small-12  large-6 leftRecruitForm">
	                <div class="row">
	                    <div class="applyTitle small-12 column">SELECT ALL THAT APPLY</div>
	                </div>
	                <div class="row">
	                    <ul class="services-ul small-12 column">
	                        <li>
	                             {{ Form::checkbox('reputation', 1 , null, array( 'id' => 'rmm_reputation' ));}} {{ Form::label('rmm_reputation', 'Academic Reputation') }}
	                        </li>                        
	                        <li>
	                             {{ Form::checkbox('location', 1 , null, array( 'id' => 'rmm_location' ));}} {{ Form::label('rmm_location', 'Location') }}
	                        </li>   
	                        <li>
	                            {{ Form::checkbox('tuition', 1 , null, array( 'id' => 'rmm_tuition' ));}} {{ Form::label('rmm_tuition', 'Cost of Tuition') }}
	                        </li>   
	                        <li>
	                            {{ Form::checkbox('program_offered', 1 , null, array( 'id' => 'rmm_program_offered' ));}} {{ Form::label('rmm_program_offered', 'Majors or Programs Offered') }}
	                        </li>   
	                        <li>
	                            {{ Form::checkbox('athletic', 1 , null, array( 'id' => 'rmm_athletic' ));}} {{ Form::label('rmm_athletic', 'Athletics') }}
	                        </li>
	                         <li>
	                            {{ Form::checkbox('onlineCourse', 1 , null, array( 'id' => 'rmm_onlineCourse' ));}} {{ Form::label('rmm_onlineCourse', 'Online Courses') }}
	                        </li>  
	                        <li>
	                            {{ Form::checkbox('campus_life', 1 , null, array( 'id' => 'rmm_campus_life' ));}} {{ Form::label('rmm_campus_life', 'Campus Life') }}
	                         </li>
	                         <li>
	                            Other
	                         </li>
	                        <li> 
	                            {{ Form::text('other', null, array('class'=>'otherReason')) }}  
	                        </li>                                   
	                    </ul>
	                </div>  
	            </div>
	            
	            <!-- This area here will change depending on the score values  -->
	            <!-- Compare scores area -->
	            <div class="small-12 medium-12 large-6  column rightRecruitCompare">
	                <div class="row">
	                    <div class="compareTitle column small-12">COMPARE YOUR SCORES TO THEIRS</div>
	                </div>
	                <div class="row">
	                    <div class="small-12 column compareMessage">
	                        Colleges will review your request and are not required to contact you.  It is completely up to their discretion and enrollment requirements.
	                    </div>
	                </div>
	                <div class="row">
	                    <div class="column small-12 large-6">
	                        <div class="row">
	                            <div class="avgScoreTitle column small-12 text-center">AVERAGE SCORES</div>
	                        </div>
	                        <div class="row">
	                            <div class="column small-6">
	                                <div class="circle avgGPA">GPA</div>
	                            </div>
	                            <div class="column small-6">
	                                <div class="circle gray">{{ $collegeScores['gpa']; }}</div>
	                            </div>
	                        </div>
	                        <div class="row pb5">
	                            <div class="column small-6">
	                                <div class="circle avgSAT">SAT</div>
	                            </div>
	                            <div class="column small-6">
	                                <div class="circle gray">{{ $collegeScores['sat']; }}</div>
	                            </div> 
	                        </div>
	                        <div class="row pb5">
	                            <div class="column small-6">
	                                <div class="circle avgACT">ACT</div>
	                            </div>
	                            <div class="column small-6">
	                                <div class="circle gray">{{ $collegeScores['act']; }}</div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="column small-12 large-6">
	                        <div class="row">
	                            <div class="scoreTitle column small-12 text-center">YOUR SCORES</div>
	                        </div>
	                        <div class="row">
	                            <div class="column small-6">
	                                <div class="circle yourGPA">GPA</div>
	                            </div>
	                            <div class="column small-6">
	                                <div class="circle gray">{{ $usrScores['gpa']; }}</div>
	                            </div>
	                        </div>
	                        <div class="row pb5">
	                            <div class="column small-6">
	                                <div class="circle yourSAT">SAT</div>
	                            </div>
	                            <div class="column small-6">
	                                <div class="circle gray">{{ $usrScores['sat']; }}</div>
	                            </div>
	                        </div>
	                        <div class="row pb5 ">
	                            <div class="column small-6">
	                                <div class="circle yourACT">ACT</div>
	                            </div>
	                            <div class="column small-6">
	                                <div class="circle gray">{{ $usrScores['act']; }}</div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <!-- End Compare scores area -->
	        </div>
	        <br/>
	        <!-- Here we need to check or display IF this school is in the plexuss network -->
	        <div class="row">
	            @if($in_our_network == 0)
	                <div class="column small-12 notInNetwork">
	                    This college is not part of our network, but we will be reaching out to them. We will let them know you are interested in their program.
	                </div>
                @elseif (isset($aorSchool) && (int)$aorSchool == 1)
                    <div class="column small-12 inNetwork">
                        This college is part of our network for its online programs. It is represented by a partner who is affiliated with the university.
                    </div>
	            @else
	                <div class="column small-12 inNetwork">
	                This college is part of our network. After you have finished your profile we will automatically let their admission office know you are interested so they can contact you.
	                </div>
	            @endif
	        </div>
	        <!-- End of in network checks. -->
	        <br/>
	        <div class="row">
	            <div class="column small-5 small-centered text-center">
	                <div class="button orangebutton" style="cursor:pointer;" onClick="submitRecruitmeModal({{ $schoolId or 'error-988'}});">Add to my list</div>
	            </div>
	        </div>
	</div>

	<script type="text/javascript">

		$(document).ready(function(){
			var page = window.location.pathname;

			if( page.indexOf('/') > -1 ) page = page.split('/')[1];
			$('#page_identifier').val(page);

			if( window.location.pathname.indexOf('7') > -1 ) $('#source_identifier').val('inquiry_pick_a_college');
		});

	    function openRegularRecruitmeModal(elem) {
			var fields = elem.closest('.userInfoNotify').find('input'), incomplete = false;

			$.each(fields, function(){
			if ( !$(this).val() && $(this).required ){
					incomplete = true;
					return false;
				}
			});

			if( !incomplete ){
				elem.parents('.userInfoNotify').hide("slide", { direction: "left" }, 400);
				$('.model-inner-div.regularRecruitme').delay(50).show('slide', { direction: "left" }, 400, function () { 
					$(this).removeClass('hide');
				});
			}
		}


		// function submitRecruitmeModal(schoolId){
		// 	var input = $('#recruitmePlsModal').serialize();
		// 	$.post('/ajax/recruiteme/' + schoolId , input, function(data, textStatus, xhr) {
		// 		// window.location.href = "/portal";
		// 		justInquired(data.inquired_list);
		// 		$('#recruitmeModal').foundation('reveal', 'close').remove();
		// 	});
		// }

		$(document).foundation({
	        abide : {
	            patterns : {
	                phoneinput : /^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/,
	                address: /^[a-zA-Z0-9\.,#\- ]+$/,
	                state: /^[a-zA-Z\.\- ]+$/,
	                city: /^[a-zA-Z\.\- ]+$/,
	                zip: /^[a-zA-Z0-9\.,\- ]+$/
	            }
	        },
	        
	        reveal :{
	            close_on_background_click: false,
	        }
	    });

	    $(document).on('keyup', '#phoneinput-with-code-2', function(){
            var val = $(this).val(),
                code = $('.code-val').text(),
                full_phone = code.trim()+val.trim();

            validatePhoneWithTwilio(full_phone);
        });

        function validatePhoneWithTwilio(full_phone){
            $.ajax({
				url: '/phone/validatePhoneNumber',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				data: {phone: full_phone},
				type: 'POST'
			}).done(function(data){

                //if no error validating phone number, hide error msg
                //else show error message
                if( data && !data.error ){
                    $('.twilio-err').hide();
                    $('.check-info-btn').prop('disabled', false);
                }else{
                    $('.twilio-err').show();
                    $('.check-info-btn').prop('disabled', true);
                }
            });
        };

	    var toggleDropdown = function(){
            var dropdown = $('#phone-code-list');
            if( !dropdown.is(':visible') ) dropdown.slideDown(250);
            else dropdown.slideUp(250);
        };

        var code = '';

        $(document).on('click', '.flag-code', function(){
            toggleDropdown(); 
        });

        $(document).on('click', '#phone-code-list li', function(e){
            code = $(this).data('phone-code');
            $('.code-val').html('+'+code)
        	console.count();
            toggleDropdown();
        });

        $(document).on('change', '#phoneinput-with-code-2', function(){
            $('.area_code').val( $('.code-val').text().trim() );

            // var phone = $('.area_code').val()+$('#phoneinput-with-code-2').val();

            // $.ajax({
            // 	url: 'https://api.plexuss.com/phone/validatePhoneNumber/'+phone,
            // 	type: 'GET',
            // })
            // .done(function(dt) {
            // 	if (dt == 'true')
            // 		console.log(dt);
            // 	else
            // 		console.log(dt);
            // });
            
        });

        $(document).on('click', '.check-info-btn', function(){
            $('.area_code').val( $('.code-val').text().trim() );

            var info = {
				address: $('#addressinput').val(),
    			city: $('#cityinput').val(),
    			state: $('#stateinput').val(),
    			phone: $('#phoneinput-with-code-2').val(),
    			area_code: $('.area_code').val()
            };

           	if( $('#txt_opt_in').is(':checked') ) info.txt_opt_in = true; 

        	if( $('input[data-invalid]').length === 0 && !$('.twilio-err').is(':visible') ){
				$.ajax({
	        		url: '/ajax/recruitmeinfo',
	        		type: 'POST',
	        		data: info,
	        		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
	        		success: function(data){
	        			console.log(data);
			            openRegularRecruitmeModal($('.check-info-btn'));
	        		},
	        		error: function(err){
	        			console.log(err);
	        		}
	        	});
        	}
        	
        });


	</script>

	{{ Form::close(); }}
</div>
