function OpenLinks(expandID, expandDiv) {
        $('#' + expandDiv).slideToggle(500, function() {
        $('#' + expandID).toggleClass("run");
    });
}

/* init the Range Sliders */
function RangeSlider(id,displaydiv,mintxt,maxtxt,mintxtval,maxtxtval) {	

	 var min_val=0;
	 var max_val;	 

	 if(displaydiv=='miles_range')
	 {max_val=250;}	
	  
	 else if(displaydiv=='applicants_range')
	 {max_val=100;}

	else if(displaydiv=='enrollment_range')
	 {max_val=250000;}
	 
	 else
	 {max_val=5000;}

	$("#" + id).slider({
        range: true,
		min: min_val,
        max: max_val,		
        values:[mintxtval,mintxtval],
        slide: function(event, ui)
		 {
            if(displaydiv=='miles_range')
			{
				$("#"+displaydiv).html(  'Within<br/>' + ui.values[0] + '-' + ui.values[1] + ' miles' );
			}
			else
			{
				$("#"+displaydiv).val( ui.values[0] + '-' + ui.values[1] );	
			}
			$("#"+mintxt).val(ui.values[0]);
			$("#"+maxtxt).val(ui.values[1]);
        }
    });
	
	if(displaydiv=='miles_range')
	{
		$("#"+displaydiv).html(  'Within<br/>' + $("#"+id).slider("values", 0) + '-' + $("#"+id).slider("values", 1) + ' miles' );
	}
	else
	{
		$("#"+displaydiv).val( $("#"+id).slider("values", 0) + '-' +  $("#"+id).slider("values", 1) );	
	}	
	$("#"+mintxt).val($("#"+id).slider("values", 0));
	$("#"+maxtxt).val($("#"+id).slider("values", 1))
	
    /*$("#"+displaydiv).val(+ $("#"+id).slider("values", 0) +
        " -" + $("#"+id).slider("values", 1));*/
}

function disableZipRangeSlider(){

	$('#slider-range-0').slider( "disable" );
}


function RangeSliderMax(id,displaydiv,hiddentxt,val){

   $("#" + id).slider({
     	range: "min",
     	min: 0,
     	max: 90000,
     	value: 500 ,
     	step : 100,
     	value: val,
	    slide: function( event, ui ) {
	    	console.log('Touched');
	    	$( "#"+displaydiv ).val( ui.value );
			$( "#"+hiddentxt ).val( ui.value );
	   }
    });

    $( "#"+displaydiv ).val( $( "#"+id ).slider( "value" ) );
	$( "#"+hiddentxt ).val(  $( "#"+id ).slider( "value" ) );
}


function AjaxSelectBox(ID,AjaxUrl,Selectbox,sel_Value,filterby){ ///// Ajax Select Box Function
	
	ID=ID.replace(/^\s+|\s+$/g,'');
	$.ajax({
	type: "GET", 
	url:'/'+AjaxUrl, 
	
	data: ({ID:ID,filterby:filterby}),
	cache: false, 
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	success: function(data){
	
		EmptyListbox(Selectbox);
		AddOptiontoListBox(Selectbox,"","No preference");
		
		if(data!="" && data!=null){
			 
			var ArrData=data.split("***");
			
			if(ArrData.length>0){
				for(var i=0;i<ArrData.length;i++){
					var mySplit=ArrData[i].split("|||");
					
					var OptionValue=mySplit[0];
					var OptionText=mySplit[1];
					 
					 AddOptiontoListBox(Selectbox,OptionValue,OptionText);
					}
					$('#'+Selectbox).val(sel_Value);
				}
			}
		}
	});
}


function AddOptiontoListBox(listBoxId,Value,Text){
	
	var elSel = document.getElementById(listBoxId);	
	var opt = document.createElement("option");
	elSel.options.add(opt);
	opt.text=Text;
	opt.value=$.trim(Value);	
}

///// Ajax Select Box Function
function EmptyListbox(listBoxId){
	var elSel = document.getElementById(listBoxId);
	for (i = elSel.length - 1; i>=0; i--) {
		elSel.remove(i);   
	}
}

function checkInteger(e){

	evt=e || window.event;
	var keypressed=evt.which || evt.keyCode;
	//alert(keypressed);
	if(keypressed!="48" &&  keypressed!="49" && keypressed!="50" && keypressed!="51" && keypressed!="52" && keypressed!="53" && keypressed!="54" && keypressed!="55" && keypressed!="8" && keypressed!="56" && keypressed!="57" && keypressed!="45" && keypressed!="46" && keypressed!="37" && keypressed!="39" && keypressed!="9")
	{
 		return false;
	}	
}


function checkZipcode(e){

	evt=e || window.event;
	var keypressed=evt.which || evt.keyCode;
	//alert(keypressed);
	if(keypressed!="48" &&  keypressed!="49" && keypressed!="50" && keypressed!="51" && keypressed!="52" && keypressed!="53" && keypressed!="54" && keypressed!="55" && keypressed!="8" && keypressed!="56" && keypressed!="57" && keypressed!="45" && keypressed!="46" && keypressed!="37" && keypressed!="39" && keypressed!="9")
	{
 		return false;
	}
	else
	{	
			$( "#zipcode-search-txt" ).keyup(function() {
			var txtval=$('#zipcode-search-txt').val();
			if(txtval=='')
			{
				$('#miles_range').hide();
				$('#slider-range-0').hide();	
				$("#miles_range_min_val").val('');				
				$('#miles_range_max_val').val('');	
				$("#miles_range_min_val").prop('disabled', true);				
				$('#miles_range_max_val').prop('disabled', true);		
			}
			else
			{
				$('#miles_range').show();
				$('#slider-range-0').show();				
				$("#miles_range_min_val").prop('disabled', false);				
				$('#miles_range_max_val').prop('disabled', false);
			}
		});
	}
}

function formReset(formID)
{
	$("#"+formID).get(0).reset()
	$('#zipcode-search-txt').val('');
	
	 /* $(':input','#'+formID).each(function(index, element) {		
		$(this).val('');
	});*/

	
		var txtval=$('#zipcode-search-txt').val();
		if(txtval=='')
		{
			$('#miles_range').hide();
			$('#slider-range-0').hide();	
			$("#miles_range_min_val").val('');				
			$('#miles_range_max_val').val('');	
			$("#miles_range_min_val").prop('disabled', true);				
			$('#miles_range_max_val').prop('disabled', true);		
		}

		$('#slider-range-1').slider('value', 0, 0);
		$("#tuition_range" ).val(0);
		$("#tuition_max_val").val(0);

		$('#slider-range-2').slider('values', 0, 0);
		$('#slider-range-2').slider('values', 1, 250000);
		$("#enrollment_range").val( 0 + ' - ' + 250000);
		$("#enrollment_min_val").val(0);
		$("#enrollment_max_val").val(250000);

		$('#slider-range-3').slider('values', 0, 0);
		$('#slider-range-3').slider('values', 1, 100);
		$("#applicants_range" ).val( 0 + ' - ' + 100);
		$("#applicants_min_val").val(0);
		$("#applicants_max_val").val(100);

		// console.log($('#tuition_max_val').val());
}
