@extends('public.footerpages.masterTemplate')

@section('content')
<div class='formbox row collapse'>
	<div class="small-12 medium-6 medium-centered column">
		<div class='row'>
			<div class="large-12 column">
				<div class='row'>
					<div class='large-12 column'>
						<h1 class="text-center">Choose the service you are interested in</h1>
					</div>
				</div>
				<div class="row text-center">
				   <a href="scholarship-intrest/free" class="white-button">Scholarship Free Services<span data-tooltip aria-haspopup="true" style="float:right;" title="Students will be introduced to your scholarship and they can show interest. Their info will be accessible via Plexuss Free CRM.">&#9432;</span></a>
					<a href="scholarship-intrest/promote" class="white-button" >Scholarship Premium Services<span data-tooltip aria-haspopup="true" style="float:right;" title="Your scholarship will have a featured place on Plexuss. Students will be provided with a link to your scholarship site. You will be provided with Plexuss CRM to track and view every student who has expressed interest.">&#9432;</span></a>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
