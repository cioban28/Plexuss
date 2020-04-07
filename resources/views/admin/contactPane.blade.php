<?php 
	//dd($key);
?>


<div class="contact-pane-wrapper sales-student-contact-pane" >
 @if(isset($key['admin_hasNum']) && $key['admin_hasNum'] == 1)
 <div class="admin_TxtSetup"></div>
 @endif

	<div class="dropdownbox">
	@include('includes.profileActionbar')

	<div class="contact-pane-cont">

		@include('private.includes.abs-ajax_loader')

		
		<div class="contact-btn-container text-center">
			<div class="contact-person-name">{{$key['name'] or ''}} </div>
			<div class="contact-btn-inner">
				<div class="contact-btn-wrapper selected">
					<div class="contact-call-btn contact-btn-img contact-nav-btn call"></div>
					<div class="contact-link">Call</div>	
				</div>

                @if (isset($is_admin_premium) && $is_admin_premium)
    				<div class="contact-btn-wrapper">
    					<div class="contact-msg-btn contact-btn-img contact-nav-btn msg"></div>
    					<div class="contact-link">Message</div>	
    				</div>
				@endif
				
				<div class="contact-btn-wrapper text-enabled
				 @if(isset($key['admin_hasNum'])
				 && (isset($key['userTxt_opt_in']) && $key['userTxt_opt_in'] == 1))  @else hide @endif">
					<div class="contact-text-btn contact-btn-img contact-nav-btn text"></div>
					<div class="contact-link">Text</div>	
				</div>	
				<div class="contact-btn-wrapper-disabled text-disabled 
				@if(isset($key['admin_hasNum'])
				|| (isset($key['userTxt_opt_in']) && $key['userTxt_opt_in'] != 1)) hide @else  @endif">
					 SMS <br/> not <br/> set up
				</div>

				<!-- <div class="contact-btn-wrapper">
					<div class="contact-email-btn contact-btn-img contact-nav-btn email"></div>
					<div class="contact-link">Email</div>	
				</div> -->
			</div>
            <div class='contact-close-btn'>&times;</div>
		</div>



		<div class="row contact-bottom-container">
			@include('admin.contactPane.contactPaneCall')
			@include('admin.contactPane.contactPaneMsg')
			@include('admin.contactPane.contactPaneText')
		
		</div>

	

	</div>

</div>
</div>




<!-- save template modal -->
<div id="saveTemplateModal" class="saveTemplateModal reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">

	<!-- foundation removes this from this place in DOM so want to store info related to container-->
	<input type="hidden" class="msg-content" />

	<div class="text-right mb20">
		<a class="close-reveal-modal" aria-label="Close">&times;</a>
	</div>
	
	<h5>Template Name:</h5>

	<input class="templateName" name="templateName" placeholder="Enter the template name" />

	<div class="contact-save-template-name">Save</div>

</div>

