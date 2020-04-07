$(document).ready(function(){
	
});

$(document).on('click', '#unsubscribe-option-5', function(){
	$('.unsubscribe-reason').slideToggle(400);
});

$(document).on('click', 'input[type="submit"]', function(){

	$('.unsubscribe-email-ajax-loader').show();

	var email = $('input[name="email"]').val();
	var button = $(this).attr("value");

	if (button == "No") {
		window.location.href = '/';
	}else{
		var unsubscribe_option = $('input[name="unsubscribe-option"]:checked');
		var reason = $('textarea').val();
		if(unsubscribe_option.length != 0 && unsubscribe_option.val() != '') {
			reason = unsubscribe_option.val().trim();
		}

		$.ajax({
	        url: '/reasonToUnsubscribe',
	        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	        data: {email: email, reason: reason},
	        type: 'POST'
	    }).done(function(data, textStatus, xhr) {
			$('.unsubscribe-email-ajax-loader').hide();
			if(data == 'success') {
				$('div.unsubscribe-email').html('<span style="margin-top: 10px;display: inline-block;">Thank you for your feedback!</span>');	
			} else {
				$('div.unsubscribe-email').html('<span style="margin-top: 10px;display: inline-block;">Bad email given.</span>');
			}
		});

		$.ajax({
	        url: '/unsubscribe/' + email,
	        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	        data: null,
	        type: 'POST'
	    }).done(function(data, textStatus, xhr) {
			// console.log('unsubscribeThisEmail success');
		});
	}
	
});

$(document).on('click', 'a.unsubscribe-confirm', function(e){
	e.preventDefault();
	var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	var email = $('input[name="user_email_addr"]').val();
	email = email.trim();

	if(email != '' && email.match(regex)) {
		$('.error').css('display', 'none').html('');
		window.location =  '/unsubscribe/' + email;		
	} else {
		$('.error').css('display', 'block').html('*Please enter a valid Email Address');
	}
});