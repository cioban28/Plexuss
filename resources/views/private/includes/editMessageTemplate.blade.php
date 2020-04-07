<!-- modal that shows list of available templates to edit -->
<div id="edit-message-template-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-modal-name="EditModal">
	<div class="clearfix">
		<div class="right">
			<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&times;</a>
		</div>
	</div>

	<div class="edit-content">
		<div class="e-title"><b>Edit Templates</b></div>

		<ul class="template-items">
			<!-- inject template items here -->
		</ul>

		<div class="edit-options mt30 clearfix">
			<div class="left"><a href="" class="edit btn notallowed" disabled>Edit</a></div>
			<div class="left"><a href="" class="delete btn">Delete</a></div>	
		</div>	

		<div class="alertMsg"><!--msg injected here--></div>
	</div>
</div>

<!-- modal with tinymce text editor -->
<div id="edit-selected-template-modal" class="reveal-modal" data-reveal aria-labelledby="secondmodaltitle" aria-hidden="true" role="dialog" data-modal-name="EditSelectedModal">
	<div class="edit-title-container">
		<div class="temp-title toggler"><div><!--title gets injected here--></div><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/edit_icon_sm.png"></div>
		<div class="row collapse title-edit-form hide">
			<div class="column small-10">
				<input type="text" class="title-edit-input" />
			</div>
			<div class="column small-2">
				<div class="confirm text-center toggler">ok</div>
			</div>
		</div>
	</div>

	<div class="editor-container tinyMCE-editor-container">
		<!--tinymce inject here-->
		<textarea id="editMsgTemplate-editor"></textarea>
	</div>

	<div class="clearfix">
		<div class="left"><a href="" class="back" data-reveal-id="edit-message-template-modal"><u>back</u></a></div>
		<div class="right"><a href="" class="save btn">save</a></div>
		<div class="right"><a href="" class="delete btn">delete</a></div>
	</div>
</div>

<!-- are you sure modal -->
<div id="sureness-modal" class="reveal-modal" data-reveal aria-labelledby="secondmodaltitle" aria-hidden="true" role="dialog">
	<div class="text-center">Are you sure you want to delete this template?</div>
	<div class="clearfix">
		<div class="left yes btn">Yes</div>
		<div class="left no btn">No</div>
	</div>
</div>