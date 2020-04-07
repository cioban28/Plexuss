var Admitsee = {
		ajaxUnlock: false /* to make sure that ajax not sent twice to unlock -- will decrement number of essays left multple time on sic if so */
	};


$(document).ready(function(){
	
	//handler for close button on message modal
	$(document).on('click', '#admitseeMessageModal .close-reveal-modal', function(){ 
		$('#admitseeMessageModal').foundation('reveal', 'close');

	});


	//set dynamic height for blured portion of essay, do not want too much blur space (visuals/UX)
	$('.premium-screen').height( $('.pre-blur').height());


	//////// unlock essay handler
	$('.unlock-essay').click(function(){

		if(Admitsee.ajaxUnlock === true)
			return;


		Admitsee.ajaxUnlock = true;

		var that = $(this);

		var nid = $('#newsContainer').data('nid');

		//send ajax to set essay unlocked for user
		//user, essay

		$('#unlockajaxloader').show();

		$.ajax({
			type: 'POST',
			url: '/news/purchaseEssay',
			data: {
					news_hashed_id: nid
				  },
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(response){

			$('#unlockajaxloader').fadeOut();

			Admitsee.ajaxUnlock = false;

			var res = JSON.parse(response);

			if(res['status'] === 'failed'){

				if( res['error'] === 'Insufficient funds'){
					$('#admitseeMessageModal .insufficientfunds-error').show();	
					$('#admitseeMessageModal .invalid-error').hide();
				}
				else{
					$('#admitseeMessageModal .insufficientfunds-error').hide();
					$('#admitseeMessageModal .invalid-error').show();
					var msg = 'The Essay could not be unlocked: ' + res['error'];
					$('#admitseeMessageModal .invalid-error .modal-message').text(msg);
				}

				$('#admitseeMessageModal').foundation('reveal', 'open');
			}
			else{

				var prem = that.closest('#essayWrapper').find('.essay-cont');
				var txt = res['content'];
				var blur = that.closest('#essayWrapper').find('.pre-blur');

				prem.html(txt);

				//in callback -- if successful
				that.closest('.unlock-cont').hide();
				blur.fadeOut();
				prem.fadeIn();

				
				var num = $('#_sic .essays .num').text() || $('#_SIC .essays .num').text();
				num--;

				$('#_sic .essays .num').text(num);
				$('#_SIC .essays .num').text(num);
				// sic.essayNum = num;

			}
		});
		//in callback -- if not successful -- ?
	});

});