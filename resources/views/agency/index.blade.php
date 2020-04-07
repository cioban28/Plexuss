<?php 
?>

@extends('agency.master')
@section('content')

	<div class='row collapse agency-main-dash-container'>

{{-- 		<div class="column small-12 text-right agency-action-bar-container">
			
			<a data-reveal-id="exp-student-modal" class="radius button action-bar-btn">
				<div class="action-bar-content"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png"></div>
				<div class="action-bar-content">EXPORT STUDENTS</div>
			</a>
			<a href="/agency/filter" class="radius button action-bar-btn">
				<div class="action-bar-content"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png"></div>
				<div class="action-bar-content">TARGETING</div>
			</a> --}}
			<!--<a href="/agency/filter" class="radius button action-bar-btn">
				<div class="action-bar-content"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png"></div>
				<div class="action-bar-content">EXPORT STUDENTS</div>
			</a>-->
{{-- 			<a href="/agency/settings" class="radius button action-bar-btn">
				<div class="action-bar-content"><img class="for-agency-settings" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/icons/settings.png"></div>
				<div class="action-bar-content">ACCOUNT SETTINGS</div>
			</a>
			<a href="/agency/studentsearch" class="radius button action-bar-btn">
				<div class="action-bar-content"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/magnifier.png"></div>
				<div class="action-bar-content">ADVANCED SEARCH</div>
			</a>
		</div> --}}

		<div id='agency_dashboard' class='small-12 small-centered column' data-agency_id="{{ $agency_collection->agency_id }}">
			<!-- \\\\\ dashboard header row - start ///// -->
			<div class='dashboard-top row'>
				<div class='left-side-dashboard-top'>
					<h3 class='header'>Hi, {{ $agency_name or '' }}</h3>
					<b>Member Since:</b> <span id='agency-member-since' data-date='{{ $agency_collection->created_at }}'>{{-- date --}}</span><br />
					<b>Member Status:</b> Free
					<div class='important-msg-container'>
						<span class='exclaim'>!</span>
						<span>
							For urgent matters, <span class='send-important-msg-btn'>click</span> to send a message
						</span>
					</div>
				</div>
				<div class='right-side-dashboard-top'>
					<div class="owl-carousel">
						<div>
							<div class='toggle-monthly-overall'>
								<div class='stats-btn active' data-type='month'>This Month</div>
								<div class='stats-btn' data-type='overall'>Overall</div>
							</div>
			    			<div class='statistics-wrapper application-stats'>
								<div class='apps-status applications'>
									<div class='apps-status-number'>0</div>
									<div class='apps-status-text'>College Applications</div>
								</div>
								<div class='divider'></div>
								<div class='apps-status accepted'>
									<div class='apps-status-number'>0</div>
									<div class='apps-status-text'>Accepted Students</div>
								</div>
								<div class='divider'></div>
								<div class='apps-status rejected'>
									<div class='apps-status-number'>0</div>
									<div class='apps-status-text'>Removed Students</div>
								</div>
								<div class='divider'></div>
								<div class='apps-status enrolled'>
									<div class='apps-status-number'>0</div>
									<div class='apps-status-text'>Enrolled Students</div>
								</div>
							</div>
						</div>

						{{-- Shows last three months of statistics --}}
		    			<div class='statistics-wrapper monthly-stats'>
							<table>
								<tr class='table-header'>
									<th class='year'></th>
									<th>Completed</th>
									<th>Pacing</th>
									<th>Accepted</th>
									<th>Removed</th>
									<th>Enrolled</th>
								</tr>
								{{-- Append last three months of statstics below --}}
							</table>
						</div>
					</div>
					<div class='unique-link-btn' data-tooltip aria-haspopup="true" title="Click here to show a unique application link for prospective students to sign up">Unique Application Link</div>
				</div>
			</div>	
			<!-- \\\\\ dashboard header row - end ///// -->


			<!-- \\\\\\\\\\\ manage students indicators - start ////////// -->

{{-- 			<div class="row dashboard-section-headers">
				<div class="column small-12">
					Manage Students
				</div>
			</div> --}}

			<div class='agency-bucket-stats-container'>
				<div class='agency-manage-students leads' data-bucket='leads'>
					<h4>Leads</h4>
					<div class='agency-bucket-stats leads'>
						<div class='bucket-icon-container'>
							<div class='bucket-icon leads'></div>
						</div>

						<div class='stats-container leads'>
							<div class='new-number leads'>0</div>
							<div class='total-number leads'>0 Total</div>
						</div>
					</div>
				</div>
				<div class='agency-manage-students opportunities' data-bucket='opportunities'>
					<h4>Opportunities</h4>
					<div class='agency-bucket-stats opportunities'>
						<div class='bucket-icon-container'>
							<div class='bucket-icon opportunities'></div>
						</div>

						<div class='stats-container opportunities'>
							<div class='new-number opportunities'>0</div>
							<div class='total-number opportunities'>0 Total</div>
						</div>
					</div>
				</div>
				<div class='agency-manage-students applications' data-bucket='applications'>
					<h4>Applications</h4>
					<div class='agency-bucket-stats applications'>
						<div class='bucket-icon-container'>
							<div class='bucket-icon applications'></div>
						</div>

						<div class='stats-container applications'>
							<div class='new-number applications'>0</div>
							<div class='total-number applications'>0 Total</div>
						</div>
					</div>
				</div>

			</div>

{{-- 			<div class="row">
				<!-- inquiries -->
				<div id='dash_inquiries' class='medium-3 column dash_indicator'>
					<div class='row'>
						<div class='small-12 column'>
							<!-- indicator numbers -->
							<div class='row'>
								<div class='small-12 column indicator_feed'>
									<div class='row'>
										<div class='small-3 column text-center managestudent-indicator-img-col'>
											<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/inquiiries-icon.png'>
										</div>
										<div class='small-8 end column text-right'>
											<span class='indicator_number'>
												{{ isset( $inquiryCnt ) ? $inquiryCnt : 0  }}
											</span>
											<span>
												 New
											</span>
											<div>
												<span class='indicator_number'>
													{{ isset( $inquiryCntTotal ) ? $inquiryCntTotal : 0  }}
												</span>
												<span>
													 Total
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- end indicator numbers -->
							<!-- indicator bottom link -->
							<div class='row collapse'>
								<div class='small-12 column'>
									<a href='/agency/inquiries'>
										<div class='row'>
											<div class='small-12 column indicator_link'>
												<span>
													Inquiries
												</span>
											</div>
										</div>
									</a>
								</div>
							</div>
							<!-- end indicator bottom link -->
						</div>
					</div>
				</div>

				<!-- recommended -->
				<div id='dash_recommended' class='medium-3 column dash_indicator'>
					<div class='row'>
						<div class='small-12 column'>
							<!-- indicator numbers -->
							<div class='row'>
								<div class='small-12 column indicator_feed'>
									<div class='row'>
										<div class='small-3 column text-center managestudent-indicator-img-col'>
											<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/recommended-icon.png'>
										</div>
										<div class='small-8 end column text-right'>
											<span class='indicator_number'>
												{{ isset( $recommendCnt ) ? $recommendCnt : 0  }}
											</span>
											<span>
												 New
											</span>
											<div>
												<span class='indicator_number'>
													{{ isset( $recommendCntTotal ) ? $recommendCntTotal : 0  }}
												</span>
												<span>
													 Total
												</span>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="column small-12 small-text-center expiration-timelimit">
											Expires in {{$expiresIn or '24'}}*
										</div>
									</div>
								</div>
							</div>
							<!-- end indicator numbers -->
							<!-- indicator bottom link -->
							<div class='row collapse'>
								<div class='small-12 column'>
									<a href='/agency/recommendations'>
										<div class='row'>
											<div class='small-12 column indicator_link'>
												<span>
													Recommended
												</span>
											</div>
										</div>
									</a>
								</div>
							</div>
							<!-- end indicator bottom link -->
						</div>
					</div>
				</div>

				<!-- approved -->

				<div id='dash_approved' class='medium-3 column dash_indicator'>
					<div class='row'>
						<div class='small-12 column'>
							<!-- indicator numbers -->
							<div class='row'>
								<div class='small-12 column indicator_feed'>
									<div class='row'>
										<div class='small-3 column text-center managestudent-indicator-img-col'>
											<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/accepted-icon.png'>
										</div>
										<div class='small-8 end column text-right'>
											<span class='indicator_number'>
												{{ isset( $approvedCnt ) ? $approvedCnt : 0  }}
											</span>
											<span>
												 Total
											</span>
										</div>
									</div>
								</div>
							</div>
							<!-- end indicator numbers -->
							<!-- indicator bottom link -->
							<div class='row collapse'>
								<div class='small-12 column'>
									<a href='/agency/approved'>
										<div class='row'>
											<div class='small-12 column indicator_link'>
												<span>
													Approved
												</span>
											</div>
										</div>
									</a>
								</div>
							</div>
							<!-- end indicator bottom link -->
						</div>
					</div>
				</div>

				<!-- new features block - start -->
				<div id='dash_newFeatures' class='medium-3 column dash_indicator'>
					<div class='row'>
						<div class='small-12 column'>

							<div class='row'>
								<div class='small-12 column indicator_feed'>
									<div class="new-feature-head">
										<b>NEW FEATURE:</b>	
									</div>

									<!-- filter recommendations -->
									<div class="feature-toggler-container filter">
										<div class="new-feature-descrip">
											Now you can set a filter <br />for your daily <br />recommendations.
										</div>

										<div>
											<a href="/agency/filter">
												<div class="set-filter-btn"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png"></div>
												<div class="set-filter-btn"><b>SET YOUR FILTER</b></div>
											</a>
										</div>	
									</div>

									<!-- export students -->
									<div class="feature-toggler-container export">
										<div class="new-feature-descrip">
											Now you can export approved <br />students into your CRM
										</div>

										<div>
											<a data-reveal-id="exp-student-modal">
												<div class="set-filter-btn"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png"></div>
												<div class="set-filter-btn"><b>EXPORT STUDENTS</b></div>
											</a>
										</div>
									</div>

									<!-- student search -->
									<div class="feature-toggler-container export">
										<div class="new-feature-descrip">
											Now you can search through <br /> our database of students
										</div>

										<div>
											<a href="/agency/studentsearch">
												<div class="set-filter-btn"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/magnifier.png"></div>
												<div class="set-filter-btn"><b>SEARCH STUDENTS</b></div>
											</a>
										</div>
									</div>

								</div>
							</div>

						</div>
					</div>
				</div>
				<!-- new features block - end -->

			</div>

			
			

			

			<!-- \\\\\\\\\\\ manage students indicators - end ////////// -->


			<!-- \\\\\\\\\\ Engage with students indicators - start ////////// -->

			<div class="row dashboard-section-headers">
				<div class="column small-12">
					Engage with Students
				</div>
			</div>

			<div class='row'>
				<!-- messages -->
				<div id='dash_messages' class='medium-3 column end dash_indicator'>
					<div class='row'>
						<div class='small-12 column'>
							<!-- indicator numbers -->
							<div class='row'>
								<div class='small-12 column indicator_feed'>
									<div class='row'>
										<div class='small-3 column text-center managestudent-indicator-img-col'>
											<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/messages-icon.png'>
										</div>
										<div class='small-8 end column text-right'>
											<span class='indicator_number'>
												 {{ isset( $messageCnt ) ? $messageCnt : 0 }}
											</span>
											<span>
												 New
											</span>
										</div>
									</div>
								</div>
							</div>
							<!-- end indicator numbers -->
							<!-- indicator bottom link -->
							<div class='row collapse'>
								<div class='small-12 column'>
									<a href='/agency/messages'>
										<div class='row'>
											<div class='small-12 column indicator_link'>
												<span>
													Go to Messages
												</span>
											</div>
										</div>
									</a>
								</div>
							</div>
							<!-- end indicator bottom link -->
						</div>
					</div>
				</div>
				<!-- end messages -->

			</div> --}}
			<!-- \\\\\\\\\\ Engage with students indicators - start ////////// -->

			<div class='agency-review-container'>
				<div class='agency-star-container'>
					<h4>Reviews</h4>
					<div class='agency-star-rating'>
						<div class='rating'>
							<div class='agency-star-score'>{{ isset($review_avg) ? ceil($review_avg) : '?' }}</div>
							<div>out of 5</div>
						</div>
						<div class='stars'>
							@for ($i = 1; $i <= 5; $i++)
								@if (isset($review_avg) && ceil($review_avg) >= $i)
									<div class='star-icon active'></div>
								@else 
									<div class='star-icon'></div>
								@endif
							@endfor
						</div>
					</div>
					<div class='edit-company-profile-btn'>
						<a href='/agency/settings'>Edit Company Profile</a>
					</div>
				</div>
				<div class='user-reviews-container'>
					<div class='review-filters'>&nbsp;{{-- Rep --}}</div>
					<div class='user-reviews'>
						No reviews yet.
						{{-- Inject reviews here --}}
					</div>
				</div>
			</div>
		</div>
	</div>


	<!-- first time agent welcome layer -->
	@if( isset($agency_collection) && $agency_collection->first_time_agent == 1 )
	<div id="first-time-agent-welcome-layer">
		<div class="welcome-layer-inner text-center">
			<div>Welcome to your dashboard,</div>
			<div>Let's get your profile setup so you</div>
			<div>can continue</div>
			<a class="get-started-btn-link">
				<div class="get-started-btn">
					Get started!
				</div>
			</a>
		</div>
	</div>
	@endif


	<!-- upgrade acct modal -->
	<div id="upgrade-acct-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<div class="row">
			<div class="column small-12 text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
			</div>
		</div>

		<div class="row upgrade-msg-row">
			<div class="column small-12 text-center">
				Upgrade your account to filter your daily student recommendations
			</div>
		</div>

		<div class="row filter-intro-container" data-equalizer>
			<div class="column small-12 medium-4">
				<div class="filter-intro-step" data-equalizer-watch>
					<div class="text-center">1</div>
					<div class="text-center">
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-1-filter.png" alt="Plexuss">
					</div>
					<div>
						You receive student recommendations daily, but you're looking for certain kinds of students
					</div>
				</div>	
			</div>
			<div class="column small-12 medium-4">
				<div class="filter-intro-step" data-equalizer-watch>
					<div class="text-center">2</div>
					<div class="text-center">
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-2-filter.png" alt="Plexuss">
					</div>
					<div>
						Choose what you'd like to filter by and save your changes (menu on the left)	
					</div>
				</div>	
			</div>
			<div class="column small-12 medium-4">
				<div class="filter-intro-step" data-equalizer-watch>
					<div class="text-center">3</div>
					<div class="text-center">
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-3-filter.png" alt="Plexuss">
					</div>
					<div>
						Based on your filters, you will receive recommendations that may be a better fit for your school	
					</div>
				</div>	
			</div>
		</div>

		<div class="row upgrade-or-naw-btn-row">
			<div class="column small-12 medium-6 large-5 large-offset-1 text-right">
				<a href="" data-reveal-id="thankyou-for-upgrading-modal" onClick="Plex.inquiries.requestToBecomeMember();" class="radius button">I'd like to upgrade my account</a>
			</div>
			<div class="column small-12 medium-6 large-5 end">
				<a href="" class="radius button secondary close-reveal-modal" aria-label="Close">I'll think about it</a>
			</div>
		</div>	
	</div>

	<!-- Export Students modal -->
	<div id="exp-student-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<div class="row">
			<div class="column small-12 text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
			</div>
		</div>

		{{ Form::open(array('action' => array('AjaxController@exportApprovedStudentsFile', "agent") , 'data-abide' , 'id'=>'form' , 'files'=> true)) }}
		<div class="row">
			<div class="column small-3 end">
				Date Range:
				{{ Form::text('date',"", array('id' => 'dtrange','required','class'=>'dash-cal','placeholder'=>"&nbsp;&nbsp;Date(S)")) }}
			</div>
		</div>
		<div class="row">
			<div class="column small-12">
				&nbsp;
			</div>
		</div>
		
		<div class="row">
			<div class="column small-3">
				@for ($i = 0; $i < ceil(count($export_fields) * 1/4); $i++)
				    {{Form::checkbox($export_fields[$i]['id'], $export_fields[$i]['id'], true, array( 'id' => 'export_'.$export_fields[$i]['id'] ))}}
				    {{Form::label('export_'.$export_fields[$i]['id'], $export_fields[$i]['name'])}}
				    <br />
				@endfor
			</div>

			<div class="column small-3">
				@for ($i = ceil(count($export_fields) * 1/4); $i < ceil(count($export_fields) * 2/4); $i++)
				    {{Form::checkbox($export_fields[$i]['id'], $export_fields[$i]['id'], true, array( 'id' => 'export_'.$export_fields[$i]['id'] ))}}
				    {{Form::label('export_'.$export_fields[$i]['id'], $export_fields[$i]['name'])}}
				    <br />
				@endfor
			</div>

			<div class="column small-3">
				@for ($i = ceil(count($export_fields) * 2/4); $i < ceil(count($export_fields) * 3/4); $i++)
				    {{Form::checkbox($export_fields[$i]['id'], $export_fields[$i]['id'], true, array( 'id' => 'export_'.$export_fields[$i]['id'] ))}}
				    {{Form::label('export_'.$export_fields[$i]['id'], $export_fields[$i]['name'])}}
				    <br />
				@endfor
			</div>

			<div class="column small-3">
				@for ($i = ceil(count($export_fields) * 3/4); $i < count($export_fields); $i++)
				    {{Form::checkbox($export_fields[$i]['id'], $export_fields[$i]['id'], true, array( 'id' => 'export_'.$export_fields[$i]['id'] ))}}
				    {{Form::label('export_'.$export_fields[$i]['id'], $export_fields[$i]['name'])}}
				    <br />
				@endfor
			</div>
		</div>

		<div class='row export_download_note'>
            <div class='small-2 column dl_button'>
                {{ Form::submit('Download', array('class'=>'radius button'))}}
            </div>
            <div class='small-10 column'>
            	<b>Note:</b> Download available for contacts in the approved folder only. 
            	@if($agency_collection->is_trial_period == 1)
            	<br /> To be able to download phone, email and last name, please contact <a href="mailto:collegeservices@plexuss.com" target="_top">collegeservices@plexuss.com</a> 
            	@endif
            </div>
        </div>
		{{ Form::close() }}
	</div>

	<div id="urgent-msg-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<a class="close-reveal-modal" aria-label="Close">&#215;</a>
		<h4>How can we be of assistance?</h4>
		<p>Please describe the issue below and we will get right on it</p>
		<form>
			<textarea rows='5' cols='10'></textarea>
			<button class='submit-urgent-msg' type='submit'>Submit</button>
		</form>
	</div>

	<div id="unique-link-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<h5 class='header'>You can provide this link for your prospective students to sign up</h5>
		<div class='unique-link-container mt30'>
			<input id='agency-signup-url' name='agency-signup-url' type='text' value="{{$agency_signup_url}}" readonly>
			<div class='copy-clipboard-btn' title="Copy to Clipboard" data-clipboard-text="{{$agency_signup_url}}"></div>
		</div>
	</div>

	<!-- thank you for upgrading your account modal -->
	<div id="thankyou-for-upgrading-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<div class="row close-modal-x">
			<div class="column small-12 text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
			</div>
		</div>
		<div class="row">
			<div class="column small-12 text-center thankyou-msg-col">
				<div>Thank you!</div>
				<div>Sina or Molly will contact you very soon to get you set up with your new account.</div>
				<div>(We're working on giving you a place to manage upgrading your account in the future, so thank you for your patience.)</div>
			</div>
		</div>
		<div class="row">
			<div class="column medium-8 large-6 medium-centered text-center">
				<a href="" class="radius button secondary close-reveal-modal" aria-label="Close">Looking forward to it ;)</a>
			</div>
		</div>
	</div>
	@include('private.includes.ajax_loader')

@stop
