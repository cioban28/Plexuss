//IMPORTANT NOTES:
// - scholarships are considered pending until the oneApp is complete
// - the scholarships page displays all scholarships
// - the OneApp displays selected scholarships and scholarships suggested by Plexuss
// - the portal is a place to manage all your scholarships: finish, submitted, accepted, not accepted
// -- the "Next" button will take users to OneApp if logged on,
// -- open signup modal if not a user or logged off
// -- if we can tell that a user has completed the OneApp, take the user to portal and automatically submit the scholarships
    //will have to have logic in "OneApp submit" to submit the scholarships listed,
    //and sense if they have already been submitted!  and not to duplicate


var PlexScholarships = {
	scholarshipsList: [],  /* list of scholarship objects */
	// chosenList: [], 		/*list of scholarships user is interested in */
	chosenCounter: 0,
	params: {},
	symbols: {
	    'USD': '$', // US Dollar
	    'AUD': '$',
	    'CAD': '$',
	    'HKD': '$',
	    'MXN': '$',
	    'NZD': '$',
	    'SGD': '$',

	    'BGN': 'лв',
	    'BRL': 'R$',
	    'CNY': '¥',
	    'CZK': 'Kč',
	    'CRC': '₡', // Costa Rican Colón
	    'DKK': 'kr',
	    'EUR': '€', // Euro
	    'GBP': '£', // British Pound Sterling
	    'HRK': 'kn',
	    'HUF': 'Ft',
	    'IDR': 'Rp',

	    'ILS': '₪', // Israeli New Sheqel
	    'INR': '₹', // Indian Rupee
	    'ISK': 'kr',
	    'JPY': '¥', // Japanese Yen
	    'KRW': '₩', // South Korean Won
	    'MYR': 'RM',
	    'NOK': 'kr',
	    'NGN': '₦', // Nigerian Naira
	    'PHP': '₱', // Philippine Peso
	    'PLN': 'zł', // Polish Zloty
	    'PYG': '₲', // Paraguayan Guarani
	    'RON': 'lei',
	    'RUB': '₽', //Russian Ruble
	    'SEK': 'kr',
	    'THB': '฿', // Thai Baht
	    'TRY': '₺', //Turkish lira
	    'UAH': '₴', // Ukrainian Hryvnia
	    'VND': '₫', // Vietnamese Dong
	    'ZAR': 'R' //South African Rand
	}

};


/*******************************************
*  scholarship object
*********************************************/
PlexScholarships.scholarship = function(context, id, name, provider, amount, due, added, usd, desc, req){

	this.context = context || null;
	this.id = id;
	this.name = name || 'Scholarship';
	this.provider = provider || 'Anonymous';
	this.amount = amount || 0;
	this.usdAmount = amount || 0;  //used in conversion, instead of making more api calls, we can always use usd get new conversion
	this.due = due || 'no deadline';
	this.added = added || false;
	this.usd = usd || 'USD';
	this.symbol = '$';
	this.req = req || 'none';

	this.desc = context.find('.sch-desc').text()  || 'none';

}
PlexScholarships.scholarship.prototype.getNodeHTML = function(node){
	this.html =  node[0].outerHTML;
}
PlexScholarships.scholarship.prototype.setHTML = function(){
	var reqToks = this.req.split(',');

	this.html =  "<div class='sch-table-result-wrapper fadeIn'  data-sid='"+this.id+"'  data-name='" + this.name +
					"' data-provider='"+this.provider+"' data-amount='"+ this.amount+
					"' added='"+this.added+"'>"+
							"<div class='sch-table-result clearfix'>"+
								"<div class='sch-col sch-col-name'>"+
									"<div class='sch-name'>" + this.name + "</div>"+
									"<div class='sch-provider'>Scholarship provided by "+ this.provider+"</div>" +

									"<div class='sch-view-details'>VIEW DETAILS</div> <div class='sch-details-arrow down'></div>"+
								"</div>"+
								"<div class='sch-col sch-col-amount'>"+
									"<div class='sch-amount'>" + this.symbol +  parseFloat(this.amount).toLocaleString() + "</div>"+
								"</div>"+
								"<div class='sch-col sch-col-due'>"+
									"<div class='sch-due'>" + this.due + "</div>"+
								"</div>"+
								"<div class='sch-col sch-col-add'>"+
									"<div class='sch-add-btn " + (this.added ? "finish": "no") + "'>" + (this.added ? "FINISH": "Add") + "</div>"+
								"</div>"+
								"<div class='sch-col sch-col-usd'>"+
									"<div class='sch-usd'>" + this.usd + "</div>"+
								"</div>"+
							"</div>"+

							"<div class='sch-result-details-cont'>"+
								"<div class='sch-desc-title sch-due-mobile'>Deadline</div>" +
								"<div class='sch-desc  sch-due-mobile'>"+ this.due +"</div>" +
								"<div class='sch-desc-title mt20'>Description</div>"+
								"<div class='sch-desc'>"+
									this.desc +
								"</div>" +

								"<div class='sch-desc-title mt20'>Elegibility Requierments</div> <ul>";

							    for(var i = 0; i < reqToks.length ; i++){
							    	this.html = this.html + "<li>" + reqToks[i] + "</li>";
							    }

								this.html = this.html +
								"</ul>" +
							"</div>" +
						"</div>";
}


/************************************
*  builds url with search params
***********************************/
PlexScholarships.buildURL = function(param){

	var url = window.location.host + window.location.pathname +  "?";

	for(var i in PlexScholarships.params){
		if(PlexScholarships.params.hasOwnProperty(i)){
			url = url + "&" + i + "=" + PlexScholarships.params[i];
		}
	}
	return url;
}


/**************************************
*  get exchange rates
*******************************************/
PlexScholarships.getExchangeRates = function(){

	$.get('https://api.fixer.io/latest?base=USD')
	.done(function(res){
		// console.log(res);

		PlexScholarships.rates = res.rates;
		PlexScholarships.baseRate = res.base;

		//populate dropdown with rates
		var rateNodes = '<div class="sch-rate"> USD </div>';

		for(var i in res.rates){
			rateNodes = rateNodes + '<div class="sch-rate">' + i + '</div>';
		}

		$('.sch-usd-dropdown').html(rateNodes);
	});
}

/*******************************************
* convert from one rate to another
*******************************************/
PlexScholarships.convertCurrency = function(to){

	var cont = $('.sch-table-content-box');
	var len = PlexScholarships.scholarshipsList.length;
	var node = '';

	cont.html('<div class="loader"></div>');

	for(var i =0; i < len ; i++){

		var item = PlexScholarships.scholarshipsList[i];

		//convert and set

		var tamt = item.usdAmount;
		var namt = ((PlexScholarships.rates[to] || 1) * tamt).toFixed(2);
		PlexScholarships.scholarshipsList[i].amount = namt;
		PlexScholarships.scholarshipsList[i].usd = to;
		PlexScholarships.scholarshipsList[i].symbol = PlexScholarships.symbols[to] || to;
		PlexScholarships.scholarshipsList[i].setHTML();


		//append to dom
		node = node + PlexScholarships.scholarshipsList[i].html;

	}
	$('.sch-usd-txt').text(to);
	cont.html('');
	cont.append(node);
}


/*****************************************************
*  view details toggle, fsrom the scholarship's page table
******************************************************/
PlexScholarships.viewDetails = function(that){
	var parent = that.closest('.sch-table-result-wrapper');
	var desc = parent.find('.sch-result-details-cont');
	var btn = parent.find('.sch-view-details');
	var arrow = parent.find('.sch-details-arrow');



	if(desc.is(':visible')){
		btn.text('VIEW DETAILS');
		arrow.addClass('down');
		arrow.removeClass('up');
	}else{

		btn.text('HIDE DETAILS');
		arrow.addClass('up');
		arrow.removeClass('down');
	}

	desc.toggle();
}



/*****************************************************
*  add/remove scholarships from list of chosen
******************************************************/
PlexScholarships.addRemoveScholarship = function(that){

	var parent = that.closest('.sch-table-result-wrapper');
	var id = parent.data('sid');

	var index = PlexScholarships.scholarshipsList.findIndex(function(el){
		return el.id == id;
	})


	var status = $("#_ScholarshipsPage").attr('data-oneapp');
	var user_id = $("#_ScholarshipsPage").attr('data-uid');
	var oldstate = that.closest('.sch-add-btn').attr('data-state');

	var state = 'finish';
	var msg = '.sch-finish-msg';

	// if(status === "scores"){
	// 	state = 'submitted';
	// 	msg = '.sch-submitted-msg';
	// }

	if(that.hasClass('yes')){
		state = null;
	}


	that.html('<div class="sm-loader"></div>');

	$.ajax({
		method: 'POST',
		url: '/ajax/queueScholarship',
		data: { user_id: user_id,
				scholarship: id,
			    status: state },
	    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
	})
	.done(function(res){
		// console.log(res);
		//note: if a user has completed their OneApp, and all additional requirements?,
		//the button will show submitted
		//otherwise the button shows finish
		if(that.hasClass('no')){
			//add to list and turn green
			that.html('Added');
			that.removeClass("no");
			that.addClass("yes");
			PlexScholarships.chosenCounter++;
			PlexScholarships.scholarshipsList[index].added = true;
			PlexScholarships.scholarshipsList[index].setHTML();
		}else{
			//remove from list and resest button
			that.html('+');
			that.removeClass("yes");
			that.addClass("no");
			PlexScholarships.chosenCounter--;
			PlexScholarships.scholarshipsList[index].added = false;
			PlexScholarships.scholarshipsList[index].setHTML();
		}
		$('.sch-num-box').text(PlexScholarships.chosenCounter);

		that.closest('.sch-add-btn').attr('data-state', state+"");

		//enable next buttons
		if(PlexScholarships.chosenCounter > 0){  // && state != "scores"
			$('.sch-next-btn').removeClass('disabled');

			//and if finish .. will have to implement that later
			$(msg).removeClass('hide');

		}else{
			$(msg).addClass('hide');
			$('.sch-next-btn').addClass('disabled');
		}

		//change messaging
		var selTxt = $('.sch-sel-txt');
		if(PlexScholarships.chosenCounter > 0){
			selTxt.text("Scholarships have been selected, click next to apply to them.")
		}else{
			selTxt.text("Select scholarships below and click next to apply.")
		}

	})




}






/*******************************************
*  sort scholarships by name
*  TO DO -- add sort filter to url query for if we need to fetch more from back
*        -- it'll be included there
*
*********************************************/
PlexScholarships.sortBy = function(direction, compareBy){
	//sort the scholarshipsList array by name
	//then clear results and add sorted results back to DOM
	var cont = $('.sch-table-content-box');

	cont.html('<div class="loader"></div>');
	PlexScholarships.scholarshipsList.sort(compareBy(direction));

	var len = PlexScholarships.scholarshipsList.length;
	var node = '';
	for(var i =0; i < len ; i++){
		node = node + PlexScholarships.scholarshipsList[i].html;
	}
	cont.html('');
	cont.append(node);


	PlexScholarships.params['sort_'+compareBy] = direction;
	//var url = PlexScholarships.buildURL();
	//window.location.href = url;
}

///////////////////////////////////////////
// sort callback -- by name
PlexScholarships.byName = function(direction){

	return function(a,b){
		if(direction === 'asc'){
			if(a.name < b.name)
				return 1;
			else if (a.name > b.name)
				return -1;
			else
				return 0;
		}else{
			if(a.name > b.name)
				return 1;
			else if (a.name < b.name)
				return -1;
			else
				return 0;
		}
	}
}

///////////////////////////////////////////
// sort callback -- by amount
PlexScholarships.byAmount = function(direction){

	return function(a,b){
		if(direction === 'asc'){
			return a.amount - b.amount;
		}else{
			return b.amount - a.amount;
		}
	}
}


///////////////////////////////////////////
// sort callback -- by deadline
PlexScholarships.byDue = function(direction){

	return function(a,b){
		if(direction === 'asc'){
			if(a.due < b.due)
				return 1;
			else if (a.due > b.due)
				return -1;
			else
				return 0;
		}else{
			if(a.due > b.due)
				return 1;
			else if (a.due < b.due)
				return -1;
			else
				return 0;
		}
	}
}

/********************************************
*  sort added column
*******************************************/
PlexScholarships.sortbyAdded = function(asc){
	var cont = $('.sch-table-content-box');
	var len = PlexScholarships.scholarshipsList.length;

	cont.html('<div class="loader"></div>');

	// sort
	var tmp1 = [];  //added
	var tmp2 = [];  //submitted   -- currently "none"  because other type not implemented yet
	var tmp3 = [];  //pending
	var tmp4 = [];  //none
	for(var i =0; i < len ; i++){
		if(PlexScholarships.scholarshipsList[i].added == true)
			tmp1.push(PlexScholarships.scholarshipsList[i]);
			tmp2.push(PlexScholarships.scholarshipsList[i]);
	}

	if(asc)
		PlexScholarships.scholarshipsList = tmp1.concat(tmp2);
	else
		PlexScholarships.scholarshipsList = tmp2.concat(tmp1);


	var node = '';
	for(var i =0; i < len ; i++){
		node = node + PlexScholarships.scholarshipsList[i].html;
	}
	cont.html('');
	cont.append(node);

}


/*************************************************
* init scholarshipsList
* will be done on ready and needs to be done if list supplied by ajax
*************************************************/
PlexScholarships.initScholarships = function(){
	//get all scholraships that populated on load
	$('.sch-table-result-wrapper').each(function(i){
			var me = $(this);
			var sch = new PlexScholarships.scholarship(me, me.data('sid'), me.data('name'), me.data('provider'), me.data('amount'), me.data('due'), me.data('added'));
			sch.getNodeHTML(me);
			PlexScholarships.scholarshipsList.push(sch);
	})
}



/****************************************************
*  things to do when page first loads -- or get ajaxed in
*
******************************************************/
PlexScholarships.pageInit = function(){
	//get and store url params
	var c_params = window.location.search;

	var toks = c_params.substring(1).split('&');
	var k = '';
	var v = '';
	var ttok = [];

	for(var i in toks){
		ttok = toks[i].split('=')
		k = ttok[0];
		v = ttok[1];
		PlexScholarships.params[k] = v;
	}


	//get scholarships from DOM
	PlexScholarships.initScholarships();

	//get exchange rates
	PlexScholarships.getExchangeRates();

	//get "finish" count from DO<
	PlexScholarships.chosenCounter = $("#_ScholarshipsPage").attr('data-fcount');

}


/////////////// document ready ///////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$(document).ready(function(){

	PlexScholarships.pageInit();


	/////////// view details -- scholarships table /////////////
	$(document).on('click', '.sch-view-details, .sch-details-arrow, .sch-name', function(){
		var that = $(this);
		PlexScholarships.viewDetails(that);
	});


	/////////////// display loader when fetching filtered contents //////////////
	$(document).on('click', '#schFilterCont a', function(){
		$('.sch-table-content-box').html('<div class="loader"></div>');
	});


	////////////// add/remove (choose or unselect) scholarship //////////////////
	$(document).on('click', '.sch-add-btn, .sch-status', function(){
		var that = $(this);
		PlexScholarships.addRemoveScholarship(that);
	});


	////////////// sort by handler ///////////////////////////////////
	$(document).on('click', '.sch-sort-up,  .sch-sort-down', function(){
		var compareFunc;
		var type = $(this).closest('.sch-sort-arrows').data('col');
		var asc = $(this).hasClass('sch-sort-up');

		switch(type){
			case 'name':
				compareFunc = PlexScholarships.byName;
				break;
			case 'amount':
				compareFunc = PlexScholarships.byAmount;
				break;
			case 'due':
				compareFunc = PlexScholarships.byDue;
				break;
			case 'added':
				PlexScholarships.sortbyAdded(asc);
				return;
			default:
				break;
		}


		if(asc){
			PlexScholarships.sortBy('asc', compareFunc);
		}else{
			PlexScholarships.sortBy('desc', compareFunc);
		}

	});


	/////////////// toggle currency type dropdown //////////
	$(document).on('click', '.sch-usd-dropdown-btn', function(){
		// $('.sch-usd-dropdown').toggle();
		var drp = $('.sch-usd-dropdown');
		if(drp.is(':visible')){
			drp.hide();
		}else{
			drp.show();
		}
	});


	///////////// signup modal -- next handler ////////////////
	$(document).on('click', '.sch-next-btn', function(){

		var status = $("#_ScholarshipsPage").attr('data-oneapp') || $('.myScholarships').attr('data-oneapp');


		//if we are past scores set OneApp page we load to scholarships
		if(status ==='scores' || status === 'colleges' || status === 'family' || status === 'awards' || status === 'clubs' || status === 'courses' ||
		   status === 'essay' || status === 'additional_info' || status === 'uploads'  || status === 'declaration' ||
		   status === 'sponsor'  || status === 'submit'  || status === 'review')
				status = 'scholarships';

		if($(this).hasClass('disabled')) return;


		//if from portal page or search page (has signed in value)
		if($('.sch-table-container').data('signin') == 1 || $('#portal').length > 0){
			window.location.href = "/college-application/" + status + "?isScholarship=true";
		}
		else{
			$('.signupModal-wrapper').show();
			$('.signupModal-back').show();
		}
	})

	//////////////// signup model -- add handler //////////
	$(document).on('click', '.sch-add-btn-login', function(){
		$('.signupModal-wrapper').show();
		$('.signupModal-back').show();
	})


	//////////// clear filter feedback ///////////////
	$('#clearSchFilter').click(function(e){
		$('.sch-table-content-box').html('<div class="loader"></div>');
	})


	//////////// convert amount //////////////////
	$(document).on('click', '.sch-rate', function(){
		PlexScholarships.convertCurrency($(this).text().trim());
	})

});
