// js file for all http error views

//return to previous page in which user came from when 'Get back on track' button is clicked
$(document).on('click', '.get-back-on-track-btn', function(){
	if (history.length <= 1) {
	    window.location.href = '/';
	}else {
		history.go(-1);
	}
});
