<?php 


?>

<!-- Mobile SIC background -->
<div class="mobile_sic-background"></div>

<!--SIC-->
<div id="_sic"  data-prem="{{$premium_user_type }}">

	<div class="sic-content-wrapper clearfix">
	
		<div class="text-right mr10" title="close"><span class="close-sic-btn">&times;</span></div>
		<div class="prem-member">
			<div class="title">Premium Membership</div>
			<div class="membership">
				<div class="plan">

					<div class="badge @if( !empty($premium_user_type) ) {{$premium_user_plan}} @endif )"></div>
					@if( !empty($premium_user_type) )
						{{ $premium_user_plan }} 
					@else
					<span class="non-prem-txt">Non Premium Plan</span>
					@endif
				</div>
			</div>
		</div>

		<div class="sic-row clearfix">
			<a class="diff apps" href="/college-application/">
				<span class="hide-for-small-only txt-deco-under">Applications Remaining</span>
				<div class="num">{{$num_of_eligible_applied_colleges or '0'}}</div>
				<div class="show-for-small-only txt-deco-under">Applications Remaining</div>
			</a>
			<a class="diff essays"  href="/news/catalog/college-essays">
				<span class="hide-for-small-only txt-deco-under">Essay Views Remaining</span>
				<span class="num">{{$num_of_eligible_premium_essays or '0'}}</span>
				<div class="show-for-small-only txt-deco-under">Essay Views Remaining</div>
			</a> 
		</div>

		<div class="diff schedule">
			<a href="#" data-reveal-id="interview_scheduler" class="schedule-btn">Schedule Interview</a>

			<div id="_skyper" class="skype-cont">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon_sic.png" />
				<a id="_skypeCall" href="skype:live:premium_156?call">Call</a>
				<a id="_skypeChat" href="skype:live:premium_156?chat">Chat</a>
			 </div>
		</div>

		<!-- ///// application status /////-->
		<div class="app-status-wrapper">
			<div class="app-status app-status-meter-cont sic-row">

				<div class="clearfix">
					<div class="title hide-for-small-only">Application Status</div>
					
					<div class="profile">
						<div class="percent">
							<div class="meter">
								<svg class="CircularProgressbar" viewBox="5 0 100 100" height="90px" width="90px">

									<circle class="CircularProgressbar-trail" r="30" cx="50" cy="50" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
									<circle class="CircularProgressbar-path" id="bar" r="30" cx="50" cy="50" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>

								</svg>
							</div>
							<span id="profile-percentage" class="percent-txt" data-percent="{{$profile_percent or '0'}}">{{$profile_percent or '0'}}%</span>
						</div>

						<span class="show-for-small-only app-stat-txt">Application Status</span>
						<span class="expand-stat-btn show-for-small-only">+</span>
						
						<!-- needs to send to point in app -->
						<div class="edit hide-for-small-only"><a class="sic-continue-btn" href="/college-application/">Continue?</a></div>
					</div>
				</div>

				<div class="stats-container">
					<div class="section-name completeToggle-name">Complete Sections <span class="completeToggle">&ndash;</span>
						<div class="sections-list complete"></div>
					</div>

					<div class="section-name inompleteToggle-name">Incomplete Sections <span class="incompleteToggle">&ndash;</span>
						<div class="sections-list incomplete">
							<div class="s-item"></div>
						</div>
					</div>

					<a class="review-route" href="/college-application/review">Review Application</a>
					
				</div>
			</div>

			<div class="submitted-apps">
				<div class="title">Submitted Applications</div>
				<div class="subm-school"></div>
			</div>
	
		</div>	

		<!-- //////  non premium members //////
		<div class="prem-msg-cont">
			<div class="title"> Sign up for Premium Services and Apply to Colleges!</div>
			<div class="signup-btn mt30"><a href="/premium-plans-info">Join Premium!</a></div>

			<div class=" small mt20">Limited to colleges in the Plexuss network</div>

		</div>
	

		<div>
		<div class="intl-title">
			<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon_hover.png"/>International Students
		</div>
		<div class="intl-btn"><a href="/international-students">College Expense Breakdown</a></div>
		</div>-->
		<!-- end non premium members -->

		

		<!-- back to top covers bottom - need margin-->
		<div class="mt50"></div>


	</div><!-- end content wrapper -->
</div>





<div id="interview_scheduler" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
	<div class="text-right"><a class="close-reveal-modal" aria-label="Close">&#215;</a></div>
	<iframe src="https://plexuss-premium.youcanbook.me/?noframe=true&skipHeaderFooter=true" id="ycbmiframeplexuss-premium" style="width:100%;height:871px;border:0px;background-color:transparent;" frameborder="0" allowtransparency="true"></iframe>
</div>

<script src="/js/oneAppConstants.js"></script>
<script type="text/javascript" data-is_signed_in="{{ $signed_in }}" src="/js/smartInteractiveColumn.js" defer></script>