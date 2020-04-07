// Rep_Messaging_Component.jsx
var Plex = Plex || {};
Plex.repElem = $('#rep_youre_messaging');

var Rep_Messaging_Component = React.createClass({
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
			<div className="rep-youre-messaging clearfix">
				<div className="school-dets">
					<div className="bk-logo" style={logo}></div>
					<div className="name-rank">
						<div><div className="rank">{'#' + (rep.rank || 'N/A') }</div></div>
						<div><div className="school-name">{rep.school_name || ''}</div></div>
					</div>
					<div className="detailed-info">{rep.address || 'Address: unavailable'}</div>
				</div>
				<div className="rep-dets text-center">
					<div className="name">{rep.name || 'College Representative'}</div>
					<div className="title">{rep.title || 'Title: Unavailable'}</div>
					<div className="since">{ rep.member_since ? 'Since ' + rep.member_since : 'N/A'}</div>
					<div className="pic" style={pic}></div>
					<div className="college">{rep.school_name || ''}</div>
				</div>
				<div className="go-to-col-btn text-center"><a href={rep.slug}>View College Stats</a></div>
			</div>
		);
	}
});

Plex.initRep = function(data){
	if( data && !_.isEmpty(data) ) ReactDOM.render( <Rep_Messaging_Component rep={data} />, document.getElementById('rep_youre_messaging') );
	else{
		if( $('#rep_youre_messaging').children().length > 0 ) ReactDOM.unmountComponentAtNode(document.getElementById('rep_youre_messaging'));
	}
};