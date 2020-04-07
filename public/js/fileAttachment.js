
Plex.attch = {
    isSaving: false
};


/***********************************************
* makes a loader
***************************************************/
Plex.attch.makeLoader = function(){

    return '<div id="spinLoader"></div>';
};


/***********************************************
*  makes a file upload image and name
************************************************/
Plex.attch.makeFileUpload = function(name, url, type){

	if(!type){
		type = 'other';
	}

    return '<div contenteditable="false" class="upload-file-wrapper mt10 mb10" data-ftype="'+ type +'">' + 
                '<input type="hidden" name="file_info" />' +
                '<div class="remove-file-btn fr">&times;</div>'+
                '<div>' +
                    '<div class="uploadDocsSpriteLarger other d-block"></div>' +
                    '<small>attachment: </small> <br/>' + name + 
                '</div>' +
                 '<a download href="'+ url + '" class="attch-downloadlink">  download </a> ' + ' | ' + 
                 '<a href="#" class="view-attachment" data-url="'+ url +'"> view </a>' +
            '</div>';

};


/***********************************************
* makes file attachment detail tiles for attachment manager
**********************************************/
Plex.attch.makeFileAttachmentDetail = function(name, url, id){

    var show = '';
    var ext = url.substr(url.lastIndexOf('.') + 1);

    if( ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif' || ext === 'bmp'){
        show = '<img src="'+ url +'" alt="Attachment preview" class="fattch-tile-img"/>';
    }
    else{
        show = '<div class="fattch-tile-noprev">Preview not Available</div>';
    }

    return '<div data-hid="'+ id +'" class="fattch-detail-tile">'+
                '<div class="fattch-tileimg-cont">' + show +'</div>'+
           '</div>';
};




/********************************************
* makes file attchment details for the panel on the right
************************************************/
Plex.attch.makeFileDetails = function(name, url, date, hid){

    var show = Plex.attch.getPreview(url);

    return '<div class="fattch-rightpanel-cont" data-url="'+ url +'" data-hid="'+  hid +'">' +
                '<div class="fattch-details-img-cont">'+
                    show +
                '</div>' + 
                '<div class="fattch-details-detail"> Name: &nbsp; <span class="fattch-name-val">' + name +'</span></div>' +
                '<div class="fattch-details-detail"> Date: &nbsp;' + date +'</div>' +
                '<div class="fattch-details-actions-cont"><div class="fattch-delete-btn">delete</div></div>'+
           '</div>';
};





/*******************************************
*
*   attach file modal toggle
********************************************/
Plex.attch.attchFileModalOpen = function(that){

    var maxed = false;

    $('.FileError').html('');
     $('.FileFeedback').html('');

    if(Plex.contact.type ||  Plex.contact.type != ''){
	    switch(Plex.contact.type ){
	        case 'Message':
	            if( Plex.contact.uploadObjsMsg > 0)
	                maxed = true;
	            break;
	        case 'Text':
	            if( Plex.contact.uploadObjsTxt > 0)
	                maxed = true;
	            break;
	    } 
	}

    if(!maxed){
        $('#attachFileModal').foundation('reveal', 'open');
    }
    else{
        var feedback =that.closest('.inquirie_row').find('.msg-feedback');
        feedback.text('may only upload 1 file per message');
        setTimeout(function(){feedback.text('');}, 2000);
    }
   

}


/******************************************
*  switches between file Attachment tabs
*  if cliking on current attchments also refreshes those,
*  or loads them first time
*********************************************/
Plex.attch.switchAttchPanes = function(that){

    var sel = that.text();
    var activate = $('.fattch-current-btn');
    var bar = that.closest('.file-attch-nav-container');
    var current = bar.find('.active');
    var hid = that.closest('#attachFileModal').data('hashedid');
    var tileCont = $('.fattch-curr-choosefrom');
    var detailCont = $('.fattch-curr-chosen');

    tileCont.html(Plex.attch.makeLoader());
    detailCont.html('<div class="fattch-select-please">Select file to view...</div>');

    //switch pane depending on selected tab
    switch(sel){
        case 'New Attachment':
            activate = $('.fattch-new-btn');
            $('.new-attch-cont').addClass('opened');
            $('.attch-manager-cont').removeClass('opened');
            break;
        case 'Current Attachments':
            $('.new-attch-cont').removeClass('opened');
            $('.attch-manager-cont').addClass('opened');
            break; 
    }
    
    current.removeClass('active');
    activate.addClass('active');
        
    /////////////
    //if the manager is opened/reopened -- AJAX in the attachments 
    if($('.attch-manager-cont').hasClass('opened')){

        $.ajax({
            url: '/ajax/getOrgSavedAttachmentsList',
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(response){

            tileCont.html('');

            for(var i in response){
                //append attatchment details
                
                tileCont.append(Plex.attch.makeFileAttachmentDetail(response[i].name, response[i].url, response[i].id))
            }
        });
    }
};




/**************************************
*  attatch files button handler
***************************************/
Plex.attch.attchFileButton = function(that){
   
    $('#attachFileModal input.FileInput').click();
  
};



/***************************************
* helper function -- returns image previews
* if image-- returns img tag else uses google docs api
**********************************************/
Plex.attch.getPreview = function(url){
    
    var showPreview = '';
    var ext = url.substr(url.lastIndexOf('.') + 1);
    ext = ext.toLowerCase();        


    if( ext == 'jpeg' || ext == 'jpg' || ext == 'png' || ext == 'gif' || ext == 'bmp'){
        
        //string to contruct image link
        var previewImg = '';
        previewImg += '<img src="' + url;
        previewImg += '" alt="File Attachment"/>';
        showPreview = previewImg;
    }else{
      
        //iframe embed for pdf uploads
        var pdfPreview = '<iframe src="https://docs.google.com/gview?url=' + url;
        pdfPreview += '&embedded=true" style="width:100%; height:500px;" frameborder="0"></iframe>';
        showPreview = pdfPreview;
    }
    
    return showPreview;
  
};




/*********************************************************************
*  on input file change event -- generates a preview for file uploads in attch modal
*  before a url is generated
**********************************************************************/
Plex.attch.previewAttchFile = function(that){

    var file = $('.FileInput')[0].files[0];

    if(!file){
        $('#attachFileModal img.fattch-preview-img').attr('src','');
        return;
    }

    var name = file.name;
    var ext = name.substr(name.lastIndexOf('.') + 1);
    ext = ext.toLowerCase();


    //create a temporay url out of file -- used to display preview
    var tmppath = URL.createObjectURL(file);

    $('#attachFileModal .none-available').remove();

    if(tmppath && (ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif' || ext === 'bmp')){
       $('#attachFileModal img.fattch-preview-img').attr('src',tmppath);
    }
    else{

        $('#attachFileModal img.fattch-preview-img').attr('src','');
        $('.fattch-preview-cont').append('<div class="none-available">No Preview Available</div>');
    }
       

}





/**************************************************
*  view attachment -- handler for 'view'  
******************************************************/
Plex.attch.viewAttch = function(that){

    var box = $('#viewFileModal .file-attch-view-cont');
    var name = that.closest('.upload-file-wrapper').find('.attch-downloadlink').attr('href');
    var showPreview = Plex.attch.getPreview(name);

    box.html(showPreview);
    $('#viewFileModal').foundation('reveal', 'open');

};



/****************************************************
* in attachment manager, handler for when user selects an attachment
* will populate panel on right with attachment details
******************************************************/
Plex.attch.manageAttachment = function(that){

    var curr = $('.fattch-detail-tile.selected');
    var rPanel = $('.fattch-curr-chosen');
    var hid = that.data('hid');

    //show selected on left panel
    curr.removeClass('selected');
    that.addClass('selected');

    $('.contact-attch-file-btn').attr('disabled', true);
    $('.contact-attch-file-btn').addClass('disabled');

    rPanel.html(Plex.attch.makeLoader());

    $.ajax({
        url: '/ajax/loadOrgSavedAttachments',
        type: 'POST',
        data: { id: hid },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(res){

        rPanel.html('<div class="fattch-select-please">Select file to view...</div>');
        rPanel.html(Plex.attch.makeFileDetails(res.name, res.url, res.date.date, hid));
    
    }).always(function(){

        $('.contact-attch-file-btn').attr('disabled', false);
        $('.contact-attch-file-btn').removeClass('disabled');
        
    });


};


/***************************************************************
*  attach file for current attachments tab -- 
*       loads an attachment and generates some markup for message
*******************************************************************/
Plex.attch.attchLoadedFile = function(that){

    if(Plex.attch.isSaving) return;

    Plex.attch.isSaving = true;

    var name = $('span.fattch-name-val').text();
    var url = $('.fattch-rightpanel-cont').data('url') || $('.fattch-detail-tile selected').find('.fattch-tile-img').attr('src');


    if(name == '' || typeof name == 'undefined' || url === '' || typeof url == 'undefined')
        return;

    if(Plex.contact.type || Plex.contact.type != ''){
	    switch(Plex.contact.type ){
	            case 'Message':
	                Plex.contact.uploadObjsMsg++;
	                $('#_contactMessage').find('form').find('.msgBody').append( Plex.attch.makeFileUpload(name, url) );
	                break;
	            case 'Text':
	                $('#_contactText').find('form').find('.msgBody').append('<div class="upload-file-wrapper">'+ url + '</div>');
	                Plex.contact.uploadObjsTxt++;
	                break;
	    } 
	}
	else{ /* so far just portal page */

		$('.msgtext').append(Plex.attch.makeFileUpload(name, url));

	}

    Plex.attch.isSaving = false;
    //close the file attachment modal
    $('#attachFileModal').foundation('reveal', 'close');
};



/*********************************************************************
*  attach file for new attchment tab -- 
*       saves and generates some markup for the message
**********************************************************************/
Plex.attch.attchNewFile = function(that){

    var feedback = $('.msg-feedback');
    var file = $('.FileInput')[0].files[0];
    var ftype = $('.FileType').val();

    if(Plex.attch.isSaving) return;

   

    $('.FileError').html('');
    $('.FileFeedback').html('');

    //if no file, return
    if(typeof file === 'undefined'){
    	$('.FileError').html('No file chosen.');
    	return;
    }

    if(typeof ftype == "undefined"){
    	ftype = '';
    }

    switch(ftype){
    	case 'Financial Document':
    		ftype = 'financial'; 
    		break;
    	case 'Resume/Portfolio':
    		ftype = 'resume';
    		break;
    }


    if(ftype === "Select a file type..."){
    	$('.FileError').html('A file type must be selected.');
    	return;
    }

    var name = $('#attachFileModal .FileName').val();
    if(name == ''){
        name = file.name;
    }

    var formD = new FormData();

    formD.append('file', file);
    formD.append('name', name);
    formD.append('file_type', ftype);


    Plex.attch.isSaving = true;
    $('.FileFeedback').html('saving attachment...');

    ////////////////////////////////////
    // AJAX to save attatchment
    // callback appends  some markup for attatchment to message
    $.ajax({
        url: '/ajax/saveOrgSavedAttachments',
        type: 'POST',
        contentType: false,
        processData: false,
        dataType: 'json',
        data: formD,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response){

        Plex.attch.isSaving = false;
        var url = response.url;

        if(Plex.contact.type ||  Plex.contact.type != ''){
	        switch(Plex.contact.type ){
	            case 'Message':
	                Plex.contact.uploadObjsMsg++;
	                $('#_contactMessage').find('form').find('.msgBody').append( Plex.attch.makeFileUpload(name, url, ftype) );
	                break;
	            case 'Text':
	                $('#_contactText').find('form').find('.msgBody').append('<div class="upload-file-wrapper">'+ url + '</div>');
	                // Plex.contact.uploadObjsTxt++;
	                break;
	        } 
	    }else{ /* so far just portal page */

			$('.msgtext').append(Plex.attch.makeFileUpload(name, url, ftype));

		}



    })
    .fail(function(){
        feedback.text('An Error occurred while trying to save attachment!');
    })
    .always(function(){

        //close the file attachment modal
        $('#attachFileModal').foundation('reveal', 'close');

    });

   
};


/***************************************************
*  deletes attahment from database
***************************************************/
Plex.attch.deleteAttachment = function(that){

    var hid = $('.fattch-rightpanel-cont').data('hid');

    $.ajax({
        url: '/ajax/deleteOrgSavedAttachments',
        type: 'POST',
        data: { id: hid},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(res){

        if(res == 'success'){
            //remove it from left side of panel
            $('.fattch-detail-tile[data-hid="'+ hid +'"]').remove();

            //clear right side panel
            $('.fattch-curr-chosen').html('<div class="fattch-select-please">Select file to view...</div>');
        }
    });
};



/******************************************
* delete/remove a file attchment 
********************************************/
Plex.attch.removeFile = function(that){
    
    var cont = '';
    if(Plex.contact.type  || Plex.contact.type != ''){
	    switch(Plex.contact.type){
	        case 'Message':
	            cont = $('#_contactMessage .msgBody');
	            break;
	        case 'Text':
	            cont = $('#_contactText .msgBody');
	            break;
	    }      
	}


    //if there is no file attatchment element
    if(cont.find('.upload-file-wrapper').length === 0) {

    	if(Plex.contact.type ||  Plex.contact.type != ''){
	        switch(Plex.contact.type){
	            case 'Message':
	                Plex.contact.uploadObjsMsg = 0;
	                break;
	            case 'Text':
	                Plex.contact.uploadObjsTxt = 0;
	                break;
	        } 
       }
    }

};


/****************************************************
*  remove attachment button handler
*****************************************************/
Plex.attch.removeFileButton = function(that, e){

    that.closest('.upload-file-wrapper').remove();

    if(Plex.contact.type ||  Plex.contact.type != ''){
	    switch(Plex.contact.type){
	        case 'Message':
	            Plex.contact.uploadObjsMsg = 0;
	            break;
	        case 'Text':
	            Plex.contact.uploadObjsTxt = 0;
	            break;
	    } 
	}
};





$(document).ready(function(){

	 /* opens attachfile modal */
    $(document).on('click', '.attch-file-open', function(){
        Plex.attch.attchFileModalOpen($(this));
    });

    /* custom attatch file button handler -- clicks the hidden input of type file  */
    $(document).on('click', '.fattch-attch-btn', function(){
        Plex.attch.attchFileButton($(this));
    });



    /* custom input file handler ,, creates an element to show new file attatchment in preview pane */
    $(document).on('change', '.FileInput', function(){
        Plex.attch.previewAttchFile($(this));
    });


    /* handler to actually save and attach a file */
    $(document).on('click', '.contact-attch-file-btn', function(){
        //get current tab opened
        var curr = $(this).closest('#attachFileModal').find('.file-attch-nav-container .active').text();

        if(curr === 'Current Attachments'){
            Plex.attch.attchLoadedFile($(this));
        }else{
            Plex.attch.attchNewFile($(this));
        }
    });

    /* switches tabs for file attachment modal */
    $(document).on('click', '.file-attch-nav-container > div', function(){
        Plex.attch.switchAttchPanes($(this));
    });


    /* selects an attachment in attachment manager */
    $(document).on('click', '.fattch-detail-tile', function(){
        Plex.attch.manageAttachment($(this));
    });


    /* delete file attachment from database */
    $(document).on('click', '.fattch-delete-btn', function(){
        Plex.attch.deleteAttachment($(this));
    });



    /* view file attachment */
    $(document).on('click', '.upload-file-wrapper .view-attachment', function(){
        Plex.attch.viewAttch($(this));
    });

    /* delete file attatchment (in message editting box) handler  -- to reset number of uploads*/
    $(document).on('keydown', '.msgBody', function(e){
        Plex.attch.removeFile($(this));
    });


    /* remove attachent (from message editting box) button -- contenteditable does not delete on  some versions of Firefox*/
    $(document).on('click', '.remove-file-btn', function(e){
        Plex.attch.removeFileButton($(this));
    });
        
});