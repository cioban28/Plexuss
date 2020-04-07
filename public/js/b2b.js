
var b2b = {
  counted : false,
  currLink: $('.product-link'),

  pfetched: false,         //flag -- are blog press fetched yet
  ffetched: false,
  AllFetched: false,
  pageNumber: 1,
  AjaxHold: 0,
  getPageAjax: 0,
  testimonialTextHeight: 0,

  //populate this when switching pages
  //then can make a call to getPage() which uses this object to get and render
  //pages can have different callbacks and different routes depending on AJAX or browser actions
  pageObj: {
    page: 'Home',             //current page (name that devs will use)
    mhistory: true,           //push to browser history?
    callbacks: [],          //aray of callbacks to perform after
    responseCallbacks: [],      //array of callbacks to perform specifically with ajax data
    url: ''             //AJAX route
  }

};



/**************************************
* set page object
*
***************************************/
b2b.setPageObj = function(ppage, history, callbacks, rcallbacks, url){

  b2b.pageObj['page'] = ppage;
  b2b.pageObj['mhistory'] = history;

  if(callbacks === null){
    b2b.pageObj['callbacks']  = [];
  }
  else{
    b2b.pageObj['callbacks'] = callbacks.slice(0);
  }

  if(rcallbacks === null){
    b2b.pageObj['responseCallbacks'] = [];
  }
  else{
    b2b.pageObj['responseCallbacks'] = rcallbacks.slice(0);
  }


  b2b.pageObj['url'] = url;

};


/********* Our Solutions page toggle *********/
$('#recruit-page').on('click', function() {
  $('#our-solutions-wrapper').toggle();
  $('#recruit-wrapper').toggle(500);
})

$('#retain-page').on('click', function() {
  $('#our-solutions-wrapper').toggle();
  $('#retain-wrapper').toggle(500);
})


/********* Testimonials Carousel **********/
$('.prev-arrow').on('click', function() {
  $('.slider').slick('slickPrev');
});

$('.next-arrow').on('click', function() {
  $('.slider').slick('slickNext');
});

$('.testimonial-carousel').on('afterChange', function(event, slick, currentSlide, nextSlide) {

  // code for removing previous and next arrow on first and last slide
  if (currentSlide == 0){$('.testimonials-container .prev-arrow').hide();}
  else{$('.testimonials-container .prev-arrow').show();}
  if (currentSlide == slick.slideCount - 1){$('.testimonials-container .next-arrow').hide();}
  else {$('.testimonials-container .next-arrow').show();}

	// var testimonialText = $('.slick-current .text');
	// if($(window).width() < 768 && $(window).height() < (testimonialText.offset().top + testimonialText.innerHeight())) {
	// 	b2b.testimonialTextHeight = '60vh';
	// 	$(testimonialText).css({'height': '60vh', 'overflow': 'hidden'});
	// 	$('.testimonial-carousel').slick('resize');
	// }
});

/********* News Page **********/
function linkedInShareClick(title) {
  window.open("https://www.linkedin.com/shareArticle?mini=true&url=" + document.URL + "&title=" + title + "&source=plexuss.com", '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
}

function fbShareClick(title) {
  window.open("https://www.facebook.com/share.php?u=" + document.URL + "&title=" + title,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
}

function twitterShareClick(title) {
  window.open("https://twitter.com/home?status=" + title + " + " + document.URL,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
}


/********* Contact Us Form Submission **********/
$('#b2b-contact-form').submit(function(e) {
	e.preventDefault();

	var form = $(this);

	$("#b2b-contact-form").validate({
  	debug: true,
	});

	var values = {};

	$('#b2b-contact-form :input').each(function() {
		values[this.name] = $(this).val();
	});

	if($(this).valid()) {
	  $.ajax({
	    url: '/saveCollegeSubmission',
	    type: 'POST',
	    data: values,
	    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	    beforeSend: function() {
	    	$('.loader-overlay').css('height', $(document).height()).show();
	    },
	    complete: function() {
	    	$('.loader-overlay').hide();
	    },
	    success: function() {
	    	form.trigger('reset');

	    	$('input').each(function(input) {
	    		$(this).removeClass('valid-input');
	    	});

	    	$(".multi-select")[0].selectize.clear();

	    	toastr.success('Thank you for your interest on Plexuss. Our Vice President for Business Development, Brad Johnson, will be in touch with you shortly!');
	    },
	    error: function(jqXHR, textStatus, error) {
	    	toastr.error('Failed to save your information!');
	    }
	  });
	}
});

$('.multi-select').change(function() {
	if ($(this).val() && $('.selectize-control').find('#client_type-error').is(':visible')) {
		$('.selectize-control').find('#client_type-error').hide();
	}
	if (!$(this).val() && $('.selectize-control').find('#client_type-error').is(':hidden')) {
		$('.selectize-control').find('#client_type-error').show();
	}
});


/*******************************************
* part of handler: returns a url based on the link selected
* also sets current link as active
*   and sets b2b.pageObj page and url
*********************************************/
b2b.getPageRouter = function(page){


  b2b.pageObj['page'] = page;
  var p = $('.b2b-topnav-container');
  var curr = '.'+p.attr('data-subpage');
  var flist = $('ul.features-list');
  var url = '/b2b-info';



    //remove active link from current link
    $('.b2b-topnav-container .active').removeClass('active');


    //get new page to switch to and set active link
    switch(page){

      case 'About Us':
        b2b.currLink = $('.about-us-link');
        b2b.pageObj['url'] = url + '/about-us';
        $('.b2b-meta-title').text(' About | Plexuss | College Partnerships ');
        // b2b.AjaxHold = 0;
        break;

      case 'Why Plexuss?':
        b2b.currLink = $('.why-plexuss-link');
        b2b.pageObj['url'] = url+ '/why-plexuss'
        $('.b2b-meta-title').text(' Why Plexuss | Plexuss | College Partnerships ');
        // b2b.AjaxHold = 1;
        break;
      case 'Our Solutions':
        b2b.currLink = $('.our-solutions-link');
        b2b.pageObj['url'] = url + '/our-solutions';
        $('.b2b-meta-title').text(' Our Solutions | Plexuss | College Partnerships ');
        // b2b.AjaxHold = 1;
        break;

      case 'Testimonials':
        b2b.pageObj['url'] = url + '/testimonials';
        b2b.currLink = $('.testimonials-link');
        $('.b2b-meta-title').text(' Testimonials | Plexuss | College Partnerships ');
        // b2b.AjaxHold = 1;
        break;

      case 'News':
        b2b.pageObj['url'] = url + '/news';
        b2b.currLink = $('.news-link');
        $('.b2b-meta-title').text('  News | Plexuss | College Partnerships ');
        // b2b.AjaxHold = 1;
        break;
    }
    //set new active link
    b2b.currLink.addClass('active');

    return b2b.pageObj['url'];
};




/*******************************************************
*  AJAX:  gets and renders a page based on global b2b.pageObj
*******************************************************/
b2b.getPage = function(){

  //hide features list if visible
  $('.features-list').fadeOut();

  //show loader
  $('.spinloader-back').show();

  if(b2b.getPageAjax === 0){
    b2b.getPageAjax = 1;

    //get view
    $.ajax({
      url: b2b.pageObj['url'],
      data: {isAjax: true},
      type: 'GET',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(res){

       b2b.getPageAjax = 0;

      // b2b.changePage(res);

      //push this page change to browser history?
      if (typeof history.pushState != "undefined" && b2b.pageObj['mhistory']) {

        var stateObj = {page: b2b.pageObj['page'], url: b2b.pageObj['url'] };
        window.history.pushState(stateObj, 'Plexuss | '+  b2b.pageObj['page'], b2b.pageObj['url']);
      }


      for(var rcallback in b2b.pageObj['responseCallbacks']){
        b2b.pageObj['responseCallbacks'][rcallback](res);
      }

      for(var callback in b2b.pageObj['callbacks']){
        b2b.pageObj['callbacks'][callback]();
      }

      b2b.reattachAbideToForms();

    });
  }

};

/*******************************************
*  Reattach abide to forms. Dynamic page
*  changes causes abide to be disabled.
*******************************************/
b2b.reattachAbideToForms = function() {

  if ($('.b2b-form').length > 0) {
    $('.b2b-form').foundation('abide');
  }

  if ($('.newsletter-form').length > 0) {
    $('.newsletter-form').foundation('abide');
  }

  if($('.b2b-form-client').length > 0) {
    $('.b2b-form-client').foundation('abide');
  }

  if($('b2b-form-student').length > 0){
    $('.b2b-form-student').foundation('abide');
  }

}

/*******************************************
*  when switching pages (between tabs)
*  used as callback to change pages
*******************************************/
b2b.changePage = function(res){

  //in case user left blog opened , show blog main page
  $('.blog-splash-wrapper').fadeIn(500);
  $('.blog-view').fadeOut(500);



  $('._b2b-content-wrapper').fadeOut(50);
  $('.spinloader-back').hide();
  $('.features-list-mobile').slideUp();
  $('.b2b-topnav-mobile-menu').removeClass('opened');
  $('.b2b-topnav-mobile-menu').slideUp();
  $('.blog-magnifier-icon').show();

  $('._b2b-content-wrapper').html(res);
  $('._b2b-content-wrapper').fadeIn(300);

  $('html,body').scrollTop(0);

  //if want to load home page/blog again, counter for animation need to be reset
  b2b.counted = false;
  b2b.AllFetched = false;
  b2b.AjaxHold = 0;
  b2b.pageNumber = 1;

  //reload owl carousel
  $('#b2b-clients').owlCarousel({
      singleItem: true,
      navigation: false,
      autoPlay: true,
      slideSpeed : 300,
      paginationSpeed : 700,
      stopOnHover: true,
      theme: 'b2b-controls'
    });


  //reinit masonry after ajax
  $('#container-box').masonry({

      itemSelector: '.newsitem'

    }).imagesLoaded(function() {

    $('#container-box').masonry('layout');

  });



};



/***********************************
* get new features page for blog
* with argument 'history': are we going to need to push history state?
******************************************/
b2b.getNewBlogSubTab = function(newTab){


    $('.blog-menu li.active').removeClass('active');


    var data = {};
    var url = '/b2b/blog/newFeatures';
    page = '';

    switch(newTab){
      case 'newFeatures':
        //get this month
        var d = new Date();
          var month = d.getMonth();
        data.month = { month };
        url = '/b2b/blog/newFeatures';
        page = 'newFeatures';
        $('.new-features-lnk').addClass('active');
        $('.b2b-meta-title').text(' New Features | Plexuss | College Partnerships ');

        break;
      case 'pressReleases':
        url = '/b2b/blog/pressReleases';
        page = 'pressReleases';
        $('.press-btn').addClass('active');
        $('.b2b-meta-title').text(' Press Releases | Plexuss | College Partnerships');
        break;
    };


    data.isAjax = true;

    //show loader
    $('.spinloader-back').show();

    $.ajax({

      url: url,
      type: 'GET',
      data : data,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

    }).done(function(res){

      switch(newTab){
        case 'newFeatures':
          $('.blog-magnifier-icon').hide();  //because ajax does partial pages, difficult to send this info to parent from server
          break;
        case 'pressReleases':
          $('.blog-magnifier-icon').show();
          break;
      };

      $('.blog-cont-wrapper').fadeOut();
      $('.blog-cont-wrapper').html(res);
      $('.blog-cont-wrapper').fadeIn();

      //show loader
      $('.spinloader-back').hide();


      b2b.AjaxHold = 0;
      b2b.AllFetched = false;
      b2b.pageNumber = 1;


      //reinit masonry after ajax
      $('#container-box').masonry({

          itemSelector: '.newsitem'

        }).imagesLoaded(function() {

        $('#container-box').masonry('layout');
      });


      if (typeof history.pushState != "undefined" && b2b.pageObj['mhistory']) {

        var stateObj = {page: page, url : url};
        window.history.pushState(stateObj, 'Plexuss | New Features', url);
      }//eventually give old browser/non AJAX users support by redirecting to new page



    });

};





/*************************************
*  see full article
*
**************************************/
b2b.seeMore = function(slug){

    var url = '/b2b/blog/getArticle/' + slug;

    //show loader
    $('.spinloader-back').show();

    $.ajax({
      url: url,
      data: {isAjax: true},
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(res){




      $('html, body').scrollTop(0);

      $('.blog-cont-wrapper').fadeOut();
      $('.blog-cont-wrapper').html(res);
      $('.blog-cont-wrapper').fadeIn();

      var h = $('.article-col').outerHeight(true);
      //set column on left to be equal to col on right

      $('.author-col').height(h);




      //show loader
      $('.spinloader-back').hide();

      var title = $('#blog-body-wrapper').attr('data-title');
      $('.b2b-meta-title').text( title + ' | Plexuss | College Partnerships ');





      if (typeof history.pushState != "undefined") {

        var stateObj = {page: 'blog-view', slug: slug, url: url};

        window.history.pushState(stateObj, 'Plexuss' + slug, url);
      }//eventually build in old browser/non AJAX users support by getting new page in else?


    });

};






/************************************************
*  infinite scroll for blog page
*
************************************************/
b2b.scrollinfinite = function(lastDataId){

  b2b.pageNumber = $('.blog-box-container').attr('data-page')*1;
  // if (b2b.pageNumber == 1) b2b.pageNumber = 2

  // if (b2b.pageNumber == 1) {
  //   b2b.pageNumber = $('.blog-box-container').attr('data-page')*1;
  // }
  // $('div#loadmoreajaxloader').show();


  if(b2b.AjaxHold === 0){
    b2b.AjaxHold = 1;
    var subCat = 'blog';

    $.ajax({
    url: "/b2b/blog/getmore",
    data: {
      page: b2b.pageNumber,
      sub_cat_id: subCat,
    },
    method: "GET",
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    success: function(html){
      b2b.AjaxHold=0;

      //if nothing more blog articles, we do not want to fetch anymore
      if(html === ''){

        $('div#loadmoreajaxloader').hide();
        b2b.AllFetched = true;

        $(".blog-cont-wrapper").append('<br><br>No more Articles');

      }

      if(html != ''){
        $("#container-box").imagesLoaded( function(){
          $("#container-box").append(html);
          $("#container-box").masonry("reloadItems").masonry("layout");
        });
      }

      $('.blog-box-container').attr('data-page', b2b.pageNumber + 1);
      $('div#loadmoreajaxloader').hide();
    },
    error:function(){
      $('div#loadmoreajaxloader').hide();
      toastr.error('Failed to load more articles!');
    }
    });

  }
};



/************************************************
*  infinite scroll for new features pages
*
************************************************/
b2b.scrollinfiniteNewFeatures = function(lastDataId){

  b2b.pageNumber = $('.newf-container').attr('data-page');

  $('div#loadmoreajaxloader').show();


  if(b2b.AjaxHold === 0){

    b2b.AjaxHold=1;


    $.ajax({
    url: "/b2b/blog/getmoreNewFeatures",
    data: {
      offset: b2b.pageNumber
    },
    method: "GET",
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    success: function(html){

      b2b.AjaxHold=0;

      //if no more blog articles, we do not want to fetch anymore
      if(html === ''){

        $('div#loadmoreajaxloader').hide();
        b2b.AllFetched = true;
        $(".newf-articles-container").append('<div><div class="newf-lcol"></div> ' +
                            '<div class="newf-rcol text-center c333">' +
                            '<br><br>No more Articles</div></div>');


      }

      $(".newf-articles-container").append(html);

      $('.newf-container').attr('data-page', 1*(b2b.pageNumber) + 1);
      $('div#loadmoreajaxloader').hide();
    },
    error:function(){
      $('div#loadmoreajaxloader').hide();
    }
    });

  }
};

b2b.getUrlParam = function( name, url ) {
    if (!url) url = location.href;
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( url );
    return results == null ? null : results[1];
}

b2b.setPixelModalParams = function() {
    var modal = $('#pixel-modal');
    var decoded = '';

    if (modal.length > 0) {

        if (b2b.getUrlParam('name')) {
            decoded = decodeURIComponent(b2b.getUrlParam('name'));
            $('#name').val(decoded);
        }

        if (b2b.getUrlParam('email')) {
            decoded = decodeURIComponent(b2b.getUrlParam('email'));
            $('#email').val(decoded);
        }

        if (b2b.getUrlParam('institution')) {
            decoded = decodeURIComponent(b2b.getUrlParam('institution'));
            $('#institution').val(decoded);
        }
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////
/******************************************
*  init foundation abide
*
********************************************/
$(document).foundation({
    abide : {
        patterns: {

            name: /^[a-zA-Z0-9\,\'\-\s\.]*$/,
            title: /^[a-zA-Z0-9\,\'\-\s\.]*$/,
            phone: /^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/,
        }
    }

});


/////////////////////////////////////////////////////
/****************************************
*  document.ready
*
*****************************************/
$(document).ready(function(){
    const subPage = $('.top-section-cont').attr('data-subpage');

    var lastScrollTop = $(document).scrollTop();

    switch(subPage) {
      case '_Home':
        var total_students = $('.total_students'), i = 0, n = 5500000, dur = 2000, int = 13, s = Math.round(n / (dur / int));

        var id = setInterval(function() {
            total_students.text(commaSeparateNumber(i += s));
            if (i >= n) {
                clearInterval(id);
                total_students.text(commaSeparateNumber(n));
            }
        }, int);

        function commaSeparateNumber(val){
          while (/(\d+)(\d{3})/.test(val.toString())){
            val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
          }
          return val;
        }
    }

    $("#b2b-contact-form").validate({
	  	ignore: ':hidden:not([class~=selectized]),:hidden > .selectized, .selectize-control .selectize-input input',
		  rules: {
		    first_name: 'required',
		    last_name: 'required',
		    company: 'required',
		    title: 'required',
		    email: {
		      required: true,
		      email: true,
		    },
		    phone: 'required',
		    client_type: 'required',
		  },
		  highlight: function(element, errorClass, validClass) {
      	$(element).nextAll('.check-mark').show().removeClass('fa-check');
	    },
	    success: function(element) {
	      $(element).nextAll('.check-mark').show().addClass('fa-check');
	    },
		  messages: {
		    first_name: 'Please specify your First name',
		    last_name: 'Please specify your Last name',
		    company: 'Please specify your institute or Company name',
		    title: 'Please specify your Position',
		    email: {
		      required: 'Please specify your email',
		      email: 'Your email address must be in the format of name@domain.com'
		    },
		    phone: 'Please specify your Phone number',
		    client_type: 'Please specify at least one Service',
		  },
		  errorPlacement: function(error, element) {
				if (element.attr("name") == "client_type") {
	        error.insertAfter($('.selectize-input'));
		    } else {
	        error.insertAfter(element);
		    }
			}
		});


  ///////////// event for scroll ////////////////////////
  $(window).scroll(function(){
    var scrollHeight = $(document).scrollTop();

    if ($('.top-section-cont').attr('data-subpage') === '_Home') {
      if (scrollHeight > 0) {
        $('.b2b-topnav-container').addClass('sticky');
        $('.b2b-logo').hide();
        $('.b2b-logo-P-black').show();
      } else {
        $('.b2b-topnav-container').removeClass('sticky');
        $('.b2b-logo-P-black').hide();
        $('.b2b-logo').show();
      }
    }

    if($('.article-block').is(':visible') && $(window).width() > 767) {
    	if(scrollHeight > $('.share-article-container').offset().top) {
	    	if($('.share-article-left-container').is(':hidden')) {
	    		$('.share-article-left-container').show();
	    	}
    	} else {
	    	if($('.share-article-left-container').is(':visible')) {
	    		$('.share-article-left-container').hide();
	    	}
    	}
    }
  });

  ///////////////// see more button on products page ///////
  $(document).on('click', '.college-see-more', function(){
    var cont = $(this).parent().find('.college-img-cont');
    var bh = $('.b2b-colleges-img-b').innerHeight();
    var ch = $('.b2b-colleges-img-c').innerHeight();

    //used to 'prevent' scrolling with growth in height of document
    var currentSP = $(window).scrollTop() - (Math.abs(bh - ch));


    if(cont.hasClass('opened')){
      cont.removeClass('opened');
      $('.b2b-colleges-img-c').hide();
      $('.b2b-colleges-img-b').fadeIn(600);
      $(window).scrollTop(currentSP);
      $(this).text('See more');
    }else{
      cont.addClass('opened');
      $('.b2b-colleges-img-c').fadeIn(600);
      $(this).text('See less');
      $('.b2b-colleges-img-b').hide();

    }
  });

	$('.multi-select').selectize({
		placeholder: 'Which Plexuss Services are you interested in?'
	});

  //////////////////////////// menu for mobile ////////
  $('.hamburger-menu').click(function(){
    var menu = $('.b2b-topnav-mobile-menu-container');

    if (menu.hasClass('opened')) {
      $('#mobile-nav-toggle').removeClass('active')
      menu.slideUp();
      menu.removeClass('opened');
    } else {
      $('#mobile-nav-toggle').addClass('active');
      menu.slideDown();
      menu.addClass('opened');
    }
  });

  /********* News Page **********/
  $(document).on('click', '.article-box, .news-slider-heading', function() {
    var title = $(this).attr('data-title');
    var slug = $(this).attr('data-slug');

    $.ajax({
      url: `/solutions/news/articles/${slug}`,
      data: {isAjax: true},
      type: 'GET',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      beforeSend: function() {
        $('.loader-overlay').css('height', $(document).height()).show();
      },
      complete: function() {
        $('.loader-overlay').css('display', 'none');``
      },
      success: function(res) {
        var splittedArray = document.URL.split('/');
        if(splittedArray.includes('articles')) {
          splittedArray.pop();
          splittedArray.push(slug);
          window.history.pushState({ page: title }, title, splittedArray.join('/'));
        } else {
          if(document.URL[document.URL.length-1] === '/')
            window.history.pushState({ page: title }, title, document.URL + 'articles/' + slug);
          else
            window.history.pushState({ page: title }, title, document.URL + '/articles/' + slug);
        }
        $('._b2b-content-wrapper').html(res);
        window.scrollTo(0, 0);
      },
      error: function(jqXHR, textStatus, error) {
        toastr.error('Failed to get requested article!');
      }
    });
  });


  ////////////////////// open request proposal modal ///////////
  $(document).on('click' , '.plans-proposal-btn', function(){

    var t = $(window).scrollTop();

    if($(window).width() > 450)
      t += 70;

    $('.b2b-form.form-modal').css('top', t+'px');
    $('.modal-ty').css('top', t+'px');
    $('.proposal-wrapper').fadeIn();

    // $(document).foundation('abide', 'reflow');
    $('.b2b-form').foundation('abide');

  });


  /////////////////// close request proposal modal  ///////////
  $(document).on('click', '.modal-close-btn', function(){

    $('.proposal-wrapper').fadeOut();

  });

  //////////////////  close modal if clicking on back ////////////////
  $(document).on('click', '.form-modal-cont, .form-modal-back', function(e){

    if($(e.target).closest('.b2b-form').length === 0 && $(e.target).closest('.hero-thank-you').length === 0)
      $('.proposal-wrapper').fadeOut();
  });

  ///////////////  submit request proposal form  ////////////////
  $(document).on('click', '.b2b-hero-submit', function(e){
    // e.preventDefault();


    var form = $(this).closest('.b2b-form');
    var data = form.serialize();
    var err_flag = false;


    //remove error class if any
    $('.empty-err').hide();

    form.find('input[type="text"]').each(function(){
      if($(this).val() === '' || typeof $(this).attr('data-invalid') != 'undefined'){
        err_flag = true;
      }
    });

    if(err_flag){
      $('.empty-err').show();
      return;
    }

    $.ajax({
      url: '/saveCollegeSubmission',
      type: 'POST',
      data: data,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(res){

      //if throguh modal -- hide it
      // $('.proposal-wrapper').fadeOut();


      if(res === 'success'){


        $('.empty-err').hide();
        $('.b2b-form').hide();
        $('.hero-thank-you').show();
        $('.hero-thank-you.modal-ty').show();
      }

    });

  });


  ////////////////////  submit subscribe to newsletter... form ///////////////////////
  $(document).on('click', '.b2b-email-submit', function(e){

    var data = $('.newsletter-form').serialize();


    //clear messages
    $('.b2b-email-cont .email-err').text('');
    $('.b2b-email-cont form .email-ty-msg').html('');

    if($('.b2b-email-cont form input').val() === '' || $('.newsletter-form').hasClass('error')){
      $('.b2b-email-cont .email-err').text('Valid email is required.');
      return;
    }

    $.ajax({
      url: '/saveCollegeSubmission',
      type: 'POST',
      data: data,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(res){

      if(res === 'success'){
        $('.b2b-email-cont form .email-ty-msg').html('Thanks for signing up! &nbsp; Please keep an eye out for us in your inbox.');
        $('#ty-modal').foundation('reveal', 'open');

      }

    });
  });


  ////////////// close ty modal //////////////
  $(document).on('click', '.close-ty-modal', function(){

    $('#ty-modal').foundation('reveal', 'close');
    $('reveal-modal-bg').fadeOut();
  });


  /////////////  features dropdown ///////////////
  $('.features-link-mobile').click(function(){

    var list = $('.features-list-mobile');

    if(list.hasClass('opened')){
      list.slideUp();
      list.removeClass('opened');
    }else{
      list.slideDown();
      list.addClass('opened');
    }

  });

  ////////////  hanlder for learn more on products page /////////
  $('.learnmore-btn').click(function(){

    var p = $(this).closest('body').find('.b2b-topnav-container');
    var curr = '.'+p.attr('data-subpage');

    // $(curr).hide();

    //show loader
    $('.spinloader-back').show();

    $.ajax({
      url: '/b2b/communication',
      type: 'GET',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(res){

      $('._b2b-content-wrapper').fadeOut(50);
      $('.spinloader-back').hide();

      $('._b2b-content-wrapper').html(res);
      $('._b2b-content-wrapper').fadeIn(300);

      $('html,body').scrollTop(0);
      p.attr('data-subpage', '_Features');

    });


    // $('._Features').fadeIn();

  });


  //////////////////    toggles the features submenu /////////////////////////////////
  $('.features-link').on('click', function(e){

    e.stopPropagation();

    var list = $(this).find('.features-list');
    if(list.is(':visible')){
      list.fadeOut();//slideUp();
    }else{
      list.fadeIn();//slideDown();


    }
  });



  /////////////////   switches views for the communication options  ///////////////////
  $(document).on('click', '.comm-block', function(){

    var id = $(this).attr('id');
    var sel = '', curr = '';

    //switch active icon
    $('.comm-block').each(function(){
      if($(this).hasClass('active')){
        curr = $(this).attr('id');
        $(this).removeClass('active');
      }
    });
    $(this).addClass('active');
    //switch content

    switch(id){
      case 'textIcon':
        sel = '.text-cont';
        break;
      case 'callIcon':
        sel = '.call-cont';
        break;
      case 'liveIcon':
        sel = '.live-cont';
        break;
      case 'campaignIcon':
        sel = '.campaign-cont';
        break;
      case 'chatIcon':
        sel = '.chat-cont';
        break;

    }
    $('.'+curr).fadeOut();
    $(sel).fadeIn();
  });





  /////////////////////  renders view for blog articles when selecting see full article  ////////////////////////
  $(document).on('click', '.seemore-btn', function(){

    var searchbox = $('.blog-search-box');
    var icon = $(this).find('.blog-magnifier');

    var slug = $(this).closest('.newsitem').attr('data-id') || $(this).closest('.newsitem').attr('data-slug')
    || $(this).closest('.featured-box').attr('data-id') || $(this).attr('data-id');

    b2b.seeMore(slug);


    searchbox.removeClass('opened');
    icon.removeClass('opened');
    searchbox.fadeOut(300);

  });


  ///////////////////// handler for getting new features from blog /////////////////
  $(document).on('click', '.new-features-lnk', function(){

    b2b.pageObj['url'] = ['/b2b/blog/newFeatures'];
    b2b.pageObj['mHistory'] = true;
    b2b.getNewBlogSubTab('newFeatures');

  });


  /////////////////////  opens mobile top navigation   ///////////////////////////
  $(document).on('click', '.mobile-menu-drop', function(){

    var menu = $('.blog-menu');

    if(menu.hasClass('opened')){
      menu.removeClass('opened');
      menu.slideUp();
    }else{
      menu.addClass('opened');
      menu.slideDown();
    }

  });


  $(window).scroll(function () {
  if ($('.top-section-cont').attr('data-subpage') === '_News' && $(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
     if(b2b.AjaxHold === 0 && b2b.AllFetched === false) {
       b2b.scrollinfinite();
     }
   }
 });

});//end document ready

dialog = $( "#pixel-modal" ).dialog({
    autoOpen: false,
    height: 700,
    width: 500,
    modal: true
});
thankYouDialog = $( "#thank-you-modal" ).dialog({
    autoOpen: false,
    height: 200,
    width: 400,
    modal: true
});
audienceDialog = $( "#audience-modal" ).dialog({
    autoOpen: false,
    height: 700,
    width: 500,
    modal: true
});

clientDialog = $( "#client-journey-modal" ).dialog({
    autoOpen: false,
    height: 700,
    width: 500,
    modal: true
});

studentDialog = $( "#student-journey-modal" ).dialog({
    autoOpen: false,
    height: 700,
    width: 500,
    modal: true
});

 //////////   Testimonial page ///////////
$(document).ready(function() {
  (function() {
    var showChar = 450;
    var ellipsestext = "...";
 if ($(window).width() < 768) {
    $(".truncate").each(function() {
      var content = $(this).html();
      if (content.length > showChar) {
        var c = content.substr(0, showChar);
        var h = content;
        var html =
          '<div class="truncate-text" style="display:block">' +
          c +
          '<span class="moreellipses">' +
          ellipsestext +
          '&nbsp;&nbsp;<a href="" class="moreless more" style="color: black; font-size: 14px;font-weight: bold; text-align: center;"> Read more</a></span></span></div><div class="truncate-text" style="display:none">' +
          h +
          '<a href="" class="moreless less" style="color: black; font-size: 14px;font-weight: bold; text-align: center;">Read less</a></span></div>';

        $(this).html(html);
      }
    });

    $(".moreless").click(function() {
      var thisEl = $(this);
      var cT = thisEl.closest(".truncate-text");
      var tX = ".truncate-text";
      var testimonialText = $('.slick-current .text');

      if (thisEl.hasClass("less")) {
        cT.prev(tX).toggle();
        cT.slideToggle();
        // $(testimonialText).css('height', '60vh');
        $(".slider").slick("setOption", '', '', true);
      } else {
        cT.toggle();
        cT.next(tX).fadeToggle();
        $(testimonialText).css('height', 'auto');
        $(".slider").slick("setOption", '', '', true);
      }
      $('.testimonial-carousel').slick('resize');
      return false;
    });
  }
    /* end iffe */
  })();

  /* end ready */
});


$(document).ready(function() {
    $(".news-carousel").slick({
      dots: true,
      infinite: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      adaptiveHeight: true,
    });

   //  $('.testimonial-carousel').on('init', function() {
   //  	var testimonialText = $('.slick-current .text');
   //    $(".testimonials-container .prev-arrow").hide();
			// if($(window).width() < 768 && $(window).height() < (testimonialText.offset().top + testimonialText.innerHeight())) {
			// 	b2b.testimonialTextHeight = '226px';
			// 	$(testimonialText).css({'height': '266px', 'overflow': 'hidden'});
			// }
   //  });

    $('.testimonial-carousel').slick({
      dots: false,
      infinite: false,
      slidesToShow: 1,
      slidesToScroll: 1,
      adaptiveHeight: true,
    });

    const target = document.querySelector(".target");
    const links = $(".b2b-topnav-menu li:not(:last-child)");

    function mouseenterFunc() {
      if (!this.classList.contains("active")) {
        for (let i = 0; i < links.length; i++) {
          if (links[i].classList.contains("active")) {
            links[i].classList.remove("active");
          }
        }

        this.classList.add("active");

        const width = this.getBoundingClientRect().width;
        const height = this.getBoundingClientRect().height;
        const left = this.getBoundingClientRect().left + window.pageXOffset;
        const top = this.getBoundingClientRect().top;

        target.style.width = `${width}px`;
        target.style.height = `${height}px`;
        target.style.left = `${left}px`;
        target.style.top = `${top}px`;
        target.style.borderColor = '#2AC56C';
        target.style.transform = "none";
      }
    }

    function mouseleaveFunc() {
      if(this.classList.contains('active')) {
        this.classList.remove('active');
        $('.target').css('border-bottom', '2px solid transparent');
      }
    }

    for (let i = 0; i < links.length; i++) {
      links[i].addEventListener("mouseenter", mouseenterFunc);
      links[i].addEventListener("mouseleave", mouseleaveFunc);
    }
});

$(document).on('click', '.plexuss-pixel-info-submit', function(e){
    var form = $(this).closest('.b2b-form');
    var data = form.serialize();
    var err_flag = false;

    $('.empty-err').hide();

    form.find('input[type="text"]').each(function(){
        if($(this).val() === '' || typeof $(this).attr('data-invalid') != 'undefined'){
            err_flag = true;
        }
    });

    if(err_flag){
        $('.empty-err').show();
        return;
    }

    $.ajax({
        url: '/savePlexussPixelInfo',
        type: 'POST',
        data: data,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        beforeSend: function(){
            $("button").attr("disabled", "disabled");
            dialog.dialog( "close" );
        },
    }).done(function(res){
        if(res === 'success'){
            $("button").prop("disabled", false);
            $('.b2b-form')[0].reset();
            $('#thank-you-modal').show();
            thankYouDialog.dialog("open");
        }

    });
});

$(document).on('click', '.audience-submit', function(e){
    var form = $(this).closest('.b2b-form');
    var data = form.serialize();
    var err_flag = false;

    $('.empty-err').hide();

    form.find('input[type="text"]').each(function(){
        if($(this).val() === '' || typeof $(this).attr('data-invalid') != 'undefined'){
            err_flag = true;
        }
    });

    if(err_flag){
        $('.empty-err').show();
        return;
    }

    $.ajax({
        url: '/saveAudienceInfo',
        type: 'POST',
        data: data,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        beforeSend: function(){
            $("button").attr("disabled", "disabled");
            audienceDialog.dialog( "close" );
        },
    }).done(function(res){
        if(res === 'success'){
            $("button").prop("disabled", false);
            $('.b2b-form')[0].reset();
            $('#thank-you-modal').show();
            thankYouDialog.dialog("open");
        }

    });
});

$(document).on('click', '.client-submit', function(e){
    var form = $(this).closest('.b2b-form-client');
    var data = form.serialize();
    var err_flag = false;

    $('.empty-err').hide();

    form.find('input[type="text"]').each(function(){
        if($(this).val() === '' || typeof $(this).attr('data-invalid') != 'undefined'){
            err_flag = true;
        }
    });

    if(err_flag){
        $('.empty-err').show();
        return;
    }

    $.ajax({
        url: '/saveClientJourney',
        type: 'POST',
        data: data,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        beforeSend: function(){
            $("button").attr("disabled", "disabled");
            clientDialog.dialog( "close" );
        },
    }).done(function(res){
        if(res === 'success'){
            $("button").prop("disabled", false);
            $('.b2b-form-client')[0].reset();
            $('#thank-you-modal').show();
            thankYouDialog.dialog("open");
        }

    });
});

$(document).on('click', '.student-submit', function(e){
    var form = $(this).closest('.b2b-form-student');
    var data = form.serialize();
    var err_flag = false;

    $('.empty-err').hide();

    form.find('input[type="text"]').each(function(){
        if($(this).val() === '' || typeof $(this).attr('data-invalid') != 'undefined'){
            err_flag = true;
        }
    });

    if(err_flag){
        $('.empty-err').show();
        return;
    }

    $.ajax({
        url: '/saveStudentJourney',
        type: 'POST',
        data: data,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        beforeSend: function(){
            $("button").attr("disabled", "disabled");
            studentDialog.dialog( "close" );
        },
    }).done(function(res){
        if(res === 'success'){
            $("button").prop("disabled", false);
            $('.b2b-form-student')[0].reset();
            $('#thank-you-modal').show();
            thankYouDialog.dialog("open");
        }

    });
});
