// editMessageTemplate.js

Plex.msgTemplates = {
	templates: {},
	selectedTemplate: null,
	lastModal: null,
	page: null
};

$(document).ready(function(){
	var path = window.location.pathname;
	path = path.split('/').pop();

	Plex.msgTemplates.page = path;
	Plex.msgTemplates.initTemplates();

});

//opens up initial edit modal
$(document).on('click', '.edit-msg-temp-link', function(e){
	//on every click, get template items from select dropdown
	Plex.msgTemplates.initTemplates();
	Plex.msgTemplates.buildEditModal();
	Plex.msgTemplates.openEditModal();
});


// --- get functions
//returns all template items from select field
Plex.msgTemplates.getDOMTemplates = function(){

	if( Plex.msgTemplates.page === 'groupmsg'){
		return $('select[id*="message_template_dropdown"] option');
	}
	else if( Plex.msgTemplates.page === 'inquiries' || Plex.msgTemplates.page === 'approved'
		|| Plex.msgTemplates.page === 'pending' || Plex.msgTemplates.page === 'verifiedHs' || Plex.msgTemplates.page === 'verifedApp'
		|| Plex.msgTemplates.page === 'presreened' || Plex.msgTemplates.page === 'rejected' || Plex.msgTemplates.page === 'removed' || 
		Plex.msgTemplates.page === 'recommended' || Plex.msgTemplates.page === 'studentsearch'){
		
			return $('select.templateDropdown option');
	}
	else {
		return $('.active select[id*="message_template_dropdown"] option');
	}
};

//returns template item DOM elems
Plex.msgTemplates.getEditDOMTemplates = function(){
	return $('#edit-message-template-modal ul.template-items li');
};

//get selected templates info from db
Plex.msgTemplates.getTemplateContent = function(){
	var selected = $('#edit-message-template-modal input[type="radio"]:checked');
	
	$.ajax({
        url: '/ajax/loadMessageTemplates',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: Plex.msgTemplates.selectedTemplate,
        type: 'POST'
    }).done(function(data) {
		Plex.msgTemplates.selectedTemplate.edit('content', data.content);
		Plex.msgTemplates.buildEditSelectedModal();
		Plex.msgTemplates.openEditSelectedModal();
	});
}
// --- get functions
/* update template */
$('.update-template-btn').click(function(){
    var template_name = $('#edit_template_name').val();
    if(template_name === ''){
    	alert('Please enter template name');
	} else {
        var template_id = $('#edit_template_id').val();
        var message_template_dropdown_chat = $('#message_template_dropdown_chat');

		var txtArea = $('.chattext').val();
		var chattext = txtArea;

        $.ajax({
            url: '/ajax/saveMessageTemplates',
            data: {id:template_id, name: template_name, content: txtArea},
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(data, textStatus, xhr) {
            var txt = '<option value="'+data.id+'">'+data.name+'</option>';
			message_template_dropdown_chat.append(txt);
            $('#edit-message-template-modal').foundation('reveal', 'close');
            location.reload();
        });
    }
});

//get templates from DOM select elem to init template objects
Plex.msgTemplates.initTemplates = function(){
   	var id = $('#message_template_dropdown_chat').val();
   	var txt = $('#message_template_dropdown_chat :selected').text();
   	if(txt !== ''){
   		if(id === ''){
            var edit_template_name = $('#edit_template_name').val("It is a default template. Please choose another!!");
        } else {
            var edit_template_name = $('#edit_template_name').val(txt);
            var edit_template_id = $('#edit_template_id').val(id);
        }
	} else {
   		if($('#message_template_dropdown_msg :selected').text() !== ''){
            var DOMTemplates = Plex.msgTemplates.getDOMTemplates();
            if( DOMTemplates.length > 0 ){
                Plex.msgTemplates.templates = new TemplateList();
                $.each(DOMTemplates, function(){
                    if( $(this).val() && $(this).text().toLowerCase() != 'insert template' ) Plex.msgTemplates.templates.add( new Template($(this).val(), $(this).text()) );
                });
            }
		}
	}

};




// --- custom obj
//template constructor function
var Template = function Template(id, name){
	this.id = id || null;
	this.name = name || null;
	this.content = null;
};

//pass property to edit with new value
Template.prototype.edit = function(prop, val){
	var args = Array.prototype.slice.call(arguments), prop = null, val = null;
	if( args.length > 0 ){
		prop = args[0];
		val = args[1];
		this[prop] = val;
	}
};

//constructor function
var TemplateList = function TemplateList(){
	this.list = [];
};

//add new template to list
TemplateList.prototype.add = function() {
	var args = Array.prototype.slice.call(arguments);
	this.list.push(_.first(args));
};

//returns number of templates in list
TemplateList.prototype.count = function() {
	return this.list.length;
};

//finds template by id
TemplateList.prototype.findTemplate = function(){
	var args = Array.prototype.slice.call(arguments);
	return _.findWhere(this.list, {id: args[0]});
};
// --- custom obj



// --- building modals
//build edit modal with the list of available templates
Plex.msgTemplates.buildEditModal = function(){
	var listElem = $('#edit-message-template-modal ul.template-items'), html = '';
	_.each(Plex.msgTemplates.templates.list, function(template){
		html += '<li>';
		html += 	'<input name="edit-msg-template" type="radio" value="'+template.id+'" id="'+template.id+'" class="template-item" />';
		html += 	'<label for="'+template.id+'">'+template.name+'</label>';
		html += '</li>';
	});

	listElem.html(html);
};

//populate fields with selected template info
Plex.msgTemplates.buildEditSelectedModal = function(){
	$('#edit-selected-template-modal .temp-title > div').html(Plex.msgTemplates.selectedTemplate.name);
	$('#edit-selected-template-modal .title-edit-input').val(Plex.msgTemplates.selectedTemplate.name);

	tinymce.activeEditor.setContent( Plex.msgTemplates.selectedTemplate.content );
};
// --- building modals


// --- open/close modals
//open edit modal
Plex.msgTemplates.openEditModal = function(){
	$('#edit-message-template-modal').foundation('reveal', 'open');
};

//close edit modal
Plex.msgTemplates.closeEditModal = function(){
	$('#edit-message-template-modal').foundation('reveal', 'close');
};

//open edit selected modal
Plex.msgTemplates.openEditSelectedModal = function(){
	$('#edit-selected-template-modal').foundation('reveal', 'open');
}


//close edit selected modal
Plex.msgTemplates.closeEditSelectedModal = function(){
	$('#edit-selected-template-modal').foundation('reveal', 'close');
	//tinymce.execCommand('mceRemoveEditor', false, 'editMsgTemplate-editor');
	//tinymce.remove('editMsgTemplate-editor');
}

//open sureness modal
Plex.msgTemplates.openSurenessModal = function(){
	$('#sureness-modal').foundation('reveal', 'open');
};

//close sureness modal
Plex.msgTemplates.closeSurenessModal = function(){
	$('#sureness-modal').foundation('reveal', 'close');
};

//return true/false if modal is visible/open
Plex.msgTemplates.modalOpen = function(modal){
	return modal.is(':visible');
};

Plex.msgTemplates.successMsg = function(msg){
	return 'Successfully '+msg+' template!';
}

Plex.msgTemplates.failedMsg = function(){
	return 'Oops. Looks like something went wrong. Please refresh and try again.';
}

Plex.msgTemplates.buildMsg = function(success, which){
	var msg = success ? Plex.msgTemplates.successMsg(which) : Plex.msgTemplates.failedMsg();

	if( success ) $('.alertMsg').html(msg).removeClass('hide');
	else $('.alertMsg').html(msg).removeClass('hide').addClass('fail');

	setTimeout(function(){
		$('.alertMsg').addClass('hide');
	}, 20000);
}
// --- open/close modals




// --- dom events
//when edit button is clicked, if a template has been chosen, open edit selected modal, else nothing
$(document).on('click', '#edit-message-template-modal .edit.btn', function(e){
	var _this = $(this);
	e.preventDefault();

	if( !_this.hasClass('notallowed') ){
		Plex.msgTemplates.getTemplateContent();
	}
});

//when a template item has been chosen, remove disabled prop and class to be enable edit button functionality
$(document).on('change', '#edit-message-template-modal .template-item', function(){
	var edit_btn = $('#edit-message-template-modal .edit.btn');

	Plex.msgTemplates.selectedTemplate = Plex.msgTemplates.templates.findTemplate($(this).val());

	if( edit_btn.hasClass('notallowed') ){
		edit_btn.removeClass('notallowed').removeAttr('disabled');
	}
});
// --- dom events



// --- toggling template title
//toggle the template title and title input
$(document).on('click', '.edit-title-container .toggler', function(){
	var _this = $(this), input = null;

	if( _this.hasClass('temp-title') ){
		_this.toggleEditForm(!0);
	}else{
		input = _this.closest('.title-edit-form').find('input');
		Plex.msgTemplates.selectedTemplate.edit('name', input.val());
		Plex.msgTemplates.buildEditSelectedModal();
		_this.closest('.title-edit-form').toggleEditForm(!1);
	}
});

$(document).on('blur', 'input.title-edit-input', function(){
	Plex.msgTemplates.updateTitle($(this));
});

$(document).on('keyup', 'input.title-edit-input', function(e){
	if( e.keyCode === 13 ) Plex.msgTemplates.updateTitle($(this));
	
});

//back button event to return to edit modal
$(document).on('click', '#edit-selected-template-modal .back', function(e){
	e.preventDefault();
	$('.title-edit-form').toggleEditForm(!1);
});

Plex.msgTemplates.updateTitle = function(elem){
	Plex.msgTemplates.selectedTemplate.edit('name', elem.val());
	Plex.msgTemplates.buildEditSelectedModal();
	elem.closest('.title-edit-form').toggleEditForm(!1);
}

//jquery plugin to toggle title text and title input
$.fn.toggleEditForm = function(showEdit){
	if( showEdit ){
		$(this).addClass('hide').closest('.edit-title-container').find('.title-edit-form').removeClass('hide');
	}else{
		$(this).addClass('hide').closest('.edit-title-container').find('.temp-title').removeClass('hide');
	}

	return this;
}
// --- toggling template title



// --- saving 
//save button trigger to run save template function 
$(document).on('click', '#edit-selected-template-modal .save', function(e){
	e.preventDefault();
	$('.title-edit-form').toggleEditForm(!1);
	Plex.msgTemplates.saveTemplate();	
});

//send selected template data to save
Plex.msgTemplates.saveTemplate = function(){
	var success = false, which = 'updated', elem = null;

	$.ajax({
        url: '/ajax/saveMessageTemplates',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: Plex.msgTemplates.selectedTemplate,
        type: 'POST'
    }).done(function(data, textStatus, xhr) {
		if( data !== 'failed' ) success = !success;
		elem = Plex.msgTemplates.findSelectedTemplateDOMElem();
		selectElem = Plex.msgTemplates.findSelectedTemplateSelectOptionDOMElem(),

		elem.parent().find('label').text(Plex.msgTemplates.selectedTemplate.name);
		selectElem.text(Plex.msgTemplates.selectedTemplate.name);

		Plex.msgTemplates.openEditModal();
		Plex.msgTemplates.buildMsg(success, which);
	});
};
// --- saving 



// --- deleting
//delete button click event to trigger deleting of selected template
$(document).on('click', '#edit-message-template-modal .delete, #edit-selected-template-modal .delete', function(e){
	e.preventDefault();
	Plex.msgTemplates.lastModal = $(this).closest('.reveal-modal').data('modal-name');
	Plex.msgTemplates.openSurenessModal();
});

$(document).on('click', '#sureness-modal .yes.btn', function(e){
	e.preventDefault();
	Plex.msgTemplates.deleteTemplate();
	Plex.msgTemplates.openEditModal();
});

$(document).on('click', '#sureness-modal .no.btn', function(e){
	e.preventDefault();
	var modal = 'open'+Plex.msgTemplates.lastModal;
	Plex.msgTemplates[modal]();
});

//ajax post to delete selected template
Plex.msgTemplates.deleteTemplate = function(){
	var success = false, which = 'deleted';

	$.ajax({
        url: '/ajax/deleteMessageTemplates',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: Plex.msgTemplates.selectedTemplate,
        type: 'POST'
    }).done(function(data){
		if( data === 'success' ){
			//alert of success and remove dom elem
			success = !success;
			Plex.msgTemplates.deleteTemplateDOMElem();
			if( Plex.msgTemplates.modalOpen($('#edit-selected-template-modal')) ) Plex.msgTemplates.openEditModal();
		}

		Plex.msgTemplates.buildMsg(success, which);
	});
};

//returns selected template dom elem
Plex.msgTemplates.findSelectedTemplateDOMElem = function(){
	return $('#edit-message-template-modal input[value="'+Plex.msgTemplates.selectedTemplate.id+'"]');
};

//return the select option of the current template
Plex.msgTemplates.findSelectedTemplateSelectOptionDOMElem = function(){
	return $('select[id*="message_template_dropdown"] option[value="'+Plex.msgTemplates.selectedTemplate.id+'"]');
};

//find dom elem and remove()
Plex.msgTemplates.deleteTemplateDOMElem = function(){
	var elem = Plex.msgTemplates.findSelectedTemplateDOMElem(),
		selectElem = Plex.msgTemplates.findSelectedTemplateSelectOptionDOMElem(),
		edit_btn = $('#edit-message-template-modal .edit.btn');

	elem.parent().remove();
	selectElem.remove();
	edit_btn.addClass('notallowed').attr('disabled', 'disabled');
};
// --- deleting

