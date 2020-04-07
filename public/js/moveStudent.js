// /js/moveStudent.js

var _Move = {
	fitBtnStream$: null,
	competitorBtnStream$: null,
	statusBtnStream$: null,

	initRx: function(fitBtn, competitorBtn, appliedBtn, fitCarousel, competitorCarousel, appliedCarousel, statusBtn){
		// save btns
		if (appliedBtn.length > 0) {
			appliedBtn.addClass('active'); // make appliedBtn active
			fitCarousel.hide();
		} else {
			fitBtn.addClass('active');
		}

		competitorCarousel.hide();


		// add event handlers to fitBtn and competitorBtn and subscribe to event and handle w/ this.toggleCarousels
		this.fitBtnStream$ = Rx.Observable.fromEvent(fitBtn, 'click')
										  .subscribe(function(e){
										  		fitBtn.addClass('active');

									  			appliedBtn.removeClass('active');
									  			competitorBtn.removeClass('active');

								  				appliedCarousel.hide();
								  				competitorCarousel.hide();

								  				fitCarousel.show();
										  });

		this.competitorBtnStream$ = Rx.Observable.fromEvent(competitorBtn, 'click')
												 .subscribe(function(e){
											  		competitorBtn.addClass('active');

										  			fitBtn.removeClass('active');
										  			appliedBtn.removeClass('active');

									  				fitCarousel.hide();
									  				appliedCarousel.hide();

									  				competitorCarousel.show();
												 });

		this.appliedBtnStream$ = Rx.Observable.fromEvent(appliedBtn, 'click')
											  .subscribe(function(e){
											  		appliedBtn.addClass('active');

										  			fitBtn.removeClass('active');
										  			competitorBtn.removeClass('active');

									  				fitCarousel.hide();
									  				competitorCarousel.hide();

									  				appliedCarousel.show();
											  });

		this.statusBtnStream$ = Rx.Observable.fromEvent(statusBtn, 'click')
											 .subscribe(function(e){
												$(e.target).parent().find('.move-student-menu').toggle();
											 });
	},
};