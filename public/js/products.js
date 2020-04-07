Plex.Products = {

	attatchClick: function(triggerId, scrollToId){
				$(triggerId).on( 'click',
					function() {
    						
    						//Plex.Products.setActive(triggerId);


    						Plex.Products.scrollTo(scrollToId);
    					
					});

			},
	scrollTo: function(scrollToId){

				if(scrollToId){
		    		$('html, body').animate({
        			scrollTop: $(scrollToId).offset().top
    				}, 1000);

		    	}
			},
	attatchHover: function(triggerId, effectedId, oldClass,  newClass){
				
				//do not want to change class if active state
				if( $(effectedId).hasClass('topnav-active-state'))
					return;

				$(triggerId).on( 'mouseenter',
					function(){
						
						$(effectedId).removeClass(oldClass);
						$(effectedId).addClass(newClass);

					});

				$(triggerId).on ('mouseleave',
					function(){

						$(effectedId).removeClass(newClass);
						$(effectedId).addClass(oldClass);				

					});
			},
	setActive: function(triggerId){
					var currentActive = $('.topnav-active-state');
					currentActive.addClass('topnav-default-state');
					currentActive.removeClass('topnav-active-state');
					

					if($(triggerId).find('.products-nav-btn'))
						$(triggerId).removeClass('topnav-default-state');
					$(triggerId).find('.products-nav-btn').addClass('topnav-active-state');

	},
	scrollHandler: function(event){
			var navbar = $('.products-nav-container');
			var scroll = $(window).scrollTop();
			var position = navbar.offset().top;
			var heightofHeader = $('.productsHeader').height();
			var headerPosition = $('.productsHeader').offset().top + heightofHeader + 60;


			////  - 80 because of space under top navigation
			var audiencePos = $('#audience').offset().top - 80;
			var recruitPos = $('#recruitment').offset().top - 80;
			var webPos = $('#webinar').offset().top - 80;

			//have active button set on scroll
			if(scroll >= audiencePos && scroll < recruitPos){
				Plex.Products.setActive('#audience_btn');
			}
			else if(scroll >= recruitPos &&  scroll < webPos){
				Plex.Products.setActive('#recruitment_btn');
			}
			else if(scroll >=  webPos){
				Plex.Products.setActive('#webinar_btn');
			}


			//if not at top -- make navigation stick to top of window
			if(scroll >= position){
				navbar.addClass('products-nav-container-sticky');
				$('.main-cont').css('padding-top', navbar.height());			
			}

			if(scroll <= headerPosition && navbar.hasClass('products-nav-container-sticky')){
				navbar.removeClass('products-nav-container-sticky');
				$('.main-cont').css('padding-top', '0');
			}
	}

};



$(document).ready(function(){


	//scroll to the top just in case user was not already
	//$('body').scrollTop(0);

	//attatch a scroll to animation to buttons
	Plex.Products.attatchClick('#audience_btn', '#audience');
	Plex.Products.attatchClick('#recruitment_btn', '#recruitment');
	Plex.Products.attatchClick('#webinar_btn', '#webinar');

	//create hover effects for buttons -- change image
	Plex.Products.attatchHover('#audience_btn', '#audience_nav_btn', 'topnav-default-state', 'topnav-hover-state');
	Plex.Products.attatchHover('#recruitment_btn', '#recruitment_nav_btn', 'topnav-default-state', 'topnav-hover-state');
	Plex.Products.attatchHover('#webinar_btn', '#webinar_nav_btn', 'topnav-default-state', 'topnav-hover-state');


	//get topnav to stick on scroll or on load(in case we are at different scroll point than top on load)
	//adds scroll listener
	//changes top navigation to sticky

	$(document).on('ready', Plex.Products.scrollHandler);
	$(window).on('scroll', Plex.Products.scrollHandler);
	





});

