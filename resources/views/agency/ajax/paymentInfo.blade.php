<div class="row agency-settings-section-paymentInfo">
	<div class="column small-12 large-6">
		
		<div>Add your Paypal Account</div>
		@if( isset($agency_collection) && $agency_collection->is_trial_period == 1 )
			<div class="trial-msg">Your <u>10</u> day trial period is currently active</div>
			<div class="trial-directions">We recommend adding your payment info now, so there is no interruption in your services. If you don't add your payment info now, we will notify you when your trial has expired. You can only view student's information after you have paid.</div>
		@endif
		<div class="charged-msg">You will be charged $5 per approved student. You will then be able to view students documents and contact information.</div>
		<!--<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ccp/paypal-btn.png" alt="Paypal">
		<div class="text-center setup-paypal-btn">Setup your Paypal</div>-->
		<div>Automatic payment processing is still under construction. Please contact college services at <a href="mailto:collegeservices@plexuss.com?Subject=I%20would%20like%20to%20add%20credit!" target="_top">collegeservices@plexuss.com</a> to add credit.</div>

	</div>
</div>