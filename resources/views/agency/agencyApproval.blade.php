@extends('agency.master')
@section('content')

	<div class="agency-approval-container">
		<div class="row">
			<div class="small-11 medium-10 large-6 small-centered columns text-center approval-txt">
				<div>{{$agency->name or 'N/A'}}</div>
				<div>will contact you shortly to see how they can help</div>
			</div>
		</div>

		<div class="row">
			<div class="small-11 medium-6 large-6 small-centered columns">
				<br />
				<div class="question-section text-center">
					<div class="text-center approval-txt">If you have any questions for them now, go ahead and ask below.</div>	
					<br />
					<textarea class="notes-textarea" name="message" cols="20" rows="10"></textarea>
					<div class="text-center"><button onClick="Plex.agencyApproval.sendMessageToAgency(this);" class="ask-agent-btn" data-agency-id="{{$agency->agency_id}}" data-thread-id="{{$thread_id}}">Ask a question</button></div>
				</div>
				<div class="text-center approval-txt">You will be able to continue messaging this agent from your <a href="/portal/messages">Portal</a>.</div>
			</div>	
		</div>
	</div>

@stop