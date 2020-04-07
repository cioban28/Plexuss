
var MajorsSearch = {
	tagList: [],
	removeTag: [],
	includeTag: [],
	query: {}
};


/***********************
*  rebuild url parameter query  from stored query
*************************************/
MajorsSearch.buildQuery = function(){

	url = window.location.pathname + "?";

	Object.keys(MajorsSearch.query).forEach(function(key,index) {
    	 url = url + "&" + key + "=" + MajorsSearch.query[key];
	});

	for(let i in MajorsSearch.removeTag){
		url = url + "&rmajor[]=" + MajorsSearch.removeTag[i];
	}

	return url;
}


/****************
* create actual tag for major tags 
******************************/
MajorsSearch.createTag = function(data){
	var li = document.createElement('div');
	var cBtn = document.createElement('div');
	cBtn.className = 'filter-majors-x';
	cBtn.innerHTML = '&times;';
	li.className = 'search-major-listing fadeIn';
    li.textContent = data.name;
    li.setAttribute('data-mid', data.id);
    li.appendChild(cBtn);
    return li;
}

/***********************
* create tags based on data given to and display majors tags
* calls createTag to create tag
****************************/
MajorsSearch.displayMajorsTags = function(data){
	var heading = document.getElementsByClassName('majors-tags-container')[0];
	heading.innerHTML = '';

	//versus appending children one at a time -- reduce reflow
	var fragment = document.createDocumentFragment();

	var resCont = document.createElement('div');
	resCont.className = 'search-major-listing-cont';

	fragment.appendChild(resCont);

	if(MajorsSearch.tagList.length > 0){
		var all = MajorsSearch.createTag({id: -1, name: 'All'});
		resCont.appendChild(all);
	}

	//want to display a max of 5
	var len = data.length;
	MajorsSearch.majorslength = len;
	var l = Math.min(len, 5);
	var i = 0;
	for(i=0; i < l; i++){
		var li = MajorsSearch.createTag(data[i]);
	    resCont.appendChild(li);
	}
	heading.appendChild(fragment);

	//if more than 5
	if(len > 5){
		var restfragment = document.createDocumentFragment();
					
		//display rest in here
		var moreresCont = document.createElement('div');
		moreresCont.className = 'more-majors-results';

		for(var k = i; k < len; k++){
			var li = MajorsSearch.createTag(data[k]);
		    moreresCont.appendChild(li);
		}


		//create and add toggle rest of majors button
		var cap = document.createElement('div');
		var arrow = document.createElement('div');
		arrow.className = 'toggle-majors-arrow';
		arrow.innerHTML = '&#8250;';
		cap.className = 'toggle-majors-results';
		cap.innerHTML = 'show remaining majors ('+ len +')';


		restfragment.appendChild(moreresCont);
		restfragment.appendChild(cap);
		restfragment.appendChild(arrow);
		heading.appendChild(restfragment);
	}

}

/***********************
*  get majors -- 
*  getting majors to store on client side memory 
*  based on category or user's majors if cat empty
************************************/
MajorsSearch.getMajorsLoad = function(){
	// console.log(MajorsSearch.query);
	if(MajorsSearch.query.type === 'college' && typeof MajorsSearch.query.department === 'undefined' && MajorsSearch.includeTag.length == 0)
		return;

	var url = '/ajax/getMajorsFromCat';

	if(typeof MajorsSearch.query.department !== 'undefined'  &&  MajorsSearch.query.department.trim() != ''){
		url = url + '?cat=' + MajorsSearch.query.department;
	}
	else if(typeof MajorsSearch.query.term !== 'undefined' && MajorsSearch.query.term.trim() != '' && MajorsSearch.query.type == 'majors'){
		url = url + '?cat=' + MajorsSearch.query.term;
	}
	else if(MajorsSearch.query.type == 'college'){
		url = url + '?cat=' + MajorsSearch.query.department;
	}
	else {
		url = url + '?cat=';
	}

	if(MajorsSearch.removeTag.length > 0){
		for(var i =0; i < MajorsSearch.removeTag.length; i++){
			url = url + '&rmajor[]=' + MajorsSearch.removeTag[i];
		}
	}

	if(typeof MajorsSearch.query.imajor !== 'undefined' && MajorsSearch.query.imajor != 'null'){
		// console.log(MajorsSearch.query.imajor);
		url = url + '&imajor=' + MajorsSearch.query.imajor;
	}

	// if(typeof MajorsSearch.query.term !== 'undefined'){

		$.ajax({
			url: url,
			type: 'GET',
		}).done(function(res){
			// console.log(res);
			MajorsSearch.tagList = res;
			// MajorsSearch.displayMajorsTags(res);
		})
	// }

}


/************************
* remove major filter
*****************************/
MajorsSearch.removeMajorFilter = function(e){
	var that = e.target;
	var tag  = $(that).closest('.search-major-listing');
	var m_id = $(tag).attr('data-mid');
	var list = MajorsSearch.tagList;
	var index = null;


	if(m_id == -1){
		MajorsSearch.tagList = [];
		MajorsSearch.query.imajor = '';
		MajorsSearch.removeTag = [];
		MajorsSearch.displayMajorsTags({});
		MajorsSearch.query.department = '';
		MajorsSearch.query.myMajors = false;
		MajorsSearch.query.degree_type = -1;

		if(MajorsSearch.query.type == 'majors'){
			MajorsSearch.query.term = '';
		}

		var url = MajorsSearch.buildQuery();
		$('.search-content-results-div').html('<div class="spinloader2"></div>');

		window.location.href = url;
	}

	//get index with obj matching id
	var l = MajorsSearch.tagList.length;
	for(var i = 0; i < l; i++){
		if(MajorsSearch.tagList[i].id  == m_id){
			index = i;
			break;
		}
	}


	//add filter to url params and set window.location.href;
	var url = window.location.pathname;

	//remove it from array
	if(index != null){

		if(MajorsSearch.query.imajor === m_id){
			MajorsSearch.query.imajor = '';
			url = url.replace(/&imajor=[a-zA-Z0-9_]+&/g, '&');
		}

		MajorsSearch.removeTag.push('' + MajorsSearch.tagList[index].id);
		MajorsSearch.tagList.splice(index, 1);
		MajorsSearch.displayMajorsTags(MajorsSearch.tagList);		
// console.log(MajorsSearch.removeTag);
		for(var i =0 ; i < MajorsSearch.removeTag.length; i++){
			if(typeof MajorsSearch.removeTag[i].id !== 'undefined')
				url = url + "&rmajor[]=" + MajorsSearch.removeTag[i].id;
		}

		if(MajorsSearch.tagList.length === 0){
			MajorsSearch.tagList = [];
			MajorsSearch.query.imajor = '';
			MajorsSearch.removeTag = [];
			MajorsSearch.query.degree_type = -1;
			MajorsSearch.query.department = '';

		}

		var url = MajorsSearch.buildQuery();

		$('.search-content-results-div').html('<div class="search-loader-cont fadeIn"><div class="spinloader2"></div></div>');
		window.location.href = url;

	}
}



/**********************************
*  filter with degree 
*  handler for the top navigation filters (All, Bachelors, and Masters)
****************************************/
MajorsSearch.filterSearchWithDegreeType = function(e){
	var that = e.target;
	var dtype = $(that).text().trim();
	var degree_type = null;
	var degree = null;

	switch(dtype){
		case 'All':
			degree_type = -1;
			degree = 'all';
			break;
		case 'Bachelors':
			degree_type = 3;
			degree = 'bachelors_degree';
			break;
		case 'Masters':
			degree_type = 4;
			degree = 'masters_degree';
			break;
		default:
			break;
	}

	$('.filter-colleges-majors-type').each(function(i){
		$(this).removeClass('active');
	});	
	$(that).addClass('active');

	
	//colleges uses a variable degree -. which filters collegse that offer a type of degree
	//this degree type filters colleges that offer a major at a degree level
	MajorsSearch.query.degree_type = degree_type;

	// MajorsSearch.query.degree = degree;

	// var cpath =  window.location.pathname + "?";

	// for( var i = 0; i < MajorsSearch.removeTag.length; i++){
	// 	cpath = cpath + '&rmajor[]=' + MajorsSearch.removeTag[i];
	// }


	// for( var k in MajorsSearch.query){
	// 	cpath = cpath + '&' + k + '=' + MajorsSearch.query[k];
	// }
	var cpath = MajorsSearch.buildQuery();

	window.location.href = cpath;	
}


//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////// document ready ///////////////////////////////////////////////////////////////
$(document).ready(function(){
	// var url = window.location.href;
	var qry = window.location.search.substring(1);
	var toks = qry.split('&');
	var query = {};

	for(var i in toks){
		var t = toks[i].split('=');
		if(t[0] == ""){
			//do nothing
		}
		else if(t[0] == 'rmajor[]'){
			MajorsSearch.removeTag.push(t[1]);
		}else{
			query[t[0]] = t[1];
		}
	} 
	MajorsSearch.query = query;
	// MajorsSearch.query.myMajors = true;

	// console.log(MajorsSearch.query);
	//majors are loaded based on a category from college pages
	//if no category is set -- search using user's majors	
	//if majors are set in query params(versus just landing on majors search (below))
	// -- then no need to get tags based on category/department/or user's selected 
	//should already be in memory on client side 
	MajorsSearch.getMajorsLoad();
	
	
	/******** toggle additional majors tags ********/
	$(document).on('click', '.toggle-majors-results, .toggle-majors-arrow', function(e){
		var more = $('.more-majors-results');
		var txt = $('.toggle-majors-results');
		more.toggle();

		if(more.is(':visible')){
			txt.text('hide remaining majors ('+ (MajorsSearch.tagList.length - 5) +')');
		}else{
			txt.text('show remaining majors ('+ (MajorsSearch.tagList.length - 5) +')');
		}
	});


	/****** remove majors tag ********/
	$(document).on('click', '.filter-majors-x', function(e){
		//remove tag from dom and perform new search with it omitted
		MajorsSearch.removeMajorFilter(e);
	});


	/******** filter current search with degree type ******/
	$(document).on('click', '.filter-colleges-majors-type', function(e){
		//get type
		MajorsSearch.filterSearchWithDegreeType(e);
	});	


	/*********** adv college search form *****************/
	// $(document).on('submit', '#advancesearchform', function(e){
	// 	e.preventDefault();

	// 	var that = $(this);
	// 	var params = that.serialize();
	// 	// console.log(params);

	// 	url = "/search?";

	// 	// for( var i =0; i < MajorsSearch.removeTag.length; i++){
	// 	// 	url = url + '&rmajor[]=' + MajorsSearch.removeTag[i];
	// 	// }

	// 	for( var k in MajorsSearch.query){
	// 		url = url + '&' + k + '=' + MajorsSearch.query[k];
	// 	}


	// 	url = url  + "&" + params;

	// 	window.location.href = url;

	// });

	/********** department select handler *****/
	$(document).on('change', '.dept-select-box', function(e){
		var that = $(this);
		var val = that.val();
		var dfrag = document.createDocumentFragment();

		if(MajorsSearch.query.type == 'majors'){
			MajorsSearch.query.term = val;
		}

		$('.sm-wh-loader').removeClass('hide');
		$('.majors-select-container').removeClass('hide');
		$('.major-degree-type-select').removeClass('hide');
		//display majors select after getting majors
		$.ajax({
			url: '/ajax/getAllMajorsFromCat?cat='+ val,
			type: 'GET',

		}).done((res)=> {
			// console.log(res);
			
			$('.sm-wh-loader').addClass('hide');

			//append default option
			var op = document.createElement('option');
			op.setAttribute('value', '');
			op.innerHTML = 'Select Major...';
			dfrag.appendChild(op);

			for(var i in res){
				var op = document.createElement('option');
				op.setAttribute('value', res[i].id);
				op.innerHTML = res[i].name;
				dfrag.appendChild(op);
			}			

			var select = document.getElementsByClassName('adv-c-s-majors-select')[0];
			
			select.innerHTML = '';
			select.appendChild(dfrag);

		})

	})

});
///////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////// end ready ////////////////////////////////////////////////////