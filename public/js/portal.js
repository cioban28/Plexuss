var PlexPortal = {
	schCheckList: []
};

var ride = $('.joyride-layer').data('ride');

$(document).on('click', '.show-tutorial', function(){
	runJoyride();
});

$(document).on('change', '#select-all-schools', function(){
	var _this = $(this), schools = $('.list-items .school.item'), checked = false,
		self = null;

	if( _this.is(':checked') ) checked = true;
	$.each(schools, function(){
		$(this).find('.select-school-chkbx').prop('checked', checked).trigger('change');
	});
});

$(document).on('change', '.select-school-chkbx', function(){
	var _this = $(this), item = _this.closest('.school.item');
	if( _this.is(':checked') ) item.addClass('selected');
	else item.removeClass('selected');
});

$(document).on('showTutorial', function(){
	ride = $('.joyride-layer').data('ride');
	if( !ride ) runJoyride();
});

$(document).on('click', '.revenue-organizaton-button', function(event) {
    var college = $(this).data('college'),
        modal = $('#partner-redirect-modal'),
        recruitmeModal = $('#recruitmeModal'),
        type = college.ro_detail.type;

    $('.manage-students-ajax-loader').show();

    $.ajax({
        url: '/ajax/recruiteme/' + college.college_id,
        type: 'GET',
        data: { ro_id: college.ro_detail.ro_id },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response) {
        if (type == 'click' || type == 'linkout') {
            if (response.status == 'success') {
                modal.data('url', response.url);
                modal.foundation('reveal', 'open');
            }
        } else if (type == 'post') {
            recruitmeModal.html(response);
            recruitmeModal.foundation('reveal', 'open');
        }

        $('.manage-students-ajax-loader').hide();

    });
});

$(document).on('click', '#partner-redirect-modal .partner-redirect-continue-button', function(event) {
    var modal = $('#partner-redirect-modal'),
        url = modal.data('url');

    window.open(url, '_blank');
    modal.foundation('reveal', 'close');
});

function runJoyride(){
//initialize instance
	var enjoyhint_instance = new EnjoyHint({
		onStart: function(){
			$('.joyride-layer').show();
		},
		onEnd: function(){
			$('.joyride-layer').hide();
			if( !ride ) hasTakenARideofJoy();
		},
		onSkip: function(){
			$('.joyride-layer').hide();
			if( !ride ) hasTakenARideofJoy();
		}
	});

	//simple config. 
	//Only one step - highlighting(with description) "New" button 
	//hide EnjoyHint after a click on the button.
	var enjoyhint_script_steps = [
	  {'next .item[data-name="navtoggle"]' : 'Use this to open and close your menu.', showSkip: false},
	  {'next .item[data-name="messages"]' : 'This is your Message Inbox. This is where we keep track of all your communication between you and colleges.', showSkip: false},
	  {'next .item[data-name="portal"]' : 'This is your list. Here, you can add colleges that you are interested in or want to be recruited by.', showSkip: false},	
	  {'next .item[data-name="recommendationlist"]' : 'Plexuss will recommend you colleges, you can say "yes" to add to your list, or "no" to remove.', showSkip: false},
	  {'next .item[data-name="collegesrecruityou"]' : 'Schools that want to recruit you will show up here. You can say "yes" to add to your list, or "no" to remove.', showSkip: false},
	  {'next .item[data-name="collegesviewedprofile"]' : 'Here you can see colleges that have viewed you. You can say "yes" to add to your list, or "no" to remove.', showSkip: false},
	  {'next .item[data-name="getTrashSchoolList"]' : 'Restore schools you have blocked communication with.', showSkip: false},
	  {'next .action.addschools' : 'Add schools to your list by clicking here.', showSkip: false},
	  {'next .action.trash' : 'Move school to trash.', showSkip: false},
	  {'next .action.compare' : 'Compare schools side by side to see which one fits your needs more.', showSkip: false},	  
	];

	//set script config
	enjoyhint_instance.set(enjoyhint_script_steps);

	//run Enjoyhint script
	enjoyhint_instance.run();
};

function hasTakenARideofJoy(){
	$.ajax({
        url: '/ajax/portal/didJoyride',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {data: 1},
        type: 'POST'
    }).done(function(data){
		// console.log(data);
	});
};

function initFunctions(){
	$(document).foundation();
	// initialize messages items since they're brought in by AJAX and not present on page load
	Plex.messages.initialize();
}

function loadPortalTabs(loadType, id){

	setTopBlackTabs(loadType);

	var element = loadType;
	//console.log(loadType);
	$('#ajaxloader').show();

	//check if this is a NEW message to a school and act accordingly 
	var _url;




	if (loadType == 'messages' && id) {
		console.log("messages called");
		_url = '/ajax/portal/'+ loadType + "/" + id + '/college';
	} else{
		_url = '/ajax/portal/' + loadType;
	};
	

	$.ajax({
		type: "GET",
		url: _url ,
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data){
			$('#ajaxloader').hide();

			//Set a data type on portalListwrapper the tracks what section we are on.
       		$('#portalListwrapper').html(data).data('type', loadType);

       		// init socket io
       		if( loadType === 'messages' ) initUserSocketClient();

			// initFunctions();

			// This toggles the Message scripts and heartbeats off and on.
			// if (loadType == 'messages') {
			// 	loadMessageScripts();
			// } else{
			// 	killmessageScripts();
			// };


			if(typeof PlexScholarships !== 'undefined' && loadType === 'scholarships'){
				PlexScholarships.pageInit();	

			}
		}
	});
}

function setTopBlackTabs(type){
	//sets the active tab on the top of the pages.
	$('.portalTab').removeClass('activeTab');

	if (type == 'portal' || type == 'recommendationlist' || type == 'getTrashSchoolList' ) {
		$('.schoolTab').addClass('activeTab');
	};

	if (type == 'messages') {
		$('.messageTab').addClass('activeTab');
	};
}

function loadMessageScripts(){
	Plex.messages.getTopicList();
	Plex.messages.startMessageHeartBeatTimer();
}

function killmessageScripts(){
	Plex.messages.stopMessageHeartBeatTimer();
}

function showMenu( expandID, expandDiv ) {
    $('.' + expandDiv).slideToggle(500, function() {
        $('.' + expandID).toggleClass("run");
    });
}

function settingPopup(){
	$('#portalSettingModel').foundation('reveal', 'open');
}

function addSchoolPopup(){
	$('#portalAddSchoolModel').foundation('reveal', 'open');
	setAutoCompleteForAddSchool();
}

function setAutoCompleteForAddSchool(){
	
	$("#addschool_1").autocomplete({
		source:"/getAutoCompleteData?zipcode=" + '95376' + "&type=college",
		minLength: 1,
		select: function(event, ui) {
			$(this).data('hsname', ui.item.label);
			$('#addTomyList').data('revealAjax', '/ajax/recruiteme/'+ui.item.id );
		}
	});

	$("#addschool_1").change(function() {
		var _this = $(this);
		if (_this.val() !== _this.data('hsname')) {
			_this.val('');
		}
	});
}

function applyScholarshipPopup(){
	$('#applyScholarshipModel').foundation('reveal', 'open');	
}

// Set Iframe height
var isInIframe = (window.location != window.parent.location) ? true : false;
if(isInIframe){ 
	parent.setIframeHeight($(document).height()+5);
}

function setIframeHeight(h){
	$("iframe").each(function(index, element) {
		if($(element).is(":visible")){
			$(this).height(h+'px');
		}    
	});
}

function stickFooter(divID){
 var floatingHeaderActive = false; 
   $(document).scroll(function(eD, eO){
     //if(floatingHeaderActive && $(window).scrollTop() == 0) {floatingHeaderActive = false; $('.'+divID).fadeOut();}
     //if(!floatingHeaderActive && $(window).scrollTop() > 0) {floatingHeaderActive = true; $('.'+divID).fadeIn();}
    // console.log($(document).scrollTop());
   });  
}

function WriteMsg(divID){
	$('.msgloader').show();
	$.ajax({
	type: "GET",
	url: '/portal/writemessage',
	//dataType: "json",	
	//data: ({ID:ID}),	
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data)
		{	
			$('.msgloader').hide();
       		$('#'+divID).html(data);	
		}
	})
}

function messageThread(divID,ID){
	$('.msgloader').show();
	$.ajax({
	type: "GET",
	url: '/portal/messagethread',
	//dataType: "json",	
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	data: ({ID:ID}),	
		success: function(data)
		{	
			$('.msgloader').hide();
       		$('#'+divID).html(data);	
		}
	})
}

//Full Calender
var m = moment();
var handleCalendar = function () {
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	var h = {};

        //predefined events
        /*$('#event_box').html("");
        addEvent("My Event 1");
        addEvent("My Event 2");
        addEvent("My Event 3");
        addEvent("My Event 4");
        addEvent("My Event 5");
        addEvent("My Event 6");*/        
  
   var liveDate = new Date(); // current Date
  
	$('#calendar').fullCalendar({
	header: h,
	defaultView: 'month',
	editable: false,
	height:"1000",
	eventClick : function(calEvent, jsEvent, view){
	attachIframe(calEvent.id);        
	},
	selectable: true,
	selectHelper: true,
	select: function(start, end, allDay) {
	
	var check = new Date(start);
	var today = new Date();
	var checkDate = $.fullCalendar.moment(check);
	checkDate=checkDate.format('MM-DD-YYYY');
	var todayDate = $.fullCalendar.moment(todayDate);
	todayDate=todayDate.format('MM-DD-YYYY'); 
	
	
		if(checkDate < todayDate)
		{
			$("#event-notallowed-modal").foundation('reveal', 'open');
		}
		else
		{
			$("#event-add-modal").foundation('reveal', 'open'); 
		}
	
	},
	
	droppable: true, // this allows things to be dropped onto the calendar !!!
	drop: function (date, allDay) {},   
	
	eventSources : '/getevents',
	eventMouseover: function(calEvent, jsEvent) {

	var startDate = calEvent.start;    
	startDate = moment(startDate,"YYYY-MM-DD hh:mm:ss a");
	startDate = startDate.format('MMM D'); 	
	
	var endDate = calEvent.end;    
	endDate = moment(endDate,"YYYY-MM-DD hh:mm:ss a");
	endDate = endDate.format('MMM D'); 	
	
	var tooltip = '';						
	tooltip +=  '<div class="event-tooltip tooltipevent">';					
	//tooltip +=  '<div class="menu-nav-div-arrow"></div>';
	tooltip +=  '<div class="event-title-detail"><span class="fs16">'+calEvent.title+'</span> <span class="fs22 d-block pt10">'+startDate+' - '+endDate+'</span></div>';
	tooltip +=  '<div class="event-body-detail">';
	tooltip +=  '<div class="small-12">';
	tooltip +=  '<div class="small-4 columns"> <img src="/images/collge_logo2.png"></div>';	
	tooltip +=  '<div class="small-8 columns no-padding pt10">';	
	tooltip +=  '<span class="c-blue fs18 f-bold">UCI</span>  <br />  <span class="c79 fs14">Irvine CA</span>';		
	tooltip +=  '</div><div class="clearfix"></div></div></div></div>';	
	
	
	$("body").append(tooltip);
	$(this).mouseover(function(e) {
	$(this).css('z-index', 10000);
	$('.tooltipevent').fadeIn('500');
	$('.tooltipevent').fadeTo('10', 1.9);
	}).mousemove(function(e) {
	$('.tooltipevent').css('top', e.pageY + 10);
	$('.tooltipevent').css('left', e.pageX + 20);
	});
	},
	
	eventMouseout: function(calEvent, jsEvent) {
	$(this).css('z-index', 8);
	$('.tooltipevent').remove();
	},
	
	eventAfterAllRender:function(view){	
	setCurrMonthText();
	}
	});
        
	$(".prev-button").click(function(){
	$('#calendar').fullCalendar('next');
	setCurrMonthText();
	});     
	$(".next-button").click(function(){
	var obj=$('#calendar').fullCalendar('prev');

	
	setCurrMonthText(); 
  });
      
 }

function setCurrMonthText(){
  $("#curr_month").html($('#calendar .fc-header-title').html());
}

function addRow(tableID){
	var table = document.getElementById(tableID);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
	rowcountvar=document.getElementById("rowcountvar").value;
	rowcountvar=rowcountvar*1;
	nval=rowcountvar+1;
	
	document.getElementById("rowcountvar").value=nval;	
	row.id="r"+nval; 
	strconc="'dataTable'"; 
		   
	var cell1 = row.insertCell(0);
	cell1.innerHTML = ' <input type="text" name="addschool[]" id="addschool_'+nval+'" placeholder="Start typing college name"><input type="hidden" name="college_id[]" id="college_id_'+nval+'" value="" class="addschool-txt">';
	var cell2 = row.insertCell(1); 
	cell2.innerHTML = '<img src="/images/nav-icons/deleteIcon.png" title="Delete" onclick="deleteRow('+nval+','+strconc+')" />';						
	collegeAutocomplete('addschool_'+nval+'','college_id_'+nval+'');				  
}

function deleteRow(rowid,tableID){
  try {
  var table = document.getElementById(tableID);
  var rowCount = table.rows.length;
  
  for(var i=0; i<rowCount; i++) {
	  var row = table.rows[i];
	  if(row.id=="r"+rowid) {
		  table.deleteRow(i);
		  rowCount--;
		  i--;					
	  } 
  }
  
  }catch(e) {
   alert(e);
  }			
 }
 
function addSchoolList(e){
	var txtval=$('.addschool-txt').val();
	if(txtval!='')
	{
		accessLoader('.model-inner-div');		
		datastring=$('.addschool-txt').serialize();	
		$.ajax({
			type: "GET",
			url: '/addschool',	
			data: ({datastring:datastring}),	
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data)
			{	
				accessLoader('.model-inner-div');		
				if(data!='')
				{
					$('#portalAddSchoolModel').foundation('reveal', 'close');
					loadPortalTabs('manageschool','menu1');
				}
			}
		})
	}
	else
	{
		alert('Select College First.')
	}
}

//setting Status
function setlistStatus(status,ID){
	var trcolor='';
	$('.msgloader').show();
	$.ajax({
	type: "GET",
	url: '/setliststatus',	
	dataType:'JSON',	
	data: ({status:status,ID:ID}),	
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(resdata)
		{	
			$('#menu-nav-div-'+ID).hide();
			$('.msgloader').hide();			
				var spanarrow = '';						
				spanarrow =  '<span class="expand-toggle-span" id="status-span-'+ID+'"></span>';
				$('#status_val'+ID).html(resdata.status_val+''+spanarrow);				
				$('#trrow_'+ID).css('background-color', resdata.trcolor);
		}
	})	
}

function trashScholarship(){

	$.ajax({
		type: "POST",
		url: '/ajax/portal/trashScholarships',
		data: { trashList : PlexPortal.schCheckList },
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data){	
			loadPortalTabs( 'scholarships' );	
		}
	})
}

function trashSpecificScholarship(id){

	$.ajax({
		type: "POST",
		url: '/ajax/portal/trashScholarships',
		data: { scholarship_id : id },
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data){	
			loadPortalTabs( 'recommendationlist' );	
		}
	})
}

function addUserScholarship(id){
	var status = "finish";

	$.ajax({
		type: "POST",
		url: '/ajax/queueScholarship',
		data: { scholarship : id, status : status },
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data){	
			loadPortalTabs( 'scholarships' );	
		}
	})
}

function trashSchool(ID){

	//We need to pass a json object to backend.
	//If a single item only send 1. If mutiselect send many
	var e ={};

	if (ID) {
		e = {"0" : ""+ID}
	} else{
		var inputs = $('#content-list-div input:checked');

		if (inputs.length < 1 ) {
			alert('Please select a school to place in the trash');
			return;
		};

		$.each(inputs, function(index, val) {
			var x = $(val).data('info');
			e[index] = x.id;
		});
	};


	json_data = JSON.stringify(e);

	$.ajax({
		type: "POST",
		url: '/ajax/recruiteme/adduserschooltotrash',
		data: { "obj" : json_data },
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data){	
			//get the section type from portalListwrapper.
			//loadPortalTabs( $('#portalListwrapper').data('type') );	
			 if( window.location.pathname.indexOf('get_started') > -1 ) justInquired(data.inquired_list);
                else window.location.href = '/portal';
		}
	})
}

function restoreSchool(ID, type){

	//We need to pass a json object to backend.
	//If a single item only send 1. If mutiselect send many
	var e =[];
	if (ID) {
		e.push({"id" : ID, "type": type});
	} else{
		var inputs = $('#content-list-div input:checked');
		if (inputs.length < 1 ) {
			alert('Please select a school to restore or click the restore button.');
			return;
		};

		$.each(inputs, function(index, val) {
			e[index] = { id:$(val).val(), type: $(val).attr('data-type') };
		});
	}

	json_data = JSON.stringify(e);

	$.ajax({
		type: "POST",
		url: '/ajax/recruiteme/restore',
		data: { "obj" : json_data },
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data){
			//get the section type from portalListwrapper.
			loadPortalTabs( $('#portalListwrapper').data('type') );			
		}
	})
}

function selectCheckbox(URL_val){
	var r= "";
	var s="";
	var selected=false;
	
	$('.check_group').each(function(index, element) {		
         if ($(element).is(':checked')) {
			 r += $(element).val()+"|";
			selected = true;
		 }
    });
	
	r += '0';
	if(selected){
		var confirm_msg;
		var success_msg;
		if(URL_val=='restoreschool')
		{
			confirm_msg="Are you sure want to restore ?";
			success_msg="The colleges you selected have been restored successfully.";
		}
		else if(URL_val=='deleteschool')
		{
			confirm_msg="Are you sure want to permanantly delete?";
			success_msg="The colleges you selected have been deleted successfully.";
		}
		else
		{
			confirm_msg="Are you sure want to trash ?";
			success_msg="The colleges you selected have been moved to the trash.";
		}
		
		var confirmation = confirm(confirm_msg);
		if(confirmation!=true){
			return false;
		}	
		
		accessLoader('.list-table');
		$.ajax({ url:'/'+URL_val,
		type: "GET",	
		data: ({ID:r}),	
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data)
			{	
				accessLoader('.list-table');
				var c_ID = r.split("|");
			
				var myarr = c_ID.length;			
				for (i = 0; i < myarr; i++)
				{
					 var rowId = c_ID[i].split("-");
					$('#trrow_'+rowId[0]).remove();	
				}				
				$('.menu-nav-div').hide();
			    alertMsg(success_msg,'success');
				
			},
			error : function(data)
			{
				$('#Notification-Popup').foundation('reveal','open');
				$(document).on('opened.fndtn.reveal', '#Notification-Popup', function (){       
				$('.alert-msg-div').html('Server error try again.');		
				});
			}
		});
	} else {				
		$('#Notification-Popup').foundation('reveal','open');
		$(document).on('opened.fndtn.reveal', '#Notification-Popup', function (){       
			$('.alert-msg-div').html('Please choose a record.');		
		});
	}
}

function checkall(thisid){
	
	if($(thisid).is(":checked")){			 
		$('.check_group').each(function(index, element) {		
			 $(element).prop("checked",true);
		});	
	}else{
		 
		$('.check_group').each(function(index, element) {		
			 $(element).prop("checked",false);
		});	
	}
}

function Opendiv(obj,list_ID,tab,menu){
	var parTr = $(obj).parents("tr");	
	var tr_id =$(parTr).attr('id');
	
	if($(parTr).next('tr').hasClass('subtr'))
	{
		$(parTr).next('tr').slideToggle('fast');
		return;
	}
	
	var ID=tr_id.split('_');
	
	accessLoader('.list-table');				
	$.ajax({
			type: "GET",
			url: '/getnotificationdeatils/' + Plex.ajaxtoken,
			
			data: ({list_ID:list_ID,tab:tab,menu:menu}),	
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				success: function(response)
				{					
					accessLoader('.list-table');	
					$('<tr class="subtr" id="subtr_'+ID[1]+'" style="display: none;"><td colspan="6">'+response+'</td></tr>').insertAfter(parTr);  
					setTimeout(function(){$(parTr).next('tr').slideToggle('fast')},1000);
				},
				error : function(data)
				{
			$('#Notification-Popup').foundation('reveal','open');
			$(document).on('opened.fndtn.reveal', '#Notification-Popup', function (){       
			$('.alert-msg-div').html('Server error try again.');		
			});
		}
			})
}

function settingStatus(UserId){
	accessLoader('.model-inner-div');
	var datastring=$('.setting_form').serialize();	
		$.ajax({	
		url: '/portal/settingstatus/' + Plex.ajaxtoken,
		data: ({datastring:datastring,UserId:UserId}),	
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data) {			
			accessLoader('.model-inner-div');
			$('#portalSettingModel').foundation('reveal','close');	
			
			$('.menu-nav-div').hide();
			alertMsg('Settings save sucsessfully','info');
			
		},
	error : function(data)
		{
			$('#Notification-Popup').foundation('reveal','open');
			$(document).on('opened.fndtn.reveal', '#Notification-Popup', function (){       
			$('.alert-msg-div').html('Server error try again.');		
			});
		}
	});	
}

$('[name="other-chk"]').click(function(){
	if($('[name="other-chk"]').is(":checked")){
	$('[name="other_val"]').show();
	}
	else
	{
	$('[name="other_val"]').hide();
	}
})

function interestedReasonSetting(UserId,collegeID){
	accessLoader('.model-inner-div');
	var datastring=$('[name="interested_reason"]').serialize();	
		$.ajax({	
		url: '/portal/interestedSetting/' + Plex.ajaxtoken,
		data: ({datastring:datastring,UserId:UserId,collegeID:collegeID}),	
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data)
		{	
			accessLoader('.model-inner-div');
			$('#PlexussNotificationPopup').foundation('reveal','close');
			
			alertMsg('Settings save sucsessfully','info');
					
		},
	error : function(data)
		{
			$('#Notification-Popup').foundation('reveal','open');
			$(document).on('opened.fndtn.reveal', '#Notification-Popup', function (){       
			$('.alert-msg-div').html('Server error try again.');		
			});
		}
	});	
}

// datatable sortable
function orderListSortable(){
	$("#list-table tbody").sortable({
		cursor: "move",
		update: function(event, ui) {
			//endPosition = ui.item.prevAll().length + 1;
			var sIDList = "";
			$('#list-table input[name=check_group]').each(function(i){
				sIDList = sIDList + ',' + $(this).val();
			});
			accessLoader('.list-table');
			$.getJSON('/portal/updategridorder', {
				method:'YourMethod',
				returnFormat:'JSON',
				listIDs:sIDList
			},
			function(data){
				if(data== 1){
					accessLoader('.list-table');
					$('#Notification-Popup').foundation('reveal','open');
					$(document).on('opened.fndtn.reveal', '#Notification-Popup', function (){       
						$('.alert-msg-div').html('Display order updated.');		
					});
				} else {
					$('#Notification-Popup').foundation('reveal','open');
					$(document).on('opened.fndtn.reveal', '#Notification-Popup', function (){       
						$('.alert-msg-div').html('Sorry, there was a problem updating the display order.');		
					});
				}
			});
		}
	});
}

function alertMsg(msg,classname){
	$('.portal_alert').show();
	$('.portal_alert').addClass(classname);
	$('.alert-div-msg').html(msg);
	$('.portal_alert').delay(4000).fadeOut(3000);				
}


function portalCompareSchools(){
	var inputs = $('#content-list-div input:not("#select-all-schools"):checked');

	if (inputs.length < 1 ) {
		alert('Please select a school to compare.');
		return;
	};

	var url ='/comparison?UrlSlugs=';

	$.each(inputs, function(index, val) {
		var x = $(val).data('info');
		url += x.slug +',' ;
	});

	window.location = url;
}

function getUserCollegeInfo(ele, schoolId){
	var ele = $(ele);
	var downarrow = ele.find('.smallArrow');
	var row = ele.parents('.item:first');
	var dropdownbox = row.find('.schooldropdown');

	if (downarrow.hasClass('active')) {
		dropdownbox.hide(300);
		downarrow.removeClass('active');
	} else {
		$('#ajaxloader').show();
		downarrow.addClass('active');
		$.ajax({
			type: "GET",
			url: '/ajax/recruiteme/portalcollegeinfo/' + schoolId,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data){
				$('#ajaxloader').hide();
	       		dropdownbox.html(data).show(300);	
			}
		})
	};	
}


function showUserrecruitInfo(ele, schoolId){
	var ele = $(ele);
	var downarrow = ele.find('.smallArrow');
	var row = ele.parents('.item:first');
	var dropdownbox = row.find('.schooldropdown');

	if (downarrow.hasClass('active')) {
		dropdownbox.hide(300);
		downarrow.removeClass('active');
	} else {
		$('#ajaxloader').show();
		downarrow.addClass('active');
		$('#ajaxloader').hide();
	    dropdownbox.show(300);
	};
}


//mobile chat
$(window).resize(function(){
	if( $(window).width() < 640 ){
		if( $('.rightMessageColumn').is(':visible') && $('.leftMessageColumn').is(':visible') ){
			$('.leftMessageColumn').show();
			$('.rightMessageColumn').hide();
		}else if( $('.rightMessageColumn').is(':visible') ){
			$('.rightMessageColumn').show();
		}else{
			$('.leftMessageColumn').show();
		}
	}else{
		$('.rightMessageColumn').show();
		$('.leftMessageColumn').show();
	}
});

$(document).on('click', '.messageMainWindow .leftMessageColumn .messageContacts:not(.loadmore-row)', function(){
	if( $(window).width() < 640 ){
		var image = "<img src=\"" + $(this).find('#user-image-msgs img')[0].src + "\">";
		var thread_names = ' < ' + image + ' ' + $(this).find('.messageName').text();
		$('.chat-msg-title-back').addClass('is-mobile-back-btn').html(thread_names);
	    Plex.common.slideHide( $(this).closest('.leftMessageColumn'), $('.rightMessageColumn'), 'left', 500);
	}
});

//when mobile chat back button is pressed to go back to list of college admins to chat with
$(document).on('click', '.chat-msg-title-back.is-mobile-back-btn', function(){
	if( $(window).width() < 640 ){
		$('.chat-msg-title-back').removeClass('is-mobile-back-btn').html('MESSAGES');
	    Plex.common.slideHide( $('.rightMessageColumn'), $('.leftMessageColumn'), 'right', 500);
	}
});

$(document).on('click', '#content-list-div .eddy-school-link', function() {
    window.open('https://www.elearners.com/a/Plexuss', '_blank');
});


$(document).on('mouseover', '.appl-tip', function(){
	$(this).parent().find('.appl-tipper').show();
});

$(document).on('mouseout', '.appl-tip', function(){
	$(this).parent().find('.appl-tipper').hide();
});


// $(document).on('click', '.sch-remove-scholarship', function(){

// });

$(document).ready(function(){

	$(document).on('click', 'input.sch-checkbox', function(){

		var parent = $(this).closest('.sch-table-result-wrapper ');
		var sid = parent.attr('data-sid');

		var i = PlexPortal.schCheckList.indexOf(sid);

		if( i !== -1){
			PlexPortal.schCheckList.splice(i, 1);
		}else{

			PlexPortal.schCheckList.push(sid);
		}

		// console.log(PlexPortal.schCheckList);
	});


});




//Select All Schools on All Schools Checkbox
// $(document).on('change', '#select-all-schools-modal', function(){
// 	var _this = $(this), schools = $('.list-items-modal .school.item'), checked = false,
// 		self = null;

// 	if( _this.is(':checked') ) checked = true;
// 	$.each(schools, function(){
// 		$(this).find('.select-school-chkbx').prop('checked', checked).trigger('change');
// 	});
// });
