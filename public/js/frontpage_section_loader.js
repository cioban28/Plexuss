// frontpage_section_loader.js

var Plex = Plex || {};
Plex.fp = {};

//frontpage constructor
var FP = function FP(){
	this.sections = [
		new FP_Section('message_a_college_carousel', !0),
		new FP_Section('colleges_near_you_carousel', !0),
		new FP_Section('top_ranked_colleges_carousel', !0),
		new FP_Section('college_virtual_tours_carousel', !0),
		new FP_Section('quad_carousel', !0),
		new FP_Section('footer_outro', !1),
		new FP_Section('plex_featured_in', !1),
		new FP_Section('get_started_section', !1, !0),
		new FP_Section('find_a_college_section', !1, !0),
		new FP_Section('member_colleges_section', !1, !0),
		new FP_Section('compare_colleges_section', !1, !0)
	];
	last_scroll_pos = 0;
};

//return first section where loaded === false
FP.prototype.getNextSection = function(){
	return _.findWhere(this.sections, {loaded: !1});	
};

//return specific section by name
FP.prototype.getSectionByName = function(section){
	return _.findWhere(this.sections, {name: section});
};

//save scroll position
FP.prototype.updateScrollPosition = function(pos){
	this.last_scroll_pos = pos;
};

//check if any sections are currently being loaded - return the section if true
FP.prototype.anySectionCurrentlyLoading = function(){
	return _.findWhere(this.sections, {currently_loading: !0});
};

FP.prototype.allSectionsLoaded = function(){
	return !_.findWhere(this.sections, {loaded: !1, is_nav_section: !1});
};

FP.prototype.showLoadingGif = function(){
	$('.section-loader').show();
};

FP.prototype.hideLoadingGif = function(){
	$('.section-loader').hide();
};

FP.prototype.getNavSection = function(name) {
	var section = this.getSectionByName(name)

	if( !section.loaded ){
		$.ajax({
			url: 'ajax/homepage/getSection/'+name,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(view){
			section.inject(view);
		});
	}
	
};

FP.prototype.loadNextSection = function(){
	var fp = this, next_section = null;
	fp.showLoadingGif();//show loading gif
	next_section = fp.getNextSection();//get the next section to be loaded

	//if section is set and a previous section isn't still loading
	if( next_section && !next_section.is_nav_section && !fp.anySectionCurrentlyLoading() ){
		next_section.isNowLoading(!0);//make isNowLoading true so that no other sections can load during this time
		$.ajax({
			url: 'ajax/homepage/getSection/'+next_section.name,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(view){
			next_section.isNowLoading(!1);//make isNowLoading false so next section can start loading
			next_section.isLoaded();//is now loaded 
			next_section.inject(view);//inject html
			fp.hideLoadingGif();//hide loader
		});
	}
};

FP.prototype.isMainContainerInViewport = function(){
    var elem = $('#fp-carousels-container'),
    	docTop = $(window).scrollTop(),
    	docBottom = docTop + $(window).height(),
    	elemTop = $(elem).offset().top,
    	elemBottom = elemTop + $(elem).height();

    return ( (elemBottom <= docBottom) && (elemTop >= docTop) );
};

//frontpage section constructor
var FP_Section = function FP_Section(name, is_carou, is_nav){
	if( name ) this.name = name;
	this.currently_loading = !1;
	this.loaded = !1;
	this.is_carousel = is_carou;
	this.is_nav_section = is_nav || !1;
};

//toggle currently loading so we don't make multiple at the same time - wait for the prev one to come back first
FP_Section.prototype.isNowLoading = function(val){
	this.currently_loading = val;
};

//change section loaded to true - called after ajax call
FP_Section.prototype.isLoaded = function(){
	this.loaded = !0;
};

//inject section view in proper area
FP_Section.prototype.inject = function(view){
	if( this.is_carousel ) $('#fp-carousels-container > .column').append(view).find('.frontpage-carousel-container').last().fadeIn(2700);
	else if( this.is_nav_section && !this.loaded ){
		if(this.name === 'get_started_section'){
			var gs = $('#get_started'), img = gs.data('bg');
			gs.css({backgroundImage: 'url('+img+')'});
		}
		$('.frontpage-side-bar-sections[data-is-section="'+this.name+'"]').html(view).fadeIn(2700);
		this.isLoaded();
	}
	else $('#fp-carousels-container').parent().append(view).find('.outer-section').last().fadeIn(2700);
};

//init frontpage (FP) constructor 
$(document).ready(function(){
	Plex.fp = new FP(); //create frontpage obj

	setTimeout(function(){
		Plex.fp.loadNextSection(); //on load, load first section
	}, 2000);
});

//scrolling event - when user scrolls down, starting loading sections
$(window).scroll(function(){
	var fp = Plex.fp;

	//if frontpage obj is set and not all the frontpage sections have been loaded yet
	if( fp && !fp.allSectionsLoaded() ){
		if( fp.last_scroll_pos < $(window).scrollTop() ) Plex.fp.loadNextSection(); //if user is scrolling down, load section
		fp.updateScrollPosition( $(window).scrollTop() ); //update scroll position to current position
	}
});

$(document).on('click', '.frontpage-custom-icon-bar a.item', function(){
    var section = $($(this).attr('href'));

    // If section has not been loaded yet, show loading
    if (section.html().indexOf('content ajaxed in on click') !== -1) {
        section.html('<div class="section-loader-container"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/loading.gif" alt="Loading gif"></div>');
    }

	Plex.fp.getNavSection( $(this).data('section') );
});

