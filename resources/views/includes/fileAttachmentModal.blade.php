
<!-- attach files modal -->
<div id="attachFileModal" class="attachFileModal reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">

	<div class="text-right">
		<a class="close-reveal-modal fattch-close-btn" aria-label="Close">&times;</a>
	</div>
	
	<h5>Choose A File:</h5>


	<div class="file-attch-nav-container">
		<div class="fattch-new-btn active">New Attachment</div>
		<div class="fattch-current-btn">Current Attachments</div>
	</div>


	<!-- new attachment panel -->
	<div class="new-attch-cont opened">

		<div class="fattch-preview-cont">
			<img class="fattch-preview-img" src=""/>
		</div>


		<input class="FileName" name="FileName" placeholder="Name the Attachment (optional)" />

		<div class="fattch-browse-cont"> 
			<div class="fattch-attch-btn">
				<div class="browse-icon"></div> <span class="fattch-txt">Browse...</span>
			</div>
		</div>

		<input class="FileInput" name="attachment" type="file" title=" "/>

		@if(isset($currentPage) && $currentPage == 'portal')
			<select class="FileType" name="FileType">
				<option selected="selected">Select a file type...</option>
				<option>Transcript</option>
				<option>Financial Document</option>
				<option>Resume/Portfolio</option>
				<option>Essay</option>
				<option>TOEFL</option>
				<option>IELTS</option>
				<option>Passport</option>
				<option>Other</option>

			</select>
		@endif
		
		<div class="FileFeedback"></div>
		<div class="FileError"></div>

	</div>



	<!-- attahment manager panel -->
	<div class="attch-manager-cont">

		<div class="row fattch-managment-container">
			<div class="column medium-8 fattch-curr-choosefrom"></div>
			<div class="column medium-4 fattch-curr-chosen"></div>
		</div>
	</div>


	<div class="text-right mt20">
		<a class="close-reveal-modal fattch-cancel-btn" aria-label="Close">Cancel</a>
		<div class="contact-attch-file-btn">Attach File</div>
	</div>
</div>




<!-- view attachment modal -->
<div id="viewFileModal" class="viewFileModal reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
	<div class="text-right">
		<!-- <a class="close-reveal-modal fattch-close-btn" aria-label="Close">&times;</a> -->
	</div>

	<div class="file-attch-view-cont text-center"></div>
</div>