//-- Admin content management: built with React JS

$(document)
  .foundation({
    abide : {
      patterns: {
        dashes_only: /^[0-9-]*$/,
        title: /^[a-zA-Z0-9 ]+$/,
      }
    }
  });

// -- top level component for content management - this will the state for entire page
var ContentMangement_App = React.createClass({
	getInitialState: function(){
		return {
			cms_nav_array: [
				{section_name: 'nav-rankings', is_active: true, active_class: 'active'}, //active nav item by default
				{section_name: 'nav-logo', is_active: false, active_class: null}, //update school logo
				{section_name: 'nav-rep', is_active: false, active_class: null} // update school rep info
			],
			// active_cms_nav: {section_name: 'nav-rankings', is_active: true, active_class: 'active'},
			ranking_title: '',
			school_rank: 0,
			source_url: '',
			rank_img: '',
			rank_description: '',
			school_slug: $('#main-content-management-container').data('school-slug'),
			rankingPin_save_id: null,
			list_of_ranking_pins_array: [],
			removePin_route: '/admin/ajax/removeRankingPin',
			rank_removed_success: {
				type: 'soft',
				backGroundColor: '#a0db39',
				textColor: '#fff',
				img: '/images/topAlert/checkmark.png',
				dur: '3000',
				msg: 'Ranking pin has been successfully deleted!'
			},
		}
	},	

	setRankingList: function(pins){
		var tmp = [];
		var _this = this;

		_.each(pins, function(obj, key, arr){
			tmp = tmp.concat({
				ranking_title: obj.title,
				school_rank: obj.rank_num,
				source_url: obj.source,
				rank_img: obj.image,
				rank_description: obj.rank_descript,
				school_slug: obj.slug,
				rankingPin_save_id: parseInt(obj.id)
			});
		});

		_this.setState({list_of_ranking_pins_array: tmp});
	},

	clearData: function(){
		this.setState({
			ranking_title: '',
			school_rank: 0,
			source_url: '',
			rank_img: '',
			rank_description: '',
			rankingPin_save_id: null,
		});
	},

	updateRankingLists: function(saveId, type){
		var temp = null;

		if( type === 'updated' ){
			temp = _.findWhere(this.state.list_of_ranking_pins_array, {rankingPin_save_id: parseInt(saveId)});
			temp.ranking_title = this.state.ranking_title;
			temp.school_rank = this.state.school_rank;
			temp.source_url = this.state.source_url;
			temp.rank_img = this.state.rank_img;
			temp.rank_description = this.state.rank_description;
			temp.school_slug = this.state.school_slug;
			this.setState({list_of_ranking_pins_array: this.state.list_of_ranking_pins_array});
		}else{
			this.setState({
				rankingPin_save_id: parseInt(saveId),
				list_of_ranking_pins_array: this.state.list_of_ranking_pins_array.concat({
						ranking_title: this.state.ranking_title,
						school_rank: this.state.school_rank,
						source_url: this.state.source_url,
						rank_img: this.state.rank_img,
						rank_description: this.state.rank_description,
						school_slug: this.state.school_slug,
						rankingPin_save_id: parseInt(saveId)
					})
			});
		}
	},

	setActiveNavItem: function(e){
		e.preventDefault();

		//set each nav item to inactive
		_.each(this.state.cms_nav_array, function(value, key, obj){
			obj[key].is_active = false;
			obj[key].active_class = null;
		}, this);

		//then find the nav item that was clicked
		var tmp = _.findWhere(this.state.cms_nav_array, {section_name: e.target.id});

		// if/when found, set that nav item to active and set it as the active nav item
		if( tmp !== undefined ){
			tmp.is_active = true;
			tmp.active_class = 'active';
			this.setState({cms_nav_array: this.state.cms_nav_array});
		}
	},

	setRankingData: function(e){
		switch(e.target.id){
			case 'ranking_title':
				this.setState({ranking_title: e.target.value});
				break;
			case 'school_rank':
				this.setState({school_rank: e.target.value});
				break;
			case 'source_url':
				this.setState({source_url: e.target.value});
				break;
			case 'rank_description':
				this.setState({rank_description: e.target.value});
				break;
			case 'rank_img':
				this.setState({rank_img: e.target.files[0]});
				break;
		}
	},

	editRankingPin: function(e){
		e.preventDefault();

		var _this = this;
		var pin_id = $(e.target).closest('.rank-item-container').data('pin-id');
		var pin = _.findWhere(_this.state.list_of_ranking_pins_array, {rankingPin_save_id: parseInt(pin_id)});

		mixpanel.track("Edit_Ranking", {
			"location": document.body.id
		});
		
		this.setState({
			ranking_title: pin.ranking_title,
			school_rank: pin.school_rank,
			source_url: pin.source_url,
			rank_img: pin.rank_img,
			rank_description: pin.rank_description,
			school_slug: pin.school_slug,
			rankingPin_save_id: parseInt(pin.rankingPin_save_id)
		});
	},

	removeRankingPin: function(e){
		e.preventDefault();
		var _this = this;
		var pin_id = $(e.target).closest('.rank-item-container').data('pin-id');
		var new_list = _.reject(this.state.list_of_ranking_pins_array, {rankingPin_save_id: parseInt(pin_id)});

		mixpanel.track("Delete_Ranking", {
			"location": document.body.id
		});
		
		if( confirm('Removing will permanently delete this Ranking pin, is that ok?') ){
			$.ajax({
				url: this.state.removePin_route,
				type: 'POST',
				data: {pinId: pin_id},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			}).done(function(ret_id){
				_this.setState({list_of_ranking_pins_array: new_list});
				_this.clearData();
				topAlert(_this.state.rank_removed_success);
			});
		}		
	},

	render: function(){
		return (
			<div>
				<CMS_Nav_Component setActiveNavItem_function={this.setActiveNavItem} cms_navItems_list={this.state.cms_nav_array} />
				{ this.state.cms_nav_array[0].is_active ? <Update_Ranking_Component clearData_function={this.clearData} 
																					editPin_function={this.editRankingPin} removePin_function={this.removeRankingPin} 
																					setRankingList_function={this.setRankingList} setRankingData_function={this.setRankingData} 
																					updateList_function={this.updateRankingLists} listOfPins={this.state.list_of_ranking_pins_array} 
																					appState={this.state} /> : null }
				{ this.state.cms_nav_array[1].is_active ? <Update_Logo_Component /> : null }
				{/* this.state.cms_nav_array[2].is_active ? <Rep_Profile_Component /> : null */}
			</div>
		);
	}
});

// -- content management nav bar
var CMS_Nav_Component = React.createClass({
	getInitialState: function(){
		return {
			super_admin: 0
		};
	},

	//triggered only on initial render
	componentWillMount: function(){
		this.state.super_admin = parseInt($('#main-content-management-container').data('super-admin'));
	},

	render: function(){
		//overview tab is being 'null'ed out until we are ready to develop overview management
		return (
			<div className="row cms-sub-nav">
				<div className="column small-12">
					<dl className="sub-nav">
						<dt>Sections:</dt>
						<dd className={this.props.cms_navItems_list[0].active_class}><a id="nav-rankings" href="" onClick={this.props.setActiveNavItem_function}>Rankings</a></dd>
						<dd className={this.props.cms_navItems_list[1].active_class}><a id="nav-logo" href="" onClick={this.props.setActiveNavItem_function}>Logo</a></dd>
						{ this.state.super_admin ? <dd className={this.props.cms_navItems_list[2].active_class}><a id="nav-rep" href="/admin/profile">Rep Profile</a></dd> : null}
					</dl>
				</div>
			</div>
		);
	}
});

//ranking cms ----------------------------------------------------------------------------------- start

//ranking component container
var Update_Ranking_Component = React.createClass({
	render: function(){
		return (
			<div className="ranking-component-container">
				<Ranking_Lists_Component clearData_function={this.props.clearData_function} editPin_function={this.props.editPin_function} removePin_function={this.props.removePin_function} setRankingList_function={this.props.setRankingList_function} listOfPins={this.props.listOfPins} appState={this.props.appState} />
				<Ranking_Build_Component clearData_function={this.props.clearData_function} setRankingData_function={this.props.setRankingData_function} updateList_function={this.props.updateList_function} appState={this.props.appState} />
			</div>
		);
	}
});

// -- ranking lists component where list of other rankings published will go - can edit/remove
var Ranking_Lists_Component = React.createClass({
	getInitialState: function(){
		return {
			getPins_route: '/admin/ajax/getSavedRankingPins',
			list_of_pins: this.props.appState.list_of_ranking_pins_array,
			num_of_pins: 0,
		}
	},

	//only called on initial rendering
	componentWillMount: function(){
		this.getPinsCall();		
		this.state.list_of_pins = this.props.appState.list_of_ranking_pins_array;
	},

	//not called on initial rendering, but called for all re-renderings
	componentWillReceiveProps: function(){

	},

	//when component updates, only update when a new pin has been saved
	componentWillUpdate: function(){
		var pin_list = this.props.appState.list_of_ranking_pins_array;
		if( pin_list.length > this.state.list_of_pins.length ){
			this.getPinsCall();
		}
	},

	addNewRanking: function(e){
		e.preventDefault();
		this.props.clearData_function();
	},

	//ajax call to get this schools saved ranking pins
	getPinsCall: function(){
		var _this = this;
		$.ajax({
			url: this.state.getPins_route,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(pins){
			_this.props.setRankingList_function(pins);
			_this.setState({list_of_pins: _this.props.appState.list_of_ranking_pins_array});
		});
	},

	render: function(){
		var _this = this;
		return (
			<div className="ranking-lists-container ranking-component-inner">

				<div className="row ranking-list-header">
					<div className="column small-12 text-center">
						Your Ranking Lists
						<div className="">
							<a href="" onClick={_this.addNewRanking}>Add a New Ranking</a>
						</div>
					</div>
				</div>

				<div className="scrolling-list-container">
					{
						_this.props.appState.list_of_ranking_pins_array.length !== 0 ?	
						_this.props.appState.list_of_ranking_pins_array.map(function(pin){
							return <Ranking_List_Item_Component key={pin.rankingPin_save_id} pinData={pin} editPin_function={_this.props.editPin_function} removePin_function={_this.props.removePin_function} />	
						}) : null
					}	
				</div>

			</div>
		);
	}
});

// -- each rank pin that has already been created
var Ranking_List_Item_Component = React.createClass({
	render: function(){
		return (
			<div className="row rank-item-container" data-pin-id={this.props.pinData.rankingPin_save_id}>
				<div className="column small-10 small-centered">
					<div className="rank-title">{this.props.pinData.ranking_title}</div>
					<div className="ranking-item-options-container">
						<div className="edit-ranking-pin-btn list-operation">
							<a href="" onClick={this.props.editPin_function}>Edit</a>
						</div>
						<div className="list-operation"> | </div>
						<div className="remove-ranking-pin-btn list-operation">
							<a href="" onClick={this.props.removePin_function}>Remove</a>
						</div>
					</div>	
				</div>
			</div>
		);
	}
});

// -- ranking build component where form fields and live preview will go
var Ranking_Build_Component = React.createClass({
	render: function(){
		return (
			<div className="ranking-build-container ranking-component-inner row">
				<BuildRankFields_Component clearData_function={this.props.clearData_function} setRankingData_function={this.props.setRankingData_function} updateList_function={this.props.updateList_function} appState={this.props.appState} />
				<PreviewRankPin_Component appState={this.props.appState} />
			</div>
		);
	}
});

// -- building rank form fields
var BuildRankFields_Component = React.createClass({
	componentDidMount: function(){
		$(document).foundation('abide', 'reflow');
	},

	render: function(){
		var data = this.props.appState;
		var rank_err_msg = 'Rank number is required. 1 - 999';
		var source_err_msg = 'Source url is required. Ex: https://plexuss.com ';
		return (
			<div className="build-fields-container column small-12 large-6">
				<form id="build-rank-pin-form" data-abide>
					<div>Add a Ranking</div>
					<div>
						<label>Ranking Title</label>
						<input id="ranking_title" type="text" value={data.ranking_title !== '' ? data.ranking_title : null} placeholder="Best School Ever in the History of Forever"  maxLength="70" required pattern="title" onChange={this.props.setRankingData_function} />
						<small className="error">Title is required. No special characters allowed. Only Letters and Numbers.</small>
					</div>
					<div>
						<label># your school is ranked</label>
						<input id="school_rank" type="number" value={data.school_rank !== 0 ? data.school_rank : null} placeholder="0" min="1" max="999" required pattern="number" onChange={this.props.setRankingData_function} />
						<small className="error">{rank_err_msg}</small>
					</div>
					<div>
						<label>Source URL (<i>So students can view the full article</i>)</label>
						<input id="source_url" type="text" value={data.source_url !== '' ? data.source_url : null} placeholder="https://url-of-source-here" required pattern="url" onChange={this.props.setRankingData_function} />
						<small className="error">{source_err_msg}</small>
					</div>
					<div>
						<label>Add an image or logo (<i>optional</i>)</label>
						<input name="rankImg" id="rank_img" type="file" accept="image/*" placeholder="https://url-of-image-here" onChange={this.props.setRankingData_function} />
					</div>
					<div>
						<label>Description (<i>optional</i>)</label>
						<textarea id="rank_description" placeholder="Enter description of this ranking..." maxLength="200" value={data.rank_description !== '' ? data.rank_description : null} onChange={this.props.setRankingData_function}></textarea>
					</div>
					<small className="invalid-form-err-msg">Could not save. Title, Rank, and Source URL cannot be empty.</small>
					<SaveRankingPin_component clearData_function={this.props.clearData_function} updateList_function={this.props.updateList_function} appState={this.props.appState} />
				</form>
			</div>
		);
	}
});

//save Ranking Pin button
var SaveRankingPin_component = React.createClass({
	getInitialState: function(){
		return {
			saveRank_route: '/admin/ajax/saveRankingPin',
			rank_save_success: {
				type: 'soft',
				backGroundColor: '#a0db39',
				textColor: '#fff',
				img: '/images/topAlert/checkmark.png',
				dur: '3000',
				msg: 'Ranking pin has been successfully saved! Go to your college ranking page to check it out.'
			},
			rank_updated_success: {
				type: 'soft',
				backGroundColor: '#a0db39',
				textColor: '#fff',
				img: '/images/topAlert/checkmark.png',
				dur: '3000',
				msg: 'Ranking pin has been successfully updated!'
			},
		}
	},

	clearForm: function(){
		$('form#build-rank-pin-form input, form#build-rank-pin-form textarea').val('');
	},

	validRankNum: function(num){
		num = parseInt(num);
		return num > 0 && num < 1000;
	},

	formIsValid: function(){
		var _this = this;
		var required_fields = $('form#build-rank-pin-form input[required]');
		var invalid_fields = $('form#build-rank-pin-form input[required][data-invalid]');
		var is_valid = true;

		//if at least one field has attr data-invalid, then return false (invalid )
		if( invalid_fields.length > 0 ){
			is_valid = false;
		}else{
			//if none of the fields have data-invalid, then check if they're empty	
			$.each(required_fields, function(){
				var field = $(this);
				//none of the fields can be empty
				if( field.val() === '' ){
					is_valid = false;
					return false;
				}else if( field.attr('id') === 'school_rank' && !_this.validRankNum(field.val()) ){
					is_valid = false;
					return false;
				}
			});
		}
	
		return is_valid;		
	},

	saveRank: function(e){
		var rank_form = $('form#build-rank-pin-form')[0];
		var rankData = new FormData(rank_form);
		var _this = this;

		if( this.formIsValid() ){
			$('#build-rank-pin-form .invalid-form-err-msg').slideUp(250);
			rankData.append('ranking_title', this.props.appState.ranking_title);
			rankData.append('school_rank', this.props.appState.school_rank);
			rankData.append('source_url', this.props.appState.source_url);
			rankData.append('rank_description', this.props.appState.rank_description);
			rankData.append('save_id', this.props.appState.rankingPin_save_id);

			mixpanel.track("Add_Ranking", {
				"location": document.body.id
			});
			$.ajax({
				url: this.state.saveRank_route,
				type: 'POST',
				data: rankData, 
				enctype: 'multipart/form-data',
				contentType: false,
	        	processData: false,
	        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			}).done(function(ret){
				//if an id is returned, pass it to set it and update view
				if( ret !== 'successfully updated' ){
					_this.props.updateList_function(parseInt(ret), 'added');
					topAlert(_this.state.rank_save_success);
				}else{
					//else, show updated {title of pin} alert bar
					_this.props.updateList_function(_this.props.appState.rankingPin_save_id, 'updated');
					_this.state.rank_updated_success.msg = 'Ranking pin, ' + _this.props.appState.ranking_title + ' has been ' + ret + '!';
					topAlert(_this.state.rank_updated_success);
				}
				_this.clearForm();
				_this.props.clearData_function();
			});
		}else{
			$('#build-rank-pin-form .invalid-form-err-msg').slideDown(250);
		}
	},

	render: function(){
		return (
			<div className="save-ranking-btn text-center" onClick={this.saveRank}>Save</div>
		);
	}
});

// -- preview of pin
var PreviewRankPin_Component = React.createClass({
	render: function(){
		var tmp_img = null;
		var appStateVars = this.props.appState;
		var slug = '/college/' + appStateVars.school_slug + '/ranking';

		//if this rank hasn't been saved yet, then if img is uploaded, use createObjectURL to show preview
		if( appStateVars.rankingPin_save_id === null ){
			if( appStateVars.rank_img !== '' ){
				tmp_img = URL.createObjectURL(appStateVars.rank_img);
			}else{
				tmp_img = null;
			}
		}else{
			if( appStateVars.rank_img === null ){
				tmp_img = null;
			}else{
				if( typeof appStateVars.rank_img === 'object' && appStateVars.rank_img !== null ){
					tmp_img = URL.createObjectURL(appStateVars.rank_img);
				}else{
					tmp_img = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/' + appStateVars.rank_img;
				}
			}
		}

		return (
			<div className="build-preview-container column small-12 large-6">
				<div className="preview-window">

					<div className="pin-preview">
						<div className="row">
							<div className="column small-12 text-center">
								{this.props.appState.ranking_title ? this.props.appState.ranking_title : '[Title of Ranking]'}	
							</div>
						</div>

						<div className="row">
							<div className="column small-6">
								<div>RANKED</div>	
								<div className="rank-num">#{this.props.appState.school_rank ? this.props.appState.school_rank : 'N/A'}</div>	
							</div>
							<div className="column small-6 text-center r_img">
								<img src={tmp_img} />
							</div>
							<div className="column small-12 descript">
								{this.props.appState.rank_description ? this.props.appState.rank_description : ''}
							</div>
							<div className="column small-12">
								<a href={this.props.appState.source_url ? this.props.appState.source_url : ''} target="_blank">See full article</a>
							</div>
						</div>
					</div>

				</div>
				<div className="view-rankings-btn text-center">
					<a href={slug} target="_blank">View your rankings</a>
				</div>
			</div>
		);
	}
});


//ranking cms ----------------------------------------------------------------------------------- end


//Logo cms ----------------------------------------------------------------------------------- start
var Update_Logo_Component = React.createClass({
	getInitialState: function(){
		return {
			uploadedFile: null,
			current_logo: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/The_University_of_Texas_at_Austin.png',
			getCollegeData_route: '/admin/ajax/getSchoolData',
			college: {
				id: null,
				name: null,
				current_logo: null,
				uploaded_logo: null,
				overview_image: null,
				address: null,
				city: null,
				state: null,
				zip: null,
				phone: null,
				contact_info: null,
				rank: null
			}
		};
	},

	componentDidMount: function(){
		this.getCollegeData();
	},

	getCollegeData: function(){
		var _this = this;

		$.ajax({
			url: this.state.getCollegeData_route,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			_this.initCollege(JSON.parse(data));
		});
	},

	initCollege: function(data){
		//never want to mutate the current state directly, so make copy of 
		//current college object state and mutate that
		var college_copy = _.clone(this.state.college),
			_this = this,
			propsToSetDynamically = ['id', 'address', 'city', 'state', 'zip', 'overview_image'];

			_.each(propsToSetDynamically, function(val, key, arr){
				if( val === 'overview_image' ) college_copy[val] = _this.buildImgUrl('overview', data[val]);
				else college_copy[val] = data[val];
			});

			college_copy.name = data.school_name;
			college_copy.current_logo = this.buildImgUrl('logo', data.logo_url);
			college_copy.phone = this.formatPhone(data.general_phone);
			college_copy.rank = data.plexuss_ranking ? data.plexuss_ranking : 'N/A';
			college_copy.contact_info = this.formatContactInfo(college_copy);

			//trigger render to update view with instatiated info
			this.setState({college: college_copy});
	},

	formatPhone: function(phone){
		var count = 4, formatted = '', beenHere = false;

		if( phone.length === 10 )
			formatted = '(' + phone.substr(0,3) + ')' + phone.substr(3,3) + '-' + phone.substr(6,4);
		else if( phone.length > 9 )
			formatted = phone.substr(0,1) +'-'+ phone.substr(1,3) +'-'+ phone.substr(4,3) +'-'+ phone.substr(7,3) +'-'+ phone.substr(10,4);
		else
			return phone;

		return formatted;
	},

	formatContactInfo: function(data){
		contactInfo = '';

		//check to see if props are empty, if empty, don't concat
		if( data.address.trim().length !== 0 ) contactInfo += data.address + ', ';
		if( data.city.trim().length !== 0 ) contactInfo += data.city + ', ';
		if( data.state.trim().length !== 0 ) contactInfo += data.state + ' ';
		if( data.zip.trim().length !== 0 ) contactInfo += data.zip + ' | ';
		if( data.phone.trim().length !== 0 ) contactInfo += data.phone;

		return contactInfo;
	},

	buildImgUrl: function(type, name){
		//building img urls for logo or overview img
		if( name ){
			if( type === 'logo' ) return 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/' + name;
			else return 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/' + name;
		}else
			return;
	},

	buildPreview: function(e){
		e.preventDefault();
		college_copy = _.clone(this.state.college);
		college_copy.uploaded_logo = e.target.files[0];
		this.setState({college: college_copy});
	},

	render: function(){
		return (
			<div className="logo-component-container clearfix">
				<Logo_Options_Component build={this.buildPreview} college={this.state.college} />
				<Logo_Preview_Component college={this.state.college} />
			</div>
		);
	}
});

var Logo_Options_Component = React.createClass({
	getInitialState: function(){
		return {
			current_logo: this.props.college.current_logo || null,
			save_route: '/admin/ajax/saveLogo',
			save_success_msg: {
				type: 'soft',
				backGroundColor: '#a0db39',
				textColor: '#fff',
				img: '/images/topAlert/checkmark.png',
				dur: '3000',
				msg: 'Your logo has been successfully saved!'
			}
		};
	},

	save: function(e){
		e.preventDefault();
		var form = new FormData($('form')[0]),
			_this = this, logo_url_copy, url_file_obj;

		$.ajax({
			url: this.state.save_route,
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			topAlert(_this.state.save_success_msg);
			url_file_obj = URL.createObjectURL(_this.props.college.uploaded_logo);
			_this.setState({current_logo: url_file_obj});
			window.location.reload();
		});
	},



	render: function(){
		var college = this.props.college,
			logo = this.state.current_logo ? this.state.current_logo : college.current_logo;

		return (
			<div className="logo-options-container left">
				<form>
					<div className="upload-container">
						<h4>Update Logo</h4>
						<label htmlFor="uploadLogoInput" className="update logo-btn text-center">Upload Logo</label>
						<input name="logo" type="file" id="uploadLogoInput" accept=".png,.jpg,.gif,.bmp" onChange={this.props.build} />
						<div className="dir"><small>Only .jpg, .png, .gif, .bmp allowed</small></div>
					</div>

					<div className="save-container">
						<h4>Current Logo</h4>
						<img src={logo} alt="Current College Logo" />
						<div className="save logo-btn text-center" onClick={this.save}>Save</div>
					</div>
				</form>
			</div>
		);
	}
});

var Logo_Preview_Component = React.createClass({
	getInitialState: function(){
		return {
			logo: this.props.college.current_logo || null,
			default_overview_img: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/default-college-page-photo_overview.jpg',
			socialIcons: {
				linkedin: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/linkedin_64_black.png',
				pinterest: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/pinterest_64_black.png',
				twitter: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/twitter_64_black.png',
				facebook: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/facebook_64_black.png'
			},
		};
	},

	render: function(){
		var icon = this.state.socialIcons,
			college = this.props.college,
			overview_img = college.overview_image ? college.overview_image : this.state.default_overview_img,
			curr_logo = college.current_logo ? college.current_logo : this.state.logo,
			preview_logo = college.uploaded_logo ? URL.createObjectURL(college.uploaded_logo) : curr_logo;
			
		return (
			<div className="logo-preview-container left">
				<div className="outer">
					<div className="head">
						<div className="clearfix">
							<div className="right rank">#{college.rank}</div>
						</div>
						<div className="clearfix">
							<div className="left img"><img src={preview_logo} alt="School Logo" /></div>
							<div className="right info">
								<h4 className="text-right">{college.name}</h4>
								<div className="addr text-right">{college.contact_info}</div>
								<div className="clearfix social">
									<div className="right">
										<img src={icon.linkedin} alt="linkedin share" />
									</div>
									<div className="right">
										<img src={icon.pinterest} alt="pinterest share" />
									</div>
									<div className="right">
										<img src={icon.twitter} alt="twitter share" />
									</div>
									<div className="right">
										<img src={icon.facebook} alt="linkedin share" />
									</div>
									<div className="right share"><b>SHARE:</b></div>
								</div>
							</div>
						</div>
						
						<div className="nav-items">
							<ul className="small-block-grid-3 medium-block-grid-4 large-block-grid-6">
								<li className="text-center">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/overview.png" alt="nav icon" />Overview
								</li>
								<li className="text-center">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/stats.png" alt="nav icon" />Stats
								</li>
								<li className="text-center">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/ranking.png" alt="nav icon" />Ranking
								</li>
								<li className="hide-for-small-only text-center">
									<img className="admissions" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/admission.png" alt="nav icon" />Admissions
								</li>
								<li className="show-for-large-up text-center">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/chat.png" alt="nav icon" />Chat
								</li>
								<li className="show-for-large-up text-center">More<span className="dropdown"></span></li>
							</ul>
						</div>
					</div>
					<div className="media">
						<div className="toggle-btns"><span>PICS</span> | VIDEO | TOUR</div>
						<img src={overview_img} alt="Slider image" />
						<div className="arrow left"></div>
						<div className="arrow right"></div>
					</div>
				</div>
				<div className="text-center preview-text">Preview</div>
			</div>
		);
	}
});
//Logo cms ----------------------------------------------------------------------------------- end


//Rep cms ------------------------------------------------------------------------------------ start
var Rep_Profile_Component = React.createClass({
	getInitialState: function(){
		return {
			rep: {},
			get_rep_data_route: '/admin/ajax/getRepData',
			save_rep_data_route: '/admin/ajax/saveRepData',
			save_rep_pic_route: '/admin/ajax/saveRepPic',
			save_success: {
				type: 'soft',
				backGroundColor: '#a0db39',
				textColor: '#fff',
				img: '/images/topAlert/checkmark.png',
				dur: '5000',
				msg: 'Your Rep Profile has been successfully updated!'
			},
			save_error: {
				type: 'soft',
				backGroundColor: '#ff0000',
				textColor: '#fff',
				img: '/images/topAlert/urgent.png',
				dur: '5000',
				msg: 'Oops! Something went wrong.'
			}
		};
	},

	//on component mount, get rep info - only called once per page load
	componentDidMount: function(){
		this.getRepData();
	},

	initRep: function(){
		var args = Array.prototype.slice.call(arguments),
			data = args[0], prop = null, tmp = {};

		for( prop in data ){
			if( data.hasOwnProperty(prop) ) tmp[prop] = data[prop];
		}

		//adding custom prop to hold uploaded prof pic
		tmp.uploaded_pic = null;

		this.setState({rep: tmp});
	},

	//get initial rep info
	getRepData: function(){
		var _this = this;

		var b64toBlob = function(b64Data, contentType, sliceSize) {
			contentType = contentType || '';
			sliceSize = sliceSize || 512;

			var byteCharacters = atob(b64Data);
			var byteArrays = [];

			for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
				var slice = byteCharacters.slice(offset, offset + sliceSize);

				var byteNumbers = new Array(slice.length);
				for (var i = 0; i < slice.length; i++) {
				 	byteNumbers[i] = slice.charCodeAt(i);
				}

				var byteArray = new Uint8Array(byteNumbers);

				byteArrays.push(byteArray);
			}

			var blob = new Blob(byteArrays, {type: contentType});
			return blob;
		};

		$.get(_this.state.get_rep_data_route, function(data){

			var rep = data[0];

			// build rep img blob url with session restored temp data
			var contentType = 'image/png';
            var tmp_img_base64 = rep['temp_img'];
            var blob = b64toBlob(tmp_img_base64, contentType);
            var blobUrl = URL.createObjectURL(blob);
            // console.log(blobUrl);

			if( data.length > 0 ) _this.initRep(rep);

			var elem = $('#rep_pic_editor');
			
			elem = elem.croppie({
	    		viewport: {
	            width: 150,
	            height: 150,
	            type: 'circle'
	        	},
		        boundary: {
		            width: 210,
		            height: 300
		        },
		        url: blobUrl,
		        enableExif: true,
	    	});

		});
			
	},

	//edit/update rep data
	edit: function(e){
		var target = $(e.target), val = target.val(), elem = target.attr('id'),
			tmp = null, repr = this.state.rep;

		switch( elem ){
			case 'rep_name':
				tmp = val.split(' ');
				repr.fname = tmp[0];
				repr.lname = tmp[1] ? tmp[1] : '';
				repr.whole_name = val ? val : '';
				break;
			case 'rep_title':
				tmp = elem.split('_');
				repr[tmp[1]] = val;
				break;
			case 'rep_description':
				tmp = elem.split('_');
				repr[tmp[1]] = val;
				break;
			case 'rep_yr_started':
				repr.member_since = val;
				break;
			case 'upload-profilePic-input':
				repr.uploaded_pic = e.target.files[0];
				break;
		}

		this.setState({rep: repr});
	},

	save: function(e){
		var target = $(e.target), btn = target.attr('id'), tmp = null,
			route = null, form = null, form_data = null, _this = this, is_save_pic = !1;

		if( btn === 'save-pic-btn' ){
			route = this.state.save_rep_pic_route;
			form = $('.rep-pic form')[0];
		}else{
			route = this.state.save_rep_data_route;
			form = $('.rep-edit form')[0];
		}
		
		form_data = new FormData(form);

		var pic = document.getElementById('rep_preview_pic');

		var imageBase64 = pic.getAttribute('data-src');

		var base64ToFile = function(base64Data, tempfilename, contentType) {
		    contentType = contentType || '';
		    var sliceSize = 1024;
		    var byteCharacters = atob(base64Data);
		    var bytesLength = byteCharacters.length;
		    var slicesCount = Math.ceil(bytesLength / sliceSize);
		    var byteArrays = new Array(slicesCount);

		    for (var sliceIndex = 0; sliceIndex < slicesCount; ++sliceIndex) {
		        var begin = sliceIndex * sliceSize;
		        var end = Math.min(begin + sliceSize, bytesLength);

		        var bytes = new Array(end - begin);
		        for (var offset = begin, i = 0 ; offset < end; ++i, ++offset) {
		            bytes[i] = byteCharacters[offset].charCodeAt(0);
		        }
		        byteArrays[sliceIndex] = new Uint8Array(bytes);
		    }
		    var file = new File(byteArrays, tempfilename, { type: contentType });
		    return file;
		};

		if(imageBase64 != null && imageBase64 != '') {
			var temp_file = base64ToFile(imageBase64, 'rep_temp_preview_pic.png', "image/png");
			form_data.append('rep_temp_preview_pic', temp_file);
		}

		$.ajax({
			url: route,
			type: 'POST',
			data: form_data, 
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			//if error, show error msg, else show success
			if( !data || data === 'Error' ){
				topAlert(_this.state.save_error);//error occured
			}else{
				topAlert(_this.state.save_success);
				if( data !== 'complete' ){
					_this.state.rep.profile_img_loc = data;
					_this.state.rep.uploaded_pic = null;
					_this.setState({rep: _this.state.rep});
				}
			}
		});
	},

	render: function(){
		return (
			<div className="rep-top-container clearfix">
				<Rep_Pic_Component rep={this.state.rep} edit={this.edit} save={this.save} />
				<Rep_Edit_Component rep={this.state.rep} edit={this.edit} save={this.save} />
				<Rep_Preview_Component rep={this.state.rep} />
			</div>
		);
	}
});

var Rep_Pic_Component = React.createClass({
	getInitialState: function(){
		return {
			profile_base_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/',
			default_pic: 'default_avatar.png'
		};
	},

	render: function(){
		var rep = this.props.rep,
			img_class = rep.profile_img_loc ? 'img' : 'img has-default',
			pic = rep.profile_img_loc ? this.state.profile_base_url + rep.profile_img_loc : this.state.profile_base_url + this.state.default_pic,
			img_style = {
				backgroundImage: 'url('+pic+')'
			};

		return (
			<div className="rep-pic left">
				<form>
					<div className="upload-container">
						<h4>Rep Profile</h4>
						<label htmlFor="upload-profilePic-input" className="update logo-btn text-center">Upload Photo</label>
						<input name="profile_pic" type="file" id="upload-profilePic-input" accept=".png,.jpg,.gif,.bmp" onChange={this.props.edit} />
						<input name="user_id" type="hidden" value={rep.user_id} />
						<div className="dir"><small>Only .jpg, .png, .gif, .bmp allowed</small></div>
					</div>

					<div className="save-container">
						<h4>Current Photo</h4>
						<div className={img_class} style={img_style}></div>						

						<div id="save-pic-btn" className="save logo-btn text-center" onClick={this.props.save}>Save</div>
					</div>
				</form>
			</div>
		);
	}
});

var Rep_Edit_Component = React.createClass({
	getInitialState: function(){
		return {
			yr_options: null,
			max_descr_count: 150,
			max_title_count: 50,
			desc_char_count: 0,
			title_char_count: 0
		};
	},

	componentWillMount: function() {
		this.state.yr_options = this.getYearRange();
	},

	getYearRange: function(){
		var this_yr = moment().year(), end_yr = 20, option = [], val = null, i = 0;

		option.push(<option key={-1} value="">{'Select one...'}</option>);
		for (i = 0; i < end_yr; i++) {
			val = this_yr--;
			date = val+'-01-01';
			option.push(<option key={i} value={date}>{val}</option>);
		};

		return option;
	},

	validate: function(e){
		var target = $(e.target), char_length = target.val().length,
			elem = target.attr('id');

		switch( elem ){
			case 'rep_title':
				if( char_length <= this.state.max_title_count ) this.props.edit(e);
				else e.preventDefault();
				break;
			case 'rep_description':
				if( char_length <= this.state.max_descr_count ) this.props.edit(e);
				else e.preventDefault();
				break;
			default:
				this.props.edit(e);
				break;
		}
	},

	render: function(){
		var blurb_msg = '(Try to keep it short and sweet)', yr_options = this.state.yr_options,
			rep = this.props.rep,
			name = rep.fname ? rep.fname : "",
			name = name && rep.lname ? name + ' ' + rep.lname : name,
			whole_name = rep.whole_name ? rep.whole_name : name,
			title_cnt = rep.title ? rep.title.length : this.state.title_char_count,
			title_cnt_class = title_cnt === this.state.max_title_count ? 'char-count max' : 'char-count min',
			count = rep.description ? rep.description.length : this.state.desc_char_count,
			count_class = count === this.state.max_descr_count ? 'char-count max' : 'char-count min';

		return (
			<div className="rep-edit left">
				<form>
					<div className="upload-container">
						<label htmlFor="rep_name">Name:</label>
						<input id="rep_name" name="name" type="text" placeholder="What is your name?" value={whole_name} onChange={this.validate} />

						<label htmlFor="rep_title">Title:</label>
						<input id="rep_title" name="title" type="text" placeholder="What is your title?" value={rep.title} onChange={this.validate} />
						<div className="char_counter"><small>Character count: <span className={title_cnt_class}>{title_cnt}</span> of {this.state.max_title_count}</small></div>

						<label htmlFor="rep_yr_started">When did you start working here:</label>
						<select name="member_since" id="rep_yr_started" onChange={this.validate} value={rep.member_since}>{yr_options}</select>

						<label htmlFor="rep_description">Blurb: <small>{blurb_msg}</small></label>
						<textarea name="description" id="rep_description" rows="5" 
									placeholder="Majored at MIT as a Computer Scientist and later wanted to take my experience to help others, so here I am!" 
									onChange={this.validate} value={rep.description}></textarea>

						<div className="char_counter"><small>Character count: <span className={count_class}>{count}</span> of {this.state.max_descr_count}</small></div>

						<input name="obp_id" type="hidden" value={rep.id} />

						<div id="save-info-btn" className="save text-center" onClick={this.props.save}>Save</div>
					</div>
				</form>
			</div>
		);
	}
});

var Rep_Preview_Component = React.createClass({

	getInitialState: function(){
		return {
			bg_base_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/',
			logo_base_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/',
			prof_base_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/',
			default_pic: 'default_avatar.png',
			isModalOpen: false
		};
	},

	openModal: function() {
		var elem = jQuery(React.findDOMNode(this.refs.chart));
		elem.croppie('bind');
        $('#RepPicEditModal').foundation('reveal', 'open');
    },

    closeModal: function() {
        $('#RepPicEditModal').foundation('reveal', 'close');
        var elem = jQuery(React.findDOMNode(this.refs.chart));
        elem.html('');
    },

    componentDidMount: function() {
    	
		var elem = jQuery(React.findDOMNode(this.refs.chart));
		var rep = this.props.rep;

	    var save_btn = jQuery(React.findDOMNode(this.refs.rep_img_save));
	    var result_shown = jQuery(React.findDOMNode(this.refs.result));	    

	    var b64toBlob = function(b64Data, contentType, sliceSize) {
			contentType = contentType || '';
			sliceSize = sliceSize || 512;

			var byteCharacters = atob(b64Data);
			var byteArrays = [];

			for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
				var slice = byteCharacters.slice(offset, offset + sliceSize);

				var byteNumbers = new Array(slice.length);
				for (var i = 0; i < slice.length; i++) {
				 	byteNumbers[i] = slice.charCodeAt(i);
				}

				var byteArray = new Uint8Array(byteNumbers);

				byteArrays.push(byteArray);
			}

			var blob = new Blob(byteArrays, {type: contentType});
			return blob;
		};

		var pic = jQuery(React.findDOMNode(this.refs.pic));
		var modal = jQuery(React.findDOMNode(this.refs.close));

	    save_btn.click(function(event) {
	    	elem.croppie('result', {
        		type: 'canvas',
        		size: 'viewport'
        	}).then(function(resp){

        		var contentType = 'image/png';
        		var tmp_img_base64 = resp.substring(22);
        		var blob = b64toBlob(tmp_img_base64, contentType);
        		var blobUrl = URL.createObjectURL(blob);

        		// blob:http%3A//plexuss.dev/9c1363ea-fdf7-4233-8161-5ec647ee4120
        		pic.css('backgroundImage', 'url('+ blobUrl +')');

        		pic.attr('data-src', tmp_img_base64);
        		
        		modal.trigger('click');
        	});

	    });
	},

	render: function(){
		var rep = this.props.rep,
			name = rep.fname ? rep.fname + ' ' + rep.lname : 'Add your name',
			title = rep.title ? rep.title : '[Add your title]',
			memberSince = rep.member_since ? 'Since '+rep.member_since.split('-')[0] : '[Add year started]',
			descrip = rep.description ? rep.description : '[Add short description about yourself]',
			school = rep.school_name ? rep.school_name : 'N/A',
			prof_pic = rep.profile_img_loc ? {backgroundImage: 'url('+this.state.prof_base_url+rep.profile_img_loc+')'} : 
						{backgroundImage: 'url('+this.state.prof_base_url+this.state.default_pic+')'},
			logo = rep.logo_url ? {backgroundImage: 'url('+this.state.logo_base_url+rep.logo_url+')'} : null,
			backg = rep.school_bk_img ? {backgroundImage: 'url('+this.state.bg_base_url+rep.school_bk_img+')'} : null;

			if(rep.uploaded_pic) {

				var elem = jQuery(React.findDOMNode(this.refs.chart));
				elem.croppie('bind', {
					url : URL.createObjectURL(rep.uploaded_pic)
				});
			}
			//if uploaded_pic has a value, use this instead until they save
			if( rep.uploaded_pic ) prof_pic = {backgroundImage: 'url('+URL.createObjectURL(rep.uploaded_pic)+')'};

			if ( rep.uploaded_pic ) {
				var pic = document.getElementById('rep_preview_pic');
		        var reader = new FileReader();

		        reader.onload = function(readerEvt) {
		            var binaryString = readerEvt.target.result;
		            pic.setAttribute('data-src', btoa(binaryString));
		        };

		        reader.readAsBinaryString(rep.uploaded_pic);
		    }
			
		return (
			<div className="rep-preview left">
				<div className="inner clearfix">
					<div className="left front">
						<div className="text-center">Front</div>
						<div className="card-outer">
							<div className="card text-center">
								<div className="details" style={backg}>
									<div className="details-inner">
										<div className="name">{name}</div>
										<div className="title">{title}</div>
										<div className="member-since">{memberSince}</div>
										<div className="pic" style={prof_pic} onClick={this.openModal} ref="pic" id="rep_preview_pic" data-src=""></div>

										<div id="RepPicEditModal" className="reveal-modal show-for-medium-up" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
											<a className="close-reveal-modal right" aria-label="Close" ref="close">X</a>
										
											<div id="rep_pic_editor" ref="chart"></div> 
											<div className="row text-center">
												<a className="save-btn button" ref="rep_img_save">Save</a>
											</div>
										</div>


										<div className="school">{school}</div>
									</div>
									<div className="layer"></div>
								</div>
								<div className="send-msg text-center">
									SEND MESSAGE
								</div>
							</div>
						</div>
					</div>
					<div className="left back">
						<div className="text-center">Back</div>
						<div className="card-outer">
							<div className="card text-center">
								<div className="details">
									<div className="school-logo" style={logo}></div>
									<div className="name">{name}</div>
									<div className="title">{title}</div>
									<div className="member-since">{memberSince}</div>
									<div className="desc">{descrip}</div>
								</div>
								<div className="send-msg text-center">
									SEND MESSAGE
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		);
	}
});

//Rep cms ------------------------------------------------------------------------------------ end

React.render( <ContentMangement_App />, document.getElementById('main-content-management-container'));
