	<?php 
		// dd($key);
		// echo "<pre>";
		// print_r($key);
		// echo "</pre>";
		// exit();
	?>

	<!-- \\\\\\\\\\\\\\ student profile pane for Edit - start /////////////// -->
		<div class='small-12 column student-profile-pane student-profile-paneS sales-student-edit-pane'  data-sid="{{$key['student_user_id']}}">
			<form id="salesProfileEdit" class="salesProfileEdit" data-abide="ajax">
			<div class="row collapse dropdownbox sales-dropdown">
				
				@include('includes.profileEditActionbar')

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

										<div class="column large-10  left-side-edit">
											<div class="row">
												<div class="column small-12 small-text-center large-text-left student-name p-info">
													{{$key['name'] or 'N/A'}}
												</div>
											</div>

											<!-- /////////// edit grad, start term, fin ////// -->
											<div class="row">
												<!-- current school -->
												<div class="column large-7 small-text-center large-text-left title3-bold  p-info end ">
													{{$key['current_school'] or 'N/A'}}
												</div>


												<!--///// grad year ///////////-->
												<?php 
													
													$today = date("Y");

													if(isset($key['hs_grad_year']) && $key['hs_grad_year'] != 'N/A')
														$gradyr = $key['hs_grad_year'];
													elseif(isset($key['college_grad_year']) && $key['college_grad_year'] != 'N/A')
														$gradyr = $key['college_grad_year'];
													else
														$gradyr = $today;
											       
											        $selected = $today;
											        $startYear = $today + 10;
											        $endYear = $today - 53;
										        ?>
												<div class="column large-9 small-text-center large-text-left p-info end">
														<div class="row collapse mt20">
															<div class="column small-4 f-bold">Grad Year:</div> 
															<div class="column small-8">
															
																<select id='infoGradYear' name="infoGradYear">
															        <option value="">Select a year</option>
															        @for ($i = $startYear; $i > $endYear; $i--)
															        @if($gradyr == $i)
															             <option value="{{$i}}" selected="selected">{{$i}}</option>
															        @else
																        <option value="{{$i}}">{{$i}}</option>
															        @endif
															        @endfor
														        </select>
															
															</div>
														</div>
												</div>
												


												<!--///////// start term ////////-->
												<?php 
        
											        $TERMS = 
											        array('Spring','Spring','Spring','Summer','Summer','Summer','Fall','Fall','Fall','Winter', 'Winter', 'Winter');
											        
											        $TERM_CYCLE = array('Spring','Summer','Fall','Winter');

											        $today = date("Y");
											        $term = $TERMS[abs(date("n") - 2 )];
											        $onCycle = array_search($term, $TERM_CYCLE);

											        $startYear = $today + 1; //start loop with next year because may be part way through current
											        $endYear = $today + 10;
										     
												?>
												<div class="column large-9 small-text-center large-text-left p-info end">
													<div class="row collapse">
														<div class="column small-4 f-bold">Start Date:</div> 
														<div class="column small-8">
										
													        <select name="profileStartTerm" >
													            <option value="">Select a term</option>
													            <!-- for remainder of this year -->
													            @for($k = $onCycle; $k < count($TERM_CYCLE); $k++)
													            	@if($key['start_term'] == $TERM_CYCLE[$k].' '.$today)
													                    <option value="{{$TERM_CYCLE[$k].' '.$today}}" selected="selected">
													                    	{{$TERM_CYCLE[$k]. ' '.$today}}
													                    </option>
													                 @else
													                 	<option value="{{$TERM_CYCLE[$k].' '.$today}}">
													                    	{{$TERM_CYCLE[$k]. ' '.$today}}
													                    </option>
													                 @endif
													            @endfor
													            
													            <!--terms for years following this year -->
													            @for($i = $startYear; $i < $endYear; $i++)
													                @foreach($TERM_CYCLE as $termc)
												                		@if(trim($key['start_term']) == $termc.' '.$i)
												                        	<option value="{{$termc.' '.$i}}" selected="selected">{{$termc.' '.$i}}</option>
												                        @else
												                        	<option value="{{$termc.' '.$i}}">{{$termc.' '.$i}}</option>
												                        @endif
													                @endforeach
													            @endfor
													        </select>
														
														</div>
													</div>
												</div>
												<!--/////////end start term /////-->


												<!--///////// Birth Date ///////////////////// -->
												<?php 
													$tok = explode('-', $key['birth_date']);
													$m = isset($tok[1]) ? $tok[1] : 01;
													$d = isset($tok[2]) ? $tok[2] : 01;
													$y = isset($tok[0]) ? $tok[0] : 1999;

													//yyyy-mm-dd in Database
												?>
												<div class="column large-9 small-text-center large-text-left p-info end mb15">
													<div class="row birthday-wrapper collapse"  data-d="{{$d}}" data-m="{{$m}}" data-y="{{$y}}">
															<div class="column small-4 f-bold">Birthday:</div> 
															
															<div class="column small-8">
																<div class=" birthday-cont">
																	<input class="bday" placeholder="mm" value="{{$m}}" name="birthMonth" maxlength='2' data-abide-validator='monthChecker'/> 
																	<span>/</span>
																	<input class="bday" placeholder="dd" value="{{$d}}" name="birthDay" maxlength='2' data-abide-validator='dayChecker'/> 
																	<span>/</span>
																	<input  class="bday" placeholder="yyyy" value="{{$y}}" name="birthYear" maxlength= '4' data-abide-validator='yearChecker'/>

																</div>


				<small class="error datedMonthError">*Please enter a valid Month.</small>
				<small class="error datedDayError">*Please enter a valid Day.</small>
				<small class="error datedYearError">*Please enter a valid Year.</small>
				<small class="error datedUnderAge">Users must be 13 years or older to sign up.</small>


			

															</div>
													</div>													
												</div>





												<!-- Financial first year affordibility -->
												<?php 
													$RANGES = array('' => '', '$0' => '$0', '$0 - 5,000' => '$0 - $5,000', '$5,000 - 10,000' => '$5,000 - $10,000',
																	'$10,000 - 20,000' => '$10,000 - $20,000', '$20,000 - 30,000' => '$20,000 - $30,000',
																	'$30,000 - 50,000' => '$30,000 - $50,000', '$50,000' => '$50,000+');
												?>
												<div class="column large-9 small-text-center large-text-left p-info end">
													<div class="row sales-financial-cont collapse">
															<div class="column small-5 f-bold">Financials for first year:</div> 
															<div class="column small-7">
											
																{{ Form::select(
																	'profileFin', 
																	$RANGES, 
																	array(
																		'style' => 'display: inline;',
																		'data-selected' => isset($key['financial_firstyr_affordibility']) ? $key['financial_firstyr_affordibility'] : 'N/A'			
																		)
																	) 
																}}
																
															</div>		
													</div>
												</div>




											</div>
										</div>



									</div>


									<!-- new row - objective, programs interested in, why interested in school -->
									<div class="below-spacing-cont collapse">
											
											<!-- edit test scores -->
											<div class=" row edit-scores-container collapse mt50">
												<div class="column small-3 f-bold scores-title">
													Test Scores :
												</div>

												<div class="column small-9 edit-scores-inner-r">
													<div class="scores-col">
														SAT<br/>
														<input name="satScore" class="userScore" pattern="sat_total" placeholder="SAT" value="@if($key['sat_score'] != 'N/A') {{$key['sat_score']}} @endif"/>
														<small class="error">invalid SAT score</small>	
													</div>
													<div class="scores-col">
														ACT<br/>
														<input name="actScore" class="userScore" pattern="act" placeholder="ACT" value="@if($key['act_composite'] != 'N/A') {{$key['act_composite']}} @endif"/>
														<small class="error">invalid ACT score</small>	
													</div>
													<div class="scores-col">
														TOEFL<br/>
														<input name="toeflScore" class="userScore" pattern="toefl" placeholder="TOEFL" value="@if($key['toefl_total'] != 'N/A') {{$key['toefl_total']}} @endif"/>
														<small class="error">invalid TOEFL score</small>	
													</div>
													<div class="scores-col">
														IELTS<br/>
														<input name="ieltsScore" class="userScore" pattern="ielts" placeholder="IELTS" value="@if($key['ielts_total'] != 'N/A') {{$key['ielts_total']}} @endif"/>
														<small class="error">invalid IELTS score</small>	
													</div>
												</div>
											</div>

											<!-- edit gpa -->
											<div class=" row edit-scores-container collapse mt15">
												<div class="column small-3 f-bold eidtGPA-title">
													GPA :
												</div>

												<div class="column small-9">
													<div class="scores-col">
														<input class="editGPA" pattern="gpa" name="editGPA" placeholder="GPA" value="{{$key['gpa']}}" />
														<small class="error">GPA must be numeric, in the range 0.0 - 4.0</small>
													</div>
												</div>
											</div>


											<div class="objective-section">
												
												<div class="f-bold mt50">
													Objective
												</div>
												
												<!-- row for degree -->
							            		<?php 
							            			$DEGREES = array("1" => "Certificate Programs", "2" => "Associate's Degree", "3" => "Bachelor's Degree", "4" => "Master's Degree", "5" => "PHD/Doctorate", "6" => "Undecided", "7" => "Diploma", "9" => "Juris Doctor" );
							            		?>
							            		<div class="row collapse mt20">
									            	<div class="column large-4 medium-4 small-12 objective-label">I would like to get a/an</div>
									            	<div class="column large-8 medium-8 small-12">
														<div style="" class="validSelecto degree-type-select">															{{ Form::select(
																'objDegree', 
																$DEGREES, 
																array(
																	'class'=>'objective-input ', 
																	'id' => 'DegreesDropDown', 
																	'style' => 'display: inline;',
																	'data-selected' => isset($key['degree_id']) ? $key['degree_id'] : null,
																	'placeholder' => 'Select a degree type...'
																	)
																) 
															}}
														</div>
													</div>
												</div>

												<!-- row for major -->
												<div class="row collapse">
													<div class="column large-4 medium-4 small-12">
														<div class="objective-label">I would like to study</div>
													</div>


													<div class="column large-8 medium-8 small-12 majors-container">

														<!-- crumb list goes here -->
														<div class="majors_crumb_list"></div>
														
														<small id="max-note">You've reached the maximum number of majors.</small>
														<div class="validSelecto objMajorContainer">
															{{
																Form::text(
																	'objMajor', '',
																	array(
																		'id' => 'objMajor',
																		'class' => 'objective-input',
																		'placeholder' => 'Type in a major...',
																		'autocomplete' => 'off'
																	)
																)
															}}

															<!-- dropdown for majors , gets populated via ajax-->
															<div class="majors-list-select">
																<span class="most-pop-right">Most Popular</span>
																<div class="popular"></div>

																<div class="line"></div>
																<div class="other"></div>
															</div>
															
															<small id="duplicate_crumb_error">You have aleady chosen this major.</small>
															<small class="majors-error">At least one major must be chosen</small>
														</div>
													</div>
												</div>

												<!-- row for work as -->
												<div class="row collapse">
													<div class="column large-4 medium-4 small-12">
										                <div class="objective-label">My dream is to one day work as a(n) </div>
										            </div>

										            <div class="column large-8 medium-8 small-12">
														<div class="validSelecto" id="objProfessionContainer">
															{{
																Form::text(
																	'objProfession',
																	isset($key['profession_name']) ? $key['profession_name'] : null,
																	array(
																		'id' => 'objProfession',
																		'class' => 'objAutocomplete objective-input objProfession',
																		'placeholder' => isset($key['profession_name']) ? $key['profession_name'] : 'Enter a profession...'
																	)
																)
															}}
														</div>
													</div>
												</div>
											</div>

											<!-- Edit Uploads -->
											<div class="row collapse uploads-edit-container" data-uhid="{{$key['hashed_id']}}">
												<div class="row collapse mt20 edit-uploads-header-container">
													<div class="column small-2 edit-uploads-header f-bold">
														Uploads
													</div>
													<div class="column small-2 edit-new-upload-btn actionbar-uploadModal">+ Add New</div>
												</div>
												<div class="mt10"></div>
												<?php $edit_interview_count = 1 ?>
												@foreach($key['upload_docs'] as $k)
												

											

												@if ($k['doc_type'] != 'application')
											


												<div class="row collapse edit-upload-item-container mt14">
											
													<div class="remove-upload-btn" data-transcript-id="{{$k['transcript_id']}}" data-doc-type="{{$k['doc_type']}}">
														Remove
													</div>


													@if ( $k['doc_type'] == 'prescreen_interview' )
													<div class="mt2  upload-menu-toggle" data-ctype="prescreen_interview">
														<div class=" uploadDocsSpriteLarger prescreen_interview upload-icon-hover">
																<div class="u-arrow"></div>
																@include('includes.uploadTypeMenu')
																<div class="upload-type-tooltip">select to change the upload type</div>
														</div>
													</div>
													@else
													<div class="mt2 upload-menu-toggle" data-ctype="{{$k['doc_type']}}">
														<div class="uploadDocsSpriteLarger {{$k['doc_type']}} upload-icon-hover">
																<div class="u-arrow"></div>
																@include('includes.uploadTypeMenu')
																<div class="upload-type-tooltip">select to change the upload type</div>
														</div>
													</div>
											
													@endif
													

													<div class=" validSelecto">
														<?php  	
															$placeholder = "";
															if ( $k['doc_type'] === 'prescreen_interview' ) {
																$placeholder = 'Interview ' . $edit_interview_count;
															} else if ( $k['doc_type'] === 'sat' || $k['doc_type'] === 'act' || $k['doc_type'] === 'toefl' || $k['doc_type'] === 'ielts' ) {
																$placeholder = strtoupper($k['doc_type']);
															} else {
																$placeholder = ucwords(str_replace("_", " ", $k['doc_type']));
															}	
														?>
														{{
															Form::text(
																'edit-uploads-label', $k['transcript_label'],
																array(
																	'id' => 'edit-uploads-input-' . $k['doc_type'],
																	'class' => 'edit-uploads-input',
																	'placeholder' => $placeholder
																)
															)
														}}
													</div>

													<div class="column small-12 edit-viewDownload-container">
														<?php 
															$file_name = substr($k['path'], strrpos($k['path'], '/') + 1);
														?>

														@if($k['doc_type'] == 'prescreen_interview')
															<a class="playtext sm2_button" type="audio/x-wav" href="{{$k['path']}}" >Play</a>
														@else
															<div onClick="openTranscriptPreview(this);"  class="upload-view-btn" data-transcript-name="{{$file_name}}">View</div>
														@endif
														 |  
														<a href="{{$k['path']}}">
															@if($k['mime_type'] == 'DNE') 
																<span style="color:red;">Broken</span>
															@else 
																Download
															@endif
														</a>
													</div>

													
												</div>
												
												@endif
												<?php 
													if ( $k['doc_type'] === 'prescreen_interview' )
														$edit_interview_count++; 
												?>
												@endforeach
											</div>
											<!-- End of Edit Uploads -->

											<div class="dotDivide mt50"></div>



											<!-- programs interested in -->
											<div class="column small-12 large-6">
												<div class="row collapse">
													<div class="column small-12 student-profile-headers program-interested">
														Program(s) Interested In
													</div>
												</div>

												<div class="row collapse">
													<div class="column small-12" >
														<?php 
															$majors = explode(',', $key['major']);
														?>
														@foreach($majors as $major)
														<div class="major-listing-prof major-listing">{{$major or 'N/A'}}</div>
														@endforeach
													</div>
													<select name="schoolType" class="type-select">
														<option value="0" @if($key['interested_school_type'] == 0) selected="selected" @endif>On Campus Only</option>
														<option value="1" @if($key['interested_school_type'] == 1) selected="selected" @endif>Online Only</option>
														<option value="2" @if($key['interested_school_type'] == 2) selected="selected" @endif>Both Campus and Online</option>
													</select>
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
											</div>
									</div>
									<!-- new row - objective, programs interested in, why interested in school -->
								</div>

								<!-- right side for notes -->
								@if( isset($key['page']) && $key['page'] != 'Recommendations' )
								<div class="column small-12 medium-12 large-5 personal-students-notes-column">
									<div class="row">
										
										<div class="column small-12 question-container edit-contact-container">
										
											<div class="row e-c-info">
												<div class="column large-4 small-3">
													<div class="contact-verify @if($key['email_confirmed'] == 1) verified @endif" id="emailc">
														<div class="contact-tooltip">
															<span class="veri-icon">&#10003;</span><span class="veri-tooltip-title">Verify</span><br/><br/>
															Click to verify this information is correct.
														</div>
													</div>
														<div class="c-info-label pl20">Email</div> 
												</div>
												<div class="column large-8 small-9 link-cont">
													<input type="text" pattern="email" name="email" value="{{$key['userEmail'] or 'Email'}}" />
													<small class="error">Please enter a valid Email</small>
												</div>
											</div>
										

											
											<div class="row e-c-info">
												<div class="column large-4 small-3">
													<div class="contact-verify @if($key['verified_skype'] == 1) verified @endif" id="skypec">
														<div class="contact-tooltip">
															<span class="veri-icon">&#10003;</span><span class="veri-tooltip-title">Verify</span><br/><br/>
															Click to verify this information is correct.
														</div>
													</div>
														<div class="c-info-label pl20">Skype</div> 
												</div>
												<div class="column large-8 small-9 link-cont">
													<input class="skype-input" name="skype" type="text" value="{{$key['skype_id'] or ''}}" />
                										<img class="skype-icon" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon.png" alt=""/>
												</div>
											</div>
											
											<div class="row e-c-info" style="margin-bottom: 22px;">
												<div class="column large-4 small-3">
													<div class="contact-verify @if($key['verified_phone'] == 1) verified @endif" id="phonecallc">
														<div class="contact-tooltip">
															<span class="veri-icon">&#10003;</span><span class="veri-tooltip-title">Verify</span><br/><br/>
															Click to verify this information is correct.
														</div>
													</div>
														<div class="c-info-label pl20">Phone</div> 
												</div>
												<div class="column large-8 small-9 link-cont">
													<input type="text" pattern="phoneinput" name="phone" value="{{$key['userPhone'] or 'Phone'}}" />
													<small>The phone number should include the country code</small>
													<small class="error">Please enter a valid phone number</small>
												</div>
											</div>

											
											<div class="row e-c-info">
												<div class="column large-4 small-3">
													<div class="contact-verify @if(isset($key['userTxt_opt_in']) && $key['userTxt_opt_in'] == 1) verified @endif" id="phonec">
														<div class="contact-tooltip">
															<span class="veri-icon">&#10003;</span><span class="veri-tooltip-title">Verify</span><br/><br/>
															Click to verify this information is correct.
														</div>
													</div>
														<div class="c-info-label pl20">SMS</div> 
												</div>
												<div class="column large-8 small-9 link-cont">
													<input type="text" name="phoneReadOnly" value="{{$key['userPhone'] or 'Phone'}}" readonly/>
												</div>
											</div>
											

											<div class="mt20"></div>
											
											<div class="row e-c-info">
												<div class="column large-4 small-3">
													<div class="c-info-label pl40">Country</div> 
												</div>
												<div class="column large-8 small-9 link-cont">
													{{
														Form::select(
															'country',
															isset($country_list) ? $country_list : [],
															array(
																'data-selected' => isset($key['country_id']) ? $key['country_id'] : null,
																'placeholder' => 'Country',
																'autocomplete' => 'off'
															)
														)
													}} 

													<!--input type="text" placeholder="{{$key['country_name'] or 'Country'}}" /-->
												</div>
											</div>
											



											<div class="row e-c-info">
												<div class="column large-4 small-3">
													<div class="c-info-label pl40">Address</div> 
												</div>
												<div class="column large-8 small-9 link-cont">
													<input type="text" pattern="address" name="address" value="{{$key['userAddress'] or ''}}" />
													<small class="error">Please enter a valid Address</small>
												</div>
											</div>


											<div class="row e-c-info">
												<div class="column large-4 small-3">
													<div class="c-info-label pl40">City</div> 
												</div>
												<div class="column large-8 small-9 link-cont">
													<input type="text" pattern="city" name="city" value="{{$key['userCity'] or ''}}" />
													<small class="error">Please enter a valid City</small>
												</div>
											</div>

											<div class="row e-c-info">
												<div class="column large-4 small-3">
													<div class="c-info-label pl40">State</div> 
												</div>
												<div class="column large-8 small-9 link-cont">
													<input type="text" pattern="state" name="state" value="{{$key['userState'] or ''}}"  />
													<small class="error">Please enter a valid State</small>
												</div>
											</div>

                                            <div class="row e-c-info">
                                                <div class="column large-4 small-3">
                                                    <div class="c-info-label pl40">Zip</div> 
                                                </div>
                                                <div class="column large-8 small-9 link-cont">
                                                    <input type="text" name="zip" value="{{$key['userZip'] or ''}}" />
                                                    <small class="error">Please enter a valid Zip</small>
                                                </div>
                                            </div>

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
									<!-- Plexuss Notes ends here -->
									@endif

								</div><!-- end of right side container -->
								@endif <!-- end if not Recommendations -->
							</div>


							<!-- student also requested to be recruited by - start -->
							@if( isset($key['competitor_colleges']) && !empty($key['competitor_colleges']) )
							<div class="row row-of-student-profile">
								
						<div class="column small-12">
							
							<!-- row header -->
							<div class="row">
								<div class="column small-12 medium-6 small-text-center medium-text-left also-recruited-by-header student-profile-headers">
									This student has also requested to be recruited by 
									<!--<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Page views are indicated in <span style='color: #26b24b'>green</span>">?</span>-->
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
															<a href="/college/{{$key['college_info']['slug']}}">{{$key['college_info']['name']}}</a>
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
													<div class="competitor-carousel-arrow leftarrow">
														<span class="competitor-arrow"></span>
													</div>
													
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
													<div class="competitor-carousel-arrow rightarrow">
														<span class="competitor-arrow"></span>
													</div>

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
														@if( isset($key['applied_colleges']) )
														@foreach( $key['applied_colleges'] as $applied )
														<div class="applied-college-item item text-center">

															<div class="row">
																<div class="column small-12">
																	<div class="college-logos-background" data-status='{{ isset($applied->status) ? $applied->status : 'pending' }}' data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$applied->logo_url or 'default-missing-college-logo.png'}}, (default)]"></div>
																</div>
																@if( isset($key['is_plexuss']) && $key['is_plexuss'] == 1 )
																<div class="remove-applied-college-btn small-2" data-college_id={{ isset($applied->college_id) ? $applied->college_id : '' }}>
																	<span>&times;</span>
																</div>
																@endif
															</div>

															<div class="row" data-equalizer-watch>
																<div class="column small-12 college-competitor-name competition-school-name-lg" data-college-name="{{$applied->school_name or ''}}">
																	<a target="_blank" href="/college/{{$applied->slug or ''}}">{{$applied->school_name or ''}}</a>
																</div>
															</div>

															<div class="row">
																@if ( isset($key['is_plexuss']) && $key['is_plexuss'] == 1 )
																<div class="column small-12">
																	@if ( isset($applied->status) )
																	<div class='applied-college-status-btn' data-status='{{ $applied->status }}'>{{ ucfirst($applied->status) }}</div>
																	@elseif ( isset($applied->submitted) && $applied->submitted )
																	<div class='applied-college-status-btn'>Pending</div>
																	@else
																	<div class='applied-college-status-btn'>Incomplete</div>
																	@endif
																	<div class='change-status-options' data-college_id={{ isset($applied->college_id) ? $applied->college_id : '' }}>
																		<li>Accepted</li>
																		<li>Rejected</li>
																		<li>Pending</li>
																	</div>
																</div>
																@else
																<div class="column small-12">
																	@if ( isset($applied->status) )
																	<div class='applied-college-status' data-status='{{ $applied->status }}'>{{ ucfirst($applied->status) }}</div>
																	@elseif ( isset($applied->submitted) && $applied->submitted )
																	<div class='applied-college-status'>Pending</div>
																	@else
																	<div class='applied-college-status'>Incomplete</div>
																	@endif
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
					@endif
					<!-- student also requested to be recruited by - end -->

					<!-- //////////// new student profile pane - end \\\\\\\\\\\\\\ -->

				</div>
			</div>

		</div><!-- end hiddenDropDown -->
		
	</div>
	</form>
</div>
		<!-- \\\\\\\\\\\\\\ student profile edit pane - end /////////////// -->




<!-- done this way on other parts of app -->
<script type="text/javascript">

$(document).ready(function(){

// Set autocomplete options for professions autocomplete field
	$('.objProfession').autocomplete({
		source: '/getObjectiveProfessions',
		appendTo: '#objProfessionContainer',
		minLength: 3,
		select: function(event, ui){
			$(this).data('selected', ui.item.value);
		},
		change: function (event, ui) { 
			$(this).attr('data-changed', '1');
		}
	});
	$('#objProfession').change(function(){
		if($(this).val() !== $(this).data('selected')){
			$(this).val('');
		}
	});

});

	
</script>