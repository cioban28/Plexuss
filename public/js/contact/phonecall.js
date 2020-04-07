

Plex.phonecall = {
	timelapse: 0,        // time lapsed while call is active
	timer: null,
	calling: false,
	connection: null,	// the Twilio connection object
    incomingConnection: null, // Twilio incoming connection object
	activeRow: null,    // current profile user is interacting with (inquirie_row)
	deviceReady: false,
    validatePhoneInterval: null,
    autoDialerIndex: 0,
    autoDialerContainer: $('.auto-dialer-row-container'),
    incomingCallModal: $('#incoming-call-modal'),
    autoDialerPeopleList: [],
    autoDialerBlackList: [],
};

/***********************************
*  init Twilio for making calls    *
************************************/
function initPhoneCall(){
   
    //reset device ready flag
    Plex.phonecall.deviceReady = false;

    Plex.phonecall.updateCallStatus("Preparing ...");
	    
    //destroy device if already exists
    if(Twilio.Device){
	    Twilio.Device.destroy();
	}

	//create Twilio Device -- get capability token
    Plex.phonecall.setupCall();
}

Plex.phonecall.initTransferOrgs = function() {
    $.ajax({
        type: 'GET',
        url: '/admin/inquiries/getTransferOrgs',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done((response) => {
        var phoneNumbers = response.Zeta.phones;

        var options = '';

        phoneNumbers.forEach(function(phone) {
            options += '<option value="' + phone.phone + '">'+ phone.phone +'</option>';
        });

        $('#transfer-phone-number-select').html(options);
    });
}

Plex.phonecall.initManualPostingDistributionData = function() {
    $.ajax({
        type: 'GET',
        url: '/admin/inquiries/getManualPostingDistributionData',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done((response) => {
        var clientOptions = '<option value="">Select one</option>';
        var collegeOptions = '';

        response.forEach(function(client) {
            clientOptions += "<option value='" + client.ro_id + "' data-college_options='" + JSON.stringify(client.options) + "'>" + client.name + "</option>";
            $('#posting-student-client-select').html(clientOptions);
        });

    });
}

Plex.phonecall.initSocketEvents = function() {
    var autoDialerRow = $('.auto-dialer-row-container'),
        org_branch_id = autoDialerRow.data('org_branch_id'),
        user_id = autoDialerRow.data('admin_user_id');

    // Do not init socket events to student-search
    if (document.body.id === 'admin-student-search') {
        return;
    }

    // Socket events
    var socket = io(window.location.host + ':3001', {
        secure: window.location.protocol.includes('https'),
    });

    socket.on('update:auto_dialer_black_list', function(data) {
        var black_list = data.black_list;
        var caller_user_id = data.caller_user_id;

        if (caller_user_id === user_id) { return; }

        if (!_.isEmpty(Plex.phonecall.autoDialerPeopleList)) {
            var indexPosition = Plex.phonecall.autoDialerIndex;
            var active_person = Plex.phonecall.autoDialerPeopleList[indexPosition];

            // If the user is already calling the person about to be removed, then don't remove them.
            if (Plex.phonecall.calling && black_list.indexOf(active_person.student_user_id) !== -1) {
                return;
            }

        }

        Plex.phonecall.updateAutoDialerBlackList(black_list);
    });

    socket.emit('join:crm_room', { org_branch_id: org_branch_id });
    // End socket events
}

Plex.phonecall.initIncomingCalling = function() {
    var minimizeContainer = $('.minimized-incoming-call');

    // minimizeContainer.hide();
}

Plex.phonecall.initAutoDialer = function() {
    this.autoDialerPeopleList = $('.auto-dialer-row-container').data('autodialerlist');
    this.autoDialerBlackList = $('.auto-dialer-row-container').data('autodialerblacklist');

    var currentPeopleList = this.autoDialerPeopleList;

    var currrentBlackList = this.autoDialerBlackList;

    // Filter out names without phone numbers and users from the blacklist
    this.autoDialerPeopleList = _.filter(currentPeopleList, function(person) {
        return person.phone && person.phone != ' ' && currrentBlackList.indexOf(person.student_user_id) === -1;
    });
}

Plex.phonecall.updateAutoDialerBlackList = function(blackList) {
    if (_.isEmpty(blackList)) return;

    var currentPeopleList = this.autoDialerPeopleList;
    var newBlackList = this.autoDialerBlackList.concat(blackList);

    this.autoDialerBlackList = newBlackList.slice();

    this.autoDialerPeopleList = _.filter(currentPeopleList, function(person) {
        return person.phone && person.phone != ' ' && newBlackList.indexOf(person.student_user_id) === -1;
    });

    this.updateAutoDialerPerson();
}

Plex.phonecall.getCallLogsWithTimeZone = function (phoneNumber) {
    var modal = $('#incoming-call-modal');
    var callLog = modal.find('.incoming-previously-called-log');

    callLog.html('<div class="refresh-spinner"></div>');

    $.ajax({
        url: '/admin/ajax/getCallLogsWithTimeZone',
        type: 'POST',
        data: {
            phoneNumber: phoneNumber,
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    }).then(function(response) {
        callLog.html('');

        response.forEach(function(log) {
            callLog.append(
                '<div class="single-call-log">' +
                    '<span style="font-weight: bold;">' +
                        log.date +
                    '</span>' +
                    '&nbsp;' +
                    '<span>' +
                        log.timeZone +
                    '</span>' +
                '</div>'
            );
        });
    });
}

/******************************************************
* For use when editing phone numbers from call screen
******************************************************/
Plex.phonecall.validatePhone = function(parent, phone) {
    $.ajax({
        url: '/phone/validatePhoneNumber',
        type: 'POST',
        data: {
            phone: phone,
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    }).then(function(response) {
        var hasError = response.error;
        if (hasError) {
            parent.find('.save-contact-phone-btn').addClass('disabled');
            parent.find('.edit-contact-phone').addClass('invalid');
        } else {
            parent.find('.save-contact-phone-btn').removeClass('disabled');
            parent.find('.edit-contact-phone').removeClass('invalid');
        }
    });
}

Plex.phonecall.updateAutoDialerPerson = function() {
    var person = Plex.phonecall.getActiveAutoDialPerson(),
        parent = $('.auto-dialer-row-container'),
        inquirie_container = $('.each-inquirie-container'),
        all_inqRow = $('.inquirie_row'),
        inqRow = null,
        name = null,
        fullName = null,
        clone = null;

    if (!person) {
        parent.find('.auto-dialer-call-button').addClass('hidden');
        parent.find('.auto-dialer-current-phone').html('');
        parent.find('.auto-dialer-current-name').prop('title', '');
        parent.find('.auto-dialer-current-name').html('');
        return;
    } else {
        parent.find('.auto-dialer-call-button').removeClass('hidden');
    }

    name = person.name.split(/\s+/);
    name = name[0] + ' ' + name[1][0] + '.';
    fullName = person.name;

    inqRow = $('.inquirie_row[data-uid=' + person.student_user_id + ']').first();

    all_inqRow.removeClass('active-auto-dialer-student');

    inqRow.addClass('active-auto-dialer-student');

    clone = inqRow.clone();
    
    if (clone.length > 0) {
        inqRow.remove();
        inquirie_container.prepend(clone);
    }

    Foundation.libs.tooltip.getTip($('.auto-dialer-current-name')).remove();

    parent.find('.auto-dialer-current-phone').html(person.phone);
    parent.find('.auto-dialer-current-name').prop('title', fullName);
    parent.find('.auto-dialer-current-name').html(name);
    parent.find('.auto-dialer-current-name').foundation('tooltip', 'reflow');

}


Plex.phonecall.resetAutoDialerUI = function() {
    // Auto dialer elements
    var callButton = $('.auto-dialer-call-button');
    var endButton = $('.auto-dialer-end-call-button');

    endButton.addClass('hidden');
    callButton.removeClass('hidden');
    
    Plex.phonecall.autoDialCalling = false;
}

/**************************************
* Returns the active auto dial person
**************************************/
Plex.phonecall.getActiveAutoDialPerson = function() {
    var rowContainer = $('.auto-dialer-row-container');
    var indexPosition = Plex.phonecall.autoDialerIndex;
    var loadMoreButton = $('.load-more-btn:visible');
    var dialerListLength = _.isEmpty(Plex.phonecall.autoDialerPeopleList) 
        ? 0 
        : Plex.phonecall.autoDialerPeopleList.length;

    rowContainer.find('.auto-dialer-previous-button').show();
    rowContainer.find('.auto-dialer-next-button').show();
    
    // If initial index, do not allow previous
    if (indexPosition === 0) {
        rowContainer.find('.auto-dialer-previous-button').hide();
    } else if (indexPosition === Plex.phonecall.autoDialerPeopleList.length - 1) {

        if (loadMoreButton.is(':visible')) {
            loadMoreButton.trigger('click');
        } else {
            rowContainer.find('.auto-dialer-next-button').hide();
        }
    }

    // Hide next button if Dialer List is empty
    if (dialerListLength === 0 || dialerListLength === 1) {
        rowContainer.find('.auto-dialer-next-button').hide();
    }

    return Plex.phonecall.autoDialerPeopleList[indexPosition];
}

/********************************
* get capability token
********************************/
Plex.phonecall.setupCall = function(uid){
    $.ajax({
        url: '/phone/makeCall',
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

    }).done(function(response){  
    
        Twilio.Device.setup(response);

    });
};



/**************************************************
* prints phone call related messages to #twiLog element
********************************************/
Plex.phonecall.updateCallStatus = function(message){

	if(Plex.phonecall.activeRow)
		Plex.phonecall.activeRow.find('.twilLog').text(message);
	else
		$('.twilLog').text(message);	
}

/*****************************************
* 	initiates a call
******************************************/
Plex.phonecall.makeCall = function(phoneNumber) {

	//var sid = Plex.phonecall.connection.parameters.CallSid;
	var uid = Plex.phonecall.activeRow.data('uid');


    //var uid = Plex.phonecall.activeRow.data('uid');
    var phoneNumber = Plex.phonecall.activeRow.find('#_contactCall').data('phone');


    Plex.phonecall.updateCallStatus( "Preparing...");


	//initialize the phone log -- only after that is done may users make calls
	$.ajax({
		url: '/phone/initilizePhoneLog',
		type: 'POST',
		data: {  
			    user_id: uid,
			    phoneNumber: phoneNumber
			  },
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(res){

		Plex.phonecall.calling = true;
		
		//make the call
	    Plex.phonecall.updateCallStatus("Calling " + phoneNumber + "...");
	    var params = {"phoneNumber": phoneNumber};
	    Plex.phonecall.connection = Twilio.Device.connect(params);

        console.log(Plex.phonecall.connection);
	
	});

	
};


/*************************************
* Auto Dialer make call
***************************************/
Plex.phonecall.autoDialerMakeCall = function (person) {
    Plex.phonecall.updateCallStatus( "Preparing...");

    var callButton = $('.auto-dialer-call-button');
    var endButton = $('.auto-dialer-end-call-button');
    var user_id = person.student_user_id;
    var phoneNumber = parseInt(person.phone);

    callButton.addClass('hidden');
    endButton.removeClass('hidden');

    $.ajax({
        url: '/phone/initilizePhoneLog',
        type: 'POST',
        data: {  
                user_id: user_id,
                phoneNumber: phoneNumber,
              },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(res){
        Plex.phonecall.autoDialCalling = true;
        Plex.phonecall.calling = true;
        
        //make the call
        Plex.phonecall.updateCallStatus("Calling " + phoneNumber + "...");
        var params = {"phoneNumber": phoneNumber};
        Plex.phonecall.connection = Twilio.Device.connect(params);

        mixpanel.track("Admin_Auto_Dialer_Make_Call", { "Location": document.body.id, "Student": person.student_user_id });
    });
}

/*************************************
* Auto Dialer end call, admin initiated end call
***************************************/
Plex.phonecall.autoDialerEndCall = function (that) {
    var callButton = $('.auto-dialer-call-button');
    var endButton = $('.auto-dialer-end-call-button');
    // var isAutoDialingOn = $('.auto-dial-button.toggle-button').is(':checked');
    var autoDialerIndex = Plex.phonecall.autoDialerIndex;
    var autoDialerPeopleList = Plex.phonecall.autoDialerPeopleList;
    var currentPerson = Plex.phonecall.getActiveAutoDialPerson();
    var currentTime = $('.auto-dialer-row-container .auto-dialer-call-duration').html();
    var toggleAutoDialerButton = $('.auto-dial-button.toggle-button');
    var person = null;
    var previousIndex = null;

    Twilio.Device.disconnectAll(that);

    mixpanel.track("Admin_Auto_Dialer_Hang_Up", { 
        "Location": document.body.id, 
        "Student": currentPerson.student_user_id, 
        "Call Length": currentTime,
        "Call Ended By": 'Admin',
    });

    clearInterval(Plex.phonecall.timer);
    Plex.phonecall.timerOn = false;
    Plex.phonecall.autoDialCalling = false;
    Plex.phonecall.calling = false;
    endButton.addClass('hidden');
    callButton.removeClass('hidden');

    toggleAutoDialerButton.prop('checked', false);
}

Plex.phonecall.startNextAutoDialerCall = function() {
    var callButton = $('.auto-dialer-call-button');
    var endButton = $('.auto-dialer-end-call-button');
    var isAutoDialingOn = $('.auto-dial-button.toggle-button').is(':checked');
    var autoDialerIndex = Plex.phonecall.autoDialerIndex;
    var autoDialerPeopleList = Plex.phonecall.autoDialerPeopleList;
    var currentPerson = Plex.phonecall.getActiveAutoDialPerson();
    var currentTime = $('.auto-dialer-row-container .auto-dialer-call-duration').html();
    var toggleAutoDialerButton = $('.auto-dial-button.toggle-button');
    var person = null;
    var previousIndex = null;

    // // Check if auto dialer on and start next call.
    if (isAutoDialingOn) {
        // Increment person
        previousIndex = Plex.phonecall.autoDialerIndex;

        Plex.phonecall.autoDialerIndex = (autoDialerIndex + 1) % autoDialerPeopleList.length;

        // Do not continue auto dialer if there is only one person.
        // Avoids calling the same person twice via auto dial.
        if (previousIndex === Plex.phonecall.autoDialerIndex) {
            $('.auto-dial-button.toggle-button').prop('checked', false)
            return;
        }

        Plex.phonecall.updateAutoDialerPerson();

        person = Plex.phonecall.getActiveAutoDialPerson();
        
        setTimeout(function() {
            // Recheck incase user turns it off while auto-dialer is doing its work
            var isAutoDialerStillOn = $('.auto-dial-button.toggle-button').is(':checked');

            // Do not try to call if user already initiated another call
            if (callButton.is(':visible') && !Plex.phonecall.calling && isAutoDialerStillOn) {
                Plex.phonecall.autoDialerMakeCall(person);
            }
        }, 2000);

    }
}



/*************************************
*	ends a call
***************************************/
Plex.phonecall.hangUp = function(that){

	Twilio.Device.disconnectAll(that);

	clearInterval(Plex.phonecall.timer);
	Plex.phonecall.timerOn = false;

};

/*************************************
*	get time elapsed (phone call duration)
*
***************************************/
Plex.phonecall.getTime = function(isAutoDialer){
    
    var start = new Date().getTime();
    
    var row = isAutoDialer 
        ? Plex.phonecall.autoDialerContainer
        : Plex.phonecall.activeRow;

    var child = isAutoDialer
        ? '.auto-dialer-call-duration'
        : '.contact-call-duration';

    Plex.phonecall.timerOn = true;

 	// window.clearInterval(Plex.phonecall.timer);
 	
 	Plex.phonecall.timer = window.setInterval(function()
	{
	    var time = new Date().getTime() - start;

	    elapsed = Math.floor(time / 100) / 10;
	    if(Math.round(elapsed) == elapsed) { elapsed += '.0'; }

	    var date = new Date(null);
		date.setSeconds(elapsed); // specify value for SECONDS here
		var result = date.toISOString().substr(11, 8);
		// console.log(result);

        row.find(child).text(result);

	}, 100);
 };


/***************************************
*  Get elapsed time for incoming calls
*
***************************************/
Plex.phonecall.getIncomingTime = function() {
    var start = new Date().getTime();
    
    var modalRow = Plex.phonecall.incomingCallModal;

    var modalChild = '.incoming-call-active-container .incoming-call-timer';

    var minimizedRow = $('.minimized-incoming-call');

    var minimizedChild = '.minimized-incoming-timer-number';

    Plex.phonecall.timerOn = true;

    // window.clearInterval(Plex.phonecall.timer);
    
    Plex.phonecall.timer = window.setInterval(function()
    {
        var time = new Date().getTime() - start;

        elapsed = Math.floor(time / 100) / 10;
        if(Math.round(elapsed) == elapsed) { elapsed += '.0'; }

        var date = new Date(null);
        date.setSeconds(elapsed); // specify value for SECONDS here
        var result = date.toISOString().substr(11, 8);
        // console.log(result);

        modalRow.find(modalChild).text(result);

        minimizedRow.find(minimizedChild).text(result);

    }, 100);
}


/**************************************
* toggle call button
* param: boolean calling - if calling toggle button to calling button
*						   else, toggle to not calling 
****************************************/
Plex.phonecall.toggleCallBtn = function(calling){
    // Function not neccessary for auto dialer.
    if (Plex.phonecall.autoDialCalling) { return; }

	var row = Plex.phonecall.activeRow;
	var recordBtn = row.find('.call-record-btn-wrapper');
	var recordTxt = recordBtn.find('.contact-record-text');
	var text = $(this).find('.contact-call-text'); 
	var icon = $(this).find('.contact-call-mute');
    var mtext = $(this).find('.contact-mute-txt');

	if(calling){
    	
		row.find('.contact-call-call-btn').removeClass('call');
	    row.find('.contact-call-call-btn').addClass('end');
	    text.text('End Call');

	    //also toggle recording
	    recordBtn.addClass('active');
	    recordTxt.text('Call is recording...');

	}
    else{
		
		row.find('.contact-call-call-btn').removeClass('end');
	    row.find('.contact-call-call-btn').addClass('call');
	    text.text('Call');
		
		//also toggle recording
	    recordBtn.removeClass('active');
	    recordTxt.text('Call will be recorded'); 

	    //toggle back mute
	    Plex.phonecall.connection.mute(false);
    	icon.removeClass('unmute');
		icon.addClass('mute');
    	mtext.text('Mute');   
    }
};

// Validates manual posting and disables or enables
Plex.phonecall.validateManualPostingFields = function() {
    var valid = false;
    var button = $('#posting-student-modal .posting-student-button');

    if ($('#posting-student-college-select').val() && $('#posting-student-client-select').val() && $('#posting-student-program-select').val()) {
        valid = true;
    }

    if (valid) {
        button.removeClass('disabled');
    } else {
        button.addClass('disabled');
    }

    return valid;
};

////////////////////// Twilio ready ///////////////////////
/* Callback to let us know Twilio Client is ready */
Twilio.Device.ready(function (device) {
	Plex.phonecall.deviceReady = true;
	$('.twilLog').text("Ready to make a call");
});

////////////////////////

/* Callback for when Twilio Client initiates a new connection */
Twilio.Device.connect(function (connection) {
    var incomingRequestContainer = $('.incoming-call-request-container');
    var incomingActiveContainer = $('.incoming-call-active-container');
    var _direction = connection._direction;

    if (_direction === 'INCOMING') {
        Plex.phonecall.updateCallStatus("In call with " + connection.message.phoneNumber);
        Plex.phonecall.calling = true;
        Plex.phonecall.getIncomingTime();
        incomingRequestContainer.addClass('hidden');
        incomingActiveContainer.removeClass('hidden');

    } else {
        Plex.phonecall.updateCallStatus("In call with " + connection.message.phoneNumber);
        Plex.phonecall.getTime(Plex.phonecall.autoDialCalling);
     	Plex.phonecall.toggleCallBtn(true);
    }

});


/* callback for offline */
Twilio.Device.offline(function(connection){
// console.log('offline');
	Plex.phonecall.updateCallStatus("Offline");
	initPhoneCall();
});


/* Callback for ending a call */
Twilio.Device.disconnect(function(connection){
    var inq_row = $(this).closest('.inquirie_row');
    var _direction = connection._direction;
    var incomingCallModal = $('#incoming-call-modal');
    var incomingRequestContainer = $('.incoming-call-request-container');
    var incomingActiveContainer = $('.incoming-call-active-container');

    window.clearInterval(Plex.phonecall.timer);
    Plex.phonecall.calling = false;
    Plex.phonecall.updateCallStatus("Call Ended");

    if (_direction === 'INCOMING') {
        incomingCallModal.foundation('reveal', 'close', { animation: 'fadeIn' });
        $('.minimized-incoming-call').addClass('hidden');

        setTimeout(function() {
            incomingRequestContainer.removeClass('hidden');
            incomingActiveContainer.addClass('hidden');
        }, 1000);
        
    } else {
        Plex.phonecall.toggleCallBtn(false);
    }

    if (Plex.phonecall.autoDialCalling) {
        var currentPerson = Plex.phonecall.getActiveAutoDialPerson();
        var currentTime = $('.auto-dialer-row-container .auto-dialer-call-duration').html();

        Plex.phonecall.resetAutoDialerUI();

        // If hangup was by the person being called, autoDial.
        if (connection.sendHangup === false) {
            mixpanel.track("Admin_Auto_Dialer_Hang_Up", { 
                "Location": document.body.id, 
                "Student": currentPerson && currentPerson.student_user_id,
                "Call Length": currentTime,
                "Call Ended By": 'Student',
            });

            Plex.phonecall.startNextAutoDialerCall();
        }
    }

// console.log('disconnect');	
});

/* callback for Twilio error */
Twilio.Device.error(function (error) {
// console.log('error');	
	Plex.phonecall.calling = false;
	Plex.phonecall.updateCallStatus("Error: " + error.message);
	Plex.phonecall.toggleCallBtn(false);

    if (Plex.phonecall.autoDialCalling) {
        Plex.phonecall.resetAutoDialerUI();
    }
});

Twilio.Device.incoming(function(connection) {
    var modal = $('#incoming-call-modal');

    Plex.phonecall.incomingConnection = connection;

    // Auto Reject Calls that do not have country code +1
    if (!connection.parameters.From.startsWith('+1')) {
        connection.reject();
        return;
    }

    Plex.phonecall.getCallLogsWithTimeZone(connection.parameters.From);

    modal.foundation('reveal', 'open', { animation: 'fadeIn' });
});

/* Callback when an incoming call is cancelled by caller */
Twilio.Device.cancel(function(connection) {
    var _direction = connection._direction;
    var incomingCallModal = $('#incoming-call-modal');
    var incomingRequestContainer = $('.incoming-call-request-container');
    var incomingActiveContainer = $('.incoming-call-active-container');

    if (_direction === 'INCOMING') {
        incomingCallModal.foundation('reveal', 'close', { animation: 'fadeIn' });
        $('.minimized-incoming-call').addClass('hidden');

        setTimeout(function() {
            incomingRequestContainer.removeClass('hidden');
            incomingActiveContainer.addClass('hidden');
        }, 1000);
        
    }
});

/////////////////////////////////////////////////////////
//////////////////// document ready /////////////////////
$(document).ready(function() {

	initPhoneCall();
    Plex.phonecall.initTransferOrgs();
    Plex.phonecall.initManualPostingDistributionData();

    Plex.phonecall.initAutoDialer();

    Plex.phonecall.initIncomingCalling();

    Plex.phonecall.initSocketEvents();

    $(document).on('click', '.main-manage-students-content .auto-dialer-row-container .auto-dialer-call-action-button', function(event) {
        var callButton = $('.auto-dialer-call-button');
        var endButton = $('.auto-dialer-end-call-button');

        var person = Plex.phonecall.getActiveAutoDialPerson();

        if ($(this).hasClass('auto-dialer-call-button')) {
            Plex.phonecall.autoDialerMakeCall(person);
        } else {
            Plex.phonecall.autoDialerEndCall($(this));
        }
    });

    $(document).on('click', '.main-manage-students-content .auto-dialer-row-container .auto-dialer-change-user-button', function(event) {
        var type = $(this).hasClass('auto-dialer-previous-button') ? 'previous' : 'next',
            autoDialerIndex = Plex.phonecall.autoDialerIndex,
            autoDialerPeopleList = Plex.phonecall.autoDialerPeopleList,
            autoDialerTimer = $('.auto-dialer-call-duration');

        // Do not allow switching between users if no call users exist or if someone is currently being called.
        if (_.isEmpty(autoDialerPeopleList) || Plex.phonecall.calling) return;

        switch (type) {
            case 'previous':
                if (autoDialerIndex === 0) {
                    break;
                }

                Plex.phonecall.autoDialerIndex--;
                autoDialerTimer.html('');

                break;

            case 'next':
                if (autoDialerIndex === autoDialerPeopleList.length - 1) {
                    break;
                }

                Plex.phonecall.autoDialerIndex++;
                autoDialerTimer.html('');

                break;

            default:
        }

        Plex.phonecall.updateAutoDialerPerson();
    });

    $(document).on('click', '.appr-btn-list .auto-dialer-button', function(event) {
        var all_inqRow = $('.inquirie_row');
        var loadMoreButton = $('.load-more-btn:visible');

        if ($(this).hasClass('active')) {
            // Hide auto-dialer row

            $(this).removeClass('active');
            $('.main-manage-students-content .auto-dialer-row-container').addClass('hidden');
            all_inqRow.removeClass('active-auto-dialer-student');
            mixpanel.track("Admin_Auto_Dialer_Hide", { "Location": document.body.id });            

        } else {
            // Show auto-dialer row
            if (_.isEmpty(Plex.phonecall.autoDialerPeopleList) && loadMoreButton.is(':visible')) {
                loadMoreButton.trigger('click');
            }
            $(this).addClass('active');
            $('.main-manage-students-content .auto-dialer-row-container').removeClass('hidden');
            Plex.phonecall.updateAutoDialerPerson();
            mixpanel.track("Admin_Auto_Dialer_Show", { "Location": document.body.id });
        }
    });

    $(document).on('click', '.auto-dial-button.toggle-button', function(event) {
        if ($(this).is(':checked')) {
            mixpanel.track("Admin_Auto_Dialer_Toggle_On", { "Location": document.body.id });
        } else {
            mixpanel.track("Admin_Auto_Dialer_Toggle_Off", { "Location": document.body.id });
        }
    });

	/////////////////////////////////
    //bind button for making call or hanging up
    $(document).on('click', '.call-call-btn-wrapper.call', function(){


    	//set active row
    	Plex.phonecall.activeRow = $(this).closest('.inquirie_row');	

    	var phonenumber = null;
    	var uid = Plex.phonecall.activeRow.data('uid');

    	

    	//get phonenumber
    	phonenumber = $(this).closest('#_contactCall').data('phone');


    	//if number does not exist
    	if(!phonenumber){
    		Plex.phonecall.updateCallStatus('Phone call could not be made. Phone number is invalid or non-existent.');
    		return;	
    	}

    	if(Plex.phonecall.calling == false  ){
    		Plex.phonecall.makingCall = true;
	        Plex.phonecall.makeCall($(this), phonenumber);
	    	//initPhoneCall(Plex.phonecall.activeRow);
    	}
	    else{
	    	Plex.phonecall.hangUp($(this));
		
	    }

    });

    /////////////////////////////////
    // bind mute button
    $(document).on('click', '.mute-btn-wrapper', function(){

    	Plex.phonecall.activeRow = $(this).closest('.inquirie_row');
    	var icon = $(this).find('.contact-call-mute');
    	var text = $(this).find('.contact-mute-txt');


    	if(Plex.phonecall.connection === null){
    		Plex.phonecall.updateCallStatus('Cannot mute.  Must be in a phone call.');
    		return;
    	}

    	if(icon.hasClass('mute')){
 
    		Plex.phonecall.connection.mute(true);
    		icon.removeClass('mute');
    		icon.addClass('unmute');
    		text.text('Unmute');
    	}
    	else{
    		
	    	Plex.phonecall.connection.mute(false);
	    	icon.removeClass('unmute');
    		icon.addClass('mute');
	    	text.text('Mute');
    	}
    });


    ///////////////////////////
    $(document).on('click', '.inquirie_row', function(){

    	Plex.phonecall.activeRow = $(this);
    });

    $(document).on('click', '#_contactCall .edit-contact-phone-btn', function(e){
        var editModeContainer = $(this).closest('.contact-call-number').siblings('.contact-call-number.edit-mode');

        $(this).closest('.contact-call-number').hide();

        editModeContainer.find('.edit-contact-phone-input').intlTelInput({
            utilsScript: "/js/phoneUtils.js"
        });

        editModeContainer.show();
    });

    $(document).on('click', '#_contactCall .save-contact-phone-btn', function(e){
        var parent = $(this).closest('#_contactCall');
        var phoneNumber = parent.find('.edit-contact-phone-input').intlTelInput('getNumber');
        var inqRow = $(this).closest('.inquirie_row');
        var user_id = inqRow.data('uid');

        if (!$(this).hasClass('disabled')) {
            inqRow.find('.absolute-ajax_loader').show();

            $.ajax({
                url: '/admin/savePhoneWithUserId',
                type: 'POST',
                data: { phone: phoneNumber, user_id: user_id },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).then(response => {
                inqRow.find('.absolute-ajax_loader').hide();

                if (response.status === 'success') {
                    $(this).closest('.contact-call-number').hide();
                    $(this).closest('.contact-call-number').siblings('.contact-call-number.view-mode').show();
                    parent.find('.user-contact-number-span').html(phoneNumber);
                    parent.data('phone', phoneNumber);
                } else {
                    alert('Something went wrong, try again later.');
                }
            });
        }

    });

    $(document).on('change input countrychange', '#_contactCall .edit-contact-phone-input', function(e) {
        var phone = $(this),
            parent = $(this).closest('#_contactCall'),
            isValid = $(this).intlTelInput('isValidNumber'),
            saveButton = parent.find('.save-contact-phone-btn');

        if (isValid) {
            saveButton.removeClass('disabled');
        } else {
            saveButton.addClass('disabled');
        }
    });

    $(document).on('click', '#incoming-call-modal .incoming-call-button', function(e) {
        var incomingConnection = Plex.phonecall.incomingConnection;
        var modal = $('#incoming-call-modal');
        var classes = $(this).prop('class');
        var incomingRequestContainer = $('.incoming-call-request-container');
        var incomingActiveContainer = $('.incoming-call-active-container');
        var callAction = classes.split(/\s+|incoming-call-button|-call-button/).join('');

        switch (callAction) {
            case 'answer':
                incomingConnection && incomingConnection.accept();
                break;

            case 'disconnect':
                incomingConnection && incomingConnection.reject();
                modal.foundation('reveal', 'close', { animation: 'fadeIn' });
                // UI reject logic
                break;

            default:
                // Nothing
        }

    });

    $(document).on('click', '#incoming-call-modal .incoming-modal-actions-container .minimize-modal-action-button', function(e) {
        var minimizeContainer = $('.minimized-incoming-call');
        var incomingModal = $('#incoming-call-modal');
        var classList = $(this).prop('class');
        var modalAction = classList.split(/\s+|minimize-modal-action-button|-incoming-modal-button/).join('');
        var incomingConnection = Plex.phonecall.incomingConnection;
        var incomingRequestContainer = $('.incoming-call-request-container');
        var incomingActiveContainer = $('.incoming-call-active-container');

        switch (modalAction) {
            case 'close':
                if (Plex.phonecall.calling) {
                    Plex.phonecall.hangUp($(this));
                } else {
                    incomingConnection && incomingConnection.reject();
                }

                incomingModal.foundation('reveal', 'close', { animation: 'fadeIn' });
                $('.minimized-incoming-call').addClass('hidden');

                // Timeout due to waiting for modal close animation
                setTimeout(function() {
                    incomingRequestContainer.removeClass('hidden');
                    incomingActiveContainer.addClass('hidden');
                }, 1000);

                break;

            case 'minimize':
                incomingModal.foundation('reveal', 'close', { animation: 'fadeIn' });
                minimizeContainer.removeClass('hidden');
                break;

            default:
                // Nothing
        }
    });

    $(document).on('click', '.minimized-incoming-call .minimized-end-button', function(e) {
        e.stopPropagation();
        var incomingRequestContainer = $('.incoming-call-request-container');
        var incomingActiveContainer = $('.incoming-call-active-container');

        var minimizeContainer = $('.minimized-incoming-call');

        incomingRequestContainer.removeClass('hidden');
        incomingActiveContainer.addClass('hidden');

        Plex.phonecall.hangUp($(this));

        minimizeContainer.addClass('hidden');
    });

    $(document).on('click', '.minimized-incoming-call', function(e) {
        var incomingModal = $('#incoming-call-modal');

        $(this).addClass('hidden');

        incomingModal.foundation('reveal', 'open');
    });

    $(document).on('click', '#incoming-call-modal .incoming-call-mute-toggle-button .incoming-call-mute-icon', function(e) {
        var isActive = $(this).hasClass('active');
        var muteText = $(this).siblings('.incoming-call-mute-text');
        var connection = Plex.phonecall.incomingConnection;

        if (isActive) {
            $(this).removeClass('active');
            muteText.html('Mute');
            connection && connection.mute(false);

        } else {
            $(this).addClass('active');
            muteText.html('Unmute');
            connection && connection.mute(true);

        }
    });

    $(document).on('click', '#incoming-call-modal .right-side-active-call .incoming-call-end-button', function(e) {
        var modal = $('#incoming-call-modal');
        var incomingRequestContainer = $('.incoming-call-request-container');
        var incomingActiveContainer = $('.incoming-call-active-container');

        modal.foundation('reveal', 'close', { animation: 'fadeIn' });
        $('.minimized-incoming-call').addClass('hidden');
        
        // Timeout due to waiting for modal close animation
        setTimeout(function() {
            incomingRequestContainer.removeClass('hidden');
            incomingActiveContainer.addClass('hidden');
        }, 1000);

        Plex.phonecall.hangUp($(this));
    });

    $(document).on('click', '.call-transfer-call-btn-wrapper .contact-transfer-call-btn', function(event) {
        var modal = $('#transfer-call-modal');

        if (!Plex.phonecall.connection || Plex.phonecall.calling == false) return;

        modal.foundation('reveal', 'open');
    });

    $(document).on('click', '.call-transfer-call-btn-wrapper .contact-posting-btn', function(event) {
        var modal = $('#posting-student-modal');
        var user_id = $(this).closest('.inquirie_row').data('uid');
        var studentName = $(this).closest('.inquirie_row').find('.inquiry-name').text();

        $('#posting-student-user_id').val(user_id);
        $('#posting-student-college-select').val('');
        $('#posting-student-client-select').val('');
        $('#posting-student-program-select').val('');
        $('#posting-student-college-select').html('');
        $('#posting-student-modal .posting-student-errors').html('');
        $('#posting-student-modal .posting-student-name').html(studentName);

        Plex.phonecall.validateManualPostingFields();

        modal.foundation('reveal', 'open');
    });

    $(document).on('change', '#posting-student-program-select, #posting-student-college-select, #posting-student-client-select', function(event) {
        Plex.phonecall.validateManualPostingFields();
    });


    $(document).on('change', '#posting-student-college-select', function(event) {
        var selectedIndex = event.target.selectedIndex;
        var option = event.target.options[selectedIndex].value;
        var programOptions = '';
        $('#posting-student-program-select').html('');
        $('#posting-student-program-select').val('');
        
          $.ajax({
            type: 'GET',
            url: '/admin/inquiries/getProgramData/'+option,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done((response) => {
          programOptions = '<option value="">Select one</option>';
          var fieldValue="";
          response.forEach(function(program) {
              program.options.forEach(function(client){
                fieldValue = client.client_value_name;
                if(!client.client_value_name){
                  fieldValue = client.client_value;
                }
              programOptions += "<option value='" + client.client_value + "'>" + fieldValue + "</option>";
              });
              $('#posting-student-program-select').html(programOptions);
          });
        });       
    });


    $(document).on('change', '#posting-student-client-select', function(event) {
        var selectedIndex = event.target.selectedIndex;
        var options = event.target.options[selectedIndex].dataset.college_options;
        var htmlOptions = '';
        if (!_.isEmpty(options)) {
            options = JSON.parse(options);
            htmlOptions = '<option value="">Select one</option>';
            options.forEach(function(option, index) {
                var defaultName = index == 0 ? 'Default' : 'Default-' + index;

                var school_name = !_.isEmpty(option.school_name) ? option.school_name : defaultName;

                htmlOptions += '<option value="' + option.college_id + '">' + school_name + '</option>';
            });

          $('#posting-student-college-select').html(htmlOptions);
        }

        Plex.phonecall.validateManualPostingFields();
    });

    $(document).on('click', '#posting-student-modal .posting-student-button', function(event) {
        var modal = $('#posting-student-modal');

        if (Plex.phonecall.validateManualPostingFields()) {
            var user_id = $('#posting-student-user_id').val();
            var ro_id = $('#posting-student-client-select').val();
            var college_id = $('#posting-student-college-select').val();
            var course_id = $('#posting-student-program-select').val();
            var gdpr_phone = $('#gdpr_phone').is(":checked");
            var gdpr_email = $('#gdpr_email').is(":checked");

            var errors_container = $('#posting-student-modal .posting-student-errors');
            var loader = $('#posting-student-modal .posting-manage-students-ajax-loader');

            loader.show();

            $.ajax({
                url: '/admin/inquiries/manuallyPostStudent',
                method: 'POST',
                data: {user_id: user_id, ro_id: ro_id, college_id: college_id, course_id: course_id, 
                      gdpr_phone: gdpr_phone, gdpr_email: gdpr_email},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).then(function(response) {
                loader.hide();

                if (response.status == 'success') {
                    topAlert(Plex.inquiries.leadSuccessfullyPosted);
                    modal.foundation('reveal', 'close');
                } else if (response.status == 'failed') {
                    if (!_.isEmpty(response.errors) && !_.isEmpty(response.errors.field_name)) {
                        errors_container.html('Student is missing ' + response.errors.field_name.join(', '));
                    } else if (!_.isEmpty(response.error_msg)) {
                        errors_container.html(response.error_msg);
                    } else {
                        errors_container.html('An error has occured with no error message. Check logs.');
                    }
                }
            });
        }
    });

    $(document).on('click', '#transfer-call-modal .transfer-call-button', function(event) {

        Plex.phonecall.setupCall();
        
        var modal = $('#transfer-call-modal');
        var phoneNumber = $('#transfer-phone-number-select').val();

        if (phoneNumber == '' || !Plex.phonecall.connection || Plex.phonecall.calling == false) return;

        var sid = Plex.phonecall.connection.parameters.CallSid;
        var userPhone = Plex.phonecall.connection.message.phoneNumber;

        modal.foundation('reveal', 'close');

        $.ajax({
            method: 'POST',
            url: '/phone/modifyLiveCalls',
            data: { sid: sid, phone: phoneNumber, user_phone: userPhone },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).then(function(response) {
            // console.log('transferred', response);
        });
    })

});