{{Form::open()}}
<div class="column small-12 medium-12 large-7">
	<div class="row free-trial">
		<div class="column small-12 medium-12 large-12 back-to-dash">
			<a href="../admin">Back</a>
		</div>

		@if(!isset($purchased_phone) || empty($purchased_phone))
		<div class="column small-12 medium-12 large-12">
			<span>Looks like it's your first time here, before you can start texting you need a number to text from.</span>
		</div>
		<div class="column small-12 medium-12 large-12">
			<span>Let's get you set up with a number.</span>
		</div>
		@elseif(isset($purchased_phone) && isset($textmsg_tier) && $textmsg_tier == 'flat_fee' && isset($textmsg_expires_date) && !empty($textmsg_expires_date) && $current_time->gt($textmsg_expires_date))
		<div class="column small-12 medium-12 large-12">
			<span>Your free trial number already expired. Please set up with a new number.</span>
		</div>
		@endif
		<div class="column small-12 medium-12 large-12 toll-free-number">
			<span>Do you need a toll free number?</span>
			{{Form::radio('isTollFree', 'yes', null, array('class' => '', 'id' => 'get-toll-free-number'))}}
			{{Form::label('get-toll-free-number', 'Yes')}}
			{{Form::radio('isTollFree', 'no', true, array('class' => '', 'id' => 'no-toll-free-number'))}}
			{{Form::label('no-toll-free-number', 'No')}}
		</div>
		<div class="column small-12 medium-4 large-4 input-area-code">
			<span>Select a number based on area code, zip. phrase</span>
			{{Form::text('term', '', array('class' => '', 'placeholder' => 'Area code, ZIP code or Phrase'))}}
			<span>or select a number based on states</span>
			{{Form::select('InRegion', $states)}}
		</div>
		<div class="column small-12 medium-4 large-4 qa">
			<span>Why do I need a number?</span>
			<span>Students will receive texts from this number and it will be associated with your account.</span>
		</div>
		<div class="column small-12 medium-12 large-12 search-number end">
			<a href="#" class="button success">Search</a>
		</div>
	</div>
	
	<div class="row phone-list-available hide">
		<div class="column small-12 medium-12 large-7 end phone-list-view">
		</div>
		<div class="column small-12 medium-12 large-6 text-center end">
			<a href="#" class="show-more-phonelist"></a>
		</div>
	</div>

	<div class="row setup-phone-number hide">
		<div class="column small-12 medium-12 large-12 back-to-search-number">
			<span>Back</span>
		</div>
		<div class="column small-12 medium-12 large-8">
			<span>Your number is <p class="number-details"></p></span>
			<span>This number will expire in 30 days if you do not upgrade to a paid plan.</span>
			<span>To get you started we've added <p class="plan-desc">500 free text messages(sent/received)</p> to your account. You can change your plan at anytime.<br/> Restrictions apply (see the list of countries to the right).</span>
			<span>The next thing you need to do is choose a list from your inquiries, or upload your own.</span>
		</div>
		<div class="column medium-12 large-offset-1 large-3 show-for-medium-up countries-for-free-trial">
			<div>
				<div class="title">
					<span>Applicable free trial countries</span>
				</div>
				<ul>
					@foreach($free_text_countries as $key => $free_text_country)
					<li class="country-item">
						<div class="flag flag-{{strtolower($free_text_country->country_code)}}"></div>
						<div class="country-name">{{$free_text_country->country_name}}</div>
					</li>
					@endforeach
				</ul>
			</div>
		</div>
		<div class="column small-12 medium-12 large-12 forward-to-textmsg">
			<a href="#" class="button success">Continue</a>
		</div>
	</div>
</div>
<div class="column small-12 medium-12 large-5 text-video">
	<span>Learn how texting works</span>
	<iframe src="https://player.vimeo.com/video/180974771?title=0&byline=0&portrait=0" width="400" height="225" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
</div>
{{Form::close()}}