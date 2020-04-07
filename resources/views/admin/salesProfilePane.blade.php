<?php 
	$key = $inquiry_list[0];
	// dd($key['applied_colleges']);
?>

<!-- \\\\\\\\\\\\\\ student profile pane - start /////////////// -->
<div class='small-12 column student-profile-pane student-profile-paneS sales-student-pane'  data-recid="{{$key['rec_id']}}" data-uid="{{$key['student_user_id']}}" data-show-matched="{{$key['show_matched_colleges'] or 0}}">
	
	<div class="row collapse hidden dropdownbox sales-dropdown">


		@include('includes.profileActionbar')

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
												<b>SAT:</b>
												@if($key['sat_score'] != '')
													{{$key['sat_score'] or 'N/A'}}
												@else
													N/A
												@endif
											</div>
											<div class="column small-3 text-center">
												<b>ACT:</b>
												@if($key['act_composite'] != '')
													{{$key['act_composite'] or 'N/A'}}
												@else
													N/A
												@endif
											</div>
											<!-- Toefl/ IELTS -->
											<div class="column small-3 text-center">
												<b>TOEFL:</b>
												@if($key['toefl_total'] != '')
													{{$key['toefl_total'] or 'N/A'}}
												@else
													N/A
												@endif
											</div>
											<div class="column small-3 text-center">
												<b>IELTS:</b>
												@if($key['ielts_total'] != '')
													{{$key['ielts_total'] or 'N/A'}}
												@else
													N/A
												@endif
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
														@if( isset($key['page']) && ($key['page'] == 'Recommendations' || $key['page'] == 'Pending'))
														Why we recommend this student
														@else
														Why Interested in your school?
														@endif
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
                                @if (isset($is_plexuss) && $is_plexuss == true && isset($key['recruitment_type']))
                                    <div class="column small-12 recruitment-type-container">
                                        <span>
                                            <b>Generated:</b>
                                        </span>
                                        <span>
                                            {{$key['recruitment_type']}}
                                        </span>
                                    </div>
                                @endif
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
                                    Add Notes
                                </div>

								<div class="column small-10 textarea-col" style="margin-bottom: 1em;">
									
									<textarea class="notes-textarea" name="studentNotes" cols="5" rows="2" data-studentid="{{$key['student_user_id']}}" style="max-height: 500px;">{{isset($key['my_notes']['note']) ? $key['my_notes']['note'] : ''}}</textarea>

									<div class="last-saved-note-time">
										@if( isset($key['my_notes']) && !empty($key['my_notes']) )
										Last Saved: <span class="note-time-updated">{{isset($key['my_notes']['updated_at']) ? $key['my_notes']['updated_at'] : '--:--'}}</span>
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
                                @if(!empty($key['other_notes']))
                                    <div class="column small-2 student-profile-headers personal-notes">
                                        Notes
                                    </div>
                                @endif
                                <div class="column small-10 textarea-col organization-notes">
                                    @foreach($key['other_notes'] as $note) 
                                        <div class='organization-single-note'>
                                            <div>
                                                <span class='note-taker'>{{$note['fname'] . ' ' . $note['lname'][0]}}</span>
                                                <span class='time-taken'> - {{$note['updated_at']}}</span>
                                            </div>
                                            <div>
                                                <span>{{$note['note']}}</span>
                                            </div>
                                        </div>
                                    @endforeach
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
							@endif

						</div><!-- end of right side container -->
						@endif <!-- end if not Recommendations -->
					</div>


					<!-- student also requested to be recruited by - start -->
					<div class="row row-of-student-profile">
						<div class="column small-12">
							@if(Session::has('handshake_power') || ( isset($key['is_plexuss']) && $key['is_plexuss'] == 1 ) )
					        <div class="column small-12 medium-8">
					            <div class="column small-12" style="margin-bottom: 0.4em;">
					                <span><b>Colleges can get matched to</b></span>
					                <div class="add-college-matches-btn closed-color">
					                    <span class="college-add-btn">&#65291;&nbsp;ADD</span>
					                    <span class="college-close-btn">&ndash;&nbsp;&nbsp;CLOSE</span>
					                </div>
					            </div>
					            <input id="student_info" type="hidden" value="{{ $uid or '' }}">

					            <!-- MODAL for adding a Matched College -->
					            <div class="column small-12 add_college_modal">
					                <div class="add-modal-container">

					                    <!--///// Search and search results screen /////-->
					                    <div class="s1">

					                        <!-- input for search -->
					                        <div class="college-search-container ui-widget"> 
					                            <input class="collegeSearch ui-autocomplete-input" type="text" value="Search for a college..."/>
					                        </div>
					                        <div class="collegeResults">
					                            <!-- college search results go in here -->
					                        </div>
					                    </div>

					                    <!--///// Select a portal screen /////-->
					                    <div class="s2">
					                        
					                        <div class="step2 college-head"> 
					                        <!-- college selected goes up here-->
					                         </div>

					                        <!-- portal selection form -->
					                        <div class="big-gray-title">Select Portal</div>
				                            <form>  
				                                <fieldset>
				                                    <div class="portals-radio-container">
				                                        <!-- for each portal radio buttons go here-->
				                                    </div>
				                                </fieldset>

				                                <div class="back-to-colleges-btn">Back</div>
				                                <button class="select-portal-btn" type="submit">Select</button>   
				                            </form>
					                    </div>

					                    <div class="s3">
					                        
					                        <div class="college-head"> 
					                        <!-- college selected goes up here-->
					                         </div>

					                        <!-- portal selection form -->
					                        <div class="big-gray-title">Select Bucket</div>
				                            <form>  
				                                <fieldset>
				                                    <div class="buckets-radio-container">
				                                        <!-- for each portal radio buttons go here-->
				                                        <label>
				                                        	<input type="radio" name="bucket" value="Pending">
				                                        	Pending
				                                        </label>

  														<label>
  															<input type="radio" name="bucket" value="PreScreened">
  															Prescreened
  														</label>
  														
  														<label>
  															<input type="radio" name="bucket" value="HandShake">
  															Handshake
  														</label>
  														
  														<label>
  															<input type="radio" name="bucket" value="VerifiedApp">
  															Verified Application
														</label>
				                                    </div>
				                                </fieldset>

				                                <div class="back-to-portals-btn">Back</div>
				                                <button class="add-college-btn">Finish</button>   
				                            </form>
					                    </div>
					                </div>
					            </div>
					        </div>
				            @endif
							<!-- row header -->
							<div class="row">
								<div class="column small-12 medium-6 small-text-center medium-text-left also-recruited-by-header student-profile-headers">
									This student has also requested to be recruited by 
									<span class="admin-side-menu-tooltip-icon" title="Page views are indicated in <span style='color: #26b24b; display: inline-block'>Green</span>">?</span>
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
								<div class="column small-12 medium-6">
									@if( isset($key['is_plexuss']) && $key['is_plexuss'] == 1 )
										<div class="fit-competitor-title also-recruited-by-header">
											@if( Session::has('handshake_power') )
											<div class="fit-competitor-btns applied">APPLIED COLLEGES</div>
											@endif
											<div class="fit-competitor-btns fit">GOOD FIT FOR STUDENT</div>
											<div class="fit-competitor-btns competitor">YOUR COMPETITORS</div>
										</div>
									@else
										<div class="fit-competitor-title also-recruited-by-header">
											@if( Session::has('handshake_power') )
											<div class="fit-competitor-btns applied">APPLIED COLLEGES</div>
											@endif
											<div class="fit-competitor-btns competitor">YOUR COMPETITORS</div>
										</div>
									@endif
								</div>
							</div>

							<!-- good fit for student carousel -->
							@if( isset($key['is_plexuss']) && $key['is_plexuss'] == 1 )
							<div class="row fit-carousel">
								<div class="column small-12">
									
									<div class="row" data-equalizer>

										<!-- your college -->
										<div class="column medium-3 large-2 text-center your-college-section sales">
											
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
															<a target="_blank" href="/college/{{$key['college_info']['slug']}}">{{$key['college_info']['name']}}</a>
														</div>
													</div>

													<div class="row">
														<div class="column small-12 page-views sales">
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
										<div class="column medium-9 large-10 text-center your-competition-section sales">
											<div class="row">
												<div class="column small-12 text-center">
													&nbsp;<br class="show-for-medium-only"> &nbsp;
												</div>
											</div>

											<div class="row your-competitor-content">
												<div class="column small-12">

													<!-- carousel arrow left -->
													@if(isset($key['matched_colleges']) && $key['matched_colleges'] != '')
														<div class="competitor-carousel-arrow leftarrow">
															<span class="competitor-arrow"></span>
														</div>
													@endif

													<div class="student-profile-matched owl-carousel owl-theme" data-isset="0">

														@if(isset($key['matched_colleges']))
															@foreach( $key['matched_colleges'] as $matched )
															<div class="item text-center">

																<div class="row">
																	<div class="column small-12">
																		<div class="college-logos-background" data-interchange="[{{$matched['logo_url'] or ''}}, (default)]"></div>
																	</div>
																</div>

																<div class="row" data-equalizer-watch>
																	<div class="column small-12 college-competitor-name competition-school-name-lg">
																		<a href="/college/{{$matched['slug']}}">{{$matched['school_name']}}</a>
																	</div>
																</div>

																<div class="row">
																	<div class="column small-12 page-views">

																		@if( $matched['has_applied'] == 1 )
																			<div class="fit-status good" data-id="{{$matched['college_id']}}" data-school="{{$matched['school_name']}}">&check;</div>
																		@elseif( $matched['has_applied'] == 0 )
																			<div class="fit-status mild" data-id="{{$matched['college_id']}}" data-school="{{$matched['school_name']}}">&check;</div>
																		@else
																			<div class="fit-status none" data-id="{{$matched['college_id']}}" data-school="{{$matched['school_name']}}">+</div>
																		@endif

																		
																	</div>
																</div>
															</div>

															@endforeach
														@endif

														<div class="text-center matched-loading">
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
													</div>

													<!-- carousel arrow right -->
													@if(isset($key['matched_colleges']) && $key['matched_colleges'] != '')
														<div class="competitor-carousel-arrow rightarrow">
															<span class="competitor-arrow"></span>
														</div>
													@endif
												</div>
											</div>


											<div id="all-move-menus" data-student-id="{{$key['student_user_id']}}"></div>


										</div>
									</div>

								</div>
							</div><!-- end of good fit row -->
							@endif

							<!-- your college, your competitors -->
							<div class="row competitor-carousel">
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
													@if(isset($key['competitor_colleges']) && $key['competitor_colleges'] != '')
														<div class="competitor-carousel-arrow leftarrow">
															<span class="competitor-arrow"></span>
														</div>
													@endif
													<div class="student-profile-also-interested-in owl-carousel owl-theme"  data-isset="0">
														@foreach( $key['competitor_colleges'] as $competitor )
														<div class="item text-center">

															<div class="row">
																<div class="column small-12">
																	<div class="college-logos-background" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$competitor['logo'] or 'default-missing-college-logo.png'}}, (default)]"></div>
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
													@if(isset($key['competitor_colleges']) && $key['competitor_colleges'] != '')
														<div class="competitor-carousel-arrow rightarrow">
															<span class="competitor-arrow"></span>
														</div>
													@endif
												</div>
											</div>

										</div>
									</div>

								</div>
							</div><!-- end of competitors row -->

							@if( Session::has('handshake_power') )
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
														@if( isset($applied_colleges) )
														@foreach( $applied_colleges as $applied )
														<div class="applied-college-item item text-center">

															<div class="row">
																<div class="column small-12">
																	<div class="college-logos-background" data-status="'{{ isset($applied->submitted) ? $applied->submitted : '0' }}'"" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$applied->logo_url or 'default-missing-college-logo.png'}}, (default)]"></div>
																</div>
																@if( isset($key['is_plexuss']) && $key['is_plexuss'] == 1 )
																<div class="remove-applied-college-btn small-2" data-college_id={{ isset($applied->college_id) ? $applied->college_id : '' }}>
																	<span>&times;</span>
																</div>
																@endif
															</div>

															<div class="row" data-equalizer-watch>
																<div class="column small-12 college-competitor-name competition-school-name-lg" data-college-name="{{$applied->school_name or ''}}">
																	<a href="/college/{{$applied->slug or '#'}}">{{$applied->school_name or ''}}</a>
																</div>
															</div>

															<div class="row">
																@if ( isset($key['is_plexuss']) && $key['is_plexuss'] == 1 )
																<div class="column small-12">
																	<div data-college_id="{{$applied->college_id}}" data-status='{{ intval($applied->submitted) }}' data-hashedid="{{$key['hashed_id']}}" class="applied-college-submitted-btn {{ ( intval($applied->submitted) == 0 ) ? 'college_applied': '' }}" data-status='{{ $applied->submitted }}'>Applied</div>
																</div>
																@else
																<div class="column small-12">
																	<div class='applied-college-submitted-btn' data-status='{{ intval($applied->submitted) ? $applied->submitted : 0  }}'>Applied</div>
																</div>
																@endif
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
							@endif

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

@include('admin.salesProfilePaneEdit')
@include('admin.contactPane')
