// carepackage.js

$(document).ready(function(){

	var sameShippingInfoCheckbox = $('#sameShippingInfoAsBillingInfo_id');
	var shippingInfoSection = $('.enter_shipping_info_section');

	//variable to store current url
	var current_path = window.location.href;
	current_path = current_path.substring(current_path.lastIndexOf('/') + 1);

	//event that checks when the 'same shipping info as billing info' checkbox has changed or been clicked
	//if clicked, then hide the shipping info section
	$(sameShippingInfoCheckbox).change(function(){

		//removes and adds 'required' attr to shipping section form fields based on if its hidden or not
		toggleRequiredAttr(sameShippingInfoCheckbox, shippingInfoSection);
	});

	//this is for if the user is on the cart tab and refreshes the page there, to continue hiding the carepackages
	if( current_path == 'carepackage#cart' || current_path == 'carepackage#orders' || current_path == 'carepackage#sponsor' ){
		$('.carepackage_items_wrapper').hide();
	}

	if( current_path == 'carepackage#sponsor' ){
		$('.donate_or_become_sponsor_section').show();
	}else{
		$('.donate_or_become_sponsor_section').hide();
	}
	

	//event that detects when the url changes, if cart page is visible and then hide the carepackage items, otherwise show them
	$(window).on('hashchange', function(e){

		if( $('#cart').is(':visible') || $('#orders').is(':visible') || $('#sponsor').is(':visible') ){
			$('.carepackage_items_wrapper').hide();
		}else{
			$('.carepackage_items_wrapper').show();
		}

		if( $('#sponsor').is(':visible') ){
			$('.donate_or_become_sponsor_section').show();
		}else{
			$('.donate_or_become_sponsor_section').hide();
		}

	});//end of url change event check


	/************ dynamic quick view modal based on which package 'add to cart' button was clicked - start 
	$('.add_lg_pack, .edit_lg_pack').on('click', function(){

		var data_array = $(this).data('quickview-modal-lg').split(',');

		//pass dynamic data to set modal with appropriate data
		setQuickViewModal( data_array[0], data_array[1], data_array[2] );

		//open quick view modal
		$('#package_quickview_modal').foundation('reveal', 'open');
	});

	$('.add_med_pack, .edit_med_pack').on('click', function(){

		var data_array = $(this).data('quickview-modal-med').split(',');
		setQuickViewModal( data_array[0], data_array[1], data_array[2] );

		//open quick view modal
		$('#package_quickview_modal').foundation('reveal', 'open');
	});

	$('.add_sm_pack, .edit_sm_pack').on('click', function(){

		var data_array = $(this).data('quickview-modal-sm').split(',');
		setQuickViewModal( data_array[0], data_array[1], data_array[2] );

		//open quick view modal
		$('#package_quickview_modal').foundation('reveal', 'open');
	});*/
	/*********** dynamic quick view modal based on which package 'add to cart' button was clicked - end */





	/************ dynamic orders detail modal - start **********/
	$('a.open_view_order_details_modal').on('click', function(e){
		e.preventDefault();
		
		var dataArray = $(this).data('order-details-data');
		var counter = $(this).data('counter');
		setOrderDetailsModal(dataArray, counter);

		$('#order_details_modal').foundation('reveal', 'open');
	});
	/************ dynamic orders detail modal - end **********/





	/* event listening for when the number input type changes to update the cart amount - start */
	$('.product_id_1').change(function(){
		var this_pack_val = parseInt( $(this).val() );
		var pack50 = parseInt( $('.product_id_2').val() );
		var pack100 = parseInt( $('.product_id_3').val() );

		var cart_total;

		var total = 0;
		var total_with_shipping = 0;

		var sm_pack_price = $('.select_quantity_1_package').data('small-price');
		var med_pack_price = $('.select_quantity_2_package').data('medium-price');
		var lg_pack_price = $('.select_quantity_3_package').data('large-price');
		
		cart_total = this_pack_val + pack50 + pack100;
		$('.added_to_cart_notification').html(cart_total);
		$('.package_1_indicator').html(this_pack_val + ' Added');

		total = (this_pack_val * sm_pack_price) + (pack50 * med_pack_price) + (pack100 * lg_pack_price);
		total_with_shipping = total + 10;

		$('.package_cost').html(formatToDollarAmt(total));
		$('.total_package_cost').html(formatToDollarAmt(total_with_shipping));
	});

	$('.product_id_2').change(function(){
		var this_pack_val = parseInt( $(this).val() );
		var pack25 = parseInt( $('.product_id_1').val() );
		var pack100 = parseInt( $('.product_id_3').val() );
		
		var cart_total;

		var total = 0;
		var total_with_shipping = 0;

		var sm_pack_price = $('.select_quantity_1_package').data('small-price');
		var med_pack_price = $('.select_quantity_2_package').data('medium-price');
		var lg_pack_price = $('.select_quantity_3_package').data('large-price');
		
		cart_total = this_pack_val + pack25 + pack100;
		$('.added_to_cart_notification').html(cart_total);
		$('.package_2_indicator').html(this_pack_val + ' Added');

		total = (this_pack_val * med_pack_price) + (pack25 * sm_pack_price) + (pack100 * lg_pack_price);
		total_with_shipping = total + 10;

		$('.package_cost').html(formatToDollarAmt(total));
		$('.total_package_cost').html(formatToDollarAmt(total_with_shipping));
	});

	$('.product_id_3').change(function(){
		var this_pack_val = parseInt( $(this).val() );
		var pack25 = parseInt( $('.product_id_1').val() );
		var pack50 = parseInt( $('.product_id_2').val() );

		var cart_total;

		var total = 0;
		var total_with_shipping = 0;

		var sm_pack_price = $('.select_quantity_1_package').data('small-price');
		var med_pack_price = $('.select_quantity_2_package').data('medium-price');
		var lg_pack_price = $('.select_quantity_3_package').data('large-price');
		
		cart_total = this_pack_val + pack25 + pack50;
		$('.added_to_cart_notification').html(cart_total);
		$('.package_3_indicator').html(this_pack_val + ' Added');

		total = (this_pack_val * lg_pack_price) + (pack25 * sm_pack_price) + (pack50 * med_pack_price);
		total_with_shipping = total + 10;

		$('.package_cost').html(formatToDollarAmt(total));
		$('.total_package_cost').html(formatToDollarAmt(total_with_shipping));
	});
	/* event listening for when the number input type changes to update the cart amount - end */


	/* event listening to when user uses the number input to update package count - start */
	$('.gc_selection input[type=checkbox]').change(function(){

		//grab and store the name of the modal of the current package and split it to get the first part and make it lowercase
		var which_package = $(this).parents('div.quickview_modal_rightside').find('div.quickview_pack_name div.pack_name').text().split(' ');
		var package_gc_data = 'gc-' + which_package[0].toLowerCase();
		var gc_label = 'gc_choice_' + which_package[0];

        if (this.checked) {
        	//uncheck all of the other options - only one gift card can be chosen
        	$('div.gc_selection input[type=checkbox]').not(this).prop('checked', false);

            //update the data attr with the gift card (gc) chosen
            $('div.gc_selection').data(package_gc_data, this.value);
            
            //update the gift card option label on review your order section
            $('.'+gc_label).html(this.value);

            //hide 'Must choose gift card!' error message
            $('.choose_gc_error').hide();

            $('.checkout_btn_link').removeClass('checkout_disabled');
            $('.continueShopping_btn_link').removeClass('checkout_disabled');
        }else{
        	$('.choose_gc_error').show();
        	$('.checkout_btn_link').addClass('checkout_disabled');
        	$('.continueShopping_btn_link').addClass('checkout_disabled');
        }
    });
    /* event listening to when user uses the number input to update package count - end */



    /* smooth scroll to select form on sponsor page - start */
    $('.becomeSponsor_donate_Link').click(function(e){
    	var sponsor_link = this.text;

    	//skips the default behavior of the element (if any)
    	e.preventDefault();

    	//smooth scroll to select element
    	$('html,body').animate({scrollTop:$(this.hash).offset().top}, 2000);

    	//bring select element to focus
    	$('#select_sponsor_donate').focus();
 
 		//
    	$('#select_sponsor_donate > option').filter(function(){

    		return $(this).text() == sponsor_link;
    	}).prop('selected', true);

    	if(sponsor_link == 'Donate to a student'){
    		$('.donate_or_become_sponsor_section .howmanystudents').show();
    		$('.donate_or_become_sponsor_section .choose_package').show();
    	}else{
    		$('.donate_or_become_sponsor_section .howmanystudents').hide();
    		$('.donate_or_become_sponsor_section .choose_package').hide();
    	}
    });
    /* smooth scroll to select form on sponsor page - end */


    /* show cart is empty on page load */
    $('.no_packages_added_to_cart_section').show();
	$('.yes_packages_added_to_cart_section').hide();




	/********** on load, check if cart has packages in it - start *********/
	var review_25 = $('.package_count_ticker.product_id_1');
	var review_50 = $('.package_count_ticker.product_id_2');
	var review_100 = $('.package_count_ticker.product_id_3');
	var cart_total = parseInt($(review_25).val()) + parseInt($(review_50).val()) + parseInt($(review_100).val());

	var review_25_row = $('.product_id_1_row');
	var review_50_row = $('.product_id_2_row');
	var review_100_row = $('.product_id_3_row');

	var add_to_cart_btn_sm = $('.select_quantity_1_package .static_package_value');
	var add_to_cart_btn_med = $('.select_quantity_2_package .static_package_value');
	var add_to_cart_btn_lg = $('.select_quantity_3_package .static_package_value');

	var sm_pack_col = $('.select_quantity_1_package .add_more_less_packages_col');
	var med_pack_col = $('.select_quantity_2_package .add_more_less_packages_col');
	var lg_pack_col = $('.select_quantity_3_package .add_more_less_packages_col');

	// var sm_pack_col = $('.select_quantity_1_package .package_1_indicator').data('package-count');
	// var med_pack_col = $('.select_quantity_2_package .package_2_indicator').data('package-count');
	// var lg_pack_col = $('.select_quantity_3_package .package_3_indicator').data('package-count');

	var sm_pack_price = $('.select_quantity_1_package').data('small-price');
	var med_pack_price = $('.select_quantity_2_package').data('medium-price');
	var lg_pack_price = $('.select_quantity_3_package').data('large-price');

	

	var total_price = ( parseInt($(review_25).val()) * sm_pack_price ) + 
					  ( parseInt($(review_50).val()) * med_pack_price ) + 
					  ( parseInt($(review_100).val()) * lg_pack_price );

	var total_price_plus_shipping = total_price + 10;

	var formattedPrice = formatToDollarAmt(total_price);
	var formattedPriceWithShipping = formatToDollarAmt(total_price_plus_shipping);
	  	
	var package_array = [];

	package_array[0] = new package( '.select_quantity_1_package', '.product_id_1', $('.package_count_ticker.product_id_1').val(), sm_pack_price, 'Small Pack' );
	package_array[1] = new package( '.select_quantity_2_package', '.product_id_2', $('.package_count_ticker.product_id_2').val(), med_pack_price, 'Medium Pack' );
	package_array[2] = new package( '.select_quantity_3_package', '.product_id_3', $('.package_count_ticker.product_id_3').val(), lg_pack_price, 'Large Pack' );

	if( review_25.val() > 0 ){
		
		$(review_25_row).css('visibility', 'visible');
		$('.yes_packages_added_to_cart_section').show();
		$('.no_packages_added_to_cart_section').hide();
		$('.added_to_cart_notification').show();
		$(add_to_cart_btn_sm).hide();
		$(sm_pack_col).show();
		// $('.select_quantity_1_package .add_more_less_packages_col .num_of_packages_indicator').data('package-count', $(review_25).val());
		$('.select_quantity_1_package .add_more_less_packages_col .num_of_packages_indicator').html($(review_25).val() + ' Added');
	}
	if( review_50.val() > 0 ){
		
		$(review_50_row).css('visibility', 'visible');
		$('.yes_packages_added_to_cart_section').show();
		$('.no_packages_added_to_cart_section').hide();
		$('.added_to_cart_notification').show();
		$(add_to_cart_btn_med).hide();
		$(med_pack_col).show();
		// $('.select_quantity_2_package .add_more_less_packages_col .num_of_packages_indicator').data('package-count', $(review_50).val());
		$('.select_quantity_2_package .add_more_less_packages_col .num_of_packages_indicator').html($(review_50).val() + ' Added');
	}
	if( review_100.val() > 0 ){
		
		$(review_100_row).css('visibility', 'visible');
		$('.yes_packages_added_to_cart_section').show();
		$('.no_packages_added_to_cart_section').hide();
		$('.added_to_cart_notification').show();
		$(add_to_cart_btn_lg).hide();
		$(lg_pack_col).show();
		// $('.select_quantity_3_package .add_more_less_packages_col  .num_of_packages_indicator').data('package-count', $(review_100).val());
		$('.select_quantity_3_package .add_more_less_packages_col  .num_of_packages_indicator').html($(review_100).val() + ' Added');
	}

	$('.added_to_cart_notification').html(cart_total);
	$('.package_cost').html(formattedPrice);
	$('.total_package_cost').html(formattedPriceWithShipping);
	/********** on load, check if cart has packages in it - end *********/


	// $('#upload_unboxing_media_form').on('valid.fndtn.abide', function() {
	//   // Handle the submission of the form
	//   $.ajax({
	// 		type: 'POST',
	// 		url: 'carepackage/uploadunboxing',
	// 		success: function(data){
	// 			console.log('This is data: ' + data);
	// 			$('#upload_unboxing_media_form').foundation('reveal', 'close');
	// 		}
	// 	});
	// });

	
	/********* must check terms of service in order to checkout - start *******/
	$('.checkout_with_paypal_btn').click(function(e){

		if( $('#ccp_termsOfService').is(':checked') ){
			$('.termsOfServiceError').hide();
			$(this).removeClass('checkout_disabled');
		}else{
			e.preventDefault();
			$('.termsOfServiceError').show();
			$(this).addClass('checkout_disabled');
		}

	});

	$('#ccp_termsOfService').change(function(){

		if( $('#ccp_termsOfService').is(':checked') ){
			$('.termsOfServiceError').hide();
			$('.checkout_with_paypal_btn').removeClass('checkout_disabled');
		}else{
			$('.termsOfServiceError').show();
			$('.checkout_with_paypal_btn').addClass('checkout_disabled');
		}

	});
	/********* must check terms of service in order to checkout - end *******/



	/********* if user has already filled out form before signing in, populate form with previous inputs - start *******/	

	
	$('.alreadyHaveAccount_link , .needAccount_link').click(function(){

		//store all shipping input values in an array and loop through each one and check which fields have been filled
		// var shipping_values = [];
		// var hidden_fields = [];

		//var par =  ;
		//console.log(par); $('.hidden_ship_info',$(this).parent('div:first'))
		

		 if ($(this).hasClass('alreadyHaveAccount_link')){
	        var hidden_fields = $('input[type="hidden"].sign_in_hidden_ship_info').map(function(){
				return $(this);
			}).toArray();
	    }

	    else {
	        var hidden_fields = $('input[type="hidden"].signup_hidden_ship_info').map(function(){
				return $(this);
			}).toArray();
	    }

		var shipping_fields = $('input.ccp_prepopulate, textarea.ccp_prepopulate').map(function(){
			return $(this);
		}).toArray();

		var shipping_values = $('input.ccp_prepopulate, textarea.ccp_prepopulate').map(function(){
			return $(this).val();
		}).toArray();

		
		//make sure there are same number of shipping fields as hidden fields
		if( shipping_fields.length == hidden_fields.length ){

			for (var i = 0; i < hidden_fields.length; i++) {
				// console.log(i + ' ' +shipping_values[i]);
				$(hidden_fields[i]).val(shipping_values[i]);
				//console.log( hidden_fields[i] );
				//console.log( hidden_fields[i].val() );

				// if( $(shipping_fields[i]).data('previous-value') != '' ){
				// 	console.log('field empty but data attr not emtpy so apply data to hidden field');
				// 	console.log( $(hidden_fields[i]).val() );
				// 	$(hidden_fields[i]).val( $(shipping_fields[i]).data('previous-value') );
				// }else if( $(shipping_fields[i]).val() == '' && $(shipping_fields[i]).data('previous-value') == '' ){
				// 	console.log('data attr is empty so make hidden field blank');
				// 	$(hidden_fields[i]).val('');
				// }
			};
		}
		

	});
	// if( $('.personal_msg_to').val() == '' && $('.personal_msg_to').data('previous-value') != '' ){
		
	// 	console.log('not empty');
	// 	console.log( $('.personal_msg_to').data('previous-value') );
	// 	$('.personal_msg_to').val( $('.personal_msg_to').data('previous-value') );
	// }else{
	// 	console.log('empty');
	// 	$('.personal_msg_to').val('');
	// }
	/********* if user has already filled out form before signing in, populate form with previous inputs - end *******/	


	

});//end of document ready

//data-abide patterns for billing and shipping form validation
$(document).foundation({
	abide: {
		patterns: {
			name: /^([a-zA-Z\'\- ])+$/,
			phone: /^1?\-?\(?([0-9]){3}\)?([\.\-])?([0-9]){3}([\.\-])?([0-9]){4}$/,
			address: /^([0-9a-zA-Z\.,#\- ])+$/,
			state: /^([a-zA-Z]){2}$/,
			city: /^[a-zA-Z\.\- ]+$/,
			zip: /^\d{5}(-\d{4})?$/,
			number: /^[0-9]{1,4}$/,
			passwordpattern: /^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/
		}
	}
});//end of data-abide

$(document).ready(function () {

	// var temp = '';
	// temp = {{$data['test']}};
    //if ({{ Input::old('autoOpenModal', 'false') }}) {
       // displayCreateAcctForm();
    //}
});

$(document).on('click', '.ccp_content_banner_footer .email-button', function() {

	var email = $('.ccp_content_banner_footer .email-textbox');
	var _thisEmail = email.val();

	//when email input field has focus, remove disabled attr in case it was disabled from previous invalid user input
	$(email).focus(function(){
		$('.email-button').removeAttr('disabled');
	});

	//check if email input is valid
	if( email.is('[data-invalid]') || _thisEmail == '' ){
		$('.email-button').attr('disabled', 'disabled');
	}else{
		$.ajax({
			url: '/ajax/carepackagenotifyme',
			type: 'POST',
			data: {email: _thisEmail},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function() {
			$('.ccp_content_banner_footer .submit-form').hide();
			$('.ccp_content_banner_footer .thank-you').show();
		});
	}

	
	
});
function displayCreateAcctForm(){

	$('.signInForm_ccpModal').hide();
	$('.createAcctForm_ccpModal').show();
	$('#signInToOrderCCP').foundation('reveal', 'open');
}


function setCCPSignInModalAction( section ){

	$('.createAcctForm_ccpModal').hide();
	$('.signInForm_ccpModal').show();
	$('.ccp_signIn_modal_form').attr('action', '/login/carepackage/'+section);
	$('#signInToOrderCCP').foundation('reveal', 'open');
}


function setOrderDetailsModal( obj, counter){
	
	var obj = $(obj);
	var htmlData ='';
	var goods;
	var cnt =0;

	$.each(obj,function(index, el) {
		if (counter == cnt) {
			goods = el.goods;
			obj = el;
		}
		cnt++;
	});
	


	htmlData +=		'<div class="row order_details_rowHeader">';
	htmlData +=			'<div class="column small-4">Name</div>';
	htmlData +=			'<div class="column small-4">Qty</div>';
	htmlData +=			'<div class="column small-4 text-left">Item Price</div>';	
	htmlData += 	'</div>';

	
	$.each(goods, function(index, el) {

		htmlData +=		'<div class="row order_details_rowData>';
		htmlData +=			'<div>';
		htmlData +=				'<div><strong class="order_name"></strong></div>';
		htmlData +=				'<div class="order_giftcard"></div>';
		htmlData +=			'</div>';
		htmlData +=			'<div class="column small-4"><strong class="product_name">'+ el.product_name+'</strong></div>';
		htmlData +=			'<div class="column small-4"><strong class="product_quantity">'+ el.product_quantity+'</strong></div>';
		htmlData +=			'<div class="column small-4"><strong class="product_price">$'+ el.product_price+'</strong></div>';
		htmlData +=      '</div>';
	});

	htmlData +=     '<br/>';

	htmlData +=	'<!-- name to payment details for large up -->';
	htmlData +=			'<div class="row order_details_rowHeader">';
	htmlData +=				'<div class="column small-2">Order #</div>';
	htmlData +=				'<div class="column small-2">Order date</div>';
	htmlData +=				'<div class="column small-2">Status</div>';
	//htmlData +=				'<div class="column small-1">Tracking #</div>';
	//htmlData +=				'<div class="column small-1">Tax</div>';
	htmlData +=				'<div class="column small-2">Shipping</div>';
	htmlData +=				'<div class="column small-2">Total</div>';
	htmlData +=				'<div class="column small-2">Payment method</div>';
	htmlData +=			'</div>';
	
	htmlData +='<!-- name to payment method order details for large up -->';
	htmlData +=		'<div class="row order_details_rowData>';
	htmlData +=			'<div>';
	htmlData +=				'<div><strong class="order_name"></strong></div>';
	htmlData +=				'<div class="order_giftcard"></div>';
	htmlData +=			'</div>';
	htmlData +=			'<div class="column small-2"><strong class="order_num">'+ obj.product_transaction_id+'</strong></div>';
	htmlData +=			'<div class="column small-2"><strong class="order_date">'+ obj.purchased_date+'</strong></div>';
	htmlData +=			'<div class="column small-2"><strong class="order_status">'+obj.product_status+'</strong></div>';
	//htmlData +=			'<div class="column small-1"><strong class="order_trackingNum">1234</strong></div>';
	//htmlData +=			'<div class="column small-1"><strong class="order_tax">$4.00</strong></div>';
	htmlData +=			'<div class="column small-2"><strong>$'+obj.shipping_cost+'</strong></div>';
	htmlData +=			'<div class="column small-2"><strong class="order_total">$'+obj.total_amount+'</strong></div>';
	htmlData +=			'<div class="column small-2"><strong>Paypal</strong></div>';
	htmlData +=		'</div>';
	htmlData +=     '<br/>';
	/*
	htmlData +=	'<!-- name to payment details for medium only -->';
	htmlData +=			'<div class="show-for-medium-only">';
	htmlData +=				'<div class="row order_details_rowHeader">';
	htmlData +=					'<div class="column small-3">Name</div>';
	htmlData +=					'<div class="column small-3">Order number</div>';
	htmlData +=					'<div class="column small-3">Order date</div>';
	htmlData +=					'<div class="column small-3">Qty</div>';
	htmlData +=				'</div>';
	htmlData +=				'<div class="row order_details_rowData">';
	htmlData +=					'<div class="column small-3">';
	htmlData +=						'<div><strong class="order_name"></strong></div>';
	htmlData +=						'<div class="order_giftcard"></div>';
	htmlData +=					'</div>';
	htmlData +=					'<div class="column small-3"><strong class="order_num">'+ el.product_name+'</strong></div>';
	htmlData +=					'<div class="column small-3"><strong class="order_date">'+ el.product_name+'</strong></div>';
	htmlData +=					'<div class="column small-3"><strong class="order_qty">'+el.product_quantity+'</strong></div>';
	htmlData +=				'</div>';

	htmlData +=				'<div class="row order_details_rowHeader">';
	htmlData +=					'<div class="column small-3">Tax</div>';
	htmlData +=					'<div class="column small-3">Shipping</div>';
	htmlData +=					'<div class="column small-3">Total</div>';
	htmlData +=					'<div class="column small-3">Payment method</div>';
	htmlData +=				'</div>';
	htmlData +=				'<div class="row order_details_rowData">';
	htmlData +=					'<div class="column small-3"><strong class="order_tax"></strong></div>';
	htmlData +=					'<div class="column small-3"><strong>'+el.shipping_cost+'</strong></div>';
	htmlData +=					'<div class="column small-3"><strong class="order_total">'+el.total_amount+'</strong></div>';
	htmlData +=					'<div class="column small-3"><strong>Paypal</strong></div>';
	htmlData +=				'</div>';
	htmlData +=			'</div>';
	*/
	/*
	htmlData +=		'<div class="row order_details_rowHeader">';
	htmlData +=			'<div class="column small-3">Tax</div>';
	htmlData +=			'<div class="column small-3">Shipping</div>';
	htmlData +=			'<div class="column small-3">Total</div>';
	htmlData +=			'<div class="column small-3">Payment method</div>';
	htmlData +=		'</div>';
	htmlData +=		'<div class="row order_details_rowData">';
	htmlData +=			'<div class="column small-3"><strong class="order_tax"></strong></div>';
	htmlData +=			'<div class="column small-3"><strong>$'+obj.shipping_cost+'</strong></div>';
	htmlData +=			'<div class="column small-3"><strong class="order_total">'+obj.total_amount+'</strong></div>';
	htmlData +=			'<div class="column small-3"><strong>Paypal</strong></div>';
	htmlData +=		'</div>';
	htmlData += '</div>';
	
	
	htmlData +=		'<!-- status and tracking row on all window sizes -->';
	htmlData +=		'<div class="row order_details_rowHeader">';
	htmlData +=			'<div class="column small-3">Status</div>';
	htmlData +=			'<div class="column small-9">Tracking #</div>';
	htmlData +=		'</div>';
	htmlData +=		'<div class="row order_details_rowData">';
	htmlData +=			'<div class="column small-3 order_status"><strong>'+obj.product_status+'</strong></div>';
	htmlData +=			'<div class="column small-9 order_trackingNum"><strong>1234</strong></div>';
	htmlData +=		'</div>';
	*/

	htmlData +=		'<!-- billing/shipping and message for medium only -->';
	htmlData +=		'<div>';
	htmlData +=			'<div class="row order_details_rowHeader">';
	htmlData +=				'<div class="column small-4">Billing information</div>';
	htmlData +=				'<div class="column small-4">Shipping information</div>';
	htmlData +=				'<div class="column small-4">Personalized Message</div>';
	htmlData +=			'</div>';
	htmlData +=			'<div class="row order_details_rowData">';
	htmlData +=				'<div class="column small-4">';
	htmlData +=					'<div><strong class="order_billName">'+obj.billing_firstname+ ' '+ obj.billing_lastname +'</strong></div>';
	htmlData +=					'<div><strong class="order_billAddress">'+obj.billing_address+ ' '+ obj.billing_apt + ' '+ obj.billing_city + ' '+ obj.billing_state + ' '+ obj.billing_zip +'</strong></div>';
	htmlData +=					'<div><strong class="order_billEmail">'+obj.billing_email+'</strong></div>';
	htmlData +=				'</div>';
	htmlData +=				'<div class="column small-4">';
	htmlData +=					'<div><strong class="order_shipName">'+obj.shipping_firstname+ ' '+ obj.shipping_lastname +'</strong></div>';
	htmlData +=					'<div><strong class="order_shipAddress">'+obj.shipping_address+ ' '+ obj.shipping_apt + ' '+ obj.shipping_city + ' '+ obj.shipping_state + ' '+ obj.shipping_zip +'</strong></div>';
	htmlData +=					'<div><strong class="order_shipEmail">'+obj.shipping_email+'</strong></div>';
	htmlData +=				'</div>';
	htmlData +=				'<div class="column small-4">';
	htmlData +=					'<div><strong>To: <span class="order_personalTo">'+obj.personal_msg_to+'</span></strong></div>';
	htmlData +=					'<div><strong>From: <span class="order_personalFrom">'+obj.personal_msg_from+'</span></strong></div>';
	htmlData +=					'<div><strong><div class="order_personalBody">'+obj.personal_msg_body+'</div></strong></div>';
	htmlData +=				'</div>';
	htmlData +=			'</div>';
	/*
	htmlData +=			'<div class="row order_details_rowHeader">';
	htmlData +=				'<div class="column small-12">Personalized Message</div>';
	htmlData +=			'</div>';
	htmlData +=			'<div class="row order_details_rowData">'
	htmlData +=				'<div class="column small-12">';
	htmlData +=					'<div><strong>To: <span class="order_personalTo">'+obj.personal_msg_to+'</span></strong></div>';
	htmlData +=					'<div><strong>From: <span class="order_personalFrom">'+obj.personal_msg_from+'</span></strong></div>';
	htmlData +=					'<div><strong><div class="order_personalBody">'+obj.personal_msg_body+'</div></strong></div>';
	htmlData +=				'</div>';
	htmlData +=			'</div>';
	htmlData +=		'</div>';
	*/
	/*
	htmlData +=		'<!-- billing/shipping and message for large up -->';
	htmlData +=		'<div class="show-for-large-up">';
	htmlData +=			'<div class="row order_details_rowHeader">';
	htmlData +=				'<div class="column small-4">Billing information</div>';
	htmlData +=				'<div class="column small-4">Shipping information</div>';
	htmlData +=				'<div class="column small-4">Personalized Message</div>';
	htmlData +=			'</div>';

	htmlData +=			'<div class="row order_details_rowData">';
	htmlData +=				'<div class="column small-4">';
	htmlData +=					'<div><strong class="order_billName"></strong></div>';
	htmlData +=					'<div><strong><div class="order_billAddress"></div></strong></div>';
	htmlData +=					'<div><strong class="order_billEmail"></strong></div>';
	htmlData +=				'</div>';
	htmlData +=				'<div class="column small-4">';
	htmlData +=					'<div><strong class="order_shipName"></strong></div>';
	htmlData +=					'<div><strong><div class="order_shipAddress"></div></strong></div>';
	htmlData +=					'<div><strong class="order_shipEmail"></strong></div>';
	htmlData +=				'</div>';
	htmlData +=				'<div class="column small-4">';
	htmlData +=					'<div><strong>To: <span class="order_personalTo"></span></strong></div>';
	htmlData +=					'<div><strong>From: <span class="order_personalFrom"></span></strong></div>';
	htmlData +=					'<div><strong><div class="order_personalBody"></div></strong></div>';
	htmlData +=				'</div>';
	htmlData +=			'</div>';
	htmlData +=		'</div>';

	htmlData +=		'<!-- billing/shipping and message for small only -->';
	htmlData +=		'<div class="hide-for-medium-up">';
	htmlData +=			'<div class="row order_details_rowHeader">';
	htmlData +=				'<div class="column small-12">Billing information</div>';
	htmlData +=			'</div>';
	htmlData +=			'<div class="row order_details_rowData">';
	htmlData +=				'<div class="column small-12">';
	htmlData +=					'<div><strong class="order_billName"></strong></div>';
	htmlData +=					'<div><strong><div class="order_billAddress"></div></strong></div>';
	htmlData +=					'<div><strong class="order_billEmail"></strong></div>';
	htmlData +=				'</div>';
	htmlData +=			'</div>';

	htmlData +=			'<div class="row order_details_rowHeader">';
	htmlData +=				'<div class="column small-12">Shipping information</div>';
	htmlData +=			'</div>';
	htmlData +=			'<div class="row order_details_rowData">';
	htmlData +=				'<div class="column small-12">';
	htmlData +=					'<div><strong class="order_shipName"></strong></div>';
	htmlData +=					'<div><strong><div class="order_shipAddress"></div></strong></div>';
	htmlData +=					'<div><strong class="order_shipEmail"></strong></div>';
	htmlData +=				'</div>';
	htmlData +=			'</div>';

	htmlData +=			'<div class="row order_details_rowHeader">';
	htmlData +=				'<div class="column small-12">Personalized Message</div>';
	htmlData +=			'</div>';
	htmlData +=			'<div class="row order_details_rowData">';
	htmlData +=				'<div class="column small-12">';
	htmlData +=					'<div><strong>To: <span class="order_personalTo"></span></strong></div>';
	htmlData +=					'<div><strong>From: <span class="order_personalFrom"></span></strong></div>';
	htmlData +=					'<div><strong><div class="order_personalBody"></div></strong></div>';
	htmlData +=				'</div>';
	htmlData +=			'</div>';
	htmlData +=		'</div>';
	*/
	
	$('#order_details_modal .inject-content').html(htmlData);

}


/* open quick view modal with data */
function setQuickViewModal( name, price, image_array ){

	$('.quickview_pack_name .pack_name').html(name);
	$('.quickview_pack_name .pack_price').html(price);
}

/* closes quick view modal */
function closeQuickViewModal( elem ){

	$(elem).foundation('reveal', 'close');
}

/* toggles the package item description when image is clicked */
function togglePackageItemDescription( elem ){

	//find and store the item description container
	var item_description_container = $(elem).parent().find('.package_item_details');

	//show/hide the item description container
	$(item_description_container).slideToggle(500);
}

function editPackage_quickview( elem, operation ){

	var this_packageIndicator = $(elem).parents('div.add_more_less_packages_col').find('div.num_of_packages_indicator');
	var sm_pack_indicator = $('.select_quantity_1_package').find('div.num_of_packages_indicator');
	var med_pack_indicator = $('.select_quantity_2_package .num_of_packages_indicator');
	var lg_pack_indicator = $('.select_quantity_3_package .num_of_packages_indicator');

	var which_pack_modal = $(elem).parents('div.quickview_modal_rightside').find('div.pack_name');
	var cart_noti = $('.added_to_cart_notification');

	var sm_pack_count = $('.select_quantity_1_package .num_of_packages_indicator').data('package-count');
	var med_pack_count = $('.select_quantity_2_package .num_of_packages_indicator').data('package-count');
	var lg_pack_count = $('.select_quantity_3_package .num_of_packages_indicator').data('package-count');

	var input_twentyfive = $('.product_id_1');
	var input_fifty = $('.product_id_2');
	var input_onehundred = $('.product_id_3');

	var sm_pack_price = $('.select_quantity_1_package').data('small-price');
	var med_pack_price = $('.select_quantity_2_package').data('medium-price');
	var lg_pack_price = $('.select_quantity_3_package').data('large-price');

	var total_count = 0;
	var total_cost = 0;
	var total_plus_shipping;

	var formattedPackageCost;
	var formattedTotal;

	var added_text = ' Added';

	switch( which_pack_modal.text() ){

		case 'Small Pack':
			$(this_packageIndicator).html(function(){

				//check which button was pressed, plus or minus
				//then check if the package count is at the max or min, if so don't allow them to add more
				if( operation == 'add'){
					if( sm_pack_count < 3 ){
						sm_pack_count += 1;
					}
				}else if( operation == 'subtract'){
					if( sm_pack_count > 0 ){
						sm_pack_count -= 1;
					}
				}

				//update the data-count attribute to reflect the current package count
				$(this).data('package-count', sm_pack_count);
				$(sm_pack_indicator).data('package-count', sm_pack_count);
				$(sm_pack_indicator).html(sm_pack_count + added_text);
				$(input_twentyfive).val(sm_pack_count);

				//return the package count plus the 'Added' text to be injected into the page
				return sm_pack_count + added_text;
			});
			break;

		case 'Medium Pack':
			
			$(this_packageIndicator).html(function(){

				//check which button was pressed, plus or minus
				//then check if the package count is at the max or min, if so don't allow them to add more
				if( operation == 'add'){
					if( med_pack_count < 3 ){
						med_pack_count += 1;
					}
				}else if( operation == 'subtract'){
					if( med_pack_count > 0 ){
						med_pack_count -= 1;
					}
				}

				//update the data-count attribute to reflect the current package count
				$(this).data('package-count', med_pack_count);
				$(med_pack_indicator).data('package-count', med_pack_count);
				$(med_pack_indicator).html(med_pack_count + added_text);
				$(input_fifty).val(med_pack_count);

				//return the package count plus the 'Added' text to be injected into the page
				return med_pack_count + added_text;
			});
			break;

		case 'Large Pack':
			
			$(this_packageIndicator).html(function(){

				//check which button was pressed, plus or minus
				//then check if the package count is at the max or min, if so don't allow them to add more
				if( operation == 'add'){
					if( lg_pack_count < 3 ){
						lg_pack_count += 1;
					}
				}else if( operation == 'subtract'){
					if( lg_pack_count > 0 ){
						lg_pack_count -= 1;
					}
				}

				//update the data-count attribute to reflect the current package count
				$(this).data('package-count', lg_pack_count);
				$(lg_pack_indicator).data('package-count', lg_pack_count);
				$(lg_pack_indicator).html(lg_pack_count + added_text);
				$(input_onehundred).val(lg_pack_count);

				//return the package count plus the 'Added' text to be injected into the page
				return lg_pack_count + added_text;
			});
			break;
	}

	total_count = sm_pack_count + med_pack_count + lg_pack_count;
	total_cost = (sm_pack_count * sm_pack_price) + (med_pack_count * med_pack_price) + (lg_pack_count * lg_pack_price);

	total_plus_shipping = total_cost + 10;

	formattedPackageCost = formatToDollarAmt(total_cost);
	formattedTotal = formatToDollarAmt(total_plus_shipping);


	$(cart_noti).html(total_count);

	$('.package_cost').html('$' + total_cost + '.00');
	$('.total_package_cost').html('$' + total_plus_shipping + '.00');
}

/* adds/removes packages to cart */
function addMoreOrLessPackagesToCart( elem, operation, pack, cost ){

	var currentPackage_packageIndicator = $(elem + ' div.num_of_packages_indicator');

	var packages_not_added_to_cart_section = $('.no_packages_added_to_cart_section');
	var packages_add_to_cart_section = $('.yes_packages_added_to_cart_section');

	var packageCount = $(currentPackage_packageIndicator).data('package-count');
	var added_to_cart_icon = $('.added_to_cart_notification');

	var review_order_package = '.' + pack + '_row';

	var package_array = [];
	var small_pack;
	var medium_pack;
	var large_pack;

	var pack_increment_ticker_input = '.'+pack;
	var added_text = ' Added';
	

	var packageCostAmount = 0;
	var shipping_cost = 10;
	var total_cost = 0;
	

	var formattedPackageCost;
	var formattedTotal;

	var totalPackCount = 0;

	var sm_pack_price = $('.select_quantity_1_package').data('small-price');
	var med_pack_price = $('.select_quantity_2_package').data('medium-price');
	var lg_pack_price = $('.select_quantity_3_package').data('large-price');

	var selectedPackage;

	//injecting html; using callback function to determine package count
	$(currentPackage_packageIndicator).html(function(){

		//check which button was pressed, plus or minus
		//then check if the package count is at the max or min, if so don't allow them to add more
		if( operation == 'add'){
			if( packageCount < 3 ){
				packageCount += 1;
			}
		}else if( operation == 'subtract'){
			if( packageCount > 0 ){
				packageCount -= 1;
			}
		}
		
		//update the data-count attribute to reflect the current package count
		$(this).data('package-count', packageCount);

		//return the package count plus the 'Added' text to be injected into the page
		return packageCount + added_text;
	});

	
	//when package is added, update the package increment/decrement ticker on the cart page
	$(pack_increment_ticker_input).val(packageCount);

	
	//instantiating new package objects
	package_array[0] = new package( '.select_quantity_1_package', '.product_id_1', $('.product_id_1').val(), sm_pack_price, pack );
	package_array[1] = new package( '.select_quantity_2_package', '.product_id_2', $('.product_id_2').val(), med_pack_price, pack );
	package_array[2] = new package( '.select_quantity_3_package', '.product_id_3', $('.product_id_3').val(), lg_pack_price, pack );

	//storing the complete total number of packages added to cart. max = 3
	totalPackCount = parseInt(package_array[0].count) + 
										parseInt(package_array[1].count) + 
										parseInt(package_array[2].count);

	/********************** calculating package cost and total cost *********************/

	//calculate package cost and format it
	packageCostAmount = (package_array[0].cost * package_array[0].count) + 
						(package_array[1].cost * package_array[1].count) + 
						(package_array[2].cost * package_array[2].count);

	formattedPackageCost = formatToDollarAmt(packageCostAmount);

	//calculate total cost and format it for presentation
	total_cost += (packageCostAmount + shipping_cost);
	formattedTotal = formatToDollarAmt(total_cost);

	//inject new formatted cost and totals into html
	$('.package_cost').html(formattedPackageCost);
	$('.total_package_cost').html(formattedTotal);
	/*******************************************/


	//update cart notification icon, show/hide checkout page based on if item has been added to cart
	if( totalPackCount > 0 ){

		//if item is added to cart, display cart notification
		added_to_cart_icon.html(totalPackCount);
		added_to_cart_icon.show();

		//if items added to cart, show checkout form and hide default message
		packages_not_added_to_cart_section.hide();
		packages_add_to_cart_section.show();

	}else{

		//if no items added to cart, don't show cart notification
		added_to_cart_icon.hide();

		//if no items added to cart, hide checkout form and show default message
		packages_not_added_to_cart_section.show();
		packages_add_to_cart_section.hide();
	}

	//only display the review order package amount form fields if those packages have been added to cart
	if( $('.'+pack).val() > 0 ){

		//make visible only the package that was added to cart
		$(review_order_package).css('visibility', 'visible');
	}else{

		//if no items added to cart, hide package review form field
		$(review_order_package).css('visibility', 'hidden');
	}

	switch(pack){
		case 'product_id_1':
			selectedPackage = package_array[0];
			break;
		case 'product_id_2':
			selectedPackage = package_array[1];
			break;
		case 'product_id_3':
			selectedPackage = package_array[2];
			break;
	}

	$.ajax({
		type: 'POST',
		url: 'carepackage/session',
		dataType: 'json',
		data: JSON.stringify({pack_1: package_array[0], pack_2: package_array[1], pack_3: package_array[2]}),
		contentType: "application/json",
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data){
		}
	});
}

/* function to format cost and total into dollar amount */
function formatToDollarAmt(num) {
    var p = num.toFixed(2).split(".");
    return "$" + p[0].split("").reverse().reduce(function(acc, num, i, orig) {
        return  num + (i && !(i % 3) ? "," : "") + acc;
    }, "") + "." + p[1];
}

/* this is the 'package' constructor to instantiate new package objects, used in addMoreOrLessPackagesToCart function */
function package( select_quantity_row, quantity_modifier_input, count, cost, name  ) {
    this.select_quantity_row = select_quantity_row;
    this.quantity_modifier_input = quantity_modifier_input;
    this.count = count;
    this.cost = cost;
    this.pack_name = name;
}

/* function to show/hide the select quantity row - hidden by default until user clicks 'select' button */
function packSelected( elem ){

	//find a store the add more or less packages row
	var plusMinusRow = $(elem).parents('div.select_plus_minus_package_row').find('div.add_more_less_packages_col');
	var added_to_cart_icon = $('.added_to_cart_notification');
	var quickview_indicator = $('.dynamic_quickview_packCount_indicator');

	//hide the 'select' package column
	$(elem).parent().hide();

	//uncheck all gift card checkboxs at start
	$('div.gc_selection input[type=checkbox]').prop('checked', false);

	//reset the quickview modal package count indicator to 1 for when every pack is initially selected
	$(quickview_indicator).html('1 Added');

	//show the add more or less packages column and the added-to-cart icon
	$(plusMinusRow).show();
	added_to_cart_icon.show();

	//show the 'choose a gift card error when modal open and no checkboxes are checked'
	$('.choose_gc_error').show();
	$('.checkout_btn_link').addClass('checkout_disabled');
    $('.continueShopping_btn_link').addClass('checkout_disabled');
}

/* function to remove 'required' attr to shipping info form field if it's same as billing info */
function toggleRequiredAttr( shipping_checkbox, shipping_section ){

	var shipping_form = shipping_section.find('input');

	if( $(shipping_checkbox).is(':checked') ){
		shipping_form.each(function(){
			//remove required attr and reset value in case they fill out the form then realize billing is same as shipping
			$(this).removeAttr('required').val('');
		});
	}else{
		shipping_form.each(function(){
			$(this).attr('required', true);
		});
	}
}

/* toggle function to toggle between payment methods, but both cannot be selected at same time */
function chosenPaymentMethod( clickedElement, nth_div ){

	//grabbing the payment method button that's not the button that was clicked
	var otherButton = '.payment_method_and_submit_row .row:nth-child(2) div:not(:nth-child('+nth_div+')) div';

	//toggle the payment button click
	$(clickedElement).toggleClass('payment_method_type_button_clicked');

	//logic that doesn't allow both payment method buttons to be enabled at the same time
	if( $(clickedElement).hasClass('payment_method_type_button_clicked') ){
		$(otherButton).removeClass('payment_method_type_button_clicked');
	}
}

/* slides down and shows the package details */
function showPackageDetails( elem ){

	var elem_parent = $(elem).parent();

	var packageListContainer = elem_parent.find('.package_details_list_container');
	var plusMinusPackageCol = elem_parent.find('.add_more_less_packages_col');
	var closeBtn = elem_parent.find('.close_package_list_btn');
	var select_package_btn = elem_parent.find('.select_package_item_btn');

	//hide the 'see whats inside button' and show the package details list
	$(elem).hide();

	//show and slide down the package list container
	$(packageListContainer).slideDown(500);

	//show the close btn, change background color of plus/minus row, and add class to select package btn that changes width size
	$(closeBtn).show();
	$(plusMinusPackageCol).css('background-color','#cacaca');
	$(select_package_btn).addClass('select_package_item_btn_on_list_open');

	//scroll the window down when package list container is opened
	$('html, body').animate({scrollTop: $(document).height()}, 'slow');
}

/* slides up and hides the package details */
function hidePackageDetails( elem ){

	var elem_parent = $(elem).parents('div.packages');

	var packageListContainer = elem_parent.find('div.package_details_list_container');
	var seeWhatsInside_btn = elem_parent.find('div.package_details_dropdown_btn');
	var plusMinusPackage_column = elem_parent.find('.add_more_less_packages_col');
	var select_package_btn = elem_parent.find('.select_package_item_btn');

	//slide the package detail list up to hide
	$(packageListContainer).slideUp(500);
	
	//hide the close btn, change background-color of plus/minus row, and remove class from select package btn that changes width
	$(elem).hide();
	$(plusMinusPackage_column).css('background-color','#eee');
	$(select_package_btn).removeClass('select_package_item_btn_on_list_open');

	//re-show the "See What's Inside" button
	$(seeWhatsInside_btn).show();
}

/* this function animates the 'see whats inside' button to hover up and down */
(function animateUpDown(){

	//div containing the text to hover
	var upDownText = $('.bounceUpDown');
	//parent of the text to hover
	var upDownTextParent = $(upDownText).parent('package_details_dropdown_btn');

	//options passed as a param to $.extend
	var options = {
    	duration: 1000,
    	easing: 'linear'
    };

    //perform animation and once complete, call this function again
    $(upDownText)
        .animate( {top: 0, left: 0, }, options)
        .animate( {top: 9, }, $.extend(true, {}, options, {
            complete: function() {
               animateUpDown();
            }
        })//end of extend function 
      )//end of 4th animate function;

})();

/************** DO NOT DELETE - THIS IS AN ANIMATION TO ANIMATE TEXT FROM LEFT TO RIGHT - MAY USE LATER ****************/
/*(function animateSideToSide() {

	var bounceText = $('.bounce_text');
	var bounceTextParent = $('.package_details_dropdown_btn .column');

    var options = {
    	duration: 2000,
    	easing: 'linear'
    };
 
    $(bounceText)
        .animate( {left: 100, top: 0, }, options)
        .animate( {left: 80, }, $.extend(true, {}, options, {
            complete: function() {
               animateSideToSide();
            }
        })//end of extend function 
      )//end of 4th animate function;
})();*/
/************** DO NOT DELETE - THIS IS AN ANIMATION TO ANIMATE TEXT FROM LEFT TO RIGHT - MAY USE LATER ****************/