<?php 
	$transcript_found = false;
	$toefl_found = false;
	$ielts_found = false;
	$financial_found = false;
	$resume_found = false;
	$essay_found = false;
	$passport_found = false;
	$other_found = false;
?>
<div class="row main-uploadcenter-container">
	<div class="column small-12">

		<div class="row">
			<div class="column small-12">
				<div>Upload Center +5% to your profile status</div>
				<div><small><i>All documents are private and secure and can only be viewed by colleges and Plexuss</i></small></div>
			</div>
		</div>

		<hr>

		<!-- Transcript row -->
		<div class="row doc-header-row">
			<div class="column small-7 medium-9 large-10">
				Transcript
			</div>
			<div class="column small-5 medium-3 large-2 text-center">
				<a href=""><div class="upload-docs-btn" data-doc-type="transcript">Upload</div></a>
			</div>
		</div>

		<hr>

		<div class="row transcript-header-row">
			<div class="column small-12 large-5">File Name</div>
			<div class="column small-5">Date Uploaded</div>
			<div class="column small-2">Remove</div>
		</div>

		@if( isset($transcript_data) && count($transcript_data) > 0 )
			@foreach( $transcript_data as $transcript )
			@if( $transcript->doc_type == 'transcript' || $transcript->doc_type == null )
				<?php $transcript_found = true; ?>
				<div class="row transcript-details-row">
					<div class="column small-12 large-5"><a class="tscript-preview-link" href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$transcript->transcript_name}}">{{$transcript->transcript_name}}</a></div>
					<div class="column small-10 large-5">{{$transcript->created_at}}</div>
					<div class="column small-2 remove-transcript-btn small-only-text-center" onClick="removeThisTranscript(this, 'transcriptremove', {{$transcript->id}});">X</div>
				</div>
			@endif
			@endforeach

			@if( $transcript_found == false )
				<div class="row no-transcript-added-msg">
					<div class="column small-12">
						<div>No transcripts added yet.</div>
						<div>Add a transcript to add 5% to your profile status.</div>
					</div>
				</div>	
			@endif
		@else
			<div class="row no-transcript-added-msg">
				<div class="column small-12">
					<div>No transcripts added yet.</div>
					<div>Add a transcript to add 5% to your profile status.</div>
				</div>
			</div>
		@endif

		<hr>

		<!-- Only International student will see this portion -->
		@if( isset($user_country_id) && $user_country_id != 1 )

			<!-- TOEFL row -->
			<div class="row doc-header-row">
				<div class="column small-7 medium-9 large-10">
					TOEFL	
				</div>
				<div class="column small-5 medium-3 large-2 text-center">
					<a href=""><div class="upload-docs-btn" data-doc-type="toefl">Upload</div></a>
				</div>
			</div>

			<hr>

			@if( isset($transcript_data) && count($transcript_data) > 0 )
				@foreach( $transcript_data as $transcript )
				@if( $transcript->doc_type == 'toefl' )
					<?php $toefl_found = true; ?>
					<div class="row transcript-details-row">
						<div class="column small-12 large-5"><a class="tscript-preview-link" href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$transcript->transcript_name}}">{{$transcript->transcript_name}}</a></div>
						<div class="column small-10 large-5">{{$transcript->created_at}}</div>
						<div class="column small-2 remove-transcript-btn small-only-text-center" onClick="removeThisTranscript(this, 'transcriptremove', {{$transcript->id}});">X</div>
					</div>
				@endif
				@endforeach

				@if( $toefl_found == false )
					<div class="row no-transcript-added-msg">
						<div class="column small-12">
							Upload your Test of English as a Foreign Language	
						</div>
					</div>
				@endif
			@else
				<div class="row no-transcript-added-msg">
					<div class="column small-12">
						Upload your Test of English as a Foreign Language	
					</div>
				</div>
			@endif

			<hr>

			<!-- IELTS row -->
			<div class="row doc-header-row">
				<div class="column small-7 medium-9 large-10">
					IELTS	
				</div>
				<div class="column small-5 medium-3 large-2 text-center">
					<a href=""><div class="upload-docs-btn" data-doc-type="ielts">Upload</div></a>
				</div>
			</div>

			<hr>

			@if( isset($transcript_data) && count($transcript_data) > 0 )
				@foreach( $transcript_data as $transcript )
				@if( $transcript->doc_type == 'ielts' )
					<?php $ielts_found = true; ?>
					<div class="row transcript-details-row">
						<div class="column small-12 large-5"><a class="tscript-preview-link" href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$transcript->transcript_name}}">{{$transcript->transcript_name}}</a></div>
						<div class="column small-10 large-5">{{$transcript->created_at}}</div>
						<div class="column small-2 remove-transcript-btn small-only-text-center" onClick="removeThisTranscript(this, 'transcriptremove', {{$transcript->id}});">X</div>
					</div>
				@endif
				@endforeach

				@if( $ielts_found == false )
					<div class="row no-transcript-added-msg">
						<div class="column small-12">
							Upload your International English Language Testing System	
						</div>
					</div>
				@endif
			@else
				<div class="row no-transcript-added-msg">
					<div class="column small-12">
						Upload your International English Language Testing System	
					</div>
				</div>
			@endif


			<hr>	
		@endif <!-- end of international students view portion -->

		<!-- Financial Documents row -->
		<div class="row doc-header-row" id="financial-docs-scrollTo">
			<div class="column small-7 medium-9 large-10">
				Financial Documents	
			</div>
			<div class="column small-5 medium-3 large-2 text-center">
				<a href=""><div class="upload-docs-btn" data-doc-type="financial">Upload</div></a>
			</div>
		</div>

		<hr>

		@if( isset($transcript_data) && count($transcript_data) > 0 )
			@foreach( $transcript_data as $transcript )
			@if( $transcript->doc_type == 'financial' )
				<?php $financial_found = true; ?>
				<div class="row transcript-details-row">
					<div class="column small-12 large-5"><a class="tscript-preview-link" href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$transcript->transcript_name}}">{{$transcript->transcript_name}}</a></div>
					<div class="column small-10 large-5">{{$transcript->created_at}}</div>
					<div class="column small-2 remove-transcript-btn small-only-text-center" onClick="removeThisTranscript(this, 'transcriptremove', {{$transcript->id}});">X</div>
				</div>
			@endif
			@endforeach

			@if( $financial_found == false )
				<div class="row no-transcript-added-msg">
					<div class="column small-12">
						Upload bank statements or other supporting documents that indicate you have adequate financial resources.	
					</div>
				</div>
			@endif
		@else
			<div class="row no-transcript-added-msg">
				<div class="column small-12">
					Upload bank statements or other supporting documents that indicate you have adequate financial resources.	
				</div>
			</div>
		@endif	

		<hr>

		<div class="row doc-header-row">
			<div class="column small-7 medium-9 large-10">
				Resume / CV / Portfolio
			</div>
			<div class="column small-5 medium-3 large-2 text-center">
				<a href=""><div class="upload-docs-btn" data-doc-type="resume">Upload</div></a>
			</div>
		</div>

		<hr>

		@if( isset($transcript_data) && count($transcript_data) > 0 )
			@foreach( $transcript_data as $transcript )
			@if( $transcript->doc_type == 'resume' )
				<?php $resume_found = true; ?>
				<div class="row transcript-details-row">
					<div class="column small-12 large-5"><a class="tscript-preview-link" href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$transcript->transcript_name}}">{{$transcript->transcript_name}}</a></div>
					<div class="column small-10 large-5">{{$transcript->created_at}}</div>
					<div class="column small-2 remove-transcript-btn small-only-text-center" onClick="removeThisTranscript(this, 'transcriptremove', {{$transcript->id}});">X</div>
				</div>
			@endif
			@endforeach

			@if( $resume_found == false )
				<div class="row no-transcript-added-msg">
					<div class="column small-12">
						Upload any kind of resume, cv, or portfolio to show work that you have done.
					</div>
				</div>
			@endif
		@else
			<div class="row no-transcript-added-msg">
				<div class="column small-12">
					Upload any kind of resume, cv, or portfolio to show work that you have done.
				</div>
			</div>
		@endif

		<hr>

		<div class="row doc-header-row">
			<div class="column small-7 medium-9 large-10">
				Essay
			</div>
			<div class="column small-5 medium-3 large-2 text-center">
				<a href=""><div class="upload-docs-btn" data-doc-type="essay">Upload</div></a>
			</div>
		</div>

		<hr>

		@if( isset($transcript_data) && count($transcript_data) > 0 )
			@foreach( $transcript_data as $transcript )
			@if( $transcript->doc_type == 'essay' )
				<?php $essay_found = true; ?>
				<div class="row transcript-details-row">
					<div class="column small-12 large-5"><a class="tscript-preview-link" href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$transcript->transcript_name}}">{{$transcript->transcript_name}}</a></div>
					<div class="column small-10 large-5">{{$transcript->created_at}}</div>
					<div class="column small-2 remove-transcript-btn small-only-text-center" onClick="removeThisTranscript(this, 'transcriptremove', {{$transcript->id}});">X</div>
				</div>
			@endif
			@endforeach

			@if( $essay_found == false )
				<div class="row no-transcript-added-msg">
					<div class="column small-12">
						Upload your essay or application essay.
					</div>
				</div>
			@endif
		@else
			<div class="row no-transcript-added-msg">
				<div class="column small-12">
					Upload your essay or application essay.
				</div>
			</div>
		@endif

		<hr>

		<div class="row doc-header-row">
			<div class="column small-7 medium-9 large-10">
				Passport
			</div>
			<div class="column small-5 medium-3 large-2 text-center">
				<a href=""><div class="upload-docs-btn" data-doc-type="passport">Upload</div></a>
			</div>
		</div>

		<hr>

		@if( isset($transcript_data) && count($transcript_data) > 0 )
			@foreach( $transcript_data as $transcript )
			@if( $transcript->doc_type == 'passport' )
				<?php $passport_found = true; ?>
				<div class="row transcript-details-row">
					<div class="column small-12 large-5"><a class="tscript-preview-link" href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$transcript->transcript_name}}">{{$transcript->transcript_name}}</a></div>
					<div class="column small-10 large-5">{{$transcript->created_at}}</div>
					<div class="column small-2 remove-transcript-btn small-only-text-center" onClick="removeThisTranscript(this, 'transcriptremove', {{$transcript->id}});">X</div>
				</div>
			@endif
			@endforeach

			@if( $passport_found == false )
				<div class="row no-transcript-added-msg">
					<div class="column small-12">
						Upload a photo of your passport here. <a href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/sample_passport.png" target="_blank">Here</a> is a sample of a good photo of a passport.
					</div>
				</div>
			@endif
		@else
			<div class="row no-transcript-added-msg">
				<div class="column small-12">
					Upload a photo of your passport here. <a href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/sample_passport.png" target="_blank">Here</a> is a sample of a good photo of a passport.
				</div>
			</div>
		@endif

		<hr>

		<div class="row doc-header-row">
			<div class="column small-7 medium-9 large-10">
				Other
			</div>
			<div class="column small-5 medium-3 large-2 text-center">
				<a href=""><div class="upload-docs-btn" data-doc-type="other">Upload</div></a>
			</div>
		</div>

		<hr>

		@if( isset($transcript_data) && count($transcript_data) > 0 )
			@foreach( $transcript_data as $transcript )
			@if( $transcript->doc_type == 'other' )
				<?php $other_found = true; ?>
				<div class="row transcript-details-row">
					<div class="column small-12 large-5"><a class="tscript-preview-link" href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$transcript->transcript_name}}">{{$transcript->transcript_name}}</a></div>
					<div class="column small-10 large-5">{{$transcript->created_at}}</div>
					<div class="column small-2 remove-transcript-btn small-only-text-center" onClick="removeThisTranscript(this, 'transcriptremove', {{$transcript->id}});">X</div>
				</div>
			@endif
			@endforeach

			@if( $other_found == false )
				<div class="row no-transcript-added-msg">
					<div class="column small-12">
						If you have any other documents or files that weren't listed you can upload those here.
					</div>
				</div>
			@endif
		@else
			<div class="row no-transcript-added-msg">
				<div class="column small-12">
					If you have any other documents or files that weren't listed you can upload those here.
				</div>
			</div>
		@endif

	</div>



	<!-- upload modal -->
	<div id="upload_docs_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<div class="text-right"><a class="close-reveal-modal" aria-label="Close">&#215;</a></div>	
		{{Form::open(array('id'=>'upload_docs_form'))}}
			{{ Form::hidden('postType', 'transcriptupload', array()) }}
			{{ Form::hidden('docType', 'transcript', array('class'=>'doctype')) }}

			<div class="row">
				<div class="column fixed-col">
					<img src="/images/transcript-img.png" alt="upload to plexuss">
				</div>
				<div class="column small-8 end">
					<div>Upload files</div>
					{{Form::file('profile_upload_files')}}
				</div>
			</div>

			<div class="row">
				<div class="column small-12 text-right">
					<div><span class="cancel-upload-btn close-reveal-modal">Cancel</span> <span class="upload-files-btn">{{Form::submit('upload')}}</span></div>	
				</div>
			</div>

		{{Form::close()}}
	</div>


	<!-- transcript preview modal -->
	<div id="transcript-preview-modal" class="reveal-modal" data-reveal>
		<div class="row">
			<div class="column small-12 small-text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
			</div>
		</div>
		<div class="row">
			<div class="column small-12 small-text-center transcript_preview_img">
			</div>
		</div>
	</div>


</div>
