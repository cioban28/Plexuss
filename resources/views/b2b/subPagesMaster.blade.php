<div class="b2b-subcont-wrapper">


@if($b2b_subpage == '_Web')
	<div class="webinar-back"></div>
@endif

	<!-- top section -->
	<div class="b2bsub-top">
		<div class="b2b-row86">


			<div class="clearfix">

				<!-- left -->
				<div class="left">
					@section('top_left')
					@show
				</div>

				<!-- right -->
				<div class="right txt-center">
					@section('top_right')
					@show
				</div>
			</div>

			<!-- top section bottom text -->
			<div class="features_desc mb50">
			@section('top_desc')
			@show
			</div>

		</div>

		@if($b2b_subpage == '_Comm' ||  $b2b_subpage == '_CRM')
			<div class="scroll-caron hovering"><div class="s-caron">v</div></div>
		@endif


		@if($b2b_subpage != '_Web')
			<div class="pb50"></div>
		@endif

	</div><!-- top section -->


	<!-- bottom section  -->
	<div id="b2b-bottom" class="b2bsub-bottom body-gradient">
		<div class="b2b-9rownm mb50">
			@section('bottom')
			@show
		</div>
	</div><!-- end bottom section -->



</div>

	<!-- ///////////////////// email form ////////////////-->
	<div class="b2b-email-cont b2b-row">
				<h3>Sign up to receive updates from our blog, press releases, and new features.</h3>
				<form  class="newsletter-form" data-abide>
					<input placeholder="Your email address" pattern="email" type="text" name="news_email" required/>
					<div class="b2b-email-submit">Sign Up</div>
					<div class="email-ty-msg mt20 clr-fff"></div>
					<small class="email-err"></small>
				</form>
	</div>




@include('b2b.proposalForm')