// /js/moveStudent.js

var _Move = {
	fitBtnStream$: null,
	competitorBtnStream$: null,
	statusBtnStream$: null,

	initRx: function(fitBtn, competitorBtn, fitCarousel, competitorCarousel){
		// save btns
		if (competitorBtn.length > 0) {
			competitorBtn.addClass('active');
			fitCarousel.hide();
		} else {
			fitBtn.addClass('active');
			competitorCarousel.hide();
		}

		// add event handlers to fitBtn and competitorBtn and subscribe to event and handle w/ this.toggleCarousels
		this.fitBtnStream$ = Rx.Observable.fromEvent(fitBtn, 'click')
										  .subscribe(function(e){
										  		fitBtn.addClass('active');

									  			competitorBtn.removeClass('active');

								  				competitorCarousel.hide();

								  				fitCarousel.show();
										  });

		this.competitorBtnStream$ = Rx.Observable.fromEvent(competitorBtn, 'click')
												 .subscribe(function(e){
											  		competitorBtn.addClass('active');

										  			fitBtn.removeClass('active');

									  				fitCarousel.hide();

									  				competitorCarousel.show();
												 });

		// this.statusBtnStream$ = Rx.Observable.fromEvent(statusBtn, 'click')
		// 									 .subscribe(function(e){
		// 										$(e.target).parent().find('.move-student-menu').toggle();
		// 									 });
	},
};