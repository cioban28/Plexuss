/*********************************************************************************
**********************************************************************************
*  contact.js -- used in the inquiry pages for the contact section of student profile drop down
*  NOTES:  globals to the contact object keep states of 
*  -which contact section (type) 
*  -which thread (currentThread)
*      -a thread id of -1 is a new thread
*  -the first and last message loaded from db (firstMsg, lastMsg)
**********************************************************************************/

Plex.contact = {
    currentThread: -1,
    firstMsg: null,
    lastMsg: null,
    uploadObjsMsg: 0,
    uploadObjsTxt: 0,
    type: '',
    refreshing: false,   //ajax flag
    gettingPrev: false,  //ajax flag
    getCallAjax: false,  //ajax flag
    ajaxList: []  //list of ajax calls that share callback code after all completed

};



/***********************************************
* makes a loader
***************************************************/
Plex.contact.makeLoader = function(){

    return '<div id="spinLoader"></div>';
};


/*******************************************
*  makes a template option for the templates dropdown
*************************************************/
Plex.contact.makeTemplateOption = function( id, name){
    return '<option value="'+ id +'"  data-name="'+ name +'">'+ name +'</option>';
};


/*******************************************
*  makes a disabled template option for the templates dropdown
*************************************************/
Plex.contact.makeTemplateOptionDisabled = function( id, name){
    return '<option value =""  data-name="'+ name +'" disabled="true" selected="true">'+ name +'</option>';
};


/*******************************************
*  makes a thread option for thread dropdown
*************************************************/
Plex.contact.makeThreadOption = function( id, name){
    return '<option value="'+ id +'"  data-name="' +' Thread '+ name +'">Thread '+ name +'</option>';
};


/*******************************************
*  makes a thread option to start a new thread
*************************************************/
Plex.contact.makeThreadOptionNew = function(){
    return '<option value="'+ -1 +'"  data-name="new thread"> Start New Thread </option>';
};



/*******************************************
*  makes a default disabled thread option for the thread dropdown -- to display in select on load
*************************************************/
Plex.contact.makeThreadOptionDefault = function(){
    return '<option  value="none" data-name="Select Thread..." disabled="true" selected="true"> Select thread...</option>';
};



/********************************
*  makes a template radio option for the edit template modal
*****************************************/
Plex.contact.makeTemplateRadio = function(id, name){
    return '<li>'+
                '<input name="edit-message-template" type="radio" value="'+  id +'"  id="' +  id +'"  class="template-item" />' +
                '<label for="'+  id +'"> '+ name + '</label>' +
            '</li>';
};




/**********************************************
*  get message threads
*  AJAX will return all, append to dropdown based on Plex.contact.type
*  a default thread is chosen on load to display
*  global currentThread will get set to that default thread id
**********************************************/
Plex.contact.getThreads = function(that){
    var inqRow = that.closest('.inquirie_row');
    var contactPane =  inqRow.find('.contact-section-container.opened');
    var msgBox = contactPane.find('.msg-box');
    var drp = contactPane.find('.threads-dropdown');
    var uid = inqRow.data('hashedid');



    //clear options to place in new each load
    drp.empty();
    // drp.append(Plex.contact.makeThreadOptionDefault());

    msgBox.text('Loading...');




    ////////////////////////////////////////
    // AJAX get threads
    /////////////////////////////////
    Plex.contact.threadsAjax = 
    $.ajax({
        url: '/admin/ajax/getThisStudentThreads',
        type: 'POST',
        data: {
            user_id: uid
        },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response){
        // console.log(response);
        var res = JSON.parse(response);

        //temp value to store last thread indexs
        var lastThreadMsg = 0;  
        var lastThreadTxt = 0;

        //index to actually use
        var index = 0;

        //for each thread append dropdown based on type
        for(var i in res){

            //if threads 
            if((res[i].thread_type === 'msg_thread' || res[i].thread_type === 'campaign_thread') && Plex.contact.type === 'Message'){
                drp.append(Plex.contact.makeThreadOption(res[i].thread_id, res[i].hashed_thread_id));
                lastThreadMsg = i;
            }
            else if(res[i].thread_type === 'text_thread' && Plex.contact.type === 'Text'){
                drp.append(Plex.contact.makeThreadOption(res[i].thread_id, res[i].hashed_thread_id));
                lastThreadTxt = i;
            }
        }


        //append the option to start a new thread
        drp.prepend(Plex.contact.makeThreadOptionNew());

        //if there are threads, set first thread on list as default thread to be displayed 
        if(drp.find(' > option').length > 1){
            //populat msg-box with first thread
            switch(Plex.contact.type){
                case 'Message':
                    index = lastThreadMsg;
                    break;
                case 'Text':
                    index = lastThreadTxt;
                    break;
            }
            drp.val(res[index].thread_id); //find('option[value="'+ res[0].thread_id +'"]').attr('selected', true);
        
            //set threadID global to first thread if thread
            Plex.contact.currentThread = res[index].thread_id;

            //get messages based on thread and type
            Plex.contact.getMessages(that);

        }
        else{
            msgBox.text('No threads have been started yet.');
        }
       

    });


    Plex.contact.ajaxList.push(Plex.contact.threadsAjax);
}



/***********************************************
*  change threads
*  will set global currentThread to thread id
*  id of -1 is a new thread
*************************************************/
Plex.contact.changeThreads = function(that){

    Plex.contact.currentThread = that.val();
    Plex.contact.getMessages(that);

};




/***********************************************
*   gets last 20 messages, only called when first loading a thread
*   type:  should be can be Plexuss message = 'Messages'
*                         text message = 'Text'
*                         email message = 'Email'
*                         (JS ,,.. no strict typing.... ^ .. *grumble grumble*  hmm instanceof  create object.. waste of time?)  
*   function gets messages based on a 'global' threadID  -> Plex.contact.currentThread                                     
************************************************/
Plex.contact.getMessages = function(that){

    var inqRow = that.closest('.inquirie_row');
    var contactPane = inqRow.find('.contact-section-container.opened');
    var msgBox = contactPane.find('.msg-box');


    var url = '/admin/ajax/messages/getNewMsgs/' + Plex.contact.currentThread;


    msgBox.text('Loading...');


    $.ajax({
        url : url,
        type: 'GET',
        data: {
            return_type: 'blade'
        },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response){
        // console.log(res);
        
        //if successful
        if(response != null){

            //append messages
            msgBox.html(response);
            
            
        }

        msgBox.find('.spinloader2').hide();
        msgBox.scrollTop(msgBox[0].scrollHeight);


    });
};



/*************************************************
*  refresh message -- gets unread messags and appends them
**************************************************/
Plex.contact.refreshMessages = function(that){

    if(Plex.contact.refreshing === true)
        return;

    Plex.contact.refreshing = true;

    var inqRow = that.closest('.inquirie_row');
    var contactPane = inqRow.find('.contact-section-container.opened');
    var uid = inqRow.data('uid');
    var cuid = inqRow.data('college');

    //get message id
    //by getting last element in msg-box
    var msgID = contactPane.find('.msg-box .contact-msg-display-wrap:last').data('msgid');
    
    var url = '/admin/ajax/messages/getNewMsgs/' + Plex.contact.currentThread + '/' + msgID;


    //if no messages, must be a new thread or empty, send no message id
    if(typeof msgID === 'undefined')
        url = '/admin/ajax/messages/getNewMsgs/' + Plex.contact.currentThread;

    $.ajax({
        url : url,
        type: 'GET',
        data: {
            return_type: 'blade'
        },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

    }).done(function(response){
        // console.log(res);
        Plex.contact.refreshing = false;

        var msgBox = contactPane.find('.msg-box');
        
        if(response != null){
            msgBox.append(response);
            msgBox.scrollTop(msgBox[0].scrollHeight);

            //if there are more messages, remove the load more that also gets appended 
            //else ther eshould not be another load more
            var nomore = msgBox.find('.contact-no-more');
            if(nomore.length == 0) 
                msgBox.find('.contact-load-more:last').remove();

            nomore.remove();


            //also remove date if same as last
            var dates = msgBox.find('.contact-date');
            var dlen = dates.length; 

            if(dlen > 1){
                var lastdate = dates.eq(dlen-1).text();
                var lastlastdate = dates.eq(dlen-2).text();

                if(lastdate === lastlastdate){
                    msgBox.find('.date-divide:last').remove();
                }
            }

        }
        
    }).always(function(){
         //for refresh button itself (could have dynamic callbacks, but takes time to build that abstraction)
        inqRow.find('.refresh-icon').removeClass('spinning');
    });  
};


/********************************************
*  gets previous messages to prepend current displayed
*********************************************/
Plex.contact.getPrevMessages = function(that){

    if(Plex.contact.gettingPrev === true)
        return;

    Plex.contact.gettingPrev = true;

    var inqRow = that.closest('.inquirie_row');
    var contactPane = inqRow.find('.contact-section-container.opened');
    var uid = inqRow.data('uid');
    var cuid = inqRow.data('college');
    var msgBox = contactPane.find('.msg-box');

    //get message id
    //by getting last element in msg-box

    var msgID = contactPane.find('.msg-box .contact-msg-display-wrap:first').data('msgid');

    msgBox.find('.contact-load-more').text('Loading previous post...');

    $.ajax({
        url: '/ajax/messaging/getHistoryMsg/' + Plex.contact.currentThread + '/-1/' + msgID + '/',
        type: 'GET',
        data: {
            return_type: 'blade',
            called_from: 'getHistoryMsg'
        },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response){

       Plex.contact.gettingPrev = false;

        //remove old loadmore
        msgBox.find('.contact-load-more').remove();
        msgBox.prepend(response);

        
    });
};


/***********************************************
*   Get template names from db
*   use to populate template select and radios
*   type should be can be Plexuss message = 'Message'
*                         text message = 'Text'
*                         email message = 'Email'
*   (JS ,,.. no strict typing.... ^ .. *grumble grumble*,, hmm instanceof  create object.. waste of time? )  
************************************************/
Plex.contact.getTemplates = function(that){

    var inqRow = that.closest('.inquirie_row');
    var uid = inqRow.data('uid');
    var cuid = inqRow.data('college');
    var contactPane = inqRow.find('.contact-section-container.opened');
    var tmpSelect = contactPane.find('form select.templateDropdown');
    var tmpRadios = $('#edit-message-template-modal').find('ul.template-items');

    //clear templates
    tmpSelect.html('');
    tmpRadios.html('');


    
    //get template names from db
    Plex.contact.templatesAjax = 
    $.ajax({
        url : '/admin/inquiries/getMsgTemplates',
        data: {
            userId: uid,
            collegeUser: cuid
        },
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 

    }).done(function(response){
            

        var res = JSON.parse(response);   
        var templates = res;
        var len = Object.keys(templates).length;


        //hide loader when done
        // inqRow.find('.absolute-ajax_loader').hide();

        for(var t in templates){

            //append to select list
            var option = templates[t];

            if(option.toLowerCase() != 'insert template')
                tmpSelect.append( Plex.contact.makeTemplateOption( t , option));
            else
                tmpSelect.append( Plex.contact.makeTemplateOptionDisabled( t , option));
           
        }
    });

    Plex.contact.ajaxList.push(Plex.contact.templatesAjax);

};

/********************************************
*  gets AJAX content of call panel
***********************************************/
Plex.contact.getCall = function(e, that){

    var inqRow = $(e.target).closest('.inquirie_row');
    var huid = inqRow.data('hashedid');
    var prevCallContainer = inqRow.find('.contact-pane-wrapper').find('.prev-call-container'); 

    Plex.contact.type = 'Call';

    prevCallContainer.css({overflow: 'hidden'});
    prevCallContainer.html('<div class="small-loader"></div>');    
    //get previous calls

    if(Plex.contact.getCallAjax === false){

        Plex.contact.getCallAjax = true;    

        $.ajax({
            url: '/admin/getPreviousCalls',
            type: 'POST',
            data: {
                user_id: huid 
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

        }).done(function(response){
            Plex.contact.getCallAjax = false;

            var res = JSON.parse(response);

            prevCallContainer.css({overflow: 'auto'});
            prevCallContainer.html('');

            for(var i in res){

                prevCallContainer.append('<div class="prev-call-detail">'+
                        '<span class="call-name f-bold">'+ res[i].caller_name +':</span>'+
                        '<span class="call-status">&nbsp;'+ res[i].call_status +'</span>'+
                        '<span class="call-time"> '+ res[i].call_date +'</span>' +
                        '<span class="call-duration ml10"> (duration: '+ res[i].recording_duration +'secs) </span>' +
                        '<a class="sm2_button" type="audio/x-wav" href="'+ res[i].recording_url +'" ><div class="call-play-btn-cont"><div class="call-play-btn" ></div></div></a>' +
                    '</div>');
            }

            if(inqRow.find('.call-play-btn').length > 0)
                soundManager.onready(basicMP3Player.init);

        });  
    }  
};



/*********************************
*  toggle contact pane
*
*************************************/
Plex.contact.getContactPane = function(e, that){

    var el = $(e.target).closest('.regular-actions');
    var pPane =  el.closest('.inquirie_row').find('.sales-student-pane');
    var contactPane = el.closest('.inquirie_row').find('.contact-pane-wrapper');
    var tmpSelect = that.closest('.inquirie_row').find('.templateDropdown');

    //toggle pane
    if(contactPane.hasClass('opened')){
        el.hide();
        contactPane.hide();
        contactPane.removeClass('opened');
         
        pPane.show();
        pPane.find('.regular-actions').show();
         
    }else{
        el.hide();
        pPane.hide();

        contactPane.show();
        contactPane.addClass('opened');
        contactPane.find('.regular-actions').show();     
    }


    var type = contactPane.find('.contact-btn-wrapper.selected .contact-link').text();
    Plex.contact.type = type;
    //Call displays when first opened
    Plex.contact.getCall(e, that);
};



/**********************************************
*  switches between the different Contact panes
*
******************************************************/
Plex.contact.switchPane = function(e, that){

    // var toHide = Plex.inquiries.contactSection;
    var inqRow = that.closest('.inquirie_row');
    var type = that.find('.contact-link').text();
    var toShow = '#_contactCall';
    var url = '';
    var hashedId = inqRow.data('hashedid');
    var collegeId = $('#topnavsearch').data('cid');

    Plex.contact.type = type;

    //get contact subsection
    switch(type){
        case 'Message':
            toShow = '#_contactMessage'; 
            break;
        case 'Email':
            toShow = '#_contactEmail';
            break;
        case 'Call':
            toShow = '#_contactCall';
            break;
        case 'Text':
            toShow = '#_contactText';
            break;
        default:
            toShow = '#_contactCall';
            break;

    }
    
    // If toShow is already visibile, break out.
    if (inqRow.find(toShow).is(':visible')) {
        return;
    }

    if (toShow === '#_contactText') {
        //show loader, will hide when templates done loading
        inqRow.find('.absolute-ajax_loader').show();
        $('.contact-charCount').show();
    } else if (toShow === '#_contactMessage') {
        //show loader, will hide when templates done loading
        inqRow.find('.absolute-ajax_loader').show();
        $('.contact-charCount').hide();
    }

    var current = that.closest('.contact-btn-container').find('.selected');
    current.removeClass('selected');
    that.addClass('selected');

    //slideUp adds display inline to override any other styling
    inqRow.find('.contact-section-container.opened').slideUp();
    inqRow.find('.contact-section-container.opened').removeClass('opened');

    inqRow.find(toShow).addClass('opened');
    inqRow.find(toShow).slideDown();

     switch(type){
        case 'Message':
            Plex.contact.getThreads(that);
            Plex.contact.getTemplates(that);
            break;
        case 'Call':
            Plex.contact.getCall(e, that);
            break;
        case 'Text':
            Plex.contact.getThreads(that);
            Plex.contact.getTemplates(that);
            break;
        case 'Email':
            Plex.contact.getTemplates(that);
            break;

    }

    $.when.apply(this, Plex.contact.ajaxList).done(function(){
        inqRow.find('.absolute-ajax_loader').hide();
    });

};






/********************************************
*  send a message
********************************************/
Plex.contact.sendMessage = function(that){
    var inqRow = that.closest('.inquirie_row');
    var contactPane = inqRow.find('.contact-section-container.opened');
    var drp = contactPane.find('.threads-dropdown');
    var uid = inqRow.data('uid');
    var cuid = $('#topnavsearch').data('cid');
    var threadId = drp.val();//contactPane.find('.contact-messages-wrapper').data('threadid'); //thread id will be -1 if new thread, set in blade
   
    var feedback = inqRow.find('.msg-feedback');
    var msgCont = that.closest('.messageForm').find('.msgBody');
    
    var data = {};
    var formData = new FormData();


    msgCont.find('.remove-file-btn').fadeOut();
    msgCont.find('.remove-file-btn').remove();
    var msg =  msgCont.html();
    var msgT = msgCont.text();

    //do not send if message empty
    if(msgT.trim().length == 0){

        feedback.text('message empty');
        setTimeout(function(){
            feedback.text('');
        }, 5000)
        return;
    }


    if(typeof threadId == 'undefined' || threadId === 'undefined')
         Plex.contact.currentThread = -1;
     else
         Plex.contact.currentThread = threadId;        

    formData.append('message', msg);
    formData.append('thread_id', Plex.contact.currentThread);
    formData.append('to_user_id', uid);
    formData.append('college_id', cuid);
    formData.append('rthash', true);

    // data['message'] = msgCont.html();
    // data['thread_id'] = Plex.contact.currentThread;
    // data['to_user_id'] = uid;
    // data['college_id'] = cuid;
    // data['rthash'] = true;

    var tmp = {};

    
    switch(Plex.contact.type){
        case 'Message':
            // data['thread_type'] = 'inquiry-msg';
            formData.append('thread_type', 'inquiry-msg');
            // data['file'] = Plex.contact.uploadObjsMsg[0];
           // if(Plex.contact.uploadObjsMsg[0])
            //    formData.append('file',  Plex.contact.uploadObjsMsg[0], Plex.contact.uploadObjsMsg[0].name);

            break;
        case 'Text':
            // data['thread_type'] = 'inquiry-txt';
            formData.append('thread_type', 'inquiry-txt');
            // data['file'] = Plex.contact.uploadObjsTxt[0];
            //if(Plex.contact.uploadObjsTxt[0])
            //    formData.append('file', Plex.contact.uploadObjsTxt[0],  Plex.contact.uploadObjsTxt[0].name);
            break;
    }

    feedback.text('sending...'); 

    $.ajax({
            url: '/ajax/messaging/postMsg',
            type: 'POST',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(response){
            
           

            //if not one of the returned "Errors"
            if(response != 'failed' && response != 'No user founded' 
                && response != 'Need to upgrade' && response != 'The school is not setup yet' 
                 && response != 'Bad Thread Id' ){

                feedback.text('message sent');

                setTimeout(function(){ feedback.text(''); }, 5000);

                //if new thread, set new thread id, append thread dropdown
                //clear message display
                if(Plex.contact.currentThread == -1){

                    Plex.contact.currentThread = response['thread_id'];
                    inqRow.find('.contact-messages-wrapper').attr('data-threadid', response['thread_id']); 


                    drp.find('option:first').after(Plex.contact.makeThreadOption(response['thread_id'], response['hashed_thread_id']));

                    drp.val(response['thread_id']);

                    contactPane.find('.msg-box').html('');
                
                    
                }


                Plex.contact.refreshMessages(that);
                switch(Plex.contact.type){
                    case 'Message':
                        Plex.contact.uploadObjsMsg = 0;
                        break;
                    case 'Text':
                        Plex.contact.uploadObjsTxt = 0;
                        break;
                }
            
                msgCont.html('');
            }
            else{
                feedback.text('message failed to send'); 
            }

        //AJAX failed    
        }).fail(function(){
            feedback.text('message failed to send');
            
            //NOTE>>: should insert close button back -- file attch

        });  


};

/*******************************************
*  counts number of characters for messaging/text/email textareas
*********************************************/
Plex.contact.countChar = function(that){
    
    //count characters    
    var count = that.text().length;
    var btn = that.closest('.inquirie_row').find('.msgSubmit');

    if( count > 160 && Plex.contact.type === 'Text'){
        
        //let users know that there are too many chars
        that.closest('.inquirie_row').find('.msg-feedback').text('Exceeded character limit');

        //disable send button
        btn.prop('disabled', true);
        btn.addClass('disabled');
    }else{

        //get rid of potential exceeed message
        that.closest('.inquirie_row').find('.msg-feedback').text('');

        //enable button
        btn.prop('disabled', false); 
        btn.removeClass('disabled');
    }

    that.closest('#messageForm').find('.contact-text-count').text(count);
    //update number
}



/************************************
*  open save template modal
* 
***************************************/
Plex.contact.openSaveTemplateModal = function(that){

    var container = that.closest('.contact-section-container');
    var modal = $('#saveTemplateModal');

    if(that.is(":checked")){
        modal.find('.msg-content').val( container.find('.msgBody').html() );
        modal.foundation('reveal', 'open');
    }
};



/****************************************
*  save templates for messages
****************************************/
Plex.contact.saveTemplate = function(that){

    var name = that.closest('#saveTemplateModal').find('.templateName').val();
    var content = that.closest('#saveTemplateModal').find('.msg-content').val();  //hidden input stores the content
    
    // console.log(content);
    var selects = $('.templateDropdown');

    //ajax/saveMessageTemplates
    $.ajax({
        url: '/ajax/saveMessageTemplates',
        type: 'POST',
        data: {
            name: name,
            content: content
        },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response){

        if(response === 'failed')
            return;

        //append template name to list
        selects.append(Plex.contact.makeTemplateOption( response.id, response.name));

        //close modal
        $('.saveTemplateModal').foundation('reveal', 'close');

    });
        
};



/**********************************
*  load template for Messages
*************************************/
Plex.contact.loadTemplate = function(that){

    var id = that.find(':selected').val();
    var msgBody = that.closest('.contact-section-container').find('.msgBody');
    var feedback = that.closest('.contact-section-container').find('.msg-feedback');

    feedback.text('loading template...')

    $.ajax({
        url: '/ajax/loadMessageTemplates',
        type: 'POST',
        data: {
            id: id,
            textOnly: 1

        },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response){
        // console.log(response);
        msgBody.html(response.content);
        that.closest('#messageForm').find('.contact-text-count').html( msgBody.text().length );

        feedback.text('');
        

    }).always(function(){
        Plex.attch.removeFile(that);
    });
};






//////////////////////////////////////////////////
$(document).ready(function(){
    // Refresh button to get call logs
    $(document).on('click', '#_contactCall .contact-call-refresh-button', function(e) {
        Plex.contact.getCall(e, $(this));
    });

    /* on messages displayed scroll up - prepend wit old messages, if any */
    $(document).on('click', '.contact-load-more', function(){
        Plex.contact.getPrevMessages($(this), 'Message');
    });


    /* contact button and contact section */
    $(document).on('click', '.contact-btn', function(e){
        // Plex.contact.getContactPane(e, $(this));  //for when message is default
        Plex.contact.getContactPane(e, $(this));
    });

    $(document).on('click', '.inquirie_row .contact-close-btn', function(){
        var contactButton = $(this).closest('.inquirie_row').find('.actionbar-btn.contact-btn').first();
        var customEvent = { target: contactButton[0] };

        Plex.contact.getContactPane(customEvent, contactButton);
    });


    /* show and hide contact sub sections */
    $(document).on('click', '.contact-btn-wrapper', function(e){
        Plex.contact.switchPane(e, $(this));
    });


    /* refresh messages box */
    $(document).on('click', '.refresh-btn', function(){
        $(this).find('.refresh-icon').addClass('spinning');
        Plex.contact.refreshMessages($(this));
    });

    /* post messsage */
    $(document).on('click', '.contact-pane-cont .msgSubmit', function(e){
        Plex.contact.sendMessage($(this));
    });


    /* count chars for posting message/text/email box */
    $(document).on('change keyup input keyInput', '.msgBody', function(){
        if($(this).find('.upload-file-wrapper').text() == '')
            $($(this).find('.upload-file-wrapper')).remove();
        if(Plex.contact.type === 'Text')
            Plex.contact.countChar($(this)); 
        Plex.attch.removeFile($(this));

    });


    /* handler for the save template checkbox, opens save template name modal */
    $(document).on('click', '.saveTemplate', function(){
        Plex.contact.openSaveTemplateModal($(this));
    });


    /* handler for the save button on save template name modal, will save the template */
    $(document).on('click', '.contact-save-template-name', function(){
        Plex.contact.saveTemplate($(this));
    });


    /* loads template from selection */
    $(document).on('change', '.templateDropdown', function(){
        Plex.contact.loadTemplate($(this));
    });


    /* change threads */
    $(document).on('change', '.threads-dropdown', function(){
        Plex.contact.changeThreads($(this));

    });



});