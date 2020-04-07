// plex_lightbox.js
// requires underscore.js
var Plex = Plex || {};
Plex.lbox = {
	id: 0,
	owl: null,
	owlItemsToShow: 4,
	lightbox: null,
	player: null
};


var Media = function Media(obj){
	this.id = ++Plex.lbox.id;

	if( !_.isEmpty(obj) ){
		for(var prop in obj ){
			if( obj.hasOwnProperty(prop) ) this[prop] = obj[prop];
		}
	}
};


/*************************************
*	list of media which have associated lightboxes
*
*********************************************/
var MediaList = function MediaList(){
	this.list = [];	
};

MediaList.prototype.add = function() {
	var args = Array.prototype.slice.call(arguments);
	this.list.push(args[0]);
};

MediaList.prototype.remove = function() {
	var args = Array.prototype.slice.call(arguments);
	this.mediaList = _.reject(this.list, args[0]);
};

MediaList.prototype.find = function() {
	var args = Array.prototype.slice.call(arguments), findObj = {};
	findObj[args[0]] = args[1];
	return _.findWhere(this.list, findObj);
};

MediaList.prototype.count = function() {
	return this.list.length;
};

MediaList.prototype.clear = function() {
	this.list.length = 0;
};



/************************************************
*  Lightbox class -- the actual lightbox
*
**************************************************/
var Plex_Lightbox = function Plex_Lightbox(){
	this.now_viewing = null;
	this.open = false;
	this.media = new MediaList();
	this.view_others = this.init();
};

Plex_Lightbox.prototype.init = function(){
	var lightboxables = $('.is-lightboxable[data-link]'),
		elem, _this = this;

	//clear list on init
	this.media.clear();

	$.each(lightboxables, function(){
		elem = $(this);
		_this.stripForData(elem);
	});

	this.renderViewOthers();
};

Plex_Lightbox.prototype.openMe = function() {
	$('#plex_lightbox').foundation('reveal', 'open');
	$('#plex-lightbox-owl').show(); 
	$('#plex-lightbox-owl').css('opacity', 1);
};

Plex_Lightbox.prototype.closeMe = function() {
	$('#plex_lightbox').foundation('reveal', 'close');
	$('#plex-lightbox-owl').css('opacity', 0);
	$('.reveal-modal-bg').hide();
};

Plex_Lightbox.prototype.view = function() {
	var args = Array.prototype.slice.call(arguments),
	media = this.media.find('link', args[0]);

	if( media ){
		this.now_viewing = media;
		this.renderNowViewing();
	}


	//if aspect ratio is greaters than 2 width/height -> hide additional videos on bottom 
	//they will end up covering main video
	var w = $(window).width();
	var h = $(window).height();

	if( w/h > 2.8)
		$('.view-other-container').hide();
	else
		$('.view-other-container').show();

};

Plex_Lightbox.prototype.renderNowViewing = function(){
	var html = '';

	switch( this.now_viewing.type ){
		case 'image':
			html += '<div class="is-image">';
			html += 	'<img src="'+this.now_viewing.link+'" alt="" />';
			html += '</div>';
			break;
		case 'video':
			html += '<div class="is-video" data-type="'+this.now_viewing.type+'" data-thumb="'+this.now_viewing.thumb+'" data-link="'+this.now_viewing.link+'">';
			html += 	'<iframe id="lbox_yt_player" src="'+this.now_viewing.link+'&autoplay=1&enablejsapi=1" frameborder="0" allowfullscreen width="100%" height="350px"></iframe>';
			html += '</div>';
			break;
		default: 
			break;
	}

	

	$('.now-viewing-container').html(html);
	Plex.lbox.player = new YT.Player('lbox_yt_player');
	this.openMe();
};

Plex_Lightbox.prototype.pauseVideo = function(){
	Plex.lbox.player.pauseVideo();
};

Plex_Lightbox.prototype.destroyVideo = function(){
	$("#plex_lightbox #lbox_yt_player").attr("src","");
	$('iframe#lbox_yt_player').remove();
	// this.media.clear();
};


Plex_Lightbox.prototype.renderViewOthers = function(){
	var html = '';

	_.each(this.media.list, function(obj){
		html += '<div class="item">';
		html += 	'<img src="'+obj.thumb+'" />';
		html +=		'<div class="play-layer is-lightboxable" data-type="'+obj.type+'" data-thumb="'+obj.thumb+'" data-link="'+obj.link+'">';
		html +=			'<div class="play is-lightboxable">';
		html +=	              '<div class="playbtn is-lightboxable"></div>';
		html +=			'</div>';
		html +=		'</div>';
		html += '</div>';
	});

	$('#plex-lightbox-owl').html(html);
};

Plex_Lightbox.prototype.stripForData = function(){

	var args = Array.prototype.slice.call(arguments), 
		vals = {}, data = null, prop, mediaObj = {};

	data = $(args[0]).data();

	//if not empty, loop through each data prop and save as new media obj
	if( !_.isEmpty(data) ){
		for( prop in data ){
			if( data.hasOwnProperty(prop) ) mediaObj[prop] = data[prop];
		}
		this.media.add( new Media(mediaObj) );
	}
};

// events
//doc.ready not firing -- using defer
// $(document).ready(function(){
	
	var lb_owl = $('#plex-lightbox-owl');
	//init lightbox on load - grabbing all is-lightboxable classes
	Plex.lbox.lightbox = new Plex_Lightbox();

	//get media count to adjust the number of items to show in carousel and adjust carousel width based on that number
	Plex.lbox.owlItemsToShow = Plex.lbox.lightbox.media.count() > 10 ? 10 : Plex.lbox.lightbox.media.count();

	//if more than 7, make carousel 100%, if between 4 and 7, make it 75%, else keep where it's at which is 50%
	// if( Plex.lbox.owlItemsToShow >= 5 )  lb_owl.css({width: '100%'});
	if( Plex.lbox.owlItemsToShow >= 4 ) lb_owl.css({width: '100%'});
	else if( Plex.lbox.owlItemsToShow < 3 ) lb_owl.css({width: '50%'});

	Plex.lbox.owl = lb_owl.owlCarousel({
		pagination: false,
		navigation: true,
        items : Plex.lbox.owlItemsToShow, //n <= 10 items 
		beforeInit: function(elem){
			elem.html('');
			if( elem.children().length === 0 ) Plex.lbox.lightbox.init();
		}
	});




	$(document).on('click', function(e){
		var target = $(e.target);

		// open video	
		if( target.hasClass('is-lightboxable') ){
			
			if( target.closest('.is-lightboxable[data-link]').data('link') ){
				
				Plex.lbox.lightbox.view( target.closest('.is-lightboxable[data-link]').data('link') );
			}
		}
		//close video
		else if( $('#plex_lightbox  #lbox_yt_player').is(':visible') && target.closest('#lbox_yt_player').length === 0  ){
			// Plex.lbox.lightbox.pauseVideo();
			Plex.lbox.lightbox.destroyVideo();
			Plex.lbox.lightbox.closeMe();
		}
	});


// });