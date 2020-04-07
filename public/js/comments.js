$(document).ready(function(){
	bindCommentFocusAnimations();
	checkCommentVal();
	bindCommentCharCounter();
	initCommentCharCount();
	bindCommentSubmitAjax();
	// startCommentHeartbeat();
	injectInitialComments();
	bindReplyButton();

	// Initialize foundation with custom patterns
	init_comment_fndtn();
});

/***********************************************************************
 *============= INITIALIZES SPECIAL FOUNDATION PATTERNS ================
 ***********************************************************************
 * Initializes foundation with special patterns.
 * Binds/rebinds reply submit button ajax
 */
function init_comment_fndtn(){
	// reflow abide
	$(document).foundation({
		abide: {
			patterns: {
				onechar: /^[\s\S]{1,}$/
			}
		}
	});

	// re-bind reply ajax
	bindReplySubmitAjax();
}
/***********************************************************************/

/***********************************************************************
 *================== INJECT COMMENTS ON PAGE LOAD ======================
 ***********************************************************************
 * This function called on page load. Gets comment from data attribute in the
 * comment_thread_wrapper class, checks if set, and calls injectComments()
 */
function injectInitialComments(){
	var comments = $( '.comment-thread-wrapper' ).data( 'comments' );
	if( comments ){
		setTimeout( injectComments( comments ), 1000 );
		//injectComments( comments );
	}
}
/***********************************************************************/

/***********************************************************************
 *================== HIDE ALL BUT ONE CHILD REPLY ======================
 ***********************************************************************
 * Hides all replies but the latest for a particular specified parent comment
 * element.
 * @param		parent_id		int			the id of the parent comment
 * @param		button			object		the jquery object of the 
 */
function hideReplies( parent_id, button ){
	var replies = $( '.child-of-' + parent_id + ':visible' );
	var time = 0;
	var interval = 50;
	var shrink_interval = 5;
	$.each( replies, function( key, reply ){
		reply = $( reply );
		setTimeout( function(){
			if( !reply.is( '.child-of-' + parent_id + ':last' ) ){
				$( reply ).slideUp( 250, 'easeInOutExpo' );
			}
		}, time );
		time += interval;
		// set max interval so comments all reveal after interval/shrink_interval
		// comments have been revealed
		interval = interval > 0 ? interval - shrink_interval : interval;
	} );

	// Run button toggle callback
	setTimeout( function(){
		visibilityShowMore( button );
	}, time );
}
/***********************************************************************/

/***********************************************************************
 *================= SHOW ALL REPLIES FOR ONE PARENT ====================
 ***********************************************************************
 * Shows all the replies for the specified parent comment element
 * @param		parent_id		int			the id of the parent comment
 */
function showReplies( parent_id, button ){
	var replies = $( '.child-of-' + parent_id + ':hidden' );
	var time = 0;
	var interval = 50;
	var shrink_interval = 5;
	$.each( replies, function( key, reply ){
		setTimeout( function(){
			$( reply ).slideDown( 250, 'easeInOutExpo' );
		}, time );
		time += interval;
		// set max interval so comments all reveal after interval/shrink_interval
		// comments have been revealed
		interval = interval > 0 ? interval - shrink_interval : interval;
	} );

	// Run button toggle callback
	setTimeout( function(){
		visibilityShowLess( button );
	}, time );
}
/***********************************************************************/

/***********************************************************************
 *============================ SHOW LESS ===============================
 ***********************************************************************
 * Toggles the comment visibility function to show the 'show less' text
 * @param		button		object			jquery object of the button to be
 * 											altered
 */
function visibilityShowLess( button ){
	var visible = button.find( 'span:visible' );
	var less = button.find( '.comment-less' );
	visible.fadeOut( 250, 'easeInOutExpo', function(){
		less.fadeIn( 250, 'easeInOutExpo' );
	} );
}
/***********************************************************************/

/***********************************************************************
 *============================ SHOW MORE ===============================
 ***********************************************************************
 * Toggles the comment visibility function to show the 'show more' text
 * @param		button		object			jquery object of the button to be
 * 											altered
 */
function visibilityShowMore( button ){
	var more = button.find( '.comment-more' );
	var visible = button.find( 'span:visible' );
	visible.fadeOut( 250, 'easeInOutExpo', function(){
		more.fadeIn( 250, 'easeInOutExpo' );
	} );
}
/***********************************************************************/

/***********************************************************************
 *=========================== SHOW LOADING =============================
 ***********************************************************************
 * Toggles the comment visibility function to show the 'show loading' text
 * @param		button		object			jquery object of the button to be
 * 											altered
 */
function visibilityShowLoading( button, callback ){
	var visible = button.find( 'span:visible' );
	var loading = button.find( '.comment-loading' );
	visible.fadeOut( 250, 'easeInOutExpo', function(){
		loading.fadeIn( 250, 'easeInOutExpo', callback );
	});
}
/***********************************************************************/

/***********************************************************************
 *============================== SHOW END ==============================
 ***********************************************************************
 * Toggles the comment visibility function to show the 'show end' text
 * @param		button		object			jquery object of the button to be
 * 											altered
 */
function visibilityShowEnd( button ){
	var visible = button.find( 'span:visible' );
	var end = button.find( '.comment-end' );
	visible.fadeOut( 250, 'easeInOutExpo', function(){
		end.fadeIn( 250, 'easeInOutExpo' );
	});
}
/***********************************************************************/

/***********************************************************************
 *==================== INJECT COMMENTS (PLURAL) ========================
 ***********************************************************************
 * Receives a comments object with new comments and optionally, child comments.
 * Loops through each and injects expected html. Then loops again to animate
 * comment reveal
 * @param		object		comments		the comments object with params
 * 											and comment html
 */
function injectComments( comments ){
	// Inject top level comments
	if( 'new_comments' in comments ){
		var new_comments = comments.new_comments;
		// check if this is a getEarlier() call
		if( 'append' in comments ){
			injectParentCommentGroup( new_comments, 1 );
		}
		else{
			injectParentCommentGroup( new_comments );
		}
	}

	// Inject child comments
	if( 'new_child_comments' in comments ){
		// check if we have a reply_parent. If so, inject AND REVEAL all new comments
		// for this grouping
		/*
		if( 'reply_parent' in comments ){
			var reply_parent = comments.reply_parent;
		}
		*/
		var new_child_comments = comments.new_child_comments;
		// loop through child comments, inject html PER GROUP
		$.each( new_child_comments, function( parent_id, comments ){
			var params = {};
			params.comments = comments;
			params.parent_id = parent_id;

			injectChildCommentGroup( params );
		} );
	}

	// Show top Level comments. reverse() to reverse animation order
	if( 'new_comments' in comments ){
		var time = 0;
		$.each( new_comments, function( key, comment ){
			setTimeout( function(){
				revealParentComment( comment );
			}, time );
			time += 100;
		} );
	}
	else{
		var time = 0;
	}

	// Show child comments. No reverse() necessary because we insert in a
	// different order
	if( 'new_child_comments' in comments ){
		var new_child_comments = comments.new_child_comments;
		var child_time = 0;
		var group_time = 0;
			setTimeout( function(){
				$.each( new_child_comments, function( key, group ){
					// reveal each group
					group_time += revealChildCommentGroup( key );
					child_time += 100 + group_time;
				} );
			}, time );
	}

	// Show visibility buttons after being injected
	setTimeout( function(){
		showVisibilityButtons();
	}, time + ( typeof child_time != 'undefined' ? child_time : 0 ) );

	// Remove hidden-comment and injected-comment classes
	$( '.comment-item:visible' ).removeClass( 'hidden-comment' );

	// Get latest comment id and set hidden input for heartbeat comments
	if( 'latest_comment_id' in comments ){
		var latest_comment_id = comments.latest_comment_id;
		$( '#latest_comment_id' ).val( latest_comment_id );
	}

	// Clean up cached items
	emptyCommentCache();
	// re-bind reply button event
	bindReplyButton();
	// re-bind like button event
	bindLikeButton();
	// check for more button at bottom of page and inject if necessary
	checkTopLevelVisibilityButton();
	// scan all parents on page, if they have more than one child, inject visibility
	injectVisibilityButtons();
	// re-bind visibility button
	bindVisibilityButtons();
	// bind top level visibility button ajax
	bindTopLevelVisibilityButtonAjax();
}
/***********************************************************************/

/***********************************************************************
 *=========================== INJECT COMMENT ===========================
 ***********************************************************************
 * Injects a given comment onto the page. There are two main paths to take
 * based on comment parameters. If the comment is a top-level or 'main' comment
 * then we inject it at the top of the list. If it has a parent_id, then we
 * inject it after the given parent id
 */
function injectParentCommentGroup( group, append ){
	// need append flag to be made in controller!
	append = typeof append != 'undefined' ? append : false;
	// string together html
	var html = '';
	$.each( group, function( key, comment ){
		html += comment.comment_html;
	} );

	// decode html
	html = decodeHtml( html )

	// inject
	if( !append ){
		var wrapper = $( '.comment-thread-wrapper' );
		wrapper.prepend( html );
	}
	else{
		var visibility = $( '#comment-visibility-top-level' ).parent( '.visibility-button-row' );
		visibility.before( html );
	}
}
/***********************************************************************/

/***********************************************************************
 *========================INJECT CHILD COMMENTS ========================
 ***********************************************************************
 * Injects a group of child comments onto the page. Receives one object, params
 * who contains these varaibles:
 * @param			comments		object			a json object with each
 * 													item being a comment
 * @param			parent_id		int				the comment set's parent_id
 * @param			reply_parent	int				if present, indicates that
 * 													the user's reply is contained
 * 													in this comment set
 * Injects a given comment onto the page. There are two main paths to take
 * based on comment parameters. If the comment is a top-level or 'main' comment
 * then we inject it at the top of the list. If it has a parent_id, then we
 * inject it after the given parent id
 */
function injectChildCommentGroup( params ){
	// unpack comment
	var comments = params.comments;
	var parent_id = params.parent_id;
	// if the user's reply is contained within this comment set
	if( 'reply_parent' in params ) var reply_parent = params.reply_parent;
	// string together html
	var html = '';
	$.each( comments, function( key, comment ){
		html += comment.comment_html;
	} );

	// decode html entities
	html = decodeHtml( html );

	// inject
	var wrapper = $( '#reply-wrapper-' + parent_id );
	wrapper.append( html );
}
/***********************************************************************/

/***********************************************************************
 *=================== BIND LIKE BUTTON CLICK AJAX ======================
 ***********************************************************************
 * Binds a click event to every comment like button.
 */
function bindLikeButton(){
	// find all like buttons
	var buttons = $( '.comment-like-button' );
	// unbind previous click events
	buttons.unbind( 'click' );
	// bind new event handler
	buttons.click( function(){
		var button = $( this );
		// build return data object
		var likeData = {};
		// fill object
		likeData.comment_id = $( this ).data( 'comment_id' );

		//do tha ajax, bro
		$.ajax({
			url: '/social/comment/like',
			method: 'POST',
			data: likeData,
			dataType: 'json',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function( return_data ){
				// stringify and re-parse json
				var data_string = JSON.stringify( return_data );
				var data = JSON.parse( data_string );

				// make topalert if error
				if( typeof data.topAlert != 'undefined' ){
					topAlert( data.topAlert );
				}
				
				// if like/unlike successful
				if( typeof data.count != 'undefined' ){
					var count = data.count;
					var counter = button.prev( '.comment-like-count' );

					// if comment was liked
					if( count > 0 ){
						var text = '+' + count;
					}
					else{
						var text = '';
					}

					updateLikeCount( counter, text );
				}
			}
		})
	} );
}
/***********************************************************************/

/***********************************************************************
 *======================== UPDATE LIKE COUNT ===========================
 ***********************************************************************
 * Updates the like count for a given counter with some given text
 */
function updateLikeCount( counter, text ){
	// hide val
	counter.fadeOut( 250, 'easeInOutExpo', function(){
		// update val
		counter.html( text );
		// show val
		counter.fadeIn( 250, 'easeInOutExpo' );
	} );
}
/***********************************************************************/

/***********************************************************************
 *=================== BIND REPLY BUTTON CLICK EVENT ====================
 ***********************************************************************
 * Binds the reply button click event. Toggles reply dialog.
 */
function bindReplyButton(){
	var reply_button = $( '.reply-button' );
	// Unbind previous binds. ( prevents multiple binding )
	reply_button.unbind( 'click' );
	// Re-bind click event
	reply_button.click( function(){
		// check clicked flag
		var thread_wrapper = $( '.comment-thread-wrapper' );
		var reply_dialog_id = thread_wrapper.data( 'reply_dialog_id' );

		// Click toggle control structure
		var button_data = $( this ).data();
		var comment_id = button_data.comment_id;
		if( typeof reply_dialog_id == 'undefined' || reply_dialog_id == 0 || reply_dialog_id == '0' ){
			openReplyDialog( button_data );
		}
		else{
			// Close dialog
			closeReplyDialog( reply_dialog_id );

			// If user clicked a different reply button, we close, then open a
			// new one in one click
			if( reply_dialog_id != comment_id ){
				openReplyDialog( button_data );
				// update new reply_dialog_id flag to current open dialog id
				thread_wrapper.data( 'reply_dialog_id', comment_id );
				var reopen = true;
			}
		}

		// if we re-opened the reply dialog, don't set open flag to zero
		if( typeof reopen == 'undefined' ){
			// else, update reply dialog open flag
		thread_wrapper.data( 'reply_dialog_id' ) ? thread_wrapper.data( 'reply_dialog_id', 0 ) : thread_wrapper.data( 'reply_dialog_id', comment_id );

		}

		bindReplyCancel();
	} );
}
/***********************************************************************/

/***********************************************************************
 *====================== BINDS REPLY CANCEL CLICK ======================
 ***********************************************************************
 * Binds the click event to the reply dialog cancel button. Takes no arguments
 * but requires that the button has a dialog_id data attribute, so it can be
 * sent as a parameter to closeReplyDialog().
 */
function bindReplyCancel(){
	var reply_cancel = $( '.reply-cancel' );
	// unbind click event
	reply_cancel.unbind( 'click' );
	reply_cancel.click( function(){
		closeReplyDialog( $( this ).data( 'dialog_id' ) );

		$( '.comment-thread-wrapper' ).data( 'reply_dialog_id', 0 );
	} );
}
/***********************************************************************/

/***********************************************************************
 *======================= CLOSE REPLY DIALOG ===========================
 ***********************************************************************
 * Closes a reply dialog based on id
 */
function closeReplyDialog( id ){
	// find reply wrapper by id
	var wrapper = $( '#reply-dialog-' + id );
	if( !wrapper.length ){
		wrapper = $( '.reply-dialog' );
	}

	// hide wrapper and delete
	wrapper.slideUp( 250, 'easeInOutExpo', function(){
		wrapper.parent( '.row.collapse' ).remove();
	} );

	// re-initialize foundation
	//init_comment_fndtn();
}
/***********************************************************************/

/***********************************************************************
 *============================= SET ANON ===============================
 ***********************************************************************
 * Updates anon status based on comment post. If user checks anon, we loopback
 * and update the cached Plex.comments namespace and the main comment checkbox
 */
function setAnon( data ){
	checkCommentsNamespace();
	// set anon
	if( typeof data.anon != 'undefined' ){
		var anon = parseInt( data.anon );
		var checkbox = $( '#post_anon' );
		// set in cache
		Plex.comments.anon = anon;
		// update post checkbox
		checkbox.prop( 'checked', anon ? true : false );
	}
}
/***********************************************************************/

/***********************************************************************
 *======================== OPEN REPLY DIALOG ===========================
 ***********************************************************************
 * Opens the reply dialog when a user clicks the reply button
 */
function openReplyDialog( button_data ){
	// unpack button data
	var id = button_data.reply_to_id;
	var reply_to_id = button_data.reply_to_id;
	var reply_to_user_id = button_data.reply_to_user_id;
	var handle = button_data.reply_to_user_handle;

	// get template
	//checkCommentsNamespace() is already called in reply template cache
	checkReplyTemplateCache();
	var reply_template = Plex.comments.reply_template;

	// SET PARENT COMMENT
	// inject dialog after #reply-wrapper-[parent_id]
	var reply_wrapper = $( '#reply-wrapper-' + id ).parent( '.row' );

	// INJECTION
	reply_wrapper.after( reply_template );

	// set anon if anon
	var anon = typeof Plex.comments.anon != 'undefined' ? Plex.comments.anon : 0;
	$( '#reply_anon' ).prop( 'checked', anon ? true : false );

	// find wrapper div and add classes so we can locate and hide
	var wrapper = $( '.reply-dialog-temp' );
	wrapper.removeClass( 'reply-dialog-temp' );
	wrapper.attr( 'id', 'reply-dialog-' + reply_to_id );

	// add data to form to be used on valid submit
	var form = wrapper.find( 'form.reply-form' );
	form.data( 'parent', reply_to_id );
	form.data( 'ref_user_id', reply_to_user_id );

	// Add id to cancel button to locate reply dialog
	var cancel_button = $( '.reply-cancel-temp' );
	cancel_button.removeClass( 'reply-cancel-temp' );
	cancel_button.data( 'dialog_id', id );

	// add reference user handle
	if( handle ){
		var reply_ref_wrapper = $( '.comment-reply-ref-temp' );
		var reply_handle_span = reply_ref_wrapper.children( 'span' );
		handle = '@' + handle;
		reply_handle_span.html( handle );
		reply_ref_wrapper.removeClass( 'comment-reply-ref-temp' );
	}

	// scroll to position
	// Show template
	wrapper.slideDown( 250, 'easeInOutExpo', function(){
	$( 'html, body' ).animate(
		{ scrollTop: wrapper.offset().top - ( window.innerHeight/2 ) },
		250, 
		'easeInOutExpo'
		);
	} );

	// re-initialize foundation
	init_comment_fndtn();
}
/***********************************************************************/

/***********************************************************************
 *====================== ANIMATE CONTENT SHOW ==========================
 ***********************************************************************
 * Runs after all comments are injected. Animates the revealing of comments and
 * hides/removes reply template after the reply with which it is associated is
 * successfully posted and returned.
 */
function revealParentComment( comment ){
	// Check if this comment is the user's just-posted reply and remove dialog
	if( comment.users_reply ){
		var reply_dialog = $( '#reply-dialog-' + comment.parent_id );
		// Hide and remove dialog
		reply_dialog.hide( 'slide', {
			direction: 'right',
			easing: 'easeInOutExpo'
		}, 500, function(){
			// Show new comment
			$( '#comment-' + comment.id ).show( 'slide', {
				direction: 'left',
				easing: 'easeInOutExpo'
			}, 500 );

			// remove dialog
			reply_dialog.parent( '.row.collapse' ).remove();
		} );
	}
	else{
	// If this was not just posted
		var this_comment = $( '#comment-' + comment.id );
		// Only show top-level comments OR the latest comment
		if( comment.parent_id == null || this_comment.hasClass( 'latest-reply-' + comment.parent_id ) ){
			// Reveal comment
			$( '#comment-' + comment.id ).show( 'slide', {
				direction: 'left',
				easing: 'easeInOutExpo'
			}, 500 );
		}
	}
}
/***********************************************************************/

/***********************************************************************
 *====================== ANIMATE CONTENT SHOW ==========================
 ***********************************************************************
 * Runs after all comments are injected. Animates the revealing of comments and
 * hides/removes reply template after the reply with which it is associated is
 * successfully posted and returned.
 */
function revealChildCommentGroup( parent_id ){
	// show last hidden child
	var count = 0;
	var animation_time = 500;
	var reply_wrapper = $( '#reply-wrapper-' + parent_id );
		var children = reply_wrapper.find( '.child-of-' + parent_id );
		var visible = children.filter( ':visible' ).length;
	var last = $( '.child-of-' + parent_id + ':last' )
	var reply_dialog = $( '#reply-dialog-' + parent_id );

	// if reply dialog is found, hide it
	if( reply_dialog.length ){
		reply_dialog.hide( 'slide', {
			direction: 'right',
			easing: 'easeInOutExpo'
		}, animation_time, function(){
			// remove dialog
			reply_dialog.remove();
			// set dialog as closed
			$( '.comment-thread-wrapper' ).data( 'reply_dialog_id', 0 );
			// select all children who are in same group as user's reply
			var replies = $( '.comment-show-reply-group-' + parent_id );
			replies.removeClass( 'comment-show-reply-group-' + parent_id );
			$.each( replies, function( key, reply ){
				$( reply ).show( 'slide', {
					direction: 'left',
					easing: 'easeInOutExpo'
				}, 500 );
			} );
		} );
	}
	// if the user has revealed all child comments, show these
	else if( visible > 1 ){
		var hidden = children.filter( ':hidden' );
		var time = 0;
		$.each( hidden, function( h_key, h_child ){
			setTimeout( function(){
				// reveal child
				$( h_child ).show( 'slide', {
					direction: 'left',
					easing: 'easeInOutExpo'
				}, 500 );
			}, time );
			time += 100;
		} );
	}
	else if( visible == 1 ){
		// do nothing if user has not opened visible
	}
	// if we've found a last, show and increment count
	else if( last.length ){
		last.show( 'slide', {
			direction: 'left',
			easing: 'easeInOutExpo'
		}, animation_time );
		count++;
	}

	return count * animation_time;
}
/***********************************************************************/

/***********************************************************************
 *======================== COMMENT HEARTBEAT ===========================
 ***********************************************************************
 * Gets latest comments since last update
 */
// function startCommentHeartbeat(){
// 	setInterval( function(){
// 		var heartbeatData = {};
// 		heartbeatData.thread_id = $( '#comment_thread' ).val();
// 		heartbeatData.latest_comment_id = $( '#latest_comment_id' ).val();
// 		heartbeatData.is_heartbeat = 1;
// 		$.ajax({
// 			url: '/social/comment/getLatest',
// 			method: 'GET',
// 			data: heartbeatData,
// 			dataType: 'json',
// 			success: function( return_data ){
// 				// stringify and re-parse json
// 				var data_string = JSON.stringify( return_data );
// 				var data = JSON.parse( data_string );

// 				// inject only if comments returned
// 				if( typeof data.new_comments != 'undefined' || typeof data.new_child_comments != 'undefined' ){
// 					injectComments( data );
// 				}
// 				/*
// 				if( data.new_comments.length || data.new_child_comments.length ){
// 					injectComments( data );
// 				}
// 				*/

// 				// set latest if returned
// 				if( typeof data.latest_comment_id != 'undefined' ){
// 					$( '#latest_comment_id' ).val( data.latest_comment_id );
// 				}
// 			}
// 		});
// 	}, 30000 );
// }
/***********************************************************************/

/***********************************************************************
 *===================== SERIALIZE TO QUERY STRING ======================
 ***********************************************************************
 * takes a single-level js object and converts to query string
 * @param		object		obj		the object to be converted
 * @return		string				the query string
 */
js_serialize = function(obj) {
  var str = [];
  for(var p in obj)
    if (obj.hasOwnProperty(p)) {
      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
  return str.join("&");
}
/***********************************************************************/

/***********************************************************************
 *===================== AJAX REPLY SUBMISSION ==========================
 ***********************************************************************
 * Bind an ajax event to valid submission of a comment reply
 */
function bindReplySubmitAjax(){
	var reply_form = $( '.reply-form' );
	// Unbind prevous, if any
	reply_form.unbind( 'valid.fndtn.abide' );
	reply_form.on( 'valid.fndtn.abide', function( e ){
		e.preventDefault();
		// hide submit button
		$( '.reply-buttons' ).slideUp( 250, 'easeInOutExpo' );

		// Set outbound ajax data
		var form = $( this );
		var reply_data = {};
		reply_data.comment_textarea = form.find( '.reply_textarea' ).val();
		//reply_data.current_page = $( "[name='current_page']" ).val();
		reply_data.item_id = $( "[name='item_id']" ).val();
		reply_data.comment_thread = $( '#comment_thread' ).val();
		reply_data.latest_comment_id = $( '#latest_comment_id' ).val();
		reply_data.parent = form.data( 'parent' );
		// will be either int ( reply to a reply ) or '' reply to top-level
		reply_data.ref_user_id = form.data( 'ref_user_id' );
		reply_data.post_anon  = $( '#reply_anon' ).is( ':checked' ) ? 1 : 0;

		// Make query string
		reply_data = js_serialize( reply_data );

		// Perform ajax
		$.ajax({
			url: '/social/comment/new',
			method: 'POST',
			data: reply_data,
			dataType: 'json',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function( return_data ){
				// stringify and re-parse json
				var data_string = JSON.stringify( return_data );
				var data = JSON.parse( data_string );

				// show topAlert
				if( typeof data.top_alert != 'undefined' ){
					topAlert( data.top_alert );
				}

				setAnon( data );

				injectComments( data );

				// set latest_comment_id
				$( '#latest_comment_id' ).val( data.latest_comment_id );

			}
		});
	} );
}
/***********************************************************************
 *===================== AJAX COMMENT SUBMISSION ========================
 ***********************************************************************
 * Performs ajax submission on valid foundation.
 */
function bindCommentSubmitAjax(){
	$( '#comment_form' ).on( 'valid.fndtn.abide', function(){
		// disable button
		var post_button = $( '#comment-post-btn' );
		var textarea = $( '#comment_textarea' );
		var char_counter = $( '#comment-psuedo-box-char-count' );
		var anon = $( '#post_anon' );
			post_button.prop( 'disabled', true );
			textarea.prop( 'readOnly', true );
			anon.prop( 'readOnly', true );

		// Perform ajax
		$.ajax({
			url: '/social/comment/new',
			method: 'POST',
			data: $( this ).serialize(),
			dataType: 'json',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function( return_data ){
				// stringify and re-parse json
				var data_string = JSON.stringify( return_data );
				var data = JSON.parse( data_string );

				// show topAlert
				if( typeof data.top_alert != 'undefined' ){
					topAlert( data.top_alert );
				}

				setAnon( data );

				injectComments( data );

				// set latest_comment_id
				$( '#latest_comment_id' ).val( data.latest_comment_id );
			}, // End success
			complete: function(){
				// Re-enable form elements
				post_button.prop( 'disabled', false );
				textarea.prop( 'readOnly', false );
				anon.prop( 'readOnly', false );

				// Clear fields
				textarea.val( '' );
				char_counter.val( '' );
				anon.prop( 'checked' );
			}
		});
	} );
}
/***********************************************************************/

/***********************************************************************
 *=================== INJECT VISIBILITY BUTTONS ========================
 ***********************************************************************
 * Injects all visibility buttons onto the page after comments are injected.
 */
function injectVisibilityButtons(){
	// select all parents on page
	var parents = $( '.comment-item.small-12' );

	// loop through parent, check if:
		// More than one child
		// does not have visibility
	// inject
	$.each( parents, function( key, _parent ){
		// convert to jquery object
		var jq_parent = $( _parent );
		// get comment id
		var comment_id = jq_parent.data( 'comment_id' );
		// get children
		//var num_children = $( '.child-of-' + comment_id ).length;
		var num_hidden_children = $( '.child-of-' + comment_id + ':hidden' ).length;

		// end this running of loop if less than 2 children
		if( num_hidden_children < 2 ){
			return true;
		}

		// check for visibility button
		var exists = $( '#comment-visibility-button-' + comment_id );
		if( exists.length ){
			return true;
		}

		injectVisibilityButton( comment_id, 11 );
	} );
}
/***********************************************************************/

/***********************************************************************
 *====================== INJECT VISIBILITY BUTTON ======================
 ***********************************************************************
 * Injects a non-ajax visibility button for a given top-level comment's comment
 * id
 * @param		comment_id		int			id of the top-level comment under
 * 											which to inject the button
 * @param		width			int			*optional* int of width 1-12. used
 * 											to set the button's width
 */
function injectVisibilityButton( comment_id, width ){
	checkCommentsNamespace();
	checkVisibilityTemplateCache();
	// get template
	var template = Plex.comments.visibility_template;
	// find parent row
	var parent_row = $( '#comment-row-' + comment_id );
	// inject
	parent_row.after( template );
	// select newly injected
	var button = $( '.comment-visibility-temp' );
		// remove temp class
		button.removeClass( 'comment-visibility-temp' );
		// add id ( used to check if exists )
		button.attr( 'id', 'comment-visibility-button-' + comment_id );
	// set parent_id so it can be used to show/hide correct comment set
	button.data( 'parent_id', comment_id );
	// set button width
		width = width || 11;
		// calculate width and offset
		width = parseInt( width );
		var offset = 12 - width;
		// Declare class prefix
		var width_class = 'small-' + width.toString();
		var offset_class = offset != 0 ? 'small-offset-' + offset.toString() : null;
		// set width
		button.addClass( width_class );
		// set offset
		if( offset_class ) button.addClass( offset_class );
}
/***********************************************************************/

/***********************************************************************
 *================== INJECT MAIN VISIBILITY BUTTON =====================
 ***********************************************************************
 * Injects the 'more' comments button for the bottom of the page to load them
 * in via AJAX
 */
function injectTopLevelVisibilityButton(){
	checkCommentsNamespace();
	checkVisibilityTemplateCache();
	// retrieve visibility template from cache
	var template = Plex.comments.visibility_template;
	// cache thread wrapper
	var thread_wrapper = $( '.comment-thread-wrapper' );
	// set default width
	var width = 12;
	// Declare class prefix
	var width_class = 'small-' + width.toString();

	// Append template
	thread_wrapper.append( template );
	// Select new button
	var button = $( '.comment-visibility-temp' );
	button.removeClass( 'comment-visibility-temp' );
	// mark as top-level visibility button
	if( button.length ){
		button.attr( 'id', 'comment-visibility-top-level' );
	}

	// finish operations on button
	if( button && button.length ){
		// set button width
		button.addClass( width_class );
	}
}
/***********************************************************************/

/***********************************************************************
 *==================== DISPLAY VISIBILIY BUTTONS =======================
 ***********************************************************************
 * Shows any hidden comment visibility buttons
 */
function showVisibilityButtons(){
	$( '.comment-visibility:hidden' ).slideDown( 250, 'easeInOutExpo' );
}
/***********************************************************************/

/***********************************************************************
 *=========== BIND COMMMENT VISIBILITY BUTTON CLICK EVENT ==============
 ***********************************************************************
 * Binds/re-binds the click event to visibility buttons to show/hide all child
 * comments.
 */
function bindVisibilityButtons(){
	// select buttons
	var buttons = $( '.comment-visibility:not( #comment-visibility-top-level )' );
	// unbind previously bound click events
	buttons.unbind( 'click' );
	// rebind click event
	buttons.click( function(){
		var visible = $( this ).data( 'visible' );
		var parent_id = $( this ).data( 'parent_id' );
		if( typeof visible == 'undefined' || !visible ){
			// SHow replies and switch button text with callback
			showReplies( parent_id, $( this ) );
			// set variable for visible flag later
			visible = 1;
		}
		else if( visible ){
			// hide replies and switch button text with callback
			hideReplies( parent_id, $( this ) );
			// set variable for visible flag later
			visible = 0;
		}

		// set button visibility flag
			$( this ).data( 'visible', visible );
	} );
}
/***********************************************************************/

/***********************************************************************
 *================== BIND TOP LEVEL VISIBILITY AJAX ====================
 ***********************************************************************
 * Binds ajax call to top level visibility button
 */
function bindTopLevelVisibilityButtonAjax(){
	var button = $( '#comment-visibility-top-level' );
	button.unbind( 'click' );
	button.click( function(){
		/* show loading button text. Use callback to
		prevent button text from clashing */ 
		visibilityShowLoading( button, function(){
			// set outgoing data
			var outgoing_data = {};
			outgoing_data.thread_id = $( '#comment_thread' ).val();
			outgoing_data.earliest_comment_id = $( '#earliest_comment_id' ).val();
			$.ajax({
				url: '/social/comment/getEarlier',
				method: 'GET',
				data: outgoing_data,
				dataType: 'json',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				success: function( return_data ){
					// stringify and re-parse
					var data_string = JSON.stringify( return_data );
					var data = JSON.parse( data_string );

					// inject if we have new data
					if( typeof data.new_comments != 'undefined' && data.new_comments.length ){
						injectComments( data );
					}

					// set earliest id and unbind ajax from button if no more left
					// and show 'end' button
					var earliest_id = data.earliest_comment_id;
					$( '#earliest_comment_id' ).val( earliest_id );
					if( earliest_id == 0 || ( typeof data.new_comments != 'undefined' && data.new_comments.length < 10 )){
						unbindTopLevelVisibilityButton();
					}
					// If there are still more, show 'more' button
					else{
						visibilityShowMore( button );
					}
				} // end success block
			});
		} );
	} );
}
/***********************************************************************/

/***********************************************************************
 *===================== CHECK COMMENT NAMESPACE ========================
 ***********************************************************************
 * Checks comment namespace, creates if not exists
 */
function checkCommentsNamespace(){
	if( typeof Plex.comments == 'undefined' ){
		Plex.comments = {};
	}
}
/***********************************************************************/

/***********************************************************************
 *=================== EMPTIES THE COMMENT CACHE ========================
 ***********************************************************************
 * Empties the comment cache. not sure if i'll use this or some other way
 */
function emptyCommentCache(){
	if( typeof Plex.comments != 'undefined' ){
		if( typeof Plex.comments.earliest != 'undefined' ){
			delete Plex.comments.earliest;
		}
	}
}
/***********************************************************************/

/***********************************************************************
 *======= UNBIND AND DISABLE TOP LEVEL COMMENT VISIBILITY BUTTON =======
 ***********************************************************************
 * This is called when there are no older comments left to show.
 */
function unbindTopLevelVisibilityButton(){
	var button = $( '#comment-visibility-top-level' );
	button.unbind();
	visibilityShowEnd( button );
}
/***********************************************************************/

/***********************************************************************
 *================= CHECK TOP LEVEL VISIBILITY BUTTON ==================
 ***********************************************************************
 * checks for top level visibility button and adds it if not present
 */
function checkTopLevelVisibilityButton(){
	var button = $( '#comment-visibility-top-level' );
	var comments = $( '.comment-item.small-12' );
	// only inject if there is at least 10 parent comments
	if( comments.length >= 10 ){
		if( !button.length ){
			injectTopLevelVisibilityButton( 'top_level' );
			bindTopLevelVisibilityButtonAjax();
		}
	}
}
/***********************************************************************

/***********************************************************************
 *=========================== INJECT COMMENT ===========================
 ***********************************************************************
 * Injects a given comment onto the page. There are two main paths to take
 * based on comment parameters. If the comment is a top-level or 'main' comment
 * then we inject it at the top of the list. If it has a parent_id, then we
 * inject it after the given parent id
 */
function injectComment( comment ){
	// Set parent id
	var parent_id = comment.parent_id;

	// Set comment width and offset
	var offset = 0;
	var width = 12;
	var offset_class = '';
	var width_class = '';

	// create comments sub-object
	checkCommentsNamespace();

	// Inject top level comments at the top of the comment container
	if( parent_id == null ){
		// APPEND to end of list
		if( typeof comment.append_earlier != 'undefined' && comment.append_earlier ){
			// cache jquery object we're appending after
			if( typeof Plex.comments.earliest == 'undefined' ){
				var earliest_id = $( '#earliest_comment_id' ).val();
				Plex.comments.earliest = $( '#comment-row-' + earliest_id );
			}
			Plex.comments.earliest.after( decodeHtml( comment.comment_html ) );
		}
		// PREPEND to beginning of list
		else{
			// cache thread wrapper
			if( typeof Plex.comments.thread_wrapper == 'undefined' ){
				Plex.comments.thread_wrapper = $( '.comment-thread-wrapper' );
			}
			Plex.comments.thread_wrapper.prepend( decodeHtml( comment.comment_html ) );
		}
		var new_comment = $( '#comment-' + comment.id );
	}
	// Inject child comments after parent
	else{
		// Check if parent already has a child under it.
		var latest_reply = $( '.latest-reply-' + parent_id );
		if( latest_reply.length ){
			// if parent has a child, inject comment after last child (bottom most)
			latest_reply.parent( '.row' ).after( decodeHtml( comment.comment_html ) );
			var new_comment = $( '#comment-' + comment.id );
			latest_reply.removeClass( 'latest-reply-' + parent_id );
		}
		else{
			// append after parent if no children
			var parent_comment_row = $( '#comment-row-' + parent_id );
			parent_comment_row.after( decodeHtml( comment.comment_html ) );
			var new_comment = $( '#comment-' + comment.id );
		}

		// Set offset, column width, offset data based on parent's offset
		var parent_comment = $( '#comment-' + parent_id );
		offset = parseInt( parent_comment.data( 'offset' ) );
		offset++;
		width = width - offset;
		offset_class = 'small-offset-' + offset.toString();

		// Add latest reply flag for all child comments
		new_comment.addClass( 'latest-reply-' + parent_id );
		new_comment.addClass( 'child-of-' + parent_id );

		// set reply id in reply button so reply is to top-level comment
		/*
		var reply_button = $( '#reply-button-' + comment.id );
		reply_button.data( 'comment_id', parent_id );
		*/
	}
	new_comment.data( 'offset', offset );


	// Set width class for all comments
	width_class = 'small-' + width.toString();

	/*
	// Add width and offset classes for reply
	var reply_width = width - 1;
	var reply_offset = offset + 1;
	var reply_width_class = 'small-' + reply_width.toString();
	var reply_offset_class = 'small-offset-' + reply_offset.toString();
	var reply = $( '.comment-' + comment.id ).parent().next( '.row' ).find( '.reply-item' );
	if( reply ){
		reply.addClass( reply_width_class );
		reply.addClass( reply_offset_class );
	}
	*/

	// Add width and offset classes
	new_comment.addClass( offset_class );
	new_comment.addClass( width_class );

	// Add offset data
	new_comment.data( 'offset', offset );
}
/***********************************************************************/

/***********************************************************************
 *=================== COMMENT FOCUS ANIMATIONS =========================
 ***********************************************************************
 * Bind animations for when comments box is in focus
 * Controls fade in/out of placeholder text, and slide in/out of big quotes
 */
function bindCommentFocusAnimations(){
	$( '#comment_textarea' ).focusin( function(){
		var placeholder = $( '#comment-psuedo-box-placeholder' );
		var ldquo_big = $( '#comment-psuedo-box-ldquo-big' );
		var rdquo_big = $( '#comment-psuedo-box-rdquo-big' );

		placeholder.fadeOut( 250, 'easeInOutExpo', function(){
			ldquo_big.show( 'slide', { direction: 'up' }, 250 );
			rdquo_big.show( 'slide', { direction: 'down' }, 250 );
		} );
	} )

	$( '#comment_textarea' ).focusout( function(){
		var placeholder = $( '#comment-psuedo-box-placeholder' );
		var ldquo_big = $( '#comment-psuedo-box-ldquo-big' );
		var rdquo_big = $( '#comment-psuedo-box-rdquo-big' );
		if( $( this ).val() == ''){
			placeholder.fadeIn( 250, 'easeInOutExpo', function(){
				ldquo_big.hide( 'slide', { direction: 'up' }, 250 );
				rdquo_big.hide( 'slide', { direction: 'down' }, 250 );
			} );
		}
		else{
			placeholder.fadeOut( 250, 'easeInOutExpo' );
		}
	} );
}
/***********************************************************************/

/***********************************************************************
 *========================= CHECK COMMENT VAL ==========================
 ***********************************************************************
 * Check comment value. If not empty then remove placeholder and add big quotes
 */
function checkCommentVal(){
	var form = $( '#comment_textarea' );
	if( form.val() != '' ){
		var placeholder = $( '#comment-psuedo-box-placeholder' );
		var ldquo_big = $( '#comment-psuedo-box-ldquo-big' );
		var rdquo_big = $( '#comment-psuedo-box-rdquo-big' );

		placeholder.fadeOut( 250, 'easeInOutExpo', function(){
			ldquo_big.show( 'slide', { direction: 'up' }, 250 );
			rdquo_big.show( 'slide', { direction: 'down' }, 250 );
		} );
	}
}
/***********************************************************************/

/***********************************************************************
 *=================== UPDATE COMMENT CHAR COUNTER ======================
 ***********************************************************************
 * Bind keyup to update character counter
 */
function bindCommentCharCounter(){
	$( '#comment_textarea' ).keyup( function(){
		var val = $( this ).val();
		var count = val.length;
		var counter_display = $( '#comment-psuedo-box-char-count' );

		counter_display.html( count != 0 ? count : '' );
	} );
}
/***********************************************************************/

/***********************************************************************
 *================ SET COMMENT CHAR COUNT ON PAGE LOAD==================
 ***********************************************************************
 * Sets comment character count on page load (if page was refreshed, comment
 * comment value must be initialized)
 */
function initCommentCharCount(){
	var val = $( '#comment_textarea' ).val();
	if( !val ){
		return false;
	}
	var count = val.length;
	$( '#comment-psuedo-box-char-count' ).html( count != 0 ? count : '' );
}
/***********************************************************************/

/***********************************************************************
 *==================== CHECK COMMENTS NAMESPACE ========================
 ***********************************************************************
 * Checks to see if comments namespace exists, and creates if necessary
 */
function checkCommentsNamespace(){
	// make comments namespace if not set
	if( typeof Plex.comments == 'undefined' ){
		Plex.comments = {};
	}
}
/***********************************************************************/

/***********************************************************************
 *=============== CHECK REPLY DIALOG TEMPLATE CACHE ====================
 ***********************************************************************
 * Checks to see if reply template is cached, and creates if not
 */
function checkReplyTemplateCache(){
	checkCommentsNamespace();
	// cache template
	if( typeof Plex.comments.reply_template == 'undefined' ){
		// cache thread wrapper
		var thread_wrapper = $( '.comment-thread-wrapper' );
		// get object that contains template
		var comments = thread_wrapper.data( 'comments' );
		// get template
		var template = comments.reply_template;
		Plex.comments.reply_template = template;
	}
}
/***********************************************************************/

/***********************************************************************
 *================ CHECKS VISIBILITY TEMPLATE CACHE ====================
 ***********************************************************************
 * Checks to see if visibility template is cached, and creates it not
 */
function checkVisibilityTemplateCache(){
	checkCommentsNamespace();
	// cache template
	if( typeof Plex.comments.visibility_template == 'undefined' ){
		// cache thread wrapper
		var thread_wrapper = $( '.comment-thread-wrapper' );
		// get object that contains template
		var comments = thread_wrapper.data( 'comments' );
		// get template
		var template = comments.visibility_template;
		Plex.comments.visibility_template = template;
	}
}
/***********************************************************************/

/***********************************************************************
 *====================== DECODE HTML ENTITIES ==========================
 ***********************************************************************
 * Decodes entities for a given string
 */
function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
/***********************************************************************/
