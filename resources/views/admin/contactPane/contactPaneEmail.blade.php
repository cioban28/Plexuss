<div  id="_contactEmail" class="contact-section-container">

	<div class="column small-12 large-6 p20">
		<div class="email-box msg-box">
			<!-- msgs go in here -->
			<!-- for each date -->
			<div class="date-divide">
				<div class="contact-date">04/18/2017</div>
			</div>

			
				<!-- for each author's turn -->
				<div class="contact-msg-display-wrap clearfix">


 					<!-- name of who sent -->
					<div class="contact-msg-name">


						<div class="chat-portrait">

							<!-- if user has portrait -->
							
								
								<!-- if the college,  data['user_id'] == msg user_id -->

								<!-- else -->
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png" alt="">
								
						
						</div>
						<div class="contact-poster-name">J Lee</div>
						
					</div>
					<!-- <div class="contact-msg-msg fr clearfix"> -->

					
					<!-- time sent -->
					<div class="contact-time">8:10AM</div>


					<!--  message  -->
					<div class="contact-msg-msg-col">
						<div class="contact-e-subject">
							Subject: &nbsp; 
							Interested in Attending UCLA
						</div>
						
						<div class="email-body-cont">
						Hi! Sample email here.
						</div>
					</div>

				</div>
			<!-- end for each date -->

		</div>

		
		<!-- refresh -->
		<div class="refresh-btn-msg refresh-btn">
			<div class="refresh-icon"></div> Refresh
		</div>

		
	</div>
	<div class="column small-12 large-6 p20">
		<form id="messageForm" class="messageForm">
			<div>
				<select id="message_template_dropdown" class="templateDropdown">
					<option>Insert Template</option>
				</select>

				<input id="saveTemplate" type="checkbox" name="saveTemplate"/>
				<label for="saveTemplate">Save as Template</label>

				<span class="edit-template-btn">Edit Template</span>
			</div>

			<div class="abs-wrapper">
				<input class="contact-emailSubject" name="emailSubject" placeholder="Email subject line..." />
				<textarea id="msgBody"  class="msgBody" name="messsageBody" placeholder="Type your message in here..."></textarea>
				<div class="msg-feedback"></div>
			</div>

			<div class="clearfix">
				
				<div class="attchFiles"> 
					<div class="attatch-icon"></div> <span class="att-files-txt">Attach Files</span>
				</div>
				<input class="custom-attch-input" name="msg-attatch" type="file" title=" "/>


				<div class="add-sample-docs">Add Sample Documents</div>
				
				<div class="emailSubmit">Send</div>
			</div>

		</form>
	</div>

</div>