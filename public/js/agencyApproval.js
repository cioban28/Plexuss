// agencyApproval.js
Plex.agencyApproval = {
	postMsg_route: '/ajax/messaging/postMsg',
	list_of_new_features: [],
	start_index: 0,
	next_index: 1,
	list_length: 0,
	after_doc_ready: false,
	features_interval: null,
}

Plex.agencyApproval.sendMessageToAgency = function(elem){

	var agencyId = $(elem).data('agency-id');
	var threadId = $(elem).data('thread-id');
	var msg = $('.agency-approval-container .notes-textarea').val();
	var threadType = 'agency-msg';
	var thankyou_msg = '<div>Thank you for your message!</div>';
	var msgData = {
		agency_id: agencyId,
		message: msg,
		thread_id: threadId,
		thread_type: threadType,
		to_user_id: null
	};

	$.ajax({
		url: '/ajax/messaging/postMsg',
		type: 'POST',
		data: msgData,
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(ret){
		$('.agency-approval-container .question-section').html(thankyou_msg);
	});
}

// -- on click send ajax call to set first_time_agent to 0 to remove welcome layer - start
$(document).on('click', '#agency .get-started-btn-link', function(e){
	e.preventDefault();
	Plex.agencyApproval.notFirstTimeAgentAnymore();
});

Plex.agencyApproval.notFirstTimeAgentAnymore = function(){
	$.ajax({
		url: '/agency/ajax/notFirstTimeAgentAnymore',
		type: 'POST',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(ret){
		window.location.href = '/agency/settings';
	});
}
// -- on click send ajax call to set first_time_agent to 0 to remove welcome layer - end
$(document).ready(function(){

	//start interval
	var new_features = $('.feature-toggler-container');

	//get and save all new features in array
	$.each(new_features, function(){
		Plex.agencyApproval.list_of_new_features.push(this);
	});

	//set the number of new features
	Plex.agencyApproval.list_length = Plex.agencyApproval.list_of_new_features.length;

	//start sliding interval - only ran on initial load
	Plex.agencyApproval.features_interval = setInterval(function(){ 
		Plex.agencyApproval.newFeatureSlider();
	}, 7000);

}); 

// -- new feature slider
//when user is this tab is not active (if user in on a different tab/window), then stop the interval
$(window).blur(function(){
	clearInterval(Plex.agencyApproval.features_interval);
	Plex.agencyApproval.after_doc_ready = true;
});

//start interval again when user back - ran after the initial interval has been ran and cleared
$(window).focus(function(){
	if(Plex.agencyApproval.after_doc_ready){
		Plex.agencyApproval.features_interval = setInterval(function(){ 
			Plex.agencyApproval.newFeatureSlider();
		}, 7000);
	}
});

//logic for cycling through each new feature
Plex.agencyApproval.newFeatureSlider = function(){
	var start_over = 0;

	//perform slide effect
	Plex.agencyApproval.slideHide( Plex.agencyApproval.list_of_new_features[Plex.agencyApproval.start_index], Plex.agencyApproval.list_of_new_features[Plex.agencyApproval.next_index], 'left', 750 );

	//increment array index
	Plex.agencyApproval.start_index++;
	Plex.agencyApproval.next_index++;

	//check index have reached the end, if so, start counter over
	if( Plex.agencyApproval.next_index >= Plex.agencyApproval.list_length ){
		Plex.agencyApproval.next_index = start_over;
	}else if( Plex.agencyApproval.start_index >= Plex.agencyApproval.list_length ){
		Plex.agencyApproval.start_index = start_over;
	}
}

//slide hide
Plex.agencyApproval.slideHide = function( hide_elem, show_elem, dir, speed ){
    
    var oppositeDirection = 'left';

    if( dir === 'left' ){
        oppositeDirection = 'right';
    }

    $(hide_elem).hide('slide', {direction: dir}, speed, function(){
        Plex.agencyApproval.slideShow( show_elem, oppositeDirection, speed );
    });
}

//slide show
Plex.agencyApproval.slideShow = function( elem, dir, speed ){
    $(elem).show('slide', {direction: dir}, speed);
}
// -- new feature slider

// Track number of clicks on download button 
$(document).on('click', '.dl_button', function(){

	mixpanel.track("Export_Students",
		{
			"location": document.body.id
		}
	);
});