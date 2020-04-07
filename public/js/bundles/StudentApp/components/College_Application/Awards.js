// /College_Application/Awards

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import TextField from './TextField'
import SaveButton from './SaveButton'
import SelectField from './SelectField'
import CheckboxField from './CheckboxField'

import { AWARDS_Q, MONTH_MAP, HAVE_AWARDS } from './constants'
import { updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'

var PAGE_DONE = '';
var FIELD_NAMES = null;

class Awards extends React.Component {
	constructor(props) {
		super(props)
		this._saveAndContinue = this._saveAndContinue.bind(this)
		this._buildNextState = this._buildNextState.bind(this)
		this._buildResetAwardsFields = this._buildResetAwardsFields.bind(this)
		this._addAward = this._addAward.bind(this)
		this._didTheyCheckHaveAwards = this._didTheyCheckHaveAwards.bind(this)
	}

	_saveAndContinue(e){
		e.preventDefault();
		
		let { dispatch, _profile } = this.props;
		let form = _.omitBy( _profile, (v, k) => k.includes('list') ),
			formNotEmpty = false,
			nextState = {};	

		this._didTheyCheckHaveAwards();

		// if any of the fields have a value, then just save it
		_.each(FIELD_NAMES, (name) => {
			if( _profile[name] ){
				formNotEmpty = true;
				return false;
			}
		});

		// get form values if form is not empty
		if( formNotEmpty ){
			nextState = this._buildNextState();
			// nextState will just be added to form during POST but won't be added to store, so calling _addAward to add it to store also
			this._addAward();
		}

		form = {...form, ...nextState};

		dispatch( saveApplication(form, 'awards', _profile.oneApp_step) );
	}

	_buildResetAwardsFields(){
		let field_name = [];	

		_.each( AWARDS_Q, (obj) => {
			let pick = _.pick(obj, 'name');
			field_name.push(pick.name);
		});

		FIELD_NAMES = field_name;
	}

	_didTheyCheckHaveAwards(){
		let { dispatch, _profile } = this.props;
		let have = _profile.have_awards || 0; // set have_awards if not set

		if( !_profile.have_awards || _.get(_profile, 'my_awards.length', 0) === 0 ) have = 0; //if have_awards is empty or awards is empty, have = 0

		dispatch( updateProfile({have_awards: have}) );
	}

	_addAward(){
		let { dispatch, _profile } = this.props,
			nextState = this._buildNextState();

		dispatch( updateProfile(nextState) );
	}

	_buildNextState(){
		let { dispatch, _profile } = this.props,
			award = {}, nextState = {}, awards = [];

		// if my_awards exists in profile, it'll be an array
		if( _.get(_profile,'my_awards') ) awards = _profile.my_awards.slice(); // make copy of my_awards

		// loop through each field, and add field value to current award obj (this object is what's saved to my_awards list)
		// also set each field name for nextState empty string (to clear out the form)
		_.each(FIELD_NAMES, (name) => {
			award = {...award, [name]: _profile[name]}; // set id
			nextState[name] = '';
		});

		//find the obj that has editing = true, if any
		let found = _.find(awards.slice(), {editing: true});

		// if found, update the award with editing true with field vals, same id, and make editing false
		if( found ) awards = awards.map((a) => a.editing ? {...award, id: found.id, editing: false} : a);
		else{
			award.id = awards.length + 1; // since not found, give it a new id
			awards = [...awards, award]; // update awards with new award
		}	

		// update or create my_awards list with new award
		return {
			...nextState,
			my_awards: awards,
		};
	}

	componentWillMount(){
		let { dispatch, route } = this.props;

		PAGE_DONE = route.id+'_form_done';

		dispatch( updateProfile({page: route.id}) );
		dispatch(clearChangedFields());
		
		this._buildResetAwardsFields();
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
			
			if( np._profile.coming_from ) browserHistory.goBack();
			else{
				let required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});
				if(required_route) browserHistory.push('/college-application/'+required_route.next + window.location.search);
			}
		}
	}

	render(){
		let { _profile, route } = this.props;

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container">

					<form onSubmit={ this._saveAndContinue }>

						<div className="page-head">Honors & Awards</div>

						<CheckboxField field={ HAVE_AWARDS } {...this.props} />

						{ _.get(_profile, 'have_awards', 0) === 1 && 
							<div className="club-container">
								<div>The following questions are optional, but highly recommended</div>

								<br />

								{ AWARDS_Q.map((a) => {
									if( a.options ) return <SelectField key={a.name} field={a} {...this.props} />;
									return <TextField key={a.name} field={a} {...this.props} />;
								}) }
	
							</div>
						}

						<div className="action-btns">
							{ (_profile.award_name && _profile.award_accord && _profile.award_received_month && _profile.award_received_year) && 
								<div className="prelim-save" onClick={ this._addAward }>Add Another</div> }
								
							<SaveButton 
								_profile={_profile}
								page_done={PAGE_DONE} />
						</div>

						<br />

						<div>
							{ _.get(_profile, 'my_awards.length') > 0 && 
								_profile.my_awards.map((a, i) => <SingleAward key={a.award_name+i} item={a} {...this.props} />) }
						</div>

					</form>	

				</div>
			</DocumentTitle>
		);
	}
}

class SingleAward extends React.Component {
	constructor(props) {
		super(props)
		this._removeAccolade = this._removeAccolade.bind(this)
		this._editAccolade = this._editAccolade.bind(this)
	}

	_removeAccolade(){
		let { dispatch, _profile, item } = this.props,
			newList = null;

		// remove this item from list and save new list
		newList = _.reject(_profile.my_awards.slice(), item);
		dispatch( updateProfile({my_awards: newList}) );
	}

	_editAccolade(){
		let { dispatch, _profile, item } = this.props,
			found = null;

		found = _.find(_profile.my_awards.slice(), item);	

		// if found, set form fields with the values of this item
		if( found ){
			found.editing = true; //set editing to true so we can find it later when saving
			dispatch( updateProfile({...found}) );
		}
	}

	render(){
		let { item } = this.props;

		return (
			<div className="single-accolade">
				<div className="preview">
					<div className="name-date">
						<span className="name">{ item.award_name || '' }</span>
						<span><span className="mon">{ item.award_received_month || '' }</span>. { item.award_received_year || '' }</span>
					</div>

					<div className="accord">{ item.award_accord || '' }</div>
					<br />
					<div>{ item.award_notes }</div>
				</div>

				<div className="actions">
					<div onClick={ this._editAccolade }>Edit</div>
					<div onClick={ this._removeAccolade }>Remove</div>
				</div>
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Awards);