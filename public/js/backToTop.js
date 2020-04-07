$(document).ready(function(){
	//Scroll Up when user clicks Back to Top
	$('.backToTop').click(function(){
		$("html, body").animate({ scrollTop: "0px" }, 250);
	});

	// Show the btt button when user is scrolled
	// further than the height of their viewport
	var timer;
	$(window).scroll(function(event){
		if(timer){
			 clearTimeout(timer);
		}
		timer = setTimeout(function(){
			var scrolled = $(window).scrollTop();
			var win_height = $(window).height() * 0.75;
			var element = $('.backToTop');
			// If the distance scrolled is greater than
			// that of the viewport, show the btt button
			// otherwise, hide it
			if(scrolled > win_height){
				if($('.backToTop').is(':hidden')){
					element.slideDown(125, 'easeInOutExpo');
				}
			}else{
				if($('.backToTop').is(':visible')){
					element.slideUp(125,'easeInOutExpo');
				}
			}
		}, 270);
	});
});
