var owl='';
$(document).ready(function(e)
{
	var $window = $(window);
	if ($window.width() <= 1300) {
		$("#sticky-search-bar1").removeClass('sticky');
		$("#sticky-search-bar2").removeClass('sticky');
	}
	
	owl = $("#owl-compare").owlCarousel({ 
	items :3,
	itemsDesktop : [1199,3],
	itemsDesktopSmall : [979,3],
	itemsMobile : [479,2],
	itemsTablet :[768,2],
	addClassActive: true,
	navigation : true, 
	slideSpeed : 300,
	paginationSpeed : 400,
	singleItem:false,
	pagination:false,	
	}).data('owlCarousel')

	comparisionAutocomplete('addschool','college_slug');	

	/*$(".next").click(function(){
    	owl.next();	
    })
    $(".prev").click(function(){
   		owl.prev();
    })*/
	
});


var owlSlider = $('#owl-compare');
//pass the scroll top on scroll to reposition the hover titles
$(window).bind( "scroll", function(e) {
	updateFloatingTitles($(window).scrollTop());
});
function updateFloatingTitles(fromTop){
	//save how far from the top the owlsider is. Rechecking each time incase it changes.
	var x = owlSlider.offset();
	var n = fromTop - x.top

	//below 0 diffrence hide and if its positive show.
	if (n < 0) {
		$('.comapreSchooltitleArea').hide();
	} else{
		$('.comapreSchooltitleArea').css({
			'top': n +'px',
			'display': 'block'
		});;
	};
}

// Add School In Battle
function Addcollege(UrlSlugs){	
	
	if(UrlSlugs==0){
		UrlSlugs =$('#college_slug').val();
	}
	
	if(UrlSlugs!=''){
		accessLoader('.model-inner-div');		
		$.ajax({
		type: "GET",
		url: '/comparison/',
		//dataType: "json",	
		data: ({UrlSlugs:UrlSlugs,type:'Ajaxcall'}),		
		cache: false,
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data) {	

				accessLoader('.model-inner-div');	

				$('#added-string').css('visibility','hidden');
				//var owl = $(".owl-carousel").data('owlCarousel');	

				var isexit=true;
				var stickdata='';

				//This checks all the boxes inthe owl compare and searchs for addSchoolBox. This should only be in a empty column!
				$("#owl-compare").find(".owl-item").each(function(index, element) {
				
					var con = $(element).find('.addSchoolBox').length;
					
					if(con){
						owl.removeItem(index);
						owl.addItem(data,index);
						$('#selectSchoolPopup').foundation('reveal', 'close');						
						owl.goTo(index);
						//This will stop the next if statment from running.. Dont ask.....
						isexit=false;
						//Will break out of loop each loop since it will return false on new column add.
						return false;
					}
					
	            });


				if($("#owl-compare").find('.addSchoolBox').length == 0 && isexit){
				   	owl.addItem(data);				 
					var lastindex = $("#owl-compare .owl-item").length;
					lastindex-=1;
					owl.goTo(lastindex) 
					$('#selectSchoolPopup').foundation('reveal', 'close');  
				}
				
			},
			error : function(data){
				$('#Notification-Popup').foundation('reveal','open');
				$(document).on('opened.fndtn.reveal', '#Notification-Popup', function (){       
				$('.alert-msg-div').html('Server error try again.');		
				});
			}
		 })
		$('#addschool').val('');
		$('#college_slug').val('');
		$('#m-addschool').val('');
		$('#m-college_slug').val('');

	} else {
		
		$('#Notification-Popup').foundation('reveal','open');
		$(document).on('opened.fndtn.reveal', '#Notification-Popup', function (){       
		$('.alert-msg-div').html('Please Select School.');		
		});
	}
	
	
}
// Remove School From Battle

$('.owl-compare').on("click",".removeitem" ,function(e){

    var maxItems = 3;

	if( $(this).hasClass("mobile") ){ 
		maxItems = 2;
	}
	
	var index_val = $(".owl-compare .owl-item").index($(this).parents(".owl-item"));
	var total_compare_items = $(".owl-compare .owl-item").length - 1;

	if(index_val < maxItems)
	{
		var that=index_val;
		accessLoader('.battle-mid-content');

		$.ajax({
			type: "GET",
			url: '/comparison/',		
			data: ({type:'Ajaxcall',remove_ele:true,}),		
			cache: false,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data){
				accessLoader('.battle-mid-content');
				owl.removeItem(index_val);

				/*if the number of schools being compared is 3 or less then remove school and add default 'add school' column
				otherwise, it must mean there is more than 3 schools being compared, so simply remove 'this' school without
				adding the default 'add school' column */
				if( total_compare_items >= 0 && total_compare_items <=2 ){
					owl.addItem(data, total_compare_items);
				}
				
				if(index_val == 0){
					$('#added-string').css('visibility','visible');
				}
			},
			error: function(data){
					$('#Notification-Popup').foundation('reveal','open');
					$(document).on('opened.fndtn.reveal', '#Notification-Popup', function(){       
					$('.alert-msg-div').html('Server error try again.');		
				});
			}

		})
	}else{
		owl.removeItem(index_val);	
	}
	
});



function comparisionAutocomplete(txtID,txthiddenId)
{
	var item_urlslug=$('#item_urlslug').val();
	$("#"+txtID).autocomplete();
	$(function() {	
	
		var  urlslug='';
		$("#owl-compare").find("[data-slugs]").each(function(index, element) {			
		var slug = $(element).data("slugs");
		if(slug!='')
			{ urlslug +=slug + ','; }
		});
	
		$("#"+txtID).autocomplete({
			source:"/getslugAutoCompleteData?type=colleges&urlslug="+urlslug,
			minLength: 1,
			select: function(event, ui) {
				$(this).data('hsname', ui.item.label)
				$('#'+txthiddenId).val(ui.item.slug);
			}
		});
		$("#"+txtID).change(function() {
			var _this = $(this);
			if (_this.val() !== _this.data('hsname')) {
				_this.val('');
			}
		});
	});	
	
}
