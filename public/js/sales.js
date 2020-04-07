/* js/jquery file for Plexuss Publisher */
Publisher = {
	menu_is_open: false,
};

$(document).ready(function(){

});

$(document).on('click', '.publisher-menu-btn', function(){
	Publisher.toggleMenu($(this));
});

Publisher.toggleMenu = function(btn){

	//if menu is already open, close it
	if( Publisher.menu_is_open ){
		$(btn).animate({
			bottom: '0.5%',
			left: '0.2%',
			width: '72px',
			borderRadius: '100px',
		}).removeClass('expanded').find('span').html('Menu');
		$('.publisher-menu-btn-bg').fadeOut(250);
		$('.publisher-menu-ul').slideUp(250);
		//wasnt open before, so now menu is open
		Publisher.menu_is_open = false;
	}else{
		
		$(btn).animate({
			bottom: '40%',
			left: '38%',
			width: '350px',
			borderRadius: '3px',
		}).addClass('expanded').find('span').html('Close');
		$('.publisher-menu-btn-bg').fadeIn(250);
		$('.publisher-menu-ul').slideDown(250);
		//now closed, so menu open is now false
		Publisher.menu_is_open = true;
	}

}


$(document).on('change', '.action-bar-select', function(e){
	console.log(e.target.value);
});

