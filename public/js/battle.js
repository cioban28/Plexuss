$(document).foundation();

$(document).ready(function(e) {
	getBattleAutocomplete('collegeInfoAutoComplete2','CollegePickedId2');	
	
});

/*function expandDivContent(expandID,expandDiv) {	

    $('#'+expandDiv).slideToggle(500, function() {
        $('#'+expandID).toggleClass("run");       
    });
}
*/
function stickHeader(divID,offsetDiv)
{
	(function ($) {
	var floatingHeaderActive = false;
	$(document).ready(function(){
	$(document).scroll(function(eD, eO){
	var battleobj =	$('#'+offsetDiv).offset();
	battleobjTop = battleobj.top;	
	if(!floatingHeaderActive && $(window).scrollTop() > parseInt(battleobjTop)) {floatingHeaderActive = true; $('#'+divID).fadeIn();}
	
	if(floatingHeaderActive && $(window).scrollTop() < parseInt(battleobjTop)) {floatingHeaderActive = false; $('#'+divID).fadeOut();}

	//console.log($(document).scrollTop());
	});
	});
	})(jQuery);
}

//------------------------------------------------ CompareBox Desktop Jquery ----------------------------------------------

function getBattleAutocomplete(txtID,txthiddenId){
	var c_id;
	$("#"+txtID).autocomplete({
			source:"getBattleAutocomplete?type=college",
			minLength: 1,
			
			create: function () {
				$(this).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				
				 var inner_html = '';
				
					var inner_html = '';
					inner_html +=  '<a><div class="list_item_container">';
					inner_html +=  '<div class="image"><img src="'+ item.image + '"></div><div class="title">' + item.label + '</div>';
					inner_html +=  '<div class="description">' + item.state + '</div>';
					inner_html +=  '</div></a>';
				
				 return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append(inner_html)
				.appendTo( ul );
				};
			},
			
			select: function(event, ui) {
				c_id = ui.item.id;

				$('#'+txthiddenId).val(c_id);
				if(txtID=='collegeInfoAutoComplete1') {
					Addcollege(c_id);
				}
				else {
					MobileAddcollege(c_id);
				}
			}
		});
}






function removeElement(obj,ID){	
	
	$("#school-logo-ul li").each(function(index, element) {
		var college=$(this).attr('id');
		var LiID ='school_'+ ID +'';			
		if(college==LiID)
		{
			$(element).draggable("enable");
			$(element).removeClass('school-li-bdr');		
			$(element).removeClass('dropped');
			$(element).css({'top':'0px','left':'0px'})	
		}
    });
	
	var chpos = $(obj).parents(".item").data("index");
	
	$(".owl-wrapper").each(function(index, element) {
		
       var owlitemobj = $(element).children(".owl-item").eq(chpos);
	  
		/*$(obj).parents(".item").each(function(index, element) 
		{
			$(element).find(".school-img").html('');
			$(element).find(".school-compare-dragdrop").show();
			//console.log($(element));
			$(element).find(".school-compare-logo").html('');	
        });*/		
		
		$(".item").each(function(index, element) 
		{
			if($(element).data("index")==chpos){
				$(element).find(".school-img").html('');
				$(element).find(".school-compare-dragdrop").show();				
				$(element).find(".school-compare-logo").html('');	
			}
        });
		
		$("#start-battle-div").children("div").each(function(index, element) {										
			var field = $(element).children("div").eq(0).data("fieldfor");		 
			if(typeof field !== "undefined"){
				$(element).children("div").eq(chpos).children("span").html('');
			}		  
		});
    });
 }
 
<!------------------------------------------------ CompareBox Desktop Jquery ---------------------------------------------->	

<!------------------------------------------------ CompareBox Mobile Jquery ---------------------------------------------->

//Add College in mobile version//


//Remove College in mobile version//
function removeElementMobile(obj,ID)
{	
	$('.selected-college-div .school-logo-ul li').each(function(index, element) {
	var college=$(this).attr('id');
	var LiID ='school_'+ID +'';		
		
		if(college==LiID)
		{
		  $(element).removeClass('school-li-bdr');			
		}
	});
	
	var chpos = $(obj).parents("li").data("collegeid");

	$("#mobile-content-battle-div").find('[data-fieldfor]').each(function(index, element)
	{
		var field = $(element).data("fieldfor");
		if(typeof field !== "undefined"){
			$(element).children("div").eq(chpos).html('');
		}
								 						
	});
	
	$('.m-college-logo').find('[data-collegeID]').eq(chpos).children('.m-school-not-blank').html('');	
	$('.m-college-logo').find('[data-collegeID]').eq(chpos).children('.m-school-blank').show();		
	$("#floating-header-mobile-battle").find('[data-collegeID]').eq(chpos).html('');
	
		$("#school-logo-ul li").draggable("enable");	
}
<!------------------------------------------------ CompareBox Mobile Jquery ---------------------------------------------->
