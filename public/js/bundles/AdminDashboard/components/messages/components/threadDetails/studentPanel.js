// /messages/components/threadDetails/studentPanel.js

import React, { Component } from 'react'

const gradeMap = {
	Senior: '12',
	Junior: '11',
	Sophmore: '10',
	Freshman: '9',
	Graduate: '',
	'Senior/Graduate': '12',
	'Pre-HighSchool': '8',
}

export default class StudentPanel extends Component{
	constructor(props){
		super(props);
		this._getLevel = this._getLevel.bind(this);
		this._getGrade = this._getGrade.bind(this);
		this._getDegree = this._getDegree.bind(this);
	}

	_getLevel(){
		let { _user } = this.props,
			grad = _user.grad_year,
			now = new Date().getFullYear(), diff = 0;

		if( !grad ) return '';

		diff = grad - now;

		switch(diff){
			case 0: return 'Senior/Graduate';
			case 1: return 'Senior';
			case 2: return 'Junior';
			case 3: return 'Sophmore';
			case 4: return 'Freshman';
			default: return diff < 0 ? 'Graduate' : 'Pre-HighSchool';
		}
	}

	_getDegree(){
		let { _user } = this.props;
		return _user.degree_name ? _user.degree_initials + ', ' + _user.major_name : null;
	}

	_getGrade(){
		var level = this._getLevel(), gradeNum = gradeMap[level];
		return gradeNum ? ( <span>{gradeNum}<sup>th</sup> Grade {level}</span> ) : level;
	}

	render(){
		let { _user } = this.props,
			level = this._getLevel(),
			grade = this._getGrade(),
			degree = this._getDegree();
			
		return (
			<div className="student-youre-messaging">
				<div className="pic" style={{backgroundImage: 'url('+_user.profile_img_loc+')'}} />
				<div className="name-country">
					<div className={'country flag flag-'+ _user.country_code.toLowerCase()} />
					<div className="name">{ _user.name || '' }</div>
				</div>

				{ level && <div className="level">{ grade }</div> }
				{ _user.current_school && <div className="bg school">{ _user.current_school }</div> }
				{ _user.grad_year && <div className="bg grad">{ 'Grad Date: ' + _user.grad_year }</div> }
				{ degree && <div className="bg degree">{ degree }</div> }
				{ _user.financial && <div className="bg financial">{ _user.financial }</div> }
				{ _user.start_date && <div className="bg start-date">{ _user.start_date }</div> }
				{ _user.status && <div className="status">{ status }</div> }

				{/*<div className="actions">
					<div className="invite">
						<div className="icon" /><span>Invite to Handshakes</span>
					</div>
					<div className="remove">Remove Student</div>
				</div>*/}
			</div> 
		);
	}
}