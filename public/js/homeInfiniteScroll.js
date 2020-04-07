$(document).ready(function() {
	var skipAmount = 6;
	var ajaxHold = 0;

	$(window).scroll(function() {
		if ($(window).scrollTop() + 2 >= $(document).height() - $(window).height()) {
			
			if(ajaxHold === 0) {
				ajaxHold = 1;
				scrollInfinite();
			}
		}
	});

	function scrollInfinite() {
		$('div#loadmoreajaxloader').show();

		$.ajax({
			url: "/home/load-more-news",
			method: "POST",
			data: { skipAmount: skipAmount },
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(html){
			$('div#loadmoreajaxloader').hide();

			$("#container-box").append(html);

			imagesLoaded('#container-box', function() {

				$('#container-box').masonry('reloadItems').masonry();

			});

			ajaxHold = 0;
			skipAmount += 6;


		})
		.fail(function(){
			$('div#loadmoreajaxloader').html('');
		});
	}
});