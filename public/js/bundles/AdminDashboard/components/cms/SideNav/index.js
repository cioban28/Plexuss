// cms/SideNav/index.js

import React from 'react'
import { Link } from 'react-router'
import createReactClass from 'create-react-class'

import * as constants from './../International/constants'
import { TUITION_COST_FIELDS } from './../Cost/constants'

import './styles.scss'

export default createReactClass({
	getInitialState(){
		return {
			scrollClass: '',
			barTop: null,
		};
	},

	componentWillMount(){
		document.addEventListener('scroll', this._scrollListener);
	},

	componentWillUnmount(){
		document.removeEventListener('scroll', this._scrollListener);
	},

	_scrollListener(e){
		// adds a class to the header to make it fixed to the top when scrolled past a certain point
		let { scrollClass, barTop } = this.state,
			classname = '',
			doc = e.srcElement.body.scrollTop,
			bar = $('#_SideNav').offset().top;

		if( barTop === null ) this.state.barTop = bar;

		// only setting state to if scrollClass isn't already set
		if( barTop && doc > barTop ){
			// only setting state to if scrollClass isn't already set
			if( !scrollClass ) this.setState({scrollClass: 'scrolledToTop'});
		}else {
			// only setting scrollClass to empty if it is set
			if( scrollClass ) this.setState({scrollClass: ''});
		}
	},
	render(){
		let { items, _state, custom, program } = this.props,
			{ scrollClass } = this.state;

		return <ul id="_SideNav" className={scrollClass}>{ items.map((rt) => <NavTab key={rt.name} _route={rt} {...this.props} />) }</ul>;
	}
});

const NavTab = createReactClass({
	_isCompleted(){
		let { _route, _state, program } = this.props;

		switch( _route.id ){

			// -- program is shared on every page that includes this SideNav component
			case 'program': return !!program;

			// -- /tools/overview pages
			case 'overview:header': return !!( _.get(_state, 'images.length', 0) > 0 && _.get(_state, 'videos.length', 0) > 0 );

			case 'overview:content': return !!_.get(_state, 'content.overview_content');

			// -- /tools/international pages
			case 'header_info': return _state.undergrad_book_supplies || _state.undergrad_room_board || _state.undergrad_total_yearly_cost || _state.undergrad_tuition ||
									_state.grad_book_supplies || _state.grad_room_board || _state.grad_total_yearly_cost || _state.grad_tuition;

			case 'testimonials': return _state.testimonialList.length > 0;

			case 'admission':
				var fields = [...constants.ADMISSION_RADIO_FIELDS, ...constants.ADMISSION_TEXT_FIELDS];
				return this._checkIfAtLeastOneFieldWasEntered(fields);

			case 'scholarship':
				var fields = [...constants.SCHOLARSHIP_RADIO_FIELDS, ...constants.SCHOLARSHIP_TEXT_FIELDS];
				return this._checkIfAtLeastOneFieldWasEntered(fields);

			case 'notes': return !!_.get(_state, _state.activeProgram+'_notes');

			case 'grades':
				var { GPA, TOEFL, IELTS, SAT, PSAT, GMAT, ACT, GRE } = constants,
					fields = [...GPA, ...TOEFL, ...IELTS, ...SAT, ...PSAT, ...GMAT, ...ACT, ...GRE];
				return this._checkIfAtLeastOneFieldWasEntered(fields);

			case 'requirements': return this._isRequirementInfoValid();

			case 'majors': return !!_.get(_state, 'major_data_init_done');

			case 'alumni': return _state.alumni_list && _state.alumni_list.length > 0;

			// -- /tools/cost pages
			case 'tuition': return this._areAllFieldsEntered(TUITION_COST_FIELDS);

			// -- /tools/application pages
			case 'family':
			case 'awards':
			case 'clubs':
			case 'uploads' :
			case 'courses' :
			case 'essay':
				return !!_state[program+'_require_'+_route.id];

			case 'additional':
			case 'custom':
				return this._checkIfCustomQuestionIsRequired();


			default: return false;
		}
	},

	_checkIfCustomQuestionIsRequired(){
		let { _state, program, _route } = this.props,
			name = _route.id+'_fields',
			fields = _.pickBy(_state[name], (v, k) => k.includes(program));

		return !_.isEmpty( _.pickBy(fields, f => !!f) );
	},

	_areAllFieldsEntered(fields){
		let { _state, program } = this.props,
			valid = false;

		_.each(fields, f => {
			let name = program+'_'+f.name;

			// if this fields val is falsy, make valid false and break out of loop
			if( !_state[name] ){
				valid = false;
				return false;
			}

			valid = true; // if it gets here, then val is truthy
		});

		return valid;
	},

	_isRequirementInfoValid(){
		let { _state, program } = this.props,
			valid = false,
			REQUIREMENTS = constants.REQUIREMENTS;

		for (var i = 0; i < REQUIREMENTS.length; i++) {
			let req = REQUIREMENTS[i].name,
				req_prop_name = program+'_'+req+'_requirements';

			// check if college has added at least one requirement to either of the 4 lists
			if( _.get(_state, req_prop_name+'.list.length', 0) > 0 ){
				valid = true;
				break;
			}
		}

		return valid;
	},

	_checkIfAtLeastOneFieldWasEntered(fields){
		let { _state, program } = this.props,
			valid = false;

		// break out of loop as soon as you find one field that is truthy
		_.each(fields, field => {
			if( _state[program+'_'+field.name] ){
				valid = true;
				return false;
			}
		});

		return valid;
	},

	_programChosen(){
		let { _route, program } = this.props,
			chosen = !!program,
			classes = '';

		// for /tools/overview, no program restrictions
		if( _route.id.includes('overview') ) return classes;

 		// determine active/inactive class
		if( !chosen && _route.id !== 'program' ) classes = ' inactive ';

		return classes;
	},

	render(){
		let { _route, _state, program } = this.props,
			_id = _route.id || '',
			_pc = this._programChosen(),
			_complete = this._isCompleted();

		let completedClass = _complete ? '' : 'hide';

		return (
			<li className={'tab '+_id+_pc}>
				<Link
					to={ _route.path }
					className="link"
					activeClassName="activeTab"
					onClick={ e => !!_pc && e.preventDefault() }>

						<div className="tab-inner">
							{ _route.name }
							{ _complete && <div className={"completed "+completedClass}>&#10003;</div> }
						</div>

				</Link>
			</li>
		);
	}
});
