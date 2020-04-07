<div class="column small-12 large-6 p20">


		<select id="msg_threads_dropdown" class="threads-dropdown">
			<option disabled="true" selected="true">Select Thread...</option>
		</select>
		

		<div class="msg-box messages-box">
			<!-- msgs go in here -->
			<!-- loader -->
			<!-- <div class="spinloader2"></div> -->
			Loading...
		</div>


		<!-- refresh -->
		<div class="refresh-btn-msg refresh-btn">
			<div class="refresh-icon"></div> Refresh
		</div>
	</div>

	<div class="column small-12 large-6 p20">
		<form id="messageForm" class="messageForm">
			<div>
				<select id="message_template_dropdown" class="templateDropdown" placeholder="Insert Template">
				</select>

				<input id="saveTemplate" class="saveTemplate" type="checkbox" name="saveTemplate"/>
				<label for="saveTemplate">Save as Template</label>

				<span class="edit-template-btn edit-msg-temp-link">Edit Template</span>
			</div>

			<div class="abs-wrapper">
				<div contenteditable="true" class="msgBody" name="messsage" placeholder="Type your message in here..." maxlength="160"></div>
				<div class="msg-feedback"></div>

				<div class="contact-charCount"><span class="contact-text-count">0</span> / 160 </div>
			
			</div>

			<div class="clearfix">
				
			
					
				<div class="attch-file-open"> 
					<div class="attatch-icon"></div> <span class="att-files-txt">File Attachments</span>
				</div>


			






				<div class="msgSubmit">Send</div>
			</div>

		</form>
	</div>