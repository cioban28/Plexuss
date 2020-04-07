<?php 
	$financial_found = false;
?>

<div class="row main-financialinfo-container">
	<div class="column small-12">
		
		<div class="row financialinfo-header-row">
			<div class="column small-12">
				Financial Information <br class="hide-for-large-up" /> <small>All information is kept confidential and only shared with Universities.</small>
				<div class="financialinfo-descrip">Most colleges want to know how much of your education and living expenses you will be able to pay while studying in the United States. Please indicate the amount below.</div>
			</div>
		</div>

		<div class="row financialinfo-form-row">
			<div class="column small-12">
				{{Form::open(array('url'=>'', 'method' => '', 'id'=>'financial-able-to-pay-form', 'data-abide'=>'ajax'))}}
					
					<div class="row">
						<div class="column small-12 medium-2 large-1 usd-label-col">
							{{Form::label('amt-able-to-pay-form', 'USD')}}
						</div>
						<div class="column small-7 medium-6 large-4">
							{{Form::select('amt_able_to_pay', array('' => 'Select an estimated amount', '0.00' => '$0', '0 - 5,000' => '$0 - $5,000', '5,000 - 10,000' => '$5,000 - $10,000', '10,000 - 20,000' => '$10,000 - $20,000', '20,000 - 30,000' => '$20,000 - $30,000', '30,000 - 50,000' => '$30,000 - $50,000', '50,000' => '$50,000 +'), $amt_able_to_pay, array('id'=>'amt-able-to-pay-form'))}}
						</div>
						<div class="column small-5 medium-4 large-2 end ">
							<div class="save-living-exp-btn text-center">Save</div>
						</div>
					</div>
					
				{{Form::close()}}
			</div>
		</div>

		<div class="row upload-financial-docs-row">
			<div class="column small-12">
				<div>Upload bank statements or other supporting documents that indicate you have adequate financial resources.</div>
			</div>
		</div>

		<div class="row upload-financial-docs-row">
			<div class="column small-12" style="padding: 5px 6px 20px;">
				<a href="#" data-reveal-id="example-doc-modal"><u>Example financial document</u></a>
			</div>
		</div>

		<div class="row upload-financial-docs-row">
			<div class="column small-12 medium-3 end">
				<div class="upload-docs-btn for-financial text-center" data-doc-type="financial">Upload documents</div>
			</div>
		</div>

		<div class="row">
			<div class="column small-12 tips">
				Here are some <a href="#" data-reveal-id="tips-modal"><u>tips</u></a>, on uploading financial documents.
			</div>
		</div>

		<div class="row">
			<div class="column small-12 medium-9 end note">
				Note: It is critical that you put in your correct financial information.  Acceptance to universities will be at risk if any information is incorrect.
			</div>
		</div>

		<div class="row text-center">
			<div class="column small-12 text-left uploads-title">Financial Uploads</div>
			<div class="column small-6 text-left upload-head">File name</div>
			<div class="column small-4 upload-head">Date uploaded</div>
			<div class="column small-2 upload-head">Remove</div>
		</div>

		@if( isset($transcript_data) && count($transcript_data) > 0 )
			@foreach( $transcript_data as $transcript )
				@if( $transcript->doc_type == 'financial' )
					<?php $financial_found = true; ?>
					<div class="row text-center transcript-details-row financials">
						<div class="column small-6 text-left file-name">
							<a class="tscript-preview-link" href="#" onClick="openTranscriptPreview(this);" data-transcript-name="{{$transcript->transcript_name}}">
								{{$transcript->transcript_name}}
							</a>
						</div>
						<div class="column small-4 file-date">{{current(explode(' ', $transcript->created_at))}}</div>
						<div class="column small-2 file-remove remove-transcript-btn" onClick="removeThisTranscript(this, 'transcriptremove', {{$transcript->id}});">X</div>
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
			<div class="row no-transcript-added-msg for-finance">
				<div class="column small-12 medium-10 medium-centered">
					Upload bank statements or other supporting documents that indicate you have adequate financial resources.	
				</div>
			</div>
		@endif	

		<!-- upload modal -->
		<div id="upload_financial_docs_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
			<div class="text-right"><a class="close-reveal-modal" aria-label="Close">&#215;</a></div>	
			{{Form::open(array('id'=>'upload_financial_docs_form', 'class'=>'is-financial-form'))}}
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

		<!-- tips modal -->
		<div id="tips-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-options="close_on_background_click: true">
			<div class="row">
				<div class="column small-3 small-offset-9 small-text-right">
					<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
				</div>
			</div>

			<h3>Important Points About Bank Statements: </h3>
			<br />
			<ol class="tips-list">
				<li>Bank Statement should be provided in the official Bank Letterhead only.</li>
				<li>Bank Statement must include the date the letter was issued. For your I-20, your bank statement must be 6 months or less than 6 months old.</li>
				<li>Name of account holder: If this is someone other than the student, please state the student's name and relationship to the account holder.</li>
				<li>Bank Statement must signed by a bank official.</li>
				<li>Bank Statement must include the Bank Stamp.</li>
				<li>If the Bank Statement contains the amount that has been shown from multiple accounts in the same Bank, then you need to provide all the Account Numbers.</li>
				<li>Some Universities just require the last 4 digits of your Account Number, so check with your University. It is fine if you give your complete account number.</li>
			</ol>
		</div>

		<!-- example doc modal -->
		<div id="example-doc-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-options="close_on_background_click: true">
			<div class="row">
				<div class="column small-3 small-offset-9 small-text-right">
					<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
				</div>
			</div>

			<object width="100%" height="500" type="application/pdf" data="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/Sample-Bank-Letter.pdf?#zoom=85&scrollbar=0&toolbar=0&navpanes=0">
				<p>The example document PDF could not be displayed due to unsupported browser. Please upgrade your browser or use Google Chrome or FireFox.</p>
			</object>
		</div>

	</div>
</div>