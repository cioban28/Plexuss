@extends('public.footerpages.masterTemplate')

@section('content')
<div class='formbox row collapse'>
	<div class="small-12 medium-8 medium-centered column">
		<div class='row'>
			<div class="large-12 column">
				<div class="row text-center">
					<div class="white-container">
							<h2 class="header2">Thank you {{Auth::user()->fname}}!</h2>
							<p class="text-center">Thank you {{Auth::user()->fname}}, our team is going to review your request.<br />
								If approved you will recieve an email to setup your scholarship<br />
								criteria/targeting.
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
