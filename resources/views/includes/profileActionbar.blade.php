<?php 
	// dd($key);
?>
<!-- ////////// Action bar /////////////////////-->
<div class="actionbar-container" data-uhid="{{$key['hashed_id']}}">

	<!-- regular actions container vs edit actions -->
	<div class="regular-actions clearfix">
		
		
		<div class="column large-1 small-2">
			<span class="action-title">Actions</span>
		</div>

		<span class="buttons-container">
			<!-- left side of bar -->
			<div class="column large-6">
				
				<!-- promote to button -->	
				@if($key['currentPage'] != 'admin-recommendations' && ((isset($is_admin_premium) && $is_admin_premium == true) || ($key['currentPage'] == 'admin-inquiries' && isset($default_organization_portal->ro_type) && $default_organization_portal->ro_type == 'click')))
					<div class="actionbar-btn actionbar-btn actionbar-btn-notactive promote-identifier clearfix">


						<span class="promote-text">Move to </span><span class="promote-arrow promote-arrow-notactive"></span>
						<ul class="promote_menu">
                           {{--  @if ($key['currentPage'] == 'admin-inquiries' && ((isset($default_organization_portal->ro_type) && $default_organization_portal->ro_type == 'click') || (!isset($default_organization_portal) && isset($organization_portals[0]) && isset($organization_portals[0]->ro_type) && isset($organization_portals[0]->ro_id))))  --}}
                            @if(Session::has('handshake_power'))
                                <li>Converted</li>
                            @endif

                            @if (isset($is_admin_premium) && $is_admin_premium == true)
    							@if($key['currentPage'] == 'admin-inquiries' || $key['currentPage'] == 'admin-pending')
    								<li>Handshakes</li>
    							@endif
    							@if($key['currentPage'] != 'admin-verifiedHs'  && $key['currentPage'] != 'admin-verifiedApp')
    								<li>Verified Handshakes</li>
    							@endif
    							@if($key['currentPage'] != 'admin-verifiedApp')
    								<li>Verified Applications</li>
    							@endif
    							@if($key['currentPage'] != 'admin-prescreened' && $key['currentPage'] != 'admin-verifiedHs' && $key['currentPage'] != 'admin-verifiedApp')
    								<li>Verified Prescreen</li>
    							@endif
    							@if($key['currentPage'] == 'admin-verifiedHs')
    								<li>Undo Promote Verified Handshakes</li>
    							@endif
    							@if($key['currentPage'] == 'admin-verifiedApp' )
    								<li>Undo Promote Verified Applications</li>
    							@endif
                            @endif
						</ul>
					</div>
				@endif
				

				<!-- status button -->
				<div class="actionbar-btn actionbar-btn actionbar-btn-notactive status-identifier clearfix">
					<span class="promote-text">
					@if(!empty($key['plexuss_status']))
						{{$key['plexuss_status']}}
					@else
						Status
					@endif 
					</span>
					<span class="promote-arrow promote-arrow-notactive"></span>

					<ul class="status-menu">
						<li>Has not scheduled prescreen</li>
						<li>Attempted phone contact</li>
						<li>Requested callback</li>
						<li>Not Scheduled Interview</li>
						<li>Scheduled Interview</li>
						<li>Interview did not Happen</li>
						@if(Session::has('handshake_power'))
						<li>Not interested in premium</li>
						<li>Tentative Commit to Premium</li>
						<li>Full Commit to Premium</li>
						@endif
						<li>Undo Status Update</li>
					</ul>
				</div>

				@if(isset($key['is_plexuss']) && $key['is_plexuss'] == 1)
				<!-- application status button -->
				<div class="actionbar-btn actionbar-btn actionbar-btn-notactive state-identifier clearfix">
					<span class="promote-text">
					@if(isset($key['application_state']))
						{{ ucfirst($key['application_state']) }}
					@else
						OneApp State
					@endif 
					</span>
					<span class="promote-arrow promote-arrow-notactive"></span>

					<ul class="state-menu">
						<li>Basic</li>
						<li>Identity</li>
						<li>Start</li>
						<li>Contact</li>
						<li>Study</li>
						<li>Citizenship</li>
						<li>Financials</li>
						<li>GPA</li>
						<li>Colleges</li>
						<li>Essay</li>
						<li>Additional info</li>
						<li>Uploads</li>
						<li>Declaration</li>
						<li>Sponsor</li>
						<li>Submit</li>
					</ul>
				</div>
				@endif
				
				@if(Session::has('handshake_power'))			
				<!-- Login as button -->
				<a href="{{$key['loginas'] or ''}}" target="_blank" class="actionbar-btn actionbar-btn-notactive">
					<span class="promote-text">Login as</span>
				</a>
				@endif

				<!-- View FB button -->
				@if(isset($key['fb_id']) && $key['fb_id'] != '')
				<a target="_blank" href="https://www.facebook.com/{{$key['fb_id'] or ''}}" class="actionbar-btn actionbar-btn-notactive">
					<span class="promote-text">View FB </span>
				</a>
				@endif
			</div><!-- end left side -->
			

			<!-- right side of bar -->
			<div class="column large-5">

				<!-- <div class="actionbar-btn send-text">
					<a href="/admin/messages/{{$key['student_user_id']}}/inquiry-txt">
						<span class="text-icon"></span>Text
					</a>
				</div> -->
				
				<div class="actionbar-btn contact-btn">
					<span class="text-icon"></span>Contact
				</div>

				<div class="actionbar-btn actionbar-btn-notactive actionbar-uploadModal">Upload Files</div>

				<div class="actionbar-btn edit-student"><span class="edit-icon"></span>&#9998; Edit Student</div>
			</div><!--end right side -->	

		</span><!-- end button container -->


		<div class="actionbar-dropdown-menu">
			MENU
			<ul class="actionbar-dropdown">
				<!-- promote to button -->	
				@if($key['currentPage'] != 'admin-recommendations')
					<li><span class="d-arrow"></span>Move to :
						<ul class="promote_menu promote_menu_small">
                            @if ($key['currentPage'] == 'admin-inquiries')
                                <li>Converted</li>
                            @endif
                            @if (isset($is_admin_premium) && $is_admin_premium == true)
    							@if($key['currentPage'] == 'admin-inquiries' || $key['currentPage'] == 'admin-pending')
    								<li>Handshakes</li>
    							@endif
    							@if($key['currentPage'] != 'admin-verifiedHs'  && $key['currentPage'] != 'admin-verifiedApp')
    								<li>Verified Handshakes</li>
    							@endif
    							@if($key['currentPage'] != 'admin-verifiedApp')
    								<li>Verified Applications</li>
    							@endif
    							@if($key['currentPage'] != 'admin-prescreened' && $key['currentPage'] != 'admin-verifiedHs' && $key['currentPage'] != 'admin-verifiedApp')
    								<li>Verified Prescreen</li>
    							@endif
    							@if($key['currentPage'] == 'admin-verifiedHs')
    								<li>Undo Promote Verified Handshakes</li>
    							@endif
    							@if($key['currentPage'] == 'admin-verifiedApp' )
    								<li>Undo Promote Verified Applications</li>
    							@endif
                            @endif
						</ul>
					</li>
				@endif
				<li><span class="d-arrow"></span>Status : &nbsp;
					<span class="actionbar-status-small"> 
					@if(!empty($key['plexuss_status']))
						{{$key['plexuss_status']}}
					@endif
					</span>
					<ul class="status-menu status-menu-small">
						<li>Not Scheduled Interview</li>
						<li>Scheduled Interview</li>
						<li>Interview did not Happen</li>
						<li>Undo Status Update</li>
					</ul>
				</li>
				<li>
					<a href="{{$key['loginas'] or ''}}" target="_blank" class="">
						Login as
					</a>
				</li>
				@if(isset($key['fb_id']) && $key['fb_id'] != '')
					<li>
						<a target="_blank" href="https://www.facebook.com/{{$key['fb_id'] or ''}}" class="">
							View FB
						</a>
					</li>
				@endif
				<!-- <li class="contact-btn">
					<a href="/admin/messages/{{$key['student_user_id']}}/inquiry-txt" class="send-text-small">
						<span class="text-icon"></span>Text
					</a>
				</li> -->
				<li class="contact-btn">
					<!-- <a href="/admin/messages/{{$key['student_user_id']}}/inquiry-txt" class="send-text-small"> -->
						<span class="text-icon"></span>Contact
					<!-- </a> -->
				</li>
				<li>
					<span class="actionbar-uploadModal actionbar-uploadModal-small">Upload Files</span>
				</li>
				<li>
					<span class="edit-student edit-student-small"><span class="edit-icon"></span>&#9998; Edit Student</span>
				</li>
			</ul>
		</div>

	</div>
</div>
<!--////// action bar END ///////-->




<div class="uploads-cont">


	<!--////////// upload files modal //////////////-->
	<div class="upload-files-modal reveal-modal" data-uhid="{{$key['hashed_id']}}" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<div class="text-right"><a class="close-ufModal" aria-label="Close">&times;</a></div>
		<h3>Upload files</h3>
		<h5>Choose which type of file you would like to upload</h5>
		<div class="row mt50 mw-center">
			<div class="col-25">
				<div class="actionbar-upload-btn">
					<div class="uploadDocsSpriteInterview interview"></div>
					<div class="actionbar-upload-txt">Interview</div>
				</div>
			</div>
			<div class="col-25">
				<div class="actionbar-upload-btn"> 
					<div class="uploadDocsSpriteLarger transcript"></div>
					<div class="actionbar-upload-txt">Transcript</div>
				</div>
			</div>
			<div class="col-25">
				<div class="actionbar-upload-btn">
					<div class="uploadDocsSpriteLarger financial"></div>
					<div class="actionbar-upload-txt">Financial Document</div>
				</div>
			</div>
			<div class="col-25">
				<div class="actionbar-upload-btn">
					<div class="uploadDocsSpriteLarger resume"></div>
					<div class="actionbar-upload-txt">Resume / Portfolio</div>
				</div>
			</div>
			<div class="col-25">
				<div class="actionbar-upload-btn">
					<div class="uploadDocsSpriteLarger essay"></div>
					<div class="actionbar-upload-txt">Essay</div>
				</div>
			</div>
			<div class="col-25">
				<div class="actionbar-upload-btn">
					<div class="uploadDocsSpriteLarger toefl"></div>
					<div class="actionbar-upload-txt">TOEFL</div>
				</div>
			</div>
			<div class="col-25">
				<div class="actionbar-upload-btn">
					<div class="uploadDocsSpriteLarger ielts"></div>
					<div class="actionbar-upload-txt">IELTS</div>
				</div>
			</div>
			<div class="col-25">
				<div class="actionbar-upload-btn">
					<div class="uploadDocsSpriteLarger passport"></div>
					<div class="actionbar-upload-txt">Passport</div>
				</div>
			</div>
			<div class="col-25">
				<div class="actionbar-upload-btn">
					<div class="uploadDocsSpriteLarger other"></div>
					<div class="actionbar-upload-txt">Other</div>
				</div>
			</div>
		</div>
	</div>




	<!--/////////////////// uploads modal part 2 ///////////////////////////////////////-->
	<div class="upload_docs_modal reveal-modal small" data-uhid="{{$key['hashed_id']}}" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
			<div class="text-right"><a class="close-udModal" aria-label="Close">×</a></div>	
			<form accept-charset="UTF-8" class="upload_docs_form_p">
			<input name="_token" type="hidden" value="{{$key['hashed_id']}}">
				<input name="postType" type="hidden">
				<input class="doctype" name="docType" type="hidden">

				<div class="row">
					<div class="column large-3 small-12">
						<img src="/images/transcript-img.png" alt="upload to plexuss">
					</div>

					<div class="column large-9 small-12 end">
						<div class="row">
							<div class="column">
								<div>Upload files</div>
								<input name="profile_upload_files" type="file">
							</div>
						</div>
						<div class="row">
							<div class="column">
								<input name="transcript_label" placeholder="Label File (Optional)" type="text">
							</div>
						</div>
						<div class="row">
							<div class="column">
								<div class="message-box"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="column small-12 text-right">
						<div><span class="cancel-upload-btn close-udModal">Cancel</span> <span class="upload-files-btn"><input type="submit" value="Upload"></span></div>	
					</div>
				</div>

			</form>
		</div>
	</div>