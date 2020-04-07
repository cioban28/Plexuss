<div id="inviteFriendsModal" class="reveal-modal" data-email="{{$email}}" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">

	<div class="invite-head">
		<h3 class="text-center">I know you just met us, but we can help your friends find colleges too!</h3>
	</div>

	<div class="invite-head dynamic">
		<h3 class="text-center">Start here. Begin inviting your friends.</h3>
	</div>

	<div class="row invite-options-row hidden">
		<div class="column small-6 medium-3 text-center">
			<div class="invite-opt">
				<a href="/googleInvite">
					<div class="gmail" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/import-contacts-sprite-sheet_gray.png, (default)]"></div>
					<div class="opt-label">Gmail</div>
				</a>	
			</div>
		</div>
		<!--<div class="column small-6 medium-3 text-center">
			<div class="invite-opt">
				<a href="/yahooInvite">
					<div class="yahoo" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/import-contacts-sprite-sheet_gray.png, (default)]"></div>
					<div class="opt-label">Yahoo</div>
				</a>
			</div>
		</div>-->
		<div class="column small-6 medium-3 text-center">
			<div class="invite-opt">
				<a href="/microsoftInvite">
					<div class="outlook" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/import-contacts-sprite-sheet_gray.png, (default)]"></div>
					<div class="opt-label">Outlook</div>
				</a>
			</div>
		</div>	
		<div class="column small-6 medium-3 end text-center">
			<div class="invite-opt">
				<a href="/microsoftInvite">
					<div class="hotmail" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/import-contacts-sprite-sheet_gray.png, (default)]"></div>
					<div class="opt-label">Hotmail</div>
				</a>
			</div>
		</div>
	</div>

	<br />

	<div class="invite-mid">
		<h5 class="text-center">Choose the contacts you would like to invite to Plexuss</h5>
	</div>

	<!-- invite friends manage contacts - start -->
	<div class="invite-friends-contact-list-container">
	    {{Form::open(array('url' => '','method' => 'POST','data-abide'))}}
		    <!-- select all checkbox -->
		    <div class="inner-contact-list-container">
			    <div class="row contact-row select-all">
			    	<div class="column small-12">
				        {{Form::checkbox('select-all', 'select-all', true, array('id' => 'select-all-contacts-checkbox', 'class'=>''))}}
				        {{HTML::decode( Form::label('select-all-contacts-checkbox', 'Select All <span class="contact-list-count"> &nbsp;</span>') )}}
			        </div>
		        </div>
				
				<div class="scrolling-list-of-contacts">
                    @if( isset($contactList) && !empty($contactList) )

						@foreach( $contactList as $contact )
							<div class="row contact-row contact-invitee selected">
								<div class="column small-1">
									{{Form::checkbox('', '', true, array('id' => $contact['invite_email'], 'class'=>'contact-checkbox', 'data-name' => $contact['invite_name']))}}
								</div>
								<div class="column small-11 large-3 name">
									{{$contact['invite_name'] == ''? 'No Name' : $contact['invite_name']}}
								</div>
								<div class="column small-11 large-8 emailaddr">
									{{$contact['invite_email']}}
								</div>
							</div>
						@endforeach

					@else
						<div class="row no-contacts-yet-msg-row text-center">
	                        <div class="column small-12 no-contacts-yet-msg">
	                            You haven't imported contacts yet!
	                        </div>
	                    </div>
					@endif
				</div>
			    
			    <!-- ajax loader -->
			    <div id="invite-friends-modal-ajax-loader" class="row text-center">
			        <div class="column small-12">
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
		    </div>

		    <div class="text-center invite-contacts-button-row">
				<button class="send-these-invites-btn">send invites</button>
				<br class="show-for-small-only" />
				<span>&nbsp;&nbsp;&nbsp; <b>or</b> &nbsp;&nbsp;&nbsp;</span>
				<br class="show-for-small-only" />
				<br class="show-for-small-only" />
				<button class="start-plexuss-btn">start plexuss</button>
			</div>
	    {{Form::close()}}
	</div>
    <!-- invite friends connect with gmail - end -->

	
</div>