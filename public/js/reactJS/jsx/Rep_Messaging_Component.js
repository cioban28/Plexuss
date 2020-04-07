// Rep_Messaging_Component.jsx
var Plex = Plex || {};
Plex.repElem = $('#rep_youre_messaging');

var Rep_Messaging_Component = React.createClass({displayName: "Rep_Messaging_Component",
	getInitialState: function(){
		return {
			rep: null
		};
	},

	componentWillMount: function(){
		if( !Plex.repElem.is(':visible') ) this.showPanel();
		this.setState({rep: this.props.rep});
	},

	componentWillReceiveProps: function(nextProps){
		if( nextProps.rep.name !== this.props.name ) this.setState({rep: nextProps.rep});
	},

	componentWillUnmount: function(){
		this.hidePanel();
	},

	hidePanel: function(){
		Plex.repElem.addClass('hardhide');
		// $('#portalListwrapper > .msging > .column > .messageMainWindow').removeClass('med-pad');
		$('#portal .rightMessageColumn').removeClass('large-6').addClass('large-9');
	},

	showPanel: function(){
		Plex.repElem.removeClass('hardhide');
		// $('#portalListwrapper > .msging > .column > .messageMainWindow').addClass('med-pad');
		$('#portal .rightMessageColumn').removeClass('large-9').addClass('large-6');
	},

	render: function(){
		var rep = this.state.rep,
			pic = {backgroundImage: 'url('+rep.profile_img_loc+')'},
			logo = {backgroundImage: 'url('+rep.logo_url+')'};

		return (
			React.createElement("div", {className: "rep-youre-messaging clearfix"}, 
				React.createElement("div", {className: "school-dets"}, 
					React.createElement("div", {className: "bk-logo", style: logo}), 
					React.createElement("div", {className: "name-rank"}, 
						React.createElement("div", null, React.createElement("div", {className: "rank"}, '#' + (rep.rank || 'N/A') )), 
						React.createElement("div", null, React.createElement("div", {className: "school-name"}, rep.school_name || ''))
					), 
					React.createElement("div", {className: "detailed-info"}, rep.address || 'Address: unavailable')
				), 
				React.createElement("div", {className: "rep-dets text-center"}, 
					React.createElement("div", {className: "name"}, rep.name || 'College Representative'), 
					React.createElement("div", {className: "title"}, rep.title || 'Title: Unavailable'), 
					React.createElement("div", {className: "since"},  rep.member_since ? 'Since ' + rep.member_since : 'N/A'), 
					React.createElement("div", {className: "pic", style: pic}), 
					React.createElement("div", {className: "college"}, rep.school_name || '')
				), 
				React.createElement("div", {className: "go-to-col-btn text-center"}, React.createElement("a", {href: rep.slug}, "View College Stats"))
			)
		);
	}
});

Plex.initRep = function(data){
	if( data && !_.isEmpty(data) ) ReactDOM.render( React.createElement(Rep_Messaging_Component, {rep: data}), document.getElementById('rep_youre_messaging') );
	else{
		if( $('#rep_youre_messaging').children().length > 0 ) ReactDOM.unmountComponentAtNode(document.getElementById('rep_youre_messaging'));
	}
};