$(document).ready(function(){

	$(document).foundation();

	initTags();

	// School Name Autocomplete
	init_ranking_autocomp(
		'#school_name_autocomp',
		'#school_name_tag_wrapper',
		"/getAutoCompleteData?zipcode=" + '95376' + "&type=college"
	);

	// State Autocomplete
	init_ranking_autocomp(
		'#state_autocomp',
		'#state_tag_wrapper',
		'/getStates'
	);

	// Religious Affiliation Autocomplete
	init_ranking_autocomp(
		'#religious_affiliation_autocomp',
		'#religious_affiliation_tag_wrapper',
		'/getCollegeReligions'
	);

	// Degree Select
	init_ranking_select(
		'#degree_select'
	);

	// School Sector/Institution Type Select
	init_ranking_select(
		'#school_sector_select'
	);

	// Ranking Source Select
	init_ranking_select(
		'#ranking_source_select'
	);

	// Campus Setting Select
	init_ranking_select(
		'#campus_setting_select'
	);

	// Religious Affiliation Select
	init_ranking_select(
		'#religious_affiliation_select'
	);

});

/***********************************************************************
 * ======================= INITIALIZE ALL TAGS =========================
 * ========================= RANKING/LISTING ===========================
 ***********************************************************************
 * For /ranking/listing advanced-search
 * This function initializes ALL of the 'build tags' functions.
 * These tags are the options that a user has selected in the advanced
 * search.
 */
function initTags(){
	// Ranking name of school
	var hiddenInput = $('input[name="ranking-search-school"]');
	var nameData = hiddenInput.data('schoolnames');

	// Build an array of id strings that correspond to the tag list hidden input
	var input_ids = [ 
		'#school_name_tag_list',
		'#state_tag_list',
		'#degree_tag_list',
		'#school_sector_tag_list',
		'#ranking_source_tag_list',
		'#campus_setting_tag_list',
		'#religious_affiliation_tag_list'
	];

	// Iterate through tag lists array and build tags for each
	$.each( input_ids, function( index, value ){
		var tag_data = $( value ).data('tags');
		buildTags( tag_data, value );
	} );
}
/***********************************************************************/

/***********************************************************************
 *===================== INITIALIZE SELECT BIND =========================
 ***********************************************************************
 */
function init_ranking_select( element ){
	var tag_list_element = element.replace( 'select', 'tag_list' );
	$( element ).change(function(){
		// Create a ui object like the one returned on autocomplete select callback
		var item = { id: $(this).val(), label: $( element + ' option:selected' ).text() };
		var ui = { item: item };
		addToTagList( tag_list_element, ui );
		// Reset select element
		$(this).val('');
	});
}
/***********************************************************************/

/***********************************************************************
 *========== INITIALIZES AUTOCOMPLETE FOR RANKING LISTING =============
 ***********************************************************************
 * Since we have multiple autocomplete elements on the listing page, why don't
 * we save space and use one function to run everything through?
 * @param		string		element			id of element to receive autocomplete
 * @param		string		route			route to use for autocomplete
 * 
 */
function init_ranking_autocomp( element, append, route ){
	var tag_list_element = element.replace( 'autocomp', 'tag_list' );
	//AutoComplete for Ranking Name of School
	//clear out the name of school text box on refresh
	$( element ).val('');

	$( element ).autocomplete({
		source: route,
		appendTo: append,
		select: function( event, ui ) {
			addToTagList( tag_list_element, ui );
			$(this).val('');
			return false;		// This one is needed to prevent jquery from autofilling the value
		},
		close: function( event, ui ) {
			$(this).val('');
		}
	});

	//Clears out the schoolname when someone bypasses the autocomplete
	$( element ).focusout(function(event) {
		$(this).val('');
	});
}
/***********************************************************************/

/***********************************************************************
 ==========================* ADD TO TAG LIST ===========================
 ***********************************************************************
 * Adds a tag to the hidden tag list element to be later added visually
 * to the tag_display element as a clickable tag.
 * @param		string		tag_list_element		the hidden element that contains
 * 													the list of tag data.
 * @param		object		uiObject				The ui object intially returned by a select
 * 													action in autocomplete.
 */
function addToTagList( tag_list_element, uiObject){
	var addtoArray = 1;
	var hiddenInput = $( tag_list_element );
	var tagData = hiddenInput.data('tags');

	//check if an array was returned. If not create one.
	if (!tagData) {
		var tagData = [];
	}

	//Check if this value is all ready in data object.
	$.each(tagData, function(index, val) {
		if (val.id == uiObject.item.id) {
			addtoArray = 0;
			return false;
		};
	});

	//if not set to false above add the school picked.
	if (addtoArray) {
		var x = {};
		$.each( uiObject.item, function(key, value){
			x[key] = value;
		} );
		tagData.push(x);

		// store the new data object
		hiddenInput.data('tags', tagData );

		var tag_display = tag_list_element.replace( 'list', 'display' );
		$( tag_display ).addClass( 'add_tag' );


		//call the function that builds tags
		buildTags(tagData, tag_list_element );
	};
}
/***********************************************************************/

/***********************************************************************
 * ============================ BUILD TAGS =============================
 ***********************************************************************
 * For /ranking/listing advanced-search
 * Builds/rebuilds tags based on an array/object of tags contained in the 
 * tag list element. This function is called on page load (during init by
 * initTags) and when a user adds a selection. It rebuilds
 * the whole tag list each time it is called.
 * @param		object		tagData				an object containing a list of tags for a particular
 * 												search input.
 * @param		string		tag_list_element	The id of the hidden element which holds the list of
 * 												tags as a data-tags value.
 */
function buildTags(tagData, tag_list_element){
	// Prevent building of tags of no tags
	if( !tagData ){
		return false;
	}
	var input = $( tag_list_element );
	var jsonStore = $( tag_list_element + '_json' );
	var tag_display = tag_list_element.replace( 'list', 'display' );
	input.data('tags', tagData );
	jsonStore.val( JSON.stringify(tagData) );

	//Build the value from the object and add into the hidden value as a string.
	var tag_list_values = [];
	var htmlcode = "";
	var default_value = input.data( 'default_value' );
	$.each(tagData, function(index, val) {
		tag_list_values.push( val[default_value] );
		htmlcode += '<div class="tag" data-id="' + val.id + '">' + val.label + '<span class="close_tag"><img src="/images/close-x-white.png" class="close_x"/></span></div>';
	});

	// Stringify tag_list_values if not empty
	tag_list_values = isEmpty( tag_list_values ) ? "" : JSON.stringify( tag_list_values );

	// Set value for hidden input
	input.val( tag_list_values );
	// Inject tag html
	$( tag_display ).html(htmlcode);
	// Animate tags
	$( '.tag:hidden' ).each( function( index ){
		var tag = $(this);
		tag.slideDown( 250, 'easeInOutExpo' );
	} );

	$( tag_display ).removeClass( 'add_tag' );

	


	// Bind click event to close button and pass the id of the element
	$('.close_tag').click(function(){
		var tag_id = $(this).parent().data('id');
		var tag_display = $(this).parent().parent('.tag_display').prop('id');
		var tag_list = '#' + tag_display.replace('display', 'list');

		//tag_display.addClass( "remove_tag" );
		// Animate hiding of the tag
		$(this).parent('.tag').slideUp( 250, 'easeInOutExpo', function(){
			removeFromTagList( tag_id, tag_list );
		} );
	});
}
/***********************************************************************/

/***********************************************************************
 *============================ REMOVES TAGS ===========================
 ***********************************************************************
 * For ranking/listing.
 * Removes items from the tag list for a particular search input criteria.
 * This fnction calls buildTags to rebuild a new list of tags once a
 * tag's information has been removed from the list
 * @param		string		id					The id of the tag.
 * @param		string		tag_list_element	the hidden element which holds a list of
 * 												tags and their information/data.
 */
function removeFromTagList( id, tag_list_element ){
	var hiddenInput = $( tag_list_element );
	var nameData = hiddenInput.data('tags');

	//Loops the remove tag onclick given and if found removes it from the array.
	$.each(nameData, function(index, vv) {
		if (id == vv.id) {
			//var endcount = index +1;   // what is this for?
			nameData.splice( index , 1 );
			return false;
		}
	});

	// Rebuild Tags
	buildTags(nameData, tag_list_element );
}
/***********************************************************************/
// Utility Function: Checks if object is empty
function isEmpty(obj) {
    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            return false;
    }
    return true;
}
