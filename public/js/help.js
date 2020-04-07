$(document).ready(function(){
	//animate rotate function
	$.fn.animateRotate = function(angle, duration, easing, complete) {
		var args = $.speed(duration, easing, complete);
		var step = args.step;
		return this.each(function(i, e) {
			args.complete = $.proxy(args.complete, e);
			args.step = function(now) {
				//$.style(e, 'transform', 'rotate(' + now + 'deg)');
				$(e).css({
					'transform': 'rotate(' + now + 'deg)',
					'-o-transform': 'rotate(' + now + 'deg)',
					'-ms-transform': 'rotate(' + now + 'deg)',
					'-moz-transform': 'rotate(' + now + 'deg)',
					'-webkit-transform': 'rotate(' + now + 'deg)',
				});
				if (step) return step.apply(e, arguments);
			};
			$({deg: 0}).animate({deg: angle}, args);
		});
	};
	//
	// Expanding pin functionality
	$('.gs-expand-buttons').click(function(){
		var card = $(this).parent();
		//Change colors 
		card.toggleClass('gs-expanded', function(){
			// Toggle in both tell me how buttons
			card.children('.gs-expand-buttons').fadeToggle(250, 'easeInOutExpo');
			// Toggle link
			card.children('.gs-link-button').fadeToggle(250, 'easeInOutExpo');

			//Animate close button
			closex = card.children('.gs-close-x');
			closex.fadeOut(50, function(){
				closex.toggleClass('gs-close-x-light gs-close-x-dark', function(){
					closex.fadeIn(50, function(){
						closex.animateRotate(360, 1000, 'easeInOutElastic');
					});
				});
			});
			// Show hidden content
			card.children('.gs-expandable').slideToggle(250, 'easeInOutExpo', function(){
				//Hide close text
				card.children('.gs-close-text').fadeToggle(250, 'easeInOutExpo');
				$('#container-box').masonry();
			});
		});
	});

	// Sets card delay on page load
	$('#card_delay').data('card_delay', 4500);
	//close help pins ajax
	$('.gs-close').click(function(){
		var card_delay_div = $('#card_delay');
		var card_delay = card_delay_div.data('card_delay');
		var pin = $(this).parent();
		var closex = pin.children('.gs-close-x');
		//Spin the close button
		closex.animateRotate(360, 800, 'easeInOutElastic', function(){
			//Flip the card/pin
			pin.css({
				'transform': 'rotateY(180deg)',
				'-o-transform': 'rotateY(180deg)',
				'-ms-transform': 'rotateY(180deg)',
				'-moz-transform': 'rotateY(180deg)',
				'-webkit-transform': 'rotateY(180deg)'
			});
			pin.next('.gs-back').css({
				'transform': 'rotateY(0deg)',
				'-o-transform': 'rotateY(0deg)',
				'-ms-transform': 'rotateY(0deg)',
				'-moz-transform': 'rotateY(0deg)',
				'-webkit-transform': 'rotateY(0deg)'
			});
			// Wait x time before...
			setTimeout(function(){
				//sliding the card up
				pin.parent().slideUp(350, 'easeInOutExpo', function(){
					setTimeout(function(){
						// Delay x time before rearranging cards
						$('#container-box').masonry();
						// Find number of visible gs cards and if none...
						if($('.gs-container:visible').length == 0){
							// Slide the orange get-started circle arrow up
							$('#gs-big-orange').slideUp(250, 'easeInOutExpo');
						}
					}, 100);
				});
		}, card_delay);
		});
		card_delay_div.data('card_delay', 4500);
		//Tell DB to remember closed pins
		var pin_number = $(this).parent().data('gs_pin');
		$.ajax({
			type: 'GET',
			url: '/home/close-getting-started-pin',
			dataType: 'json',
			data: { pin: pin_number },
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data){
			}
		});
	});
	
});
