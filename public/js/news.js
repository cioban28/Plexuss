
Plex.news = {
	body: $('body'),
	sawBannerModal: false,
};

//////////////////////////// doc ready ////////////////////////////
$(document).ready(function(){
	
	$('.news-drpdwn-articles').click(function(e){
		e.stopPropagation();
		if($(e.target).closest('.articles-menu').length === 0)
			slideMenu($('.articles-menu'), 100);

	});

});

$(window).scroll(function() {

	// only check if user is on an article page
	const modal = $('#banner_ad_modal');
	const onArticle = window.location.pathname.includes('/news/article/');
	const signed_out = $('#_banner_ad_bar').is(':visible');

	// must be signed out, on an article, modal not already visible, and has not seen the modal yet
	if( signed_out && onArticle && !modal.is(':visible') && !Plex.news.sawBannerModal ){

		const position = $(window).scrollTop() + $(window).height();
		const height = $(document).height() - 2000;

		if( position >= height ) {
			Plex.news.sawBannerModal = true;
			modal.foundation('reveal', 'open');
	    }
	} 

});