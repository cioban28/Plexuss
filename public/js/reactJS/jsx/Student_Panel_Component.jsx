// Student_Panel_Component.jsx

var Plex = Plex || {};
Plex.studentElem = $('#student_youre_messaging');

var Student_Messaging_Component = React.createClass({
	getInitialState: function(){
		return {
			student: null
		};
	},

	componentWillMount: function(){
		if( !Plex.studentElem.is(':visible') ) this.showPanel();
		this.setState({student: this.props.student});
	},

	componentWillReceiveProps: function(nextProps){
		if( nextProps.student.name !== this.props.name ) this.setState({student: nextProps.student});
	},

	componentWillUnmount: function(){
		this.hidePanel();
	},

	hidePanel: function(){
		Plex.studentElem.addClass('hardhide');
		// $('#portalListwrapper > .msging > .column > .messageMainWindow').removeClass('med-pad');
		$('#admin-messages .rightMessageColumn').removeClass('large-6').addClass('large-9');
	},

	showPanel: function(){
		Plex.studentElem.removeClass('hardhide');
		// $('#admin-messagesListwrapper > .msging > .column > .messageMainWindow').addClass('med-pad');
		$('#admin-messages .rightMessageColumn').removeClass('large-9').addClass('large-6');
	},

	getGrade: function(){
		var level = this.getLevel();

		switch(level){
			case 'Senior': return '12th Grade';
			case 'Junior': return '11th Grade';
			case 'Sophmore': return '10th Grade';
			case 'Freshman': return '9th Grade';
			default: return '';
		}
	},

	getLevel: function(){
		var grad = this.state.student.grad_year,
		now = new Date().getFullYear(), diff = 0;

		diff = grad - now;

		switch(diff){
			case 0: return 'Senior/Graduate';
			case 1: return 'Senior';
			case 2: return 'Junior';
			case 3: return 'Sophmore';
			case 4: return 'Freshman';
			default: 
				if( diff < 0 ) return 'Graduate';
				return 'Pre-HighSchool';
		}
	},

	getDegree: function(){
		return this.state.student.degree_name ? this.state.student.degree_initials + ', ' + this.state.student.major_name : 'N/A';
	},

	getFlag: function(){
		return 'country flag flag-' + this.state.student.country_code.toLowerCase();
	},

	getFinancial: function(){
		return this.state.student.financial ? this.state.student.financial : 'N/A';
	},

	getStartDate: function(){
		return this.state.student.start_date ? this.state.student.start_date : 'N/A';
	},

	render: function(){
		var student = this.state.student,
			pic = {backgroundImage: 'url('+student.profile_img_loc+')'},
			grade = student ? this.getGrade() : '',
			level = student ? this.getLevel() : '',
			degree = student ? this.getDegree() : '',
			country = student ? this.getFlag() : 'country',
			financial = student ? this.getFinancial() : '',
			start_date = student ? this.getStartDate() : '';

		return (
			<div className="student-youre-messaging clearfix">
				<div className="pic" style={pic}></div>
				<div className="name">{student.name || ''}</div>
				<div className="name-country">
					<div className={country}></div>
					<div>{student.country_name || ''}</div>
					
				</div>
				{ grade ? <div className="grade">{grade}</div> : null }
				{ level ? <div className="level">{level}</div> : null }
				{ student.current_school ? <div className="school">{student.current_school}</div> : null }
				{ student.grad_year ? <div className="grad">{'Grad Date: ' + student.grad_year}</div> : null }
				{ degree !== 'N/A' ? <div className="degree">{degree}</div> : null }
				{ financial !== 'N/A' ? <div className="financial">{financial}</div> : null }
				{ start_date !== 'N/A' ? <div className="start-date">{start_date}</div> : null }
			</div>
		);
	}
});

Plex.initRep = function(data){
	if( data && (!_.isEmpty(data) && !data.is_list_user) ) ReactDOM.render( <Student_Messaging_Component student={data} />, document.getElementById('student_youre_messaging') );
	else{
		if( $('#student_youre_messaging').children().length > 0 ) ReactDOM.unmountComponentAtNode(document.getElementById('student_youre_messaging'));
	}
};

Plex.unmountStudentPanel = function(){
	ReactDOM.unmountComponentAtNode(document.getElementById('student_youre_messaging'));
};
