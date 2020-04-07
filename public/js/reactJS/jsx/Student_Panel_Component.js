// Student_Panel_Component.jsx

var Plex = Plex || {};
Plex.studentElem = $('#student_youre_messaging');

var Student_Messaging_Component = React.createClass({displayName: "Student_Messaging_Component",
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
			React.createElement("div", {className: "student-youre-messaging clearfix"}, 
				React.createElement("div", {className: "pic", style: pic}), 
				React.createElement("div", {className: "name"}, student.name || ''), 
				React.createElement("div", {className: "name-country"}, 
					React.createElement("div", {className: country}), 
					React.createElement("div", null, student.country_name || '')
					
				), 
				 grade ? React.createElement("div", {className: "grade"}, grade) : null, 
				 level ? React.createElement("div", {className: "level"}, level) : null, 
				 student.current_school ? React.createElement("div", {className: "school"}, student.current_school) : null, 
				 student.grad_year ? React.createElement("div", {className: "grad"}, 'Grad Date: ' + student.grad_year) : null, 
				 degree !== 'N/A' ? React.createElement("div", {className: "degree"}, degree) : null, 
				 financial !== 'N/A' ? React.createElement("div", {className: "financial"}, financial) : null, 
				 start_date !== 'N/A' ? React.createElement("div", {className: "start-date"}, start_date) : null
			)
		);
	}
});

Plex.initRep = function(data){
	if( data && (!_.isEmpty(data) && !data.is_list_user) ) ReactDOM.render( React.createElement(Student_Messaging_Component, {student: data}), document.getElementById('student_youre_messaging') );
	else{
		if( $('#student_youre_messaging').children().length > 0 ) ReactDOM.unmountComponentAtNode(document.getElementById('student_youre_messaging'));
	}
};

Plex.unmountStudentPanel = function(){
	ReactDOM.unmountComponentAtNode(document.getElementById('student_youre_messaging'));
};
