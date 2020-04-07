@extends('public.footerpages.master')
@section('content')
	<div class='row'>
		<div class='text-center large-12 column'>
			<h1 class='header1'>Join as a College Prep Company</h1>
		</div>
	</div>
	<div class='row'>
		<!-- left column area -->
		<div class="small-12 column footerCollegePrepRightSide">
			<div class="row">
				<div class=" prepheader column small-10 small-centered text-center">
					With Plexuss you will get to chat with students, create a company profile, and have access to reporting and analytics tools.
				</div>
			</div>
			<div class="row">
				<div class="column small-12 medium-6 large-6 small-text-center medium-text-right"><img class='prepImage' src="/images/footerpages/prepschool1.png" alt=""></div>
				<div class="column small-12 medium-6 large-6 small-text-center medium-text-left"><img class='prepImage' src="/images/footerpages/prepschool2.png" alt=""></div>
			</div>
			<div class="row">
				<div class="column small-12">
					<div class="row collapse">
						<div class="column small-12 medium-6 medium-centered large-3">
							<a class="button" href='/signup?requestType=prep&utm_campaign=prep&utm_source=SEO&utm_medium={{$currentPage or ''}}'>Sign up</a>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="column small-12 text-center joingAsCollege">
					If you would like to join as a college, <a href="/college-submission">click here.</a>
				</div>
			</div>
		</div>
	</div>
@stop
