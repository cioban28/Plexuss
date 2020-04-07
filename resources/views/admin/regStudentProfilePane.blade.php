<?php 
	$key = $inquiry_list[0];
?>

	<!-- \\\\\\\\\\\\\\ student profile pane - start /////////////// -->

		<div class='small-12 column student-profile-pane'>
			<div class="row collapse hidden dropdownbox">
				<div class="column small-12 hiddenDropDown"> <!-- hiddenDropDown -->

					<div class="row">
						<div class="column small-12">


							<!-- //////////// new student profile pane - start \\\\\\\\\\\\\\ -->


							<!-- new row 1 - name, school, grad year, view full profile btn -->
							<div class="row">
								<div class="column large-8 small-12 left-side-studentProfile">

									<div class="row">
										<div class="column small-2 small-centered large-2 large-uncentered small-text-center large-text-left">

											<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/{{$key['profile_img_loc'] or 'default.png'}}" alt="">

										</div>


										<div class="column large-10">
											<div class="row collapse">
												<div class="column small-12 small-text-center large-text-left student-name">
													{{$key['name'] or 'N/A'}}
												</div>
											</div>

											<div class="row collapse">
												<div class="column large-7 small-text-center large-text-left student-profile-headers end">
													{{$key['current_school'] or 'N/A'}}
												</div>


												<div class="column large-7 small-text-center large-text-left end">
													@if( isset($key['in_college']) && $key['in_college'] == 1 )

														College Grad Year: <span class="student-profile-headers">{{$key['college_grad_year'] or 'N/A'}}</span>

													@else

														High School Grad Year: <span class="student-profile-headers">{{$key['hs_grad_year'] or 'N/A'}}</span>

													@endif

												</div>
											</div>
										</div>
									</div>


									<!-- new row - objective, programs interested in, why interested in school -->
									<div class="row row-of-student-profile">
										<!-- left side student info -->

										<!-- Scores -->
										<div class="row">
											<div class="column small-12 medium-6 large-8 end">
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

										<div class="row">
											<div class="column small-12 medium-6 large-12 end">

												<!-- Financial first year affordibility -->
												<div class="row financial row sales-financial-cont">
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
																Program(s) Interested In
															</div>
														</div>
														<div class="row collapse">
															<div class="column small-12">
																{{$key['major'] or 'N/A'}}
															</div>
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

																	{{$key['why_recommended'] or ''}}

																@else

																	@if( isset($key['why_interested']) )
																		@foreach ($key['why_interested'] as $wi)
																			{{$wi or 'N/A'}}
																		@endforeach
																	@else
																		N/A
																	@endif
																@endif
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>	
									</div>
									<!-- new row - objective, programs interested in, why interested in school -->
								</div>



								<!-- right side for notes -->
								@if( isset($key['page']) && $key['page'] != 'Recommendations' )
								<div class="column small-12 medium-6 large-4 personal-students-notes-column">
									<div class="row">
										@if(($key['page'] == 'approved' && $bachelor_plan == 1) || (Session::has('handshake_power') && $key['page'] == 'Pending'))

										@if(Session::has('handshake_power')  && $key['page'] == 'Pending')

										<div class="column small-12 add-to-hsh" data-studentid="{{$key['student_user_id']}}">
											Add to H
										</div>
										@endif


										<div class="column small-12">
											@if(isset($key['userEmail']))
											<div class="row c-info">
												<div class="column small-3"><b>Email</b></div>
												<div class="column small-9"><a href="mailto:{{$key['userEmail']}}">{{$key['userEmail']}}</a></div>
											</div>
											@endif

											@if(isset($key['skype_id']))
											<div class="row c-info">
												<div class="column small-3"><b>Skype</b></div>
												<div class="column small-9">
													<a href="skype:{{$key['skype_id']}}?chat">
                										<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon.png" alt=""/>
													{{$key['skype_id']}}
													</a>
												</div>
											</div>
											@endif

											@if(isset($key['userPhone']))
											<div class="row c-info">
												<div class="column small-3"><b>Phone</b></div>
												<div class="column small-9">
													<a href="callto://{{str_replace(' ', '', trim($key['userPhone']))}}">{{$key['userPhone']}}</a>

													@if( isset($key['userTxt_opt_in']) && $key['userTxt_opt_in'] == 1 )
														<a href="/admin/messages/{{$key['student_user_id']}}/inquiry-txt">
															@if( isset($key['haveTexted']) && $key['haveTexted'] )
															<div class="send-text-to-user-btn haveTexted">
																<div class="send-t-icon texted"></div>
																<div class="send-t-text">
																	<span data-tooltip aria-haspopup="true" class="has-tip txt-tip" style="border: none;" title="Have texted before.">
																		Send text
																	</span>
																</div>
															</div>
															@else

															<div class="send-text-to-user-btn">
																<div class="send-t-icon texted"></div>
																<div class="send-t-text">
																	<span data-tooltip aria-haspopup="true" class="has-tip txt-tip" style="border: none;" title="Have NOT texted before.">
																		Send text
																	</span>
																</div>
															</div>
															@endif
														</a>

													@endif
												</div>
											</div>
											@endif

											@if(isset($key['userAddress']) || isset($key['userCity']) ||  isset($key['userState']) || isset($key['userZip']) || isset($key['country_name']))
											<div class="row c-info">
												<div class="column small-3"><b>Address</b></div>
												<div class="column small-9">

													{{$key['userAddress'] or ''}}<br/>
													{{$key['userCity'] or ''}},&nbsp;{{$key['userState'] or ''}}&nbsp; {{$key['userZip'] or ''}}
													{{$key['country_name'] or ''}}
												</div>
											</div>
											@endif
										</div>
										@endif

										<div class="column small-12">
											<div class="row">												
												@if(isset($key['upload_docs']) && empty($key['upload_docs']))
													<div class="column small-3">
														<b>Uploads</b>
													</div>
													<div class="column small-9">
														No files have been uploaded yet.
													</div>
												@else

													<div class="column small-12">
														<b>Uploads</b>
													</div>
												@endif
											</div>
										</div>


										<!-- Uploads -->
										<?php 
											$cnt = count($key['upload_docs']);
											$counter = 1;
									 	?>
										@foreach($key['upload_docs'] as $k)
										<div class="column small-6 @if($counter == $cnt) end @endif" style="margin-top: 1em ! important;">
											<div class="row">
												<div class="column small-12">
													<div class="uploadDocsSpriteLarge {{$k['doc_type']}}"></div>
													
													<div class="row uploadDetail">
														<div class="column small-12"><b>
															@if($k['doc_type']== 'transcript') Transcript 
															@elseif($k['doc_type']== 'toefl') TOEFL 
															@elseif($k['doc_type']== 'ielts') IELTS 
															@elseif($k['doc_type']== 'financial') Financial Docs 
															@elseif($k['doc_type']== 'resume') Resume/Portfolio 
															@elseif($k['doc_type']== 'application') Application 
															@elseif($k['doc_type']== 'prescreen_interview') Plexuss Interview  
															@elseif($k['doc_type']== 'other') Other
															@elseif($k['doc_type']== 'essay') Essay 
															@elseif($k['doc_type']== 'passport') Passport
															@endif</b>
														</div>
														<div class="column small-3 @if($k['doc_type'] == 'application') end @endif">
															@if($k['doc_type']== 'application') 
																<a href="{{$k['path']}}" target="_blank">View</a>
															@else
																<a href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$k['transcript_name']}}">View</a>
															@endif
														</div>
														@if($k['doc_type'] != 'application') 
															<div class="column small-1"> | </div>
															<div class="column small-3 end">
																<a href="{{$k['path']}}"> Download</a>
															</div>
														@endif
													</div>
												</div>
											</div>
										</div>
										<?php $counter++; ?>
										@endforeach
									</div>

									@if(Session::has('handshake_power'))
									<div class="row">
										<div class="column small-12">
					                        <div><b><a href="{{$key['loginas'] or ''}}" target="_blank">Login As</a></b></div>
					                    </div>
					                    <div class="column small-12">
					                    	<div class="row">
					                    		<div class="column small-3"><b>User id</b></div>
					                    		<div class="column small-9">{{$key['student_user_id'] or ''}}</div>
					                    	</div>
                					    </div>
					                    @if(isset($key['fb_id']) && $key['fb_id'] != '')
					                    <div class="column small-12">
					                        <div><a target="_blank" href="https://www.facebook.com/{{$key['fb_id'] or ''}}">FB</a></div>
					                    </div>
					                    @endif
									</div>
									@endif



									<div class="row noteContainer">
										<div class="column small-12 student-profile-headers personal-notes">
											<div class="row">
												<div class="column small-12">
													Notes <small>(For your personal notes)</small>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="column small-12 textarea-col">
											<div class="row">
												<div class="column small-12">
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

										</div>

									</div>

									

									@if(Session::has('handshake_power'))

									<!-- Plexuss Notes Start -->

									<div class="row noteContainer">

										<div class="column small-12 student-profile-headers personal-notes">

											<div class="row">

												<div class="column small-12" style="color: #8000ff;">

													Plexuss Notes 												

												</div>

											</div>

										</div>

									</div>

									<div class="row">

										<div class="column small-12 textarea-col">

											<div class="row">

												<div class="column small-12">

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

										</div>

									</div>

									<!-- Plexuss Notes ends here -->

									@endif



								</div>

								@endif

							</div>

							<!-- new row 1 - name, school, grad year, view full profile btn -->



							<!-- student also requested to be recruited by - start -->

							@if( isset($key['competitor_colleges']) && !empty($key['competitor_colleges']) )

							<div class="row row-of-student-profile">

								<div class="column small-12">

									

									<!-- row header -->

									<div class="row">

										<div class="column small-12 small-text-center medium-text-left also-recruited-by-header student-profile-headers">

											This student has also requested to be recruited by <span class="admin-side-menu-tooltip-icon" title="Page views are indicated in <span style='color: #26b24b; display: inline-block;'>green</span>">?</span>

										</div>

									</div>



									<!-- your college, your competitors -->

									<div class="row">

										<div class="column small-12">

											

											<div class="row" data-equalizer>



												<!-- your college -->

												<div class="column medium-3 large-2 text-center your-college-section">

													

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

												<div class="column medium-9 large-10 text-center your-competition-section">



													<div class="row">

														<div class="column small-12 text-center">

															YOUR<br class="show-for-medium-only"> COMPETITORS

														</div>

													</div>



													<div class="row your-competitor-content">

														<div class="column small-12">



															<!-- carousel arrow left -->

															<div class="competitor-carousel-arrow leftarrow">

																<span class="competitor-arrow"></span>

															</div>

															

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
