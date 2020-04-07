<?php 
	$key = $inquiry_list[0];
	// dd($key['applied_colleges']);
	// dd($inquiry_list);
?>

<!-- \\\\\\\\\\\\\\ student profile pane - start /////////////// -->
<div class='small-12 column student-profile-pane student-profile-paneS sales-student-pane'  data-recid="{{$key['rec_id']}}" data-uid="{{$key['student_user_id']}}" data-show-matched="1">
	
	<div class="row collapse hidden profile dropdownbox sales-dropdown">


		@include('agency.includes.profileActionbar')

		<div class="column small-12 hiddenDropDown"> <!-- hiddenDropDown -->

			<div class="row">
				<div class="column small-12">

					<!-- //////////// student profile pane - start \\\\\\\\\\\\\\ -->

					<!-- left side -->
					<div class="row">
						<div class="column large-7 small-12 left-side-studentProfile">
							
							<!-- new row 1 - name, school, grad year, start date -->	
							<div class="row">
								<div class="column small-2 small-centered large-2 large-uncentered small-text-center large-text-left">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/{{$key['profile_img_loc'] or 'default.png'}}" alt="">
								</div>

								<div class="column large-10">
									<div class="row collapse">
										<div class="column small-12 small-text-center large-text-left student-name p-info">
											{{$key['name'] or 'N/A'}}
										</div>
									</div>

									

									<div class="row collapse">
										<div class="column large-7 small-text-center large-text-left title3-bold  p-info end ">
											{{$key['current_school'] or 'N/A'}}
										</div>

										<div class="column large-7 small-text-center large-text-left p-info end">
											@if( isset($key['in_college']) && $key['in_college'] == 1 )
												Grad Year: <span class="student-profile-headers ml10">{{$key['college_grad_year'] or 'N/A'}}</span>
											@else
												Grad Year: <span class="student-profile-headers ml10">{{$key['hs_grad_year'] or 'N/A'}}</span>
											@endif
										</div>
										<div class="column large-7 small-text-center large-text-left p-info end">
											Start Date: <span class="student-profile-headers ml10">
											@if(!empty($key['start_term'])) {{$key['start_term']}} @else 'N/A' @endif
											</span>
											
										</div>

									</div>



									<!-- birthday -->
									<?php 
										$bdate = 'unknown';	

										if(isset($key['birth_date'])){
											$tok = explode('-', $key['birth_date']);
											$m = $tok[1];
											$d = $tok[2];
											$y = $tok[0];

											$bdate = $m.'/'.$d.'/'.$y;
										}

										//yyyy-mm-dd in Database

									?>
									<div class="row collapse">
										<div class="column small-12 small-text-center large-text-left p-info end">
											Birthday:  <span class="student-profile-headers ml10">{{$bdate}}</span>
										</div>
									</div>


								</div>
							</div>

							<!-- new row - objective, programs interested in, why interested in school -->
							<div class="row row-of-student-profile">

								<!-- Scores -->
								<div class="row">
									<div class="column small-12 medium-12 large-11 end">
										<div class="row scores">
																				
											<div class="column small-3 text-center">
												<b>SAT:</b> {{$key['sat_score'] or 'N/A'}}
											</div>
											<div class="column small-3 text-center">
												<b>ACT:</b> {{$key['act_composite'] or 'N/A'}}
											</div>
											<!-- Toefl/ IELTS -->
											<div class="column small-3 text-center">
												<b>TOEFL:</b> {{$key['toefl_total'] or 'N/A'}}
											</div>
											<div class="column small-3 text-center">
												<b>IELTS:</b> {{$key['ielts_total'] or 'N/A'}}
											</div>
										</div>
									</div>
								</div>

								<!-- financials, objective, uploads -->
								<div class="row">
									<div class="column small-12 medium-12 large-12 end">
										

										<!-- gpa -->
										<div class="row gpa-cont mt30">
												<span class="title3-bold">GPA:</span> {{$key['gpa'] or 'N/A'}}			
										</div>

										<!-- Financial first year affordibility -->
										<div class="row sales-financial-cont">
												<span class="title3-bold">Financials for first year:</span> {{$key['financial_firstyr_affordibility'] or 'N/A'}}			
										</div>

										<!-- objective -->
										<div class="row">
											<div class="student-profile-headers">
												Objective
											</div>
											<div class="content-sec">
												"{{$key['objective'] or 'N/A'}}"
											</div>
										</div>


										<!-- Uploads -->
										<div class="uploads-container-prof">

											<!-- uploads title -->
											<div class="row uploads-section-title">											
												@if(isset($key['upload_docs']) && empty($key['upload_docs']))
													<div class="uploads-title">
														<b>Uploads</b>
													</div>
													<div class="uploads-none content-sec">
														No files have been uploaded yet.
													</div>
												@else
													<div class="uploads-title">
														<b>Uploads</b>
													</div>
												@endif		
											</div>

											<!-- uploads themsleves -->
											<div class="row collapse uploads-display-container">
												<?php 
													$cnt = count($key['upload_docs']);
													$counter = 1;
											 	?>
												@foreach($key['upload_docs'] as $k)
												@if($k['doc_type'] == 'prescreen_interview')
												<?php
													$prescreen_interviews[] = $k; 
												?>
												@else
												<div class="uploadDoc-box" data-transcript-id="@if(isset($k['transcript_id'])){{$k['transcript_id']}}@endif">
													<div class="row">
														<div class="column small-12">
															<div class="uploadDocsSpriteLarge {{$k['doc_type']}}"></div>
															<div class="row uploadDetail">
																<div class="column small-12 transcript-label" title="@if(isset($k['transcript_label']) && !is_null($k['transcript_label']) && strlen($k['transcript_label']) >= 17){{ $k['transcript_label'] }}@endif">
																	<b>
																		@if(isset($k['transcript_label']) && !is_null($k['transcript_label']) && trim($k['transcript_label']) !== '') 
																			<?php 
																				$transcript_label = $k['transcript_label'];
																				if(strlen($transcript_label) >= 17)
																					$transcript_label = substr($transcript_label, 0, 13) . "...";
																			?>
																			{{ $transcript_label }}
																		@elseif($k['doc_type']== 'transcript') Transcript 
																		@elseif($k['doc_type']== 'toefl') TOEFL 
																		@elseif($k['doc_type']== 'ielts') IELTS 
																		@elseif($k['doc_type']== 'financial') Financial Docs 
																		@elseif($k['doc_type']== 'resume') Resume/Portfolio 
																		@elseif($k['doc_type']== 'application') Application 
																		@elseif($k['doc_type']== 'prescreen_interview') Plexuss Interview  
																		@elseif($k['doc_type']== 'other') Other
																		@elseif($k['doc_type']== 'essay') Essay
																		@elseif($k['doc_type']== 'passport') Passport
																		@endif
																	</b>
																</div>
																<div class="column small-3 @if($k['doc_type'] == 'application') end @endif">
																	@if($k['doc_type']== 'application')
																		<a href="{{$k['path']}}" target="_blank">View</a>
																	@elseif($k['doc_type'] == 'prescreen_interview')
																		{{-- <a type="{{$k['mime_type']}}" class="sm2_button" href="{{$k['path']}}" title="Plexuss Interview"></a> --}}
																		<div id="sm2-container"></div>
																		<div type="{{$k['mime_type']}}" class="ui360">
																			<a href="{{$k['path']}}">Plexuss Interview</a>
																		</div>
																	@else
																		<a href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$k['transcript_name']}}">View</a>
																	@endif
																</div>
																@if($k['doc_type'] != 'application') 
																	<div class="column small-1"> | </div>
																	<div class="column small-3 end">
																		<a href="{{$k['path']}}">
																			@if($k['mime_type'] == 'DNE') 
																			<span style="color:red;">Broken</span>
																			@else Download
																			@endif
																		</a>
																	</div>
																@endif
															</div>
														</div>
													</div>
												</div>
												@endif
												<?php $counter++; ?>
												@endforeach
												<!-- Prescreen Interviews -->
												<?php $interview_count =  1 ?>
												@if(isset($prescreen_interviews) && count($prescreen_interviews) !== 0)
												<div class="prescreen-interview-title mt20">
														<b>Interviews</b>
												</div>
												<div class="sm2-bar-ui playlist-open mt20 prescreen-interview-player">
												@else
												<div class="prescreen-interview-title mt20 hidden">
														<b>Interviews</b>
												</div>
												<div class="sm2-bar-ui playlist-open mt20 prescreen-interview-player hidden">
												@endif
												    <div class="bd sm2-main-controls">
												        <div class="sm2-inline-texture"></div>
												        <div class="sm2-inline-gradient"></div>
												        <div class="sm2-inline-element sm2-button-element">
												            <div class="sm2-button-bd">
												                <a href="#play" class="sm2-inline-button play-pause">Play / pause</a>
												            </div>
												        </div>
												        <div class="sm2-inline-element sm2-inline-status">
												            <div class="sm2-playlist">
												                <div class="sm2-playlist-target">
												                    <!-- playlist <ul> + <li> markup will be injected here -->
												                    <!-- if you want default / non-JS content, you can put that here. -->
												                    <noscript>
												                        <p>JavaScript is required.</p>
												                    </noscript>
												                </div>
												            </div>
												            <div class="sm2-progress">
												                <div class="sm2-row">
												                    <div class="sm2-inline-time">0:00</div>
												                    <div class="sm2-progress-bd">
												                        <div class="sm2-progress-track">
												                            <div class="sm2-progress-bar"></div>
												                            <div class="sm2-progress-ball">
												                                <div class="icon-overlay"></div>
												                            </div>
												                        </div>
												                    </div>
												                    <div class="sm2-inline-duration">0:00</div>
												                </div>
												            </div>
												        </div>
												        <div class="sm2-inline-element sm2-button-element sm2-menu">
												            <div class="sm2-button-bd">
												                <a href="#menu" class="sm2-inline-button menu">menu</a>
												            </div>
												        </div>
												    </div>
												    <div class="bd sm2-playlist-drawer sm2-element">
												        <div class="sm2-inline-texture">
												            <div class="sm2-box-shadow"></div>
												        </div>
												        <!-- playlist content is mirrored here -->
												        <div class="sm2-playlist-wrapper">
												            <ul class="sm2-playlist-bd">
												                <!-- item with "download" link -->
																@if(isset($prescreen_interviews) && count($prescreen_interviews) !== 0)
												                @foreach($prescreen_interviews as $k)
												                <li class="interview-links" data-transcript-id="@if(isset($k['transcript_id'])){{$k['transcript_id']}}@endif">
												                	<div class="sm2-row">
												                        <div class="sm2-col sm2-wide">
																			<a type="{{$k['mime_type']}}" href="{{$k['path']}}">
																				<b>
																					@if($k['transcript_label'] !== null && $k['transcript_label'] !== '')
																	                {{$k['transcript_label']}}
																	                @else
																	                Interview {{$interview_count}}
																	                @endif
																				</b>
																			</a>
												                        </div>
												                        <div class="sm2-col">
												                        	@if($k['mime_type'] == 'DNE')
												                        	<a href="{{$k['path']}}" class="broken-player-link" target="_blank" title="Download">Broken</a>
												                        	@else
																			<a href="{{$k['path']}}" target="_blank" title="Download" class="sm2-icon sm2-music sm2-exclude">Download this track</a>
																			@endif
												                        </div>
												                    </div>
												                </li>
												                <?php $interview_count++; ?>
												                @endforeach
												                @endif
												            </ul>
												        </div>
												        <div class="sm2-extra-controls">
												            <div class="bd">
												                <div class="sm2-inline-element sm2-button-element">
												                    <a href="#prev" title="Previous" class="sm2-inline-button previous">&lt; previous</a>
												                </div>
												                <div class="sm2-inline-element sm2-button-element">
												                    <a href="#next" title="Next" class="sm2-inline-button next">&gt; next</a>
												                </div>
												            </div>
												        </div>
												    </div>
												</div>
											</div>
											<!-- End Prescreen Interviews -->
										</div>
										<!-- end uploads -->


										<div class="row row-of-student-profile">
											
											<!-- programs interested in -->
											<div class="column small-12 large-6">
												<div class="row collapse">
													<div class="column small-12 student-profile-headers program-interested">
														Program(s) Interested In
													</div>
												</div>

												<div class="row collapse">
													<div class="column small-12">
														<?php 
															$majors = explode(',', $key['major']);
														?>
														@foreach($majors as $major)
														<div class="major-listing-prof">{{$major or 'N/A'}}</div>
														@endforeach
													</div>
													On 
													<b>
														@if($key['interested_school_type'] == 0)
															On Campus Only
														@elseif($key['interested_school_type'] == 1)
															Online Only
														@else
															Both Campus and Online 
														@endif
													</b>
												</div>
											</div>

											<!-- why interested -->
											<div class="column small-12 large-6">
												<div class="row collapse">
													<div class="column small-12 student-profile-headers why-interested">
														Why we recommend this student
													</div>
												</div>

												<div class="row collapse">
													<div class="column small-12">
														@if( isset($key['page']) && ($key['page'] == 'Recommendations' || $key['page'] == 'Pending'))
															<div class="major-listing-prof">
															{{$key['why_recommended'] or ''}}
															</div>
														@else
															@if( isset($key['why_interested']) )
																@foreach ($key['why_interested'] as $wi)
																	<div class="major-listing-prof">	
																	{{$wi or 'N/A'}}
																	</div>
																@endforeach
															@else
																N/A
															@endif
														@endif
													</div>
												</div>
											</div><!--end why interrested -->

										</div><!-- end container for program and interested -->
									</div>
								</div>	
							</div>
							<!-- new row - objective, programs interested in, why interested in school -->
						</div>


						<!-- right side for notes and contact -->
						@if( isset($key['page']) && $key['page'] != 'Recommendations' )
						<div class="column small-12 medium-12 large-5 personal-students-notes-column">
							
							<div class="row">
								<div class="column small-12 question-container">
									@if(isset($key['userEmail']))
									<div class="row c-info">
										<div class="column large-4 small-3">
											<div class="contact-verify @if(isset($key['email_confirmed']) && $key['email_confirmed']) verified @endif" id="emailc">
												
												<div class="contact-tooltip">
													<span class="veri-icon">&#10003;</span><span class="veri-tooltip-title">Verify</span><br/><br/>
													Click to verify this information is correct.
												</div>
											</div>
												<div class="c-info-label pl20">Email</div> 
										</div>
										<div class="column large-8 small-9 link-cont"><a class="right-link" href="mailto:{{$key['userEmail']}}">{{$key['userEmail']}}</a></div>
									</div>
									@endif

									@if(isset($key['skype_id']))
									<div class="row c-info">
										<div class="column large-4 small-3">
											<div class="contact-verify @if(isset($key['verified_skype']) && $key['verified_skype']) verified @endif" id="skypec">
												
												<div class="contact-tooltip">
													<span class="veri-icon">&#10003;</span><span class="veri-tooltip-title">Verify</span><br/><br/>
													Click to verify this information is correct.
												</div>
											</div>
												<div class="c-info-label pl20">Skype</div> 
										</div>
										<div class="column large-8 small-9 link-cont">
											<a class="right-link" href="skype:{{$key['skype_id']}}?chat">
        										<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon.png" alt=""/>
											{{$key['skype_id']}}
											</a>
										</div>
									</div>
									@endif

									@if(isset($key['userPhone']))
									<div class="row c-info">
										<div class="column large-4 small-3">
											<div class="contact-verify  @if(isset($key['verified_phone']) && $key['verified_phone'] == 1) verified @endif" id="phonecallc">
												
												<div class="contact-tooltip">
													<span class="veri-icon">&#10003;</span><span class="veri-tooltip-title">Verify</span><br/><br/>
													Click to verify this information is correct.
												</div>
											</div>
											<div class="c-info-label pl20">Phone</div> 
										</div>
										<div class="column large-8 small-9 link-cont">
											<a class="right-link" href="callto://{{str_replace(' ', '', trim($key['userPhone']))}}">{{$key['userPhone']}}</a>
										</div>
									</div>
									<!-- SMS -->
									<div class="row c-info">
										<div class="column large-4 small-3">
											<div class="contact-verify  @if(isset($key['userTxt_opt_in']) && $key['userTxt_opt_in'] == 1) verified @endif" id="phonec">
												
												<div class="contact-tooltip">
													<span class="veri-icon">&#10003;</span><span class="veri-tooltip-title">Verify</span><br/><br/>
													Click to verify this information is correct.
												</div>
											</div>
												<div class="c-info-label pl20">SMS</div> 
										</div>
										<div class="column large-8 small-9 link-cont">
											<a class="right-link" href="/admin/messages/{{$key['student_user_id']}}/inquiry-txt">{{$key['userPhone']}}</a>

										</div>
									</div>
									@endif
									@if(isset($key['userAddress']) || isset($key['userCity']) ||  isset($key['userState']) || isset($key['userZip']) || isset($key['country_name']))
									<div class="row c-info collapse">
										<div class="column large-4 small-3"><span class="c-info-title">Address</span></div>
										<div class="column large-8 small-9 address-box">
											<span class="address-ln1">
											{{$key['userAddress'] or ''}}  
											@if( isset($key['userCity']) || isset($key['userState']) || isset($key['userZip']) || isset($key['country_name']) )
											</span>
												<span class="add-show-more">Show</span>
												<div class="address-more-info"> 
													{{$key['userCity'] or ''}},&nbsp;{{$key['userState'] or ''}}&nbsp; {{$key['userZip'] or ''}}
													{{$key['country_name'] or ''}}
												</div>
											@endif
										</div>
									</div>
									@endif
								</div><!--end question container -->
							</div><!--end row-->
							

							<!--///// Notes /////-->
							<div class="row noteContainer-s">
								
								<div class="column small-2 student-profile-headers personal-notes">
									Notes
								</div>


								<div class="column small-10 textarea-col">
									
									<textarea class="notes-textarea" name="studentNotes" cols="5" rows="2" data-studentid="{{$key['student_user_id']}}">{{$key['note'] or ''}}</textarea>

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

							<!--///// Plexuss Notes /////-->
							@if(Session::has('handshake_power'))

                            @if (isset($is_plexuss) && $is_plexuss == 1)
							<!-- Plexuss Notes Start -->						
							<div class="row noteContainer-s">
							
								<div class="column small-2 student-profile-headers personal-notes plexuss-notes">
									Plexuss Notes 												
								</div>

								<div class="column small-10 textarea-col">
									
									<textarea class="plexuss-notes-textarea" name="studentNotes" cols="5" rows="2" data-studentid="{{$key['student_user_id']}}">{{$key['plexuss_note'] or ''}}</textarea>

									<div class="last-saved-note-time">
										@if( isset($key['plexuss_note']) && !empty($key['plexuss_note']) )
										Last Saved: <span class="note-time-updated">{{$key['plexuss_note_updated_at'] or '--:--'}}</span>
										@endif
									</div>

									<!-- ajax loader -->
				                    <div class="text-center plexuss-save-note-ajax-loader">
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
                            @endif
							<!-- Plexuss Notes ends here -->
							<!-- Post Students Start -->
							@if(isset($key['post_students']))
							<div class="row noteContainer-s">
								<div class="column small-2">
									Post Students
								</div> 
								<div class="column small-4 text-center actionbar-btn actionbar-btn-notactive post_students" onclick="Plex.inquiries.postStudent({{$key['student_user_id']}});"><span class="promote-text">Post</span></div>

									@if(isset($key['post_students']['is_eligible']))
										@if($key['post_students']['is_eligible']['status'] != "success")
											<div class="column small-12 text-center" style="color: red;">Not Eligible, Reason(s) :</div>
											<?php

											$field_name 	 = $key['post_students']['is_eligible']['errors']->field_name;
											$possible_fields = $key['post_students']['is_eligible']['errors']->possible_fields;

											?>
											@for($i=0; $i< count($field_name); $i++)
												<div class="column small-4">{{$field_name[$i] or ''}}</div>
												<div class="column small-8">
													<div class="row">
														@foreach($possible_fields[$i] as $k => $v)
														<div class="column small-12">{{$v}}</div>
														<div class="column small-12">---------------</div>
														@endforeach
													</div>
												</div>
											@endfor
										@else
											<div class="column small-12" style="color: green;">This inquiry is eligble to send</div>
										@endif
									@endif
									@if(isset($key['post_students']['responses']))	
										@foreach($key['post_students']['responses'] as $k)
											@if($k['success'] == 1)
												<div class="column small-12 text-center" style="color: green;">Inquiry was sent at {{$k['date']}} successfully!</div>
											@else
												<div class="column small-12 text-center" style="color: red;">Inquiry was rejected at {{$k['date']}} because of "{{$k['error_msg']}}"</div>
											@endif
											<div class="column small-12 text-center" style="color: green;">-----------</div>
										@endforeach
									@endif
							</div>
							@endif
							<!-- Post Students ends here -->
							@endif

							{{-- Sponsors section --}}
							@if( isset($key['sponsors']) && !empty($key['sponsors']) )
								<div class='sponsors-container'>
									<div class="column small-2 student-profile-headers">
										Sponsors
									</div>
									<div class='column'>
										<div class='row'>
											The applicant has indicated that the following sponsor(s) will provide financial assistance.
										</div>
										@foreach( $key['sponsors'] as $sponsor )
											<div class='mt20'>
											@if ($sponsor['option'] == 'parent')
												<div class='row'>
													<div class='column small-6 sponsor-header'>Parent First Name</div>
													<div class='column small-6'>{{$sponsor['fname']}}</div>
												</div>
												<div class='row'>
													<div class='column small-6 sponsor-header'>Parent Last Name</div>
													<div class='column small-6'>{{$sponsor['lname']}}</div>
												</div>
											@elseif ($sponsor['option'] == 'relative')
												<div class='row'>
													<div class='column small-6 sponsor-header'>Relative First Name</div>
													<div class='column small-6'>{{$sponsor['fname']}}</div>
												</div>
												<div class='row'>
													<div class='column small-6 sponsor-header'>Relative Last Name</div>
													<div class='column small-6'>{{$sponsor['lname']}}</div>
												</div>
												<div class='row'>
													<div class='column small-6 sponsor-header'>Relation</div>
													<div class='column small-6'>{{$sponsor['relation']}}</div>
												</div>
											@elseif ($sponsor['option'] == 'sponsor')
												<div class='row'>
													<div class='column small-6 sponsor-header'>Organization</div>
													<div class='column small-6'>{{$sponsor['org_name']}}</div>
												</div>
												<div class='row'>
													<div class='column small-6 sponsor-header'>Title</div>
													<div class='column small-6'>{{$sponsor['title']}}</div>
												</div>
												<div class='row'>
													<div class='column small-6 sponsor-header'>Contact Name</div>
													<div class='column small-6'>{{$sponsor['contact_name']}}</div>
												</div>
											@endif
												<div class='row'>
													<div class='column small-6 sponsor-header'>Phone Number</div>
													<div class='column small-6'>{{$sponsor['phone']}}</div>
												</div>
												<div class='row'>
													<div class='column small-6 sponsor-header'>Email Address</div>
													<div class='column small-6'>{{$sponsor['email']}}</div>
												</div>
											</div>
										@endforeach
									</div>
								</div>
							@endif
						</div><!-- end of right side container -->
						@endif <!-- end if not Recommendations -->

					</div>
					<!-- student also requested to be recruited by - start -->
					<div class="row row-of-student-profile">
								
						<div class="column small-12">
							
							<!-- row header -->
							<div class="row">
								<div class="column small-12 medium-6 small-text-center medium-text-left also-recruited-by-header student-profile-headers">
									This student has applied to the following schools 
									<span class="admin-side-menu-tooltip-icon" title="These colleges were selected when the student filled out their college application">?</span>
									<!--<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Page views are indicated in <span style='color: #26b24b'>green</span>">?</span>-->
									<script>
										(function() {
											// Enable jQuery tooltip
										    $('.admin-side-menu-tooltip-icon').tooltip({
										        content: function () {
										            return $(this).prop('title');
										        }
										    });
										})();
									</script>
								</div>
							</div>
							<!-- applied colleges -->
							<div class="row applied-carousel">
								<div class="column small-12">
									
									<div class="row" data-equalizer>

										<!-- your college -->
										<div class="column medium-3 large-2 text-center your-college-section comp">
											
											<div class="row your-college-title">
												<div class="column small-12 text-center">
													YOUR<br class="show-for-medium-only"> COLLEGE
												</div>
											</div>

											<div class="row your-college-content">
												<div class="column small-12">
													
													<div class="row">
														<div class="column small-12 text-center">
															<div class="college-logos-background" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$key['college_info']['logo'] or 'default-missing-college-logo.png'}}, (default)]"></div>
														</div>
													</div>

													<div class="row" data-equalizer-watch>
														<div class="column small-12 college-competitor-name">
															<a href="/college/{{$key['college_info']['slug']}}">{{$key['college_info']['name']}}</a>
														</div>
													</div>

													<div class="row">
														<div class="column small-12 page-views">
															@if($key['college_info']['page_views'] = 0)
															{{$key['college_info']['page_views']}} views
															@else
															--
															@endif
														</div>
													</div>

												</div>
											</div>
										</div>

										<!-- your competitors -->
										<div class="column medium-9 large-10 text-center your-competition-section comp">
											<div class="row">
												<div class="column small-12 text-center">
													&nbsp;<br class="show-for-medium-only"> &nbsp;
												</div>
											</div>

											<div class="row your-competitor-content">
												<div class="column small-12">

													<!-- carousel arrow left -->
													<div class="competitor-carousel-arrow leftarrow">
														<span class="competitor-arrow"></span>
													</div>
													
													<div class="student-profile-also-interested-in owl-carousel owl-theme"  data-isset="0">
														@if( isset($key['applied_colleges']) )
														@foreach( $key['applied_colleges'] as $applied )
														<div class="applied-college-item item text-center">

															<div class="row">
																<div class="column small-12">
																	<div class="college-logos-background" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$applied->logo_url or 'default-missing-college-logo.png'}}, (default)]"></div>
																</div>
															</div>

															<div class="row" data-equalizer-watch>
																<div class="column small-12 college-competitor-name competition-school-name-lg" data-college-name="{{$applied->school_name or ''}}">
																	<a target="_blank" href="/college/{{$applied->slug or ''}}">{{$applied->school_name or ''}}</a>
																</div>
															</div>
														</div>
														@endforeach
														@endif
													</div>

													<!-- carousel arrow right -->
													<div class='competitor-carousel-arrow rightarrow'>
														<span class='competitor-arrow'></span>
													</div>

												</div>
											</div>

										</div>
									</div>

								</div>
							</div><!-- end of competitors row -->
						</div>
					</div>
						</div>
					</div>
					<!-- student also requested to be recruited by - end -->

					<!-- //////////// new student profile pane - end \\\\\\\\\\\\\\ -->

				</div>
			</div>

		</div><!-- end hiddenDropDown -->
	</div>

</div>
<!-- \\\\\\\\\\\\\\ student profile pane - end /////////////// -->


@include('agency.profilePaneEdit')
@include('admin.contactPane')
