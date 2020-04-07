/* \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ left side - build article component - end ////////////////////////////////*/

/*** paragraph component - start ***/
var ArticleBody_icon_component = React.createClass({
	render: function(){
		var classes = "nav-icon-div text-center " + this.props.activeClass;
		return (
			<div id="articlebody-button" className={classes} onClick={this.props.setActiveComponent}>
				C
			</div>
		);
	}
});

var AddHTMLButtons = React.createClass({
	render: function(){
		return (
			<div className="">
				<ul className="button-group radius">
					<li><a id="paragraph_tag_btn" className="small button secondary" onClick={this.props.addTagsFunction}>&para;</a></li>
					<li><a id="anchor_tag_btn" className="small button secondary" onClick={this.props.addTagsFunction}>L</a></li>
					<li><a id="bold_tag_btn" className="small button secondary" onClick={this.props.addTagsFunction}><strong>B</strong></a></li>
					<li><a id="image_tag_btn" className="small button secondary" onClick={this.props.addTagsFunction}>&#x00130;</a></li>
					<li><a id="ul_btn" className="small button secondary" onClick={this.props.addTagsFunction}>ul</a></li>
					<li><a id="list_template_btn" className="small button info" onClick={this.props.addTagsFunction}>T</a></li>
				</ul>
			</div>
		);
	}
});

var ArticleBody_content_component = React.createClass({


	getInitialState: function(){
		return {
			clicked_tag_component: null,
			addTagButtons_array: [
				{tag_name: 'paragraph_tag_btn', was_clicked: true, html_tag: '\n<div>\n<p>\n[Enter text here]\n</p>\n</div>'},
				{tag_name: 'anchor_tag_btn', was_clicked: false, html_tag: '<a href="[URL here]" target="_blank">[Link text here]</a>\n'},
				{tag_name: 'bold_tag_btn', was_clicked: false, html_tag: '<strong>[Enter text here]</strong>\n'},
				{tag_name: 'image_tag_btn', was_clicked: false, html_tag: '<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/" alt="" />\n'},
				{tag_name: 'ul_btn', was_clicked: false, html_tag: ''},
				{tag_name: 'list_template_btn', was_clicked: false, html_tag: ''},
			],
		}
	},

	buildListTemplate: function(){
		//building a list of ten items that follow our news article format
		var template = '';
		for( var i = 1; i < 11; i++ ){
			template += '<div>\n\t<p>\n\t\t<strong>\n\t\t\t<a href="" target="_blank">'+i+'. [Header Here]</a>\n\t\t</strong>\n\t</p>\n</div>\n';
			template += '<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/[Image Name Here]" alt="[Alt Name]" />\n';
			template += '<p>Source: \n\t<a href="[Link URL Here]" target="_blank">\n\t\t[Link Name Here]\n\t</a>\n</p>\n';
			template += '<div>\n\t<p>\n\t\t[Paragraph Here]\n\t</p>\n</div>\n';
			if(i !== 10){
				template += '<hr />';
			}
			template += '\n\n\n';
		}

		template += '<br />';
		template += '<div><p>Did we miss anything? Anything to add? Let us know in the comments below!</p></div>';
		
		return template;
	},

	buildUL: function(){
		//build an html unordered list of 3 items
		var ul = '<ul>\n';

		for( var i = 1; i < 4; i++ ){
			ul += '\t<li>[Item '+i+' Here]</li>\n';
		}
		ul += '</ul>\n';

		return ul;
	},

	addTags: function(e){
		//set all html tag object was_clicked to false
		_.each(this.state.addTagButtons_array, function(value, key, obj){
			obj[key].was_clicked = false;
		}, this);

		//for the tag button that was clicked, set that respective objects was_clicked to true
		_.each(this.state.addTagButtons_array, function(value, key, obj){
			if( e.target.id === obj[key].tag_name ){
				obj[key].was_clicked = true;
				if( e.target.id === 'list_template_btn' && obj[key].html_tag === '' ){
					obj[key].html_tag = this.buildListTemplate();
				}else if( e.target.id === 'ul_btn' && obj[key].html_tag === '' ){
					obj[key].html_tag = this.buildUL();
				}
			}
		}, this);

		//find the object where was_clicked is true and save that object in clicked_tag_component
		var temp = _.where(this.state.addTagButtons_array, {was_clicked: true});
		this.setState({clicked_tag_component: temp[0]});
	},

	render: function(){
		var content = this.state.clicked_tag_component ? this.props.articleDetails.article_body_content.concat(this.state.clicked_tag_component.html_tag): this.props.articleDetails.article_body_content;
		var basic_content = this.state.clicked_tag_component ? this.props.articleDetails.article_body_content_basic.concat(this.state.clicked_tag_component.html_tag): this.props.articleDetails.article_body_content_basic;
		var premium_content = this.state.clicked_tag_component ? this.props.articleDetails.article_body_content_premium.concat(this.state.clicked_tag_component.html_tag): this.props.articleDetails.article_body_content_premium;
		var highlighted = this.state.clicked_tag_component ? this.props.articleDetails.highlighted.concat(this.state.clicked_tag_component.html_tag) : this.props.articleDetails.highlighted;

		//reset to null after use
		this.state.clicked_tag_component = null;


		var _pa = this.props.articleDetails;

		return (
			<div>
				<h4>Article Content</h4>
				<AddHTMLButtons addAnchorTagFunction={this.addAnchorTag} addTagsFunction={this.addTags} />
				{ !_pa.premium_only && <textarea 
										id="_all"
										rows="15" 
										placeholder="Enter main article content here..." 
										value={content} 
										onChange={this.props.setArticlebodyFunction}></textarea> }

				{ _pa.premium_only && <div>
										<label>Basic Content</label>
										<textarea 
											id="_basic"
											rows="5" 
											placeholder="Enter part of main article here for Basic users..." 
											value={basic_content} 
											onChange={this.props.setArticlebodyFunction}></textarea>

										<label>Premium Content</label>
										<textarea 
											id="_premium"
											rows="15" 
											placeholder="Enter whole article content here for Premium users..." 
											value={premium_content} 
											onChange={this.props.setArticlebodyFunction}></textarea>
									</div> }
				{_pa.category == 'Blog' || _pa.category == 'B2B Press' ?  
					<div>
					<label className="mb5">Highlighted Content</label>
					<textarea id="_highlighted"
							  rows="2"
							  placeholder="Enter highlighted content..."
							  value={highlighted}
							  onChange={this.props.setHighlighted}></textarea>
					</div>
				 : null}
			</div>
		);
	}
});
/*** paragraph component - end ***/


/*** author info component - start ***/
var AuthorInfo_icon_component = React.createClass({
	render: function(){
		var classes = "nav-icon-div text-center " + this.props.activeClass;
		return (
			<div id="authorinfo-button" className={classes} onClick={this.props.setActiveComponent}>
				B
			</div>	
		);
	}
});

var AuthorInfo_content_component = React.createClass({
	render: function(){
		return (
			<div>
				<h4>Author Details</h4>

				<input type="text" id="author-name" placeholder="Enter author's name..." value={this.props.articleDetails.author_name} onChange={this.props.setAuthorFunction} />
				<input type="text" 
				       id="author-institution" 
				       placeholder="Enter Institution's name (eg. US News or Plexuss.com)"
					   value={this.props.articleDetails.author_institution} onChange={this.props.setAuthorFunction} />		
				<input type="text" id="author-img" placeholder="Enter author's image name..." value={this.props.articleDetails.author_img} onChange={this.props.setAuthorFunction} />
				<input type="text" id="author-descrip" placeholder="Enter author's description..." value={this.props.articleDetails.author_description} onChange={this.props.setAuthorFunction} />
				<input type="text" id="author-link" placeholder="Enter author's link..." value={this.props.articleDetails.author_link} onChange={this.props.setAuthorFunction} />
			</div>
		);
	}
});

var AuthorInfo_component = React.createClass({
	render: function(){
		return (
			<div className="row">
				<div className="column small-12 medium-4">
					<AuthorInfo_icon_component />
				</div>
				<div className="column small-12 medium-8">
					<AuthorInfo_content_component setAuthorFunction={this.props.setAuthorFunction} />
				</div>
			</div>
		);
	}
});
/*** author info component - end ***/


/*** Metadata component - start ***/
var MetaData_icon_component = React.createClass({
	render: function(){
		var classes = "nav-icon-div text-center " + this.props.activeClass;
		return (
			<div id="metadata-button" className={classes} onClick={this.props.setActiveComponent}>
				A
			</div>
		);
	}
});

var MetaData_content_component = React.createClass({
	render: function(){
		var _s = this.state;

		return (
			<div>
				<h4>Article Details</h4>
				<input type="text" id="title" placeholder="Enter meta page title..." value={this.props.articleDetails.meta_title} onChange={this.props.setMetaFunction} />
				<input type="text" id="keywords" placeholder="Enter meta keywords..." value={this.props.articleDetails.meta_keywords} onChange={this.props.setMetaFunction} />
				<input type="text" id="description" placeholder="Enter meta description..." value={this.props.articleDetails.meta_description} onChange={this.props.setMetaFunction} />
				<input type="text" id="url" placeholder="Enter URL..."      value={this.props.articleDetails.meta_url} onChange={this.props.setMetaFunction} />
				<input type="text" id="slug" placeholder="Enter slug..." value={this.props.articleDetails.slug} onChange={this.props.setMetaFunction} />
				<select id="category" onChange={this.props.setMetaFunction}>
					<option value="Select a category">Choose a category</option>
					<option value="Campus Life">Campus Life</option>
					<option value="Celebrity Alma Mater">Celebrity Alma Mater</option>
					<option value="College Sports">College Sports</option>
					<option value="Getting Into College">Getting Into College</option>
					<option value="Ranking">Ranking</option>
					<option value="Financial-Aid">Financial Aid</option>
					<option value="Careers">Careers</option>
					<option value="Blog">Blog</option>
					<option value="Plexuss New Features">Plexuss New Features</option>
					<option value="B2B Press">B2B Press</option>
				</select>
				<input type="text" id="article_title" placeholder="Enter article title..." value={this.props.articleDetails.article_title} onChange={this.props.setMetaFunction} />
				<input type="text" id="lg_img_path" placeholder="Enter large banner image path" value={this.props.articleDetails.lg_img_path} onChange={this.props.setMetaFunction} />
				<input type="text" id="sm_img_path" placeholder="Enter small thumbnail image path" value={this.props.articleDetails.sm_img_path} onChange={this.props.setMetaFunction} />

				<label>
					<input type="checkbox" value="premium-only" checked={ this.props.articleDetails.premium_only } onChange={ this.props.forPremium } />
					Full Article Access for Premium Users Only
				</label>
			</div>
		);
	}
});
/*** Metadata component - end ***/

//left side - build article component
var BuildArticleContainer_component = React.createClass({
	getInitialState: function(){
		return {
			is_active_class: '',
			active_elem: {},
			nav_icon_status_array: [
				{nav_name: 'metadata-button', is_active: true, active_class: 'active'},
				{nav_name: 'authorinfo-button', is_active: false, active_class: ''},
				{nav_name: 'articlebody-button', is_active: false, active_class: ''}
			],
			active_nav_icon_obj: {nav_name: 'metadata-button', is_active: true, active_class: 'active'},
			premium_only: false,
		}
	},	

	getActiveComponent: function(e){

		//set all nav icons to inactive and empty active class
		_.each(this.state.nav_icon_status_array, function(value, key, obj){

			obj[key].is_active = false;
			obj[key].active_class = '';
		}, this);

		//for the tag button that was clicked, set that respective objects was_clicked to true
		_.each(this.state.nav_icon_status_array, function(value, key, obj){
			if( e.target.id === obj[key].nav_name ){

				obj[key].is_active = true;
				obj[key].active_class = ' active';
			}
		}, this);

		//find the object where was_clicked is true and save that object in clicked_tag_component
		var temp_arr = _.where(this.state.nav_icon_status_array, {is_active: true});
		this.setState({active_nav_icon_obj: temp_arr[0]});
	},

	render: function(){
		var metaIcon = '';
		var authorIcon = '';
		var contentIcon = '';
		switch( this.state.active_nav_icon_obj.nav_name ){
			case 'metadata-button':
				metaIcon = this.state.active_nav_icon_obj.active_class;
			break;
			case 'authorinfo-button':
				authorIcon = this.state.active_nav_icon_obj.active_class;
			break;
			case 'articlebody-button':
				contentIcon = this.state.active_nav_icon_obj.active_class;
			break;
		}

		return (
			<div className="column small-12 large-6 build-article-container">
				<div className="row">
					<div className="column small-12 medium-2">
						<div className="height-centering-container">
							<div><MetaData_icon_component setActiveComponent={this.getActiveComponent} activeClass={metaIcon} /></div>
							<div><AuthorInfo_icon_component setActiveComponent={this.getActiveComponent} activeClass={authorIcon} /></div>
							<div><ArticleBody_icon_component setActiveComponent={this.getActiveComponent} activeClass={contentIcon} /></div>
						</div>
					</div>
					<div className="column small-12 medium-10 article-form-container">
						<div className="height-centering-container">
							{this.state.nav_icon_status_array[0].is_active ? <MetaData_content_component 
																				forPremium={this.props.forPremium}
																				premium_only={this.props.premium_only}
																				articleDetails={this.props.articleDetails} 
																				setMetaFunction={this.props.setMetaFunction} /> : null}

							{this.state.nav_icon_status_array[1].is_active ? <AuthorInfo_content_component 
																				articleDetails={this.props.articleDetails} 
																				setAuthorFunction={this.props.setAuthorFunction} /> : null}

							{this.state.nav_icon_status_array[2].is_active ? <ArticleBody_content_component 
																				articleData={this.state}
																				articleDetails={this.props.articleDetails} 
																				setArticlebodyFunction={this.props.setArticlebodyFunction} 
																				setHighlighted={this.props.setHighlighted} /> : null}
						</div>
					</div>
				</div>
			</div>
		);
	}
});

var CreateEventPopup_component = React.createClass({
	getInitialState: function(){
		return{
			event_title: '',
			event_url: '',
			event_city: '',
			event_date_time: '',
			event_description: '',
			file: '',
			imagePreviewUrl: '',
		}
	},
	onChange: function(e){
        this.setState({ [e.target.name]: e.target.value});
      },

	onRemove: function(e){
		this.setState({file : '', imagePreviewUrl: ''});
	},

	onClick : function(e){
		this.props.onClose(this.props.id);
		this.onRemove();
	},

  _handleImageChange : function(e) {
    e.preventDefault();

    var reader = new FileReader();
    var file = e.target.files[0];

	const scope = this;

    reader.onloadend = function(){
      scope.setState({
        file: file,
        imagePreviewUrl: reader.result,
      });
	};
    reader.readAsDataURL(file);
  },

	render: function(){
		let {imagePreviewUrl} = this.state;
		let $imagePreview = null;
		if (imagePreviewUrl) {
		  $imagePreview = (<img src={imagePreviewUrl} />);
		  $text = "Upload new photo";
		} else {
			$text = "Upload event photo";
		}
		if(!this.props.show) {
		  return null;
		}
		return(
		<div className="event-backdrop">
			<div className="event-popup text-center">
				<h4 className="create-event-heading">Create New Event</h4>
				<img src="/images/close-x.png" className="right close-button" onClick={this.onClick} />
				<div className="wrapper_eventform row">
					<form>
					<div className="event_form column small-12 medium-6">
							<input type="text" placeholder="Event Title" name="event_title" onChange={this.onChange}/>
							<input type="text" placeholder="Event URL" name="event_url" onChange={this.onChange}/>
							<input type="text" placeholder="Event City" name="event_city" onChange={this.onChange}/>
							<div className="fake-input">
								<input type="text" placeholder="Choose Date & Time" name="event_date_time" className="event_date" onFocus={this.onChange}/>
								<img src="/images/Calendar.png" />
							</div>
							<textarea  rows="6" placeholder="Enter description for event here" name="event_description" maxLength="170" onChange={this.onChange}></textarea>
					</div>
					<div className="event_upload column small-12 medium-4 right" id="event_upload">
						<div className="previewComponent">
							<div className="imgPreview">
							  {$imagePreview}
							</div>
							<label htmlFor="fileInput" className="button grey-btn" >{$text}</label>
							<input id="fileInput" className="fileInput" type="file"  accept="image/*" onChange={this._handleImageChange}/>
						</div>
					</div>
					</form>
				</div>
				<PublishEventBtn_component wholeEventDetails={this.state}/>
			</div>
		</div>
		);
	}
});

var EditEventPopup_component = React.createClass({
	getInitialState: function(){
		var starttimestring = this.props.value.event_start_time;
		var H = +starttimestring.substr(0, 2);
		var h = H % 12 || 12;
		var ampm = (H < 12 || H === 24) ? " AM" : " PM";
		starttimestring = h + starttimestring.substr(2, 3) + ampm;

		var endtimestring = this.props.value.event_end_time;
		var H = +endtimestring.substr(0, 2);
		var h = H % 12 || 12;
		var ampm = (H < 12 || H === 24) ? " AM" : " PM";
		endtimestring = h + endtimestring.substr(2, 3) + ampm;

		var startdate = moment(this.props.value.event_start_date, 'YYYY-MM-DD').format('MM/DD/YYYY');
		var enddate = moment(this.props.value.event_end_date, 'YYYY-MM-DD').format('MM/DD/YYYY');

		return{
			event_title : this.props.value.event_title,
			event_url : this.props.value.event_url,
			event_city : this.props.value.event_city,
			event_date_time : startdate+' '+starttimestring+' to '+enddate+' '+endtimestring,
			event_description : this.props.value.event_description,
			event_id : this.props.value.id,
			file : '',
		}
	},

	onChange: function(e){
        this.setState({ [e.target.name]: e.target.value});
      },

	onRemove: function(e){
		this.setState({
			file : '',
			imagePreviewUrl: '',
			event_title : this.props.value.event_title,
			event_url : this.props.value.event_url,
			event_city : this.props.value.event_city,
			event_date_time : this.props.value.event_start_date+' '+this.props.value.event_start_time+' - '+this.props.value.event_end_date+' '+this.props.value.event_end_time,
			event_description : this.props.value.event_description,
		});
	},

	onClick : function(e){
		this.props.onClose(this.props.id);
		this.onRemove();
	},

  _handleImageChange : function(e) {
    e.preventDefault();

    var reader = new FileReader();
    var file = e.target.files[0];

	const scope = this;

    reader.onloadend = function(){
      scope.setState({
        file: file,
        imagePreviewUrl: reader.result,
      });
	};
    reader.readAsDataURL(file);
  },

	render: function(){
		let {imagePreviewUrl} = this.state;
		let $imagePreview = null;
		if (imagePreviewUrl) {
		  $imagePreview = (<img src={imagePreviewUrl} />);
		  $text = "Upload new photo";
		} else {
			$imagePreview = (<img src={this.props.value.event_image} />);
			$text = "Upload new photo";
		}
		if(!this.props.show) {
		  return null;
		}

		return(
		<div className="event-backdrop">
			<div className="event-popup text-center">
				<h4 className="create-event-heading">Edit Event</h4>
				<img src="/images/close-x.png" className="right close-button" onClick={this.onClick} />
				<div className="wrapper_eventform row">
					<div className="event_form column small-12 medium-6">
						<input type="text" required placeholder="Event Title" name="event_title" value={this.state.event_title} onChange={this.onChange}/>
						<input type="text" required placeholder="Event URL" name="event_url" value={this.state.event_url} onChange={this.onChange}/>
				        <input type="text" required placeholder="Event City" name="event_city" value={this.state.event_city} onChange={this.onChange}/>
						<div className="fake-input">
							<input type="text" required placeholder="Choose Date & Time" name="event_date_time" className="event_date" value={this.state.event_date_time} onFocus={this.onChange}/>
							<img src="/images/Calendar.png" />
						</div>
						<textarea  rows="6" required placeholder="Enter description for event here" name="event_description" maxLength="170" value={this.state.event_description} onChange={this.onChange}></textarea>
					</div>
					<div className="event_upload column small-12 medium-4 right" id="event_upload">
						<div className="previewComponent">
							<div className="imgPreview">
							  {$imagePreview}
							</div>
							<label htmlFor="fileInput" className="button grey-btn" >{$text}</label>
							<input id="fileInput" className="fileInput" type="file"  accept="image/*" onChange={this._handleImageChange}/>
						</div>
					</div>
				</div>
				<EditEventBtn_component wholeEventDetails={this.state} />
			</div>
		</div>
		);
	}
});
/* \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ left side - build article component - end //////////////////////////////// */




/* \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ right side - preview article component - start //////////////////////////////// */
//Publish Article btn component
var PublishArticleBtn_component = React.createClass({

	getInitialState: function(){
		return {
			postArticle_route: '/publisher/post',
		}
	},

	postArticle: function(){
		var route = this.state.postArticle_route;	

		$.ajax({
			type: 'POST',
			url: route,
			data: {articleData: this.props.wholeArticleDetails},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(ret){
			console.log('done');
		});
	},

	render: function(){
		return (
			<div className="publish-article-btn text-right">
				<span onClick={this.postArticle} className="post_dev">Publish</span>
			</div>
		);
	}
});


//Publish Event btn component
var PublishEventBtn_component = React.createClass({
	getInitialState: function(){
		return {
			postEvent_route: '/publisher/postEvent',
			message: 0,
			message_value: '',
		}
	},

	postEvent: function(){
		var _this = this;
		var route = _this.state.postEvent_route;
		var file = _this.props.wholeEventDetails.file;
		var event_date_time = _this.props.wholeEventDetails.event_date_time;
		var event_title = _this.props.wholeEventDetails.event_title;
		var event_url = _this.props.wholeEventDetails.event_url;
		var event_city = _this.props.wholeEventDetails.event_city;
		var event_description = _this.props.wholeEventDetails.event_description;
		var data = new FormData();
		data.append('file', file);
		data.append('event_date_time', event_date_time);
		data.append('event_title', event_title);
		data.append('event_description', event_description);
		data.append('event_url', event_url);
		data.append('event_city', event_city);
		$.ajax({
			type: 'POST',
			url: route,
			data: data,
			dataType: "JSON",
			processData: false,
			contentType: false,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			if(data['status'] == 'success'){
				_this.setState({ message: 1, message_value: data['msg']});
				setTimeout(function(){ location.reload() }, 1000);
			} else {
				_this.setState({ message: 2, message_value: data['msg']});
			}
		});
	},

	render: function(){
		if (this.state.message == 1) {
			alert = (<span className="alert-success">{this.state.message_value}</span>);
		} else if(this.state.message == 2){
			alert = (<span className="alert-fail">{this.state.message_value}</span>);
		} else {
			alert = null;
		}
		return (
			<div className="publish-event-btn">
				{alert}
				<br/>
				<button id="create_event" className="button success_btn" onClick={this.postEvent}>Create new event</button>
			</div>
		);
	}
});
//Edit Event btn component
var EditEventBtn_component = React.createClass({
	getInitialState: function(){
		return {
			postEvent_route: '/publisher/updateEvent',
			message: 0,
			message_value: '',
		}
	},

	postEvent: function(){
		var _this = this;
		var route = _this.state.postEvent_route;
		var file = _this.props.wholeEventDetails.file;
		var event_date_time = _this.props.wholeEventDetails.event_date_time;
		var event_title = _this.props.wholeEventDetails.event_title;
		var event_url = _this.props.wholeEventDetails.event_url;
		var event_city = _this.props.wholeEventDetails.event_city;
		var event_description = _this.props.wholeEventDetails.event_description;
		var event_id = _this.props.wholeEventDetails.event_id;
		var data = new FormData();
		data.append('file', file);
		data.append('event_date_time', event_date_time);
		data.append('event_title', event_title);
		data.append('event_url', event_url);
		data.append('event_description', event_description);
		data.append('event_id', event_id);
		data.append('event_city',event_city );
		$.ajax({
			type: 'POST',
			url: route,
			data: data,
			processData: false,
			contentType: false,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			if(data['status'] == 'success'){
				_this.setState({ message: 1, message_value: data['msg']});
				setTimeout(function(){ location.reload() }, 1000);
			} else {
				_this.setState({ message: 2, message_value: data['msg']});
			}
		});
	},

	render: function(){
		if (this.state.message == 1) {
			alert = (<span className="alert-success">{this.state.message_value}</span>);
		} else if(this.state.message == 2){
			alert = (<span className="alert-fail">{this.state.message_value}</span>);
		} else {
			alert = null;
		}
		return (
			<div className="publish-event-btn">
				{alert}
				<br/>
				<button id="create_event" className="button success_btn" onClick={this.postEvent}>Update event</button>
			</div>
		);
	}
});


//article body content view
var ArticleBodyContent = React.createClass({
	buildInnerHTML: function(){
		return {__html: this.props.article_content};
	},

	render: function(){
		return (
			<div className="column small-12 medium-9">
				<div dangerouslySetInnerHTML={ this.buildInnerHTML() } />
			</div>
		);
	}
});

//author view
var AuthorView = React.createClass({
	render: function(){
		var linkable_name = this.props.a_link ? <a href={this.props.a_link}>{this.props.a_name}</a> : <strong>{this.props.a_name}</strong>;
		var img_url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/';
		var default_img = this.props.a_img ? img_url.concat(this.props.a_img) : img_url.concat('author_default.png');
		return (
			<div className="column small-12 medium-3">
				<div>By: {linkable_name}</div>
				<div><img src={default_img} alt="This is author img" /></div>
				<div>{this.props.a_descrip}</div>
			</div>
		);
	}
});

//article title view
var ArticleTitle = React.createClass({
	render: function(){
		return (
			<div>
				<h1>{this.props.mainTitle}</h1>
			</div>
		);
	}
});

//metaTags view
var MetaTags = React.createClass({
	render: function(){
		var bg_img_added_class = '';
		var bg_img = null;
		if( this.props.bgImg !== '' ){
			bg_img = {
				backgroundImage: 'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/' + this.props.bgImg + '")'
			};
			bg_img_added_class = 'bgImgAdded';
		}

		return (
			<div style={bg_img} className={bg_img_added_class}>
				<div><strong>Meta Title: </strong>{this.props.metaTitle}</div>	
				<div><strong>Meta Keywords: </strong>{this.props.metaKeywords}</div>
					<div><strong>Meta Url: </strong>{this.props.metaUrl}</div>
				<div><strong>Meta Description: </strong>{this.props.metaDescription}</div>
				<div><strong>Slug: </strong>{this.props.slug}</div>
				<div><strong>Category: </strong>{this.props.category}</div>
			</div>
		);
	}
});

//preview component
var ArticlePreviewContainer_component = React.createClass({
	getInitialState: function(){
		return {
			toggler: 'basic',
		};
	},

	_toggle: function(e){
		this.setState({toggler: e.target.id});
	},

	render: function(){
		var basic_active = this.state.toggler === 'basic';
		var premium_active = this.state.toggler === 'premium';

		var article_body = this.props.articleInfo.article_body_content;

		if( this.props.articleInfo.premium_only ){
			if( basic_active ) article_body = this.props.articleInfo.article_body_content_basic;
			if( premium_active ) article_body = this.props.articleInfo.article_body_content_premium;
		}

		return (
			<div className="column small-12 large-6 article-preview-container">
				<div className="inner-preview-container">
					{ this.props.articleInfo.premium_only && <div className="clearfix premium-view-toggler">
																<div 
																	id="basic" 
																	onClick={ this._toggle }
																	className={"left "+(basic_active ? 'active' : '')}>Basic View</div>
																<div 
																	id="premium" 
																	onClick={ this._toggle }
																	className={"right "+(premium_active ? 'active' : '')}>Premium View</div>
															</div> }
					<MetaTags metaTitle={this.props.articleInfo.meta_title} metaKeywords={this.props.articleInfo.meta_keywords} metaUrl={this.props.articleInfo.meta_url}  metaDescription={this.props.articleInfo.meta_description} slug={this.props.articleInfo.slug} category={this.props.articleInfo.category} bgImg={this.props.articleInfo.lg_img_path} />	
					<ArticleTitle mainTitle={this.props.articleInfo.article_title} />
					<div className="row author-content-view-section">
						<AuthorView 
							a_name={this.props.articleInfo.author_name} 
							a_img={this.props.articleInfo.author_img} 
							a_descrip={this.props.articleInfo.author_description} 
							a_link={this.props.articleInfo.author_link} />

						<ArticleBodyContent 
							article_content={article_body} />
					</div>
					<PublishArticleBtn_component wholeArticleDetails={this.props.articleInfo} />
				</div>
			</div>
		);
	}
});

/* \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ right side - preview article component - end //////////////////////////////// */




/* \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ top level component - start //////////////////////////////// */
var PlexPublisherApp = React.createClass({
	getInitialState: function(){
		return{
			article_id: null,
			meta_title: '',
			meta_keywords: '',
			meta_description: '',
			slug: '',
			category: '',
			lg_img_path: '',
			sm_img_path: '',
			author_name: '',
			author_img: '',
			author_description: '',
			author_link: '',
			author_institution: '',
			article_title: '',
			article_body_content: '',
			article_body_content_basic: '',
			article_body_content_premium: '',
			highlighted: '',
			// publisher menu
			publisher_page_array: [
				{page_name: 'addArticle', is_active: true, active_class: 'active'},
				{page_name: 'editArticle', is_active: false, active_class: ''},
				{page_name: 'viewAuthor', is_active: false, active_class: ''},
				{page_name: 'viewManageEvents', is_active: false, active_class: ''}
			],
			active_pub_page_obj: {page_name: 'addArticle', is_active: true, active_class: 'active'},
			//author list vars/objects
			listOfAuthors_array: null,
			listOfArticle_array: null,
			listOfEvent_array: null,
			temp_author_img: '',
			premium_only: false,
			page_name: 'Build Article',
		}
	},

	_forPremium: function(){
		this.setState({premium_only: !this.state.premium_only});
	},

	setMetaInfo: function(e){
		switch( e.target.id ){
			case 'title':
				this.setState({ meta_title: e.target.value});
			break;
			case 'keywords':
				this.setState({ meta_keywords: e.target.value});
			break;
			case 'description':
				this.setState({ meta_description: e.target.value});
			break;
			case 'slug':
				this.setState({ slug: e.target.value});
			break;
			case 'url':
				this.setState({ meta_url: e.target.value});
			break;
			case 'category':
				this.setState({ category: e.target.value});
			break;
			case 'article_title':
				this.setState({ article_title: e.target.value});
			break;
			case 'lg_img_path':
				this.setState({ lg_img_path: e.target.value});
			break;
			case 'sm_img_path':
				this.setState({ sm_img_path: e.target.value});
			break;
			default:
				this.setState({ meta_title: e.target.value});
			break;
		}

		//clear highlighted if not blog or B2B Press
		if(this.state.category != 'Blog' || this.state.category != 'B2B Press')
			this.setState({ highlighted: ' '});
	},

	setArticleBody: function(e){
		var id = e.target.id,
			val = e.target.value;

		var newState = {};

		switch( id ){
			case '_all': 
				newState['article_body_content'] = val;
				break;

			case '_basic':
				newState['article_body_content_basic'] = val;
				break;

			case '_premium':
				newState['article_body_content_premium'] = val;
				break;
		}

		this.setState(newState);
	},

	setHighlighted: function(e){

		var val = e.target.value;

		this.setState({highlighted: val });

	},

	setAuthorInfo: function(e){
		if( e.target.id == 'author-name' ){
			this.setState({ author_name: e.target.value});
		}else if( e.target.id == 'author-img' ){
			this.setState({ author_img: e.target.value});
		}else if( e.target.id == 'author-descrip' ){
			this.setState({ author_description: e.target.value});
		}
		else if(e.target.id === 'author-institution'){
			this.setState({ author_institution: e.target.value});
		}else{
			this.setState({ author_link: e.target.value});
		}
	},

	setActivePublisherPage: function(e, from_elsewhere){
		var page = '';

		if( from_elsewhere === 'addArticle' ){
			page = from_elsewhere;
		}else{
			page = e.target.id;
		}

		//set all nav icons to inactive and empty active class
		_.each(this.state.publisher_page_array, function(value, key, obj){
			obj[key].is_active = false;
			obj[key].active_class = '';
		}, this);

		//for the tag button that was clicked, set that respective objects was_clicked to true
		_.each(this.state.publisher_page_array, function(value, key, obj){
			if( page === obj[key].page_name ){
				obj[key].is_active = true;
				obj[key].active_class = ' active';
			}
		}, this);

		//find the object where was_clicked is true and save that object in clicked_tag_component
		var temp_arr = _.where(this.state.publisher_page_array, {is_active: true});
		this.setState({active_pub_page_obj: temp_arr[0], page_name: e.target.innerHTML});
	},

	setListOfObjects: function(list, type){
		if( type === 'authors' ){
			this.setState({listOfAuthors_array: list});
		}else if(type === 'articles'){
			this.setState({listOfArticle_array: list});
		}else{
			this.setState({listOfEvent_array: list});
		}
	},

	setArticleID: function(id){
		this.setState({article_id: id});
	},

	fillFormToEdit: function(data, page, elem){
		if( page === 'edit_article' ){
			this.setState({
				article_id: data.id,
				meta_title: data.page_title,
				meta_keywords: data.meta_keywords,
				meta_description: data.meta_description,
				meta_url:data.meta_url,
				slug: data.slug,
				category: data.news_subcategory_id,
				lg_img_path: data.img_lg,
				sm_img_path: data.img_sm,
				author_name: data.external_author,
				author_img: data.authors_img,
				author_description: data.authors_description,
				author_link: data.authors_profile_link,
				article_title: data.title,
				article_body_content: data.content
			});
		}else{
			this.setState({
				author_name: data.name,
				author_img: data.img_path,
				author_description: data.description,
				author_link: data.link,
			});
		}
		
		this.setActivePublisherPage('tmp', 'addArticle');
	},

	render: function(){
		var active_page = null;

		switch( this.state.active_pub_page_obj.page_name ){
			case 'addArticle':
				active_page = <BuildNewArticle_Page 
									appState={this.state} 
									articleIdValue={this.state.article_id} 
									setArticleIdFunction={this.setArticleID} 
									setMetaFunction={this.setMetaInfo} 
									setAuthorFunction={this.setAuthorInfo} 
									setArticlebodyFunction={this.setArticleBody}
									setHighlighted={this.setHighlighted}
									forPremium={this._forPremium} />;
				break;
			case 'editArticle':
				active_page = <EditArticle_Page 
								articlesList={this.state.listOfArticle_array} 
								setListFunction={this.setListOfObjects} 
								fillToEditFunction={this.fillFormToEdit} />
				break;
			case 'viewAuthor': 
				active_page = <ViewAuthor_Page 
								authorList={this.state.listOfAuthors_array} 
								setListFunction={this.setListOfObjects} 
								fillToEditFunction={this.fillFormToEdit} />
				break;
			case 'viewManageEvents' :
				active_page = <ManageEvents_Page
								eventLists={this.state.listOfEvent_array}
								setListFunction={this.setListOfObjects}
								/>
				break;
		}

		return (
			<div className="PlexPublisher_App">
				<NavComponent setPage={this.setActivePublisherPage.bind(this)} setTitle={this.state.page_name}/>
				{active_page}
			</div>
		);
	}
});

// ---------- Add Article Page
var BuildNewArticle_Page = React.createClass({
	render: function(){
		return (
			<div className="row main-plex-buildArticle-row">
				<BuildArticleContainer_component 
					articleDetails={this.props.appState} 
					setMetaFunction={this.props.setMetaFunction} 
					setAuthorFunction={this.props.setAuthorFunction} 
					forPremium={this.props.forPremium}
					setArticlebodyFunction={this.props.setArticlebodyFunction}
					setHighlighted={this.props.setHighlighted} />

				<ArticlePreviewContainer_component 
					articleInfo={this.props.appState} />

				{/*<AutoSave_component 
					entireArticleData={this.props.appState} 
					articleIdValue={this.props.articleIdValue} 
					setArticleIdFunction={this.props.setArticleIdFunction} />*/}
			</div>
		);
	}
});

// ---------- Edit Article Page
var EditArticle_Page = React.createClass({
	getInitialState: function(){
		return {
			articleList: this.props.articlesList || null,
			get_allArticles_route: '/ajax/getAllArticles',
			page_type: 'articles',
		}
	},

	componentWillMount: function(){
		var _this = this;

		if( _this.state.articleList === null ){
			$.ajax({
				url: _this.state.get_allArticles_route,
				type: 'GET',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			}).done(function(data){
				_this.setState({articleList: data});
				_this.props.setListFunction(data, _this.state.page_type);
			});
		}

	},

	render: function(){
		var _this = this;
		return (
			<div className="row main-plex-editArticle-row">
				<div className="column small-8 small-centered">
					<h4>List of Articles</h4>
					{	
						this.state.articleList ? 
						this.state.articleList.map(function(article) {
							var time_ago = moment(article.updated_at).fromNow();
						   return <div id={article.id} className="row single-article-container collapse" data-this-articles-info={article} onClick={_this.props.fillToEditFunction.bind(_this, article, 'edit_article')}>
						   				<div className="column small-12">
						   					<div className="title">{article.title}</div>
						   				</div>
						   				<div className="column small-6">
						   					<div className="author">Author: {article.external_author}</div>
						   				</div>
						   				<div className="column small-6 text-right">
						   					<div className="last-saved">{time_ago}</div>
						   				</div>
						   		  </div>
						}) : null
					}
				</div>
			</div>
		);
	}
});
// ----------- Edit Article Page

// ---------- View Author Page
var ViewAuthor_Page = React.createClass({
	getInitialState: function(){
		return {
			authors_list: this.props.authorList || null,
			get_author_route: '/ajax/getAuthors',
			page_type: 'authors',
		}
	},

	componentWillMount: function(){
		var _this = this;

		//check if state variables are set, if not, run ajax call and use return to save state variables
		//if already set, no need to make another ajax call
		if( _this.state.authors_list === null ){
			$.ajax({
				url: _this.state.get_author_route,
				type: 'GET',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			}).done(function(data){
				_this.setState({authors_list: data});
				_this.props.setListFunction(data, _this.state.page_type);
			});
		}	

	},

	render: function(){
		var _this = this;
		var img_src = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/';
		var last_author = _this.state.authors_list === null ? 0 : _this.state.authors_list.length;
		var not_last_col = 'column small-12 medium-4 single-author-container';
		var last_col = not_last_col + ' end';
		var numOfAuthors_text = _this.state.authors_list === null ? '' : 'Number of Authors: ' + _this.state.authors_list.length;

		return (
			<div className="row main-plex-viewAuthor-row">
				<div className="column small-12">
					<h4>View Authors</h4>
					<h5>{numOfAuthors_text}</h5>
						<div className="row">
							{	
								this.state.authors_list ? 
								this.state.authors_list.map(function(author) {
								   return <div id={author.id} className={author.id === last_author ? last_col : not_last_col} data-this-author-info={author} onClick={_this.props.fillToEditFunction.bind(_this, author, 'view_authors')}>
								   				<div className="single-author-inner-container">
								   					<div className="text-center author_img">
								   						<img src={img_src + author.img_path} alt="Plexuss" />
								   					</div>
									   				<div className="text-center author_name">{author.name}</div>
									   				<div className="author_descrip">{author.description}</div>
									   				<div className="text-center num_published">
									   					<small>Published</small>
									   					<div>{author.articles_published}</div>
									   				</div>
								   				</div>
								   		  </div>
								}) : null
							}
						</div>
				</div>
			</div>
		);
	}
});
// ---------- View Author Page

// ---------- Manage Events Page
var ManageEvents_Page = React.createClass({
	getInitialState: function(){
		return {
			event_lists: this.props.eventLists || null,
			get_event_route: '/ajax/getAllEvents',
			isOpen: false,
			page_type: 'events',
		}
	},

	componentWillMount: function(){
		var _this = this;

		//check if state variables are set, if not, run ajax call and use return to save state variables
		//if already set, no need to make another ajax call
		if( _this.state.event_lists === null ){
			$.ajax({
				url: _this.state.get_event_route,
				type: 'GET',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				//~ async: false,
			}).done(function(data){
				var arr=[];
				for (var key in data) {
				  arr.push(data[key]);
				}
				_this.setState({event_lists: arr});
				_this.props.setListFunction(data, _this.state.page_type);
			});
		}

	},

	openPopup : function(){
		this.setState({
		  isOpen: true
		});
	  },

	closePopup : function(){
		this.setState({
		  isOpen: false
		});
	  },
	render: function(){
		return (
				<div className="manage-events-bg">
				<button className="create_eventPopup success_btn" onClick={this.openPopup}><img src="/images/admin/white-plus.png" /> Create New Event</button>
					<div className="column small-12">
						 {
								this.state.event_lists ? this.state.event_lists.map((event_list,i)=> <EventCard key={i} value={event_list}/>) : null
							}

					</div>
					<CreateEventPopup_component show={this.state.isOpen} onClose={this.closePopup.bind(this)}/>
				</div>
		);
	}
});
// ---------- Manage Events Page

// ---------- Publisher Menu
//~ var PublisherMenu = React.createClass({
	//~ render: function(){
		//~ return (
			//~ <div>
				//~ <div className="publisher-menu-btn text-center">
					//~ <span>Menu</span>
					//~ <ul className="publisher-menu-ul">
						//~ <li id="addArticle1" onClick={this.props.setPage}>Build Article</li>
						//~ <li id="editArticle1" onClick={this.props.setPage}>Edit Article</li>
						//~ <li id="viewAuthor1" onClick={this.props.setPage}>View Authors</li>
						//~ <li id="viewManageEvents1" onClick={this.props.setPage}>Manage Events</li>
					//~ </ul>
				//~ </div>
				
				//~ <div className="publisher-menu-btn-bg"></div>
			//~ </div>
		//~ );
	//~ }
//~ });
// ---------- Publisher Menu

// ---------- Autosave component
var AutoSave_component = React.createClass({
	getInitialState: function(){
		return {
			timer: 0,
			autoSaveArticle_route: '/ajax/autoSaveArticle',
		}
	},

	componentWillMount: function(){
		this.interval = setInterval(this._tick, 20000);	
	},

	_tick: function(){
		var _this = this;
		var route = _this.state.autoSaveArticle_route;

		//if article id is not null, then add the id to the route
		if( _this.props.articleIdValue !== null ){
			route += ('/' + _this.props.articleIdValue);
		}

		$.ajax({
			url: route,
			type: 'POST',
			data: {articleData: _this.props.entireArticleData},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			if( data !== 'successfully updated' ){
				_this.props.setArticleIdFunction(data);
			}
		});
	},

	componentWillUnmount: function(){
		clearTimeout(this.interval);
	},

	render: function(){
		return (
			<div className="autosave_article_container">
				Put Last saved time here {this.state.timer}
			</div>
		);
	}
});
// ---------- Autosave component

/*\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ Top navbar ///////////////////////// */

var NavComponent = React.createClass({
	getInitialState: function(){
		return {
			isOpen: false,
		}
	},

	onClose : function(e){
		this.props.setPage(e);
		this.closeMenu();
	},

	openMenu : function(){
		this.setState({
		  isOpen: true
		});
	  },

	closeMenu : function(e){
		this.setState({
		  isOpen: false
		});
	  },

	render: function() {
		page_name = this.props.setTitle;
		if(this.state.isOpen) {
			 menuList = (
					<div className="menu-overlay">
					<div className="menu-expand">
							<img src="/images/close-x-white.png" className="close-menu" onClick={this.closeMenu} /> <span className="menu-heading">Publisher</span>
							<ul className="menu-expand-ul">
								<li id="addArticle" onClick={this.onClose}>Build Article</li>
								<li id="editArticle" onClick={this.onClose}>Edit Article</li>
								<li id="viewAuthor" onClick={this.onClose}>View Authors</li>
								<li id="viewManageEvents" onClick={this.onClose}>Manage Events</li>
							</ul>
						</div>
					</div>);
		} else {
			menuList = null;
		}

		return (
			<nav>
				<div className="navWide">
					<div className="wideDiv">
						{menuList}
						<img src="/images/hamburger_button.png" className="hamburger-img" onClick={this.openMenu} />
						<span className="page-title">{page_name}</span>
						<a href="/admin/dashboard" className="publisher-logo">
							<img className="plex_logo_resize" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" />
						</a>
					</div>
				</div>
			</nav>
		);
	},
});
/*\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ Top navbar ///////////////////////// */
/*\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ Event Card ///////////////////////// */

var EventCard = React.createClass({
	getInitialState: function(){
		return {
			removeEvent_route: '/publisher/removeEvent',
			event_id : this.props.value.id,
		}
	},

	removeEvent: function(){
		var event_id = this.state.event_id;
		var route = this.state.removeEvent_route;

		$.ajax({
			type: 'POST',
			url: route,
			data: {event_id:event_id},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			if(data['status'] == 'fail'){
				alert(data['msg']);
			}else {
				location.reload();
			}
		});
	},

	openPopup : function(){
		this.setState({
		  isOpen: true
		});
	  },

	closePopup : function(){
		this.setState({
		  isOpen: false
		});
	  },

	render : function(){
		var starttimestring = this.props.value.event_start_time;
		var H = +starttimestring.substr(0, 2);
		var h = H % 12 || 12;
		var ampm = (H < 12 || H === 24) ? " AM" : " PM";
		starttimestring = h + starttimestring.substr(2, 3) + ampm;

		var endtimestring = this.props.value.event_end_time;
		var H = +endtimestring.substr(0, 2);
		var h = H % 12 || 12;
		var ampm = (H < 12 || H === 24) ? " AM" : " PM";
		endtimestring = h + endtimestring.substr(2, 3) + ampm;

		var startdate = moment(this.props.value.event_start_date, 'YYYY-MM-DD').format('MMMM DD, YYYY');
		var enddate = moment(this.props.value.event_end_date, 'YYYY-MM-DD').format('MMMM DD, YYYY');

		return(
		<div className="column small-12 large-3 event-card">
			<div className="event-card-content">
				<img src={this.props.value.event_image} />
				<h5>{this.props.value.event_title}</h5>
				<p>{this.props.value.event_description}</p>
				<div className="event_schedule">
					<img src="/images/clock.png" />  {starttimestring} - {endtimestring}<br />
					<img src="/images/cal.png" /> {startdate} - {enddate}
				</div>
				<div className="details">
					<div>
						<button className="btn btn-clear" onClick={this.openPopup}>Edit Event</button>
						<button className="btn btn-clear red" onClick={this.removeEvent}>Remove Event</button>
					</div>
				</div>
			</div>
			<EditEventPopup_component show={this.state.isOpen} onClose={this.closePopup.bind(this)} value={this.props.value}/>
		</div>
		);
	},

});

/*\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ Event Card ///////////////////////// */


React.render( <PlexPublisherApp />, document.getElementById('plex-publisher'));
/*\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ top level component - end //////////////////////////////// */
