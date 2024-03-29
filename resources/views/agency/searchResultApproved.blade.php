<?php
	$inquiries = $inquiry_list;
	// dd($data);
	// dd(get_defined_vars());
?>

<div class="hasResults hide" data-last-results="{{$has_searchResults or 'null'}}" data-more-results="{{json_encode($inquiries)}}></div>

@foreach ($inquiries as $key)

	<div class="row item inquirie_row @if( isset($key['is_notified']) && $key['is_notified'] == false ) unread @else read @endif @if($key['applied'] == 1) has-applied @else not-applied @endif">
		
		<!-- not paid layer - check if approved page to check if paid or not, else dont bother -->
		@if( ($currentPage == 'agency-approved') )
			@if(!isset($key['paid']) || $key['paid'] == 0)
				<div class="pay-to-view-layer text-center"><a href="/agency/settings/paymentInfo">Add credit to view</a></div>
			@endif
		@endif

		<!-- student name -->
		@if( (isset($key['paid']) && $key['paid'] == 1) || ($currentPage == 'agency-pending') )

		<div class="column small-1 student-chkbx-col text-right">
			{{Form::checkbox('name', 'student', false, array('class' => 'student-row-chkbx'))}}
		</div>
		
			@if( $currentPage == 'agency-pending' )
			<div class="column small-5 medium-3 large-2 messageName text-left clearfix" data-hashedid='{{$key['hashed_id'] or ""}}' data-is-paid="@if(isset($key['paid'])){{$key['paid']}}@else{{0}}@endif" OnClick='inquiriesToggleMenu(this);'>
			@else
			<div class="column small-5 medium-3 large-2 messageName text-left clearfix" data-hashedid='{{$key['hashed_id'] or ""}}' data-is-paid="@if(isset($key['paid'])){{$key['paid']}}@else{{0}}@endif">
			@endif
				<span class='arrow make-inline'></span>
				<span class="inquiry-name make-inline">{{$key['name'] or ''}} </span>
			<!--<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/hasNotes.jpg" alt="Plexuss User Has Notes">-->
			</div>

		<!-- applied column -->
		<div class="column text-center small-2 medium-2 hide-for-large-up applied-star">
			@if($key['applied'] == 1)
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/applied.jpg" alt="Applied Student Star">
			@else
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/notapplied.jpg" alt="Applied Student Star">
			@endif
		</div>

		<!-- student gpa -->
		<div class="text-center column medium-1 @if( $currentPage == 'agency-inquiries' || $currentPage == 'agency-approved' ) show-for-large-up @else small-2 @endif messageGPA">
			{{$key['gpa']  or 'N/A'}}
		</div>

		<!-- student Programs Interested in -->
		<div class="text-center column small-12 medium-2 large-1 show-for-medium-up programsIntrest">
			{{$key['major'] or 'N/A'}}
		</div>
		
		<!-- student Country -->
		<div class="text-center column small-2 medium-1 large-1 show-for-large-up country">
			@if (isset($key['country_code']) && $key['country_code'] != 'N/A')
				<span class="has-tip tip-top" data-tooltip aria-haspopup="true" title="{{$key['country_name']}}">
					<div class="flag flag-{{ strtolower($key['country_code']) }}"> </div>
				</span>
			@else
				N/A
			@endif
		</div>

		<!-- student date -->
		<div class="text-center column small-12 medium-1 show-for-large-up date">
			{{$key['date'] or ''}}
		</div>

		<!-- uploads doc -->
		<div class="text-center column small-12 medium-4 large-1 show-for-large-up uploadsDoc">
			
			<ul id="uploadDocs">
			  @if($key['transcript'] == true)
			  <li class="uploadDocsSpriteSmall transcript">&nbsp;</li>
			  @endif
			  @if($key['toefl'] == true)
			  <li class="uploadDocsSpriteSmall toefl">&nbsp;</li>
			  @endif
			  @if($key['ielts'] == true)
			  <li class="uploadDocsSpriteSmall ielts">&nbsp;</li>
			  @endif
			  @if($key['financial'] == true)
			  <li class="uploadDocsSpriteSmall financial">&nbsp;</li>
			  @endif
			  @if($key['resume'] == true)
			  <li class="uploadDocsSpriteSmall resume">&nbsp;</li>
			  @endif
			  @if($key['transcript'] == false && 
			  	  $key['toefl'] == false && 
			  	  $key['ielts'] == false && 
			  	  $key['financial'] == false &&
			  	  $key['resume'] == false)

			  	  <li>&nbsp;</li>
			  @endif


			</ul>
		</div>

		<!-- message student icon -->
		@if( $currentPage == 'agency-inquiries' || $currentPage == 'agency-approved' )
		<div class="messageIconArea text-center column small-2 medium-2 large-1 selected">
			<div class='showMessageIcon '>
				<a href="/agency/messages/{{$key['student_user_id']}}">
					<div class='message showMessageIcon'>&nbsp;</div>
				</a>
			</div>

			<div class='ShowNA '>
				N/A
			</div>
		</div>
		@endif

		<!-- applied column -->
		<div class="column large-1 text-center show-for-large-up applied-star">
			@if($key['applied'] == 1)
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/applied.jpg" alt="Applied Student Star">
			@else
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/notapplied.jpg" alt="Applied Student Star">
			@endif
		</div>

		@if($currentPage == 'agency-removed')
		<!-- restore from list button -->
		<div class="text-center column small-3 medium-2 large-1 text-center restore-student-col" data-studentid="{{$key['student_user_id']}}">&nbsp;
		</div>
		@else
		<!-- remove from list button -->
		<div class="text-center column small-2 medium-2 large-1 text-center remove-student-col" data-studentid="{{$key['student_user_id']}}" data-in-pending="@if($title == 'Pending') true @else false @endif">
			X
		</div>
		@endif
		<!-- \\\\\\\\\\\\\\ student profile pane - start /////////////// -->
		<div class='small-12 column student-profile-pane'>
			
			<div class="row collapse dropdownbox" style="display:none;">
				<div class="column small-12 hiddenDropDown"> <!-- hiddenDropDown -->

					<div class="row">
						<div class="column small-12">

							<!-- //////////// new student profile pane - start \\\\\\\\\\\\\\ -->

							<!-- new row 1 - name, school, grad year, view full profile btn -->
							<div class="row">
								<div class="column small-12">

									<div class="row">
										<div class="column small-2 small-centered large-1 large-uncentered small-text-center large-text-left">
											<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/{{$key['profile_img_loc'] or 'default.png'}}" alt="">
										</div>

										<div class="column large-11">
											<div class="row collapse">
												<div class="column small-12 small-text-center large-text-left student-name">
													{{$key['name'] or 'N/A'}}
												</div>
											</div>

											<div class="row collapse">
												<div class="column large-7 small-text-center large-text-left student-profile-headers">
													{{$key['current_school'] or 'N/A'}}
												</div>

												<div class="column large-5 small-text-center large-text-left">
													@if( isset($key['in_college']) && $key['in_college'] == 1 )
														College Grad Year: <span class="student-profile-headers">{{$key['college_grad_year'] or 'N/A'}}</span>
													@else
														High School Grad Year: <span class="student-profile-headers">{{$key['hs_grad_year'] or 'N/A'}}</span>
													@endif
												</div>
											</div>
										</div>
									</div>

								</div>
							</div>
							<!-- new row 1 - name, school, grad year, view full profile btn -->



							<!-- new row - objective, programs interested in, why interested in school -->
							<div class="row row-of-student-profile">

								<!-- left side student info -->
								<div class="column small-12 medium-6 large-8">
									@if($currentPage == 'agency-approved')
									<!-- email/phone -->
									<div class="row scores">
											
										<div class="column small-6">
											<b>Email:</b> {{$key['user_email'] or 'N/A'}}
										</div>
										<div class="column small-6">
											<b>Phone:</b> {{$key['user_phone'] or 'N/A'}}
										</div>
											
									</div>
									@endif

									<!-- Scores -->
									<div class="row scores">
											
										<div class="column small-6">
											<b>SAT:</b> {{$key['sat_score'] or 'N/A'}}
										</div>
										<div class="column small-6">
											<b>ACT:</b> {{$key['act_composite'] or 'N/A'}}
										</div>
											
									</div>

									<!-- Toefl/ IELTS -->
									<div class="row scores">
											
										<div class="column small-6">
											<b>TOEFL:</b> {{$key['toefl_total'] or 'N/A'}}
										</div>
										<div class="column small-6">
											<b>IELTS:</b> {{$key['ielts_total'] or 'N/A'}}
										</div>
											
									</div>

									<!-- Financial first year affordibility -->
									<div class="row scores">
											
										<div class="column small-12">
											<b>Financials for first year:</b> {{$key['financial_firstyr_affordibility'] or 'N/A'}}
										</div>
											
									</div>

									<!-- objective -->
									<div class="row">
										<div class="column small-12 student-profile-headers">
											Objective
										</div>
										<div class="column small-12">
											"{{$key['objective'] or 'N/A'}}"
										</div>
									</div>

									<div class="row row-of-student-profile">
										
										<!-- programs interested in -->
										<div class="column small-12 large-6">
											<div class="row collapse">
												<div class="column small-12 student-profile-headers program-interested">
													Program Interested In
												</div>
											</div>

											<div class="row collapse">
												<div class="column small-12">
													{{$key['major'] or 'N/A'}}
												</div>
											</div>
										</div>
										
									</div>
								</div>

								<!-- right side for notes -->
								@if( isset($title) && $title != 'Recommendations' )
								<div class="column small-12 medium-6 large-4 personal-students-notes-column">
									<div class="row">
										<div class="column small-12 student-profile-headers personal-notes">
										Uploads
										</div>
										
										@if(isset($key['upload_docs']) &&empty($key['upload_docs']))
										<div class="row">
											<div class="column small-12">
												No files have been uploaded yet.
											</div>
										</div>
										@endif
										

										@foreach($key['upload_docs'] as $k)
										<div class="column small-6" style="margin-top: 1em ! important;">
											<div class="uploadDocsSpriteLarge {{$k['doc_type']}}"></div>
											
											<div class="row uploadDetail">
												<div class="column small-12"><b>@if($k['doc_type']== 'transcript') Transcript @elseif($k['doc_type']== 'toefl') TOEFL @elseif($k['doc_type']== 'ielts') IELTS @elseif($k['doc_type']== 'financial') Financial Docs @elseif($k['doc_type']== 'resume') Resume/Portfolio @endif</b></div>
												<div class="column small-3"><a href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$k['transcript_name']}}">View</a></div>
												<div class="column small-1">|</div>
												<div class="column small-3 end"><a href="{{$k['path']}}"> Download</a></div>
											</div>
											
											
										</div>
										@endforeach
										
									</div>
									<div class="row noteContainer">
										<div class="column small-12 student-profile-headers personal-notes">
											Notes <small>(For your personal notes)</small>
										</div>
									</div>
									<div class="row">
										<div class="column small-12 textarea-col">
											<textarea class="notes-textarea" name="studentNotes" cols="30" rows="10" data-studentid="{{$key['student_user_id']}}">{{$key['note'] or ''}}</textarea>

											<div class="last-saved-note-time">
												@if( isset($key['note']) && !empty($key['note']) )
												Last Saved: <span class="note-time-updated">{{$key['note_updated_at'] or '--:--'}}</span>
												@endif
											</div>

											<!-- ajax loader -->
						                    <div class="text-center save-note-ajax-loader">
					                            <svg width="70" height="20">
					                                <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
					                                    <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
					                                    <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
					                                    <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
					                                    <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
					                                </rect>
					                                <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
					                                    <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
					                                    <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
					                                    <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
					                                    <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
					                                </rect>
					                                <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
					                                    <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
					                                    <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
					                                    <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
					                                    <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
					                                </rect>
					                            </svg>
						                    </div>
						                    <!-- end of ajax loader -->
										</div>
									</div>
								</div>
								@endif
							</div>
							<!-- new row - objective, programs interested in, why interested in school -->




							<!-- student also requested to be recruited by - start -->
							@if( isset($key['competitor_colleges']) && !empty($key['competitor_colleges']) )
							<div class="row row-of-student-profile">
								<div class="column small-12">
									
									<!-- row header -->
									<div class="row">
										<div class="column small-12 small-text-center medium-text-left also-recruited-by-header student-profile-headers">
											This student has also requested to be recruited by <span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Page views are indicated in <span style='color: #26b24b'>green</span>">?</span>
										</div>
									</div>

									<!-- your college, your competitors -->
									<div class="row">
										<div class="column small-12">
											
											<div class="row" data-equalizer>

												<!-- your competitors -->
												<div class="column small-12 text-center your-competition-section">

													<div class="row your-competitor-content">
														<div class="column small-12">

															<!-- carousel arrow left -->
															<div class="competitor-carousel-arrow leftarrow">
																<span class="competitor-arrow"></span>
															</div>
															
															<div class="student-profile-also-interested-in owl-carousel owl-theme">
																@foreach( $key['competitor_colleges'] as $competitor )
																<div class="item text-center">

																	<div class="row">
																		<div class="column small-12">
																			<div class="college-logos-background" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$competitor['logo']}}, (default)]"></div>
																		</div>
																	</div>

																	<div class="row" data-equalizer-watch>
																		<div class="column small-12 college-competitor-name competition-school-name-lg">
																			<a href="/college/{{$competitor['slug']}}">{{$competitor['name']}}</a>
																		</div>
																	</div>

																	<div class="row">
																		<div class="column small-12 page-views">
																			@if( $competitor['page_views'] != 0 )
																			{{$competitor['page_views']}} views
																			@else
																			--
																			@endif
																		</div>
																	</div>
																</div>
																@endforeach
															</div>

															<!-- carousel arrow right -->
															<div class="competitor-carousel-arrow rightarrow">
																<span class="competitor-arrow"></span>
															</div>
															
														</div>
													</div>

												</div>
											</div>

										</div>
									</div>

								</div>
							</div>
							@endif
							<!-- student also requested to be recruited by - end -->

							<!-- //////////// new student profile pane - end \\\\\\\\\\\\\\ -->

						</div>
					</div>

				</div><!-- end hiddenDropDown -->
			</div>

		</div>
		@else
		<div class="column small-12">&nbsp;</div>
		@endif<!-- end of is paid or not -->
		<!-- \\\\\\\\\\\\\\ student profile pane - end /////////////// -->
	</div><!-- end of inquirie row -->
@endforeach