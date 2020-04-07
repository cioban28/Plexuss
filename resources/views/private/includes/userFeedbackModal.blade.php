
<div id="userFeedbackModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-school-name="{{$user_feedback_college->school_name}}">

	<div class="text-center question">Since joining Plexuss, have you submitted an application to <span></span>?</div>

	<div class="school-banner" style="background-image: url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/{{$user_feedback_college->img_url or 'no-image-default.png'}})">
		<div class="school-logo" style="background-image: url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$user_feedback_college->logo_url or 'default-missing-college-logo.png'}})"></div>
	</div>

	<div class="actions text-center">
		<div>
			<button class="yes radius" 
					data-pr="{{$user_feedback_college->pr_id}}"
					data-rec="{{$user_feedback_college->rec_id}}">Yes</button>
		</div>

		<div>
			<button class="no radius" 
					data-pr="{{$user_feedback_college->pr_id}}"
					data-rec="{{$user_feedback_college->rec_id}}">No</button>
		</div>
	</div>

	@if( isset($user_feedback_college->app_url) )
	<div class="app-link text-center">
		You can <a id="collegeapplynow"
									href="/college-application"
									target="_blank" 
									data-url="{{$user_feedback_college->app_url}}"
									data-slug="{{$user_feedback_college->slug or ''}}">click here</a> to apply
	</div>
	@endif

</div>

<div id="appliedToOtherSchoolsModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">

	<div class="text-center question">Have you applied to any other schools?</div>

	<div class="search">
		<div style="position: relative;">
			<input id="_search_applied" type="text" name="search" placeholder="Schools you've applied to...">
			<div id="applied_search_results" class="search-results stylish-scrollbar-mini">
			</div>
		</div>
	</div>

	<div id="_applied_results" class="applied-results stylish-scrollbar-mini"></div>

	<div class="actions text-center">
		<div><button class="submit radius" disabled="disabled">Submit</button></div>
		<div class="havent-applied"><u>I haven't applied to any other schools</u></div>
	</div>
	
</div>

<script src="/js/userFeedback.js" defer></script>
