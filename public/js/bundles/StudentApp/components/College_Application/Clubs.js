// /College_Application/Clubs

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import TextField from './TextField'
import SaveButton from './SaveButton'
import SelectField from './SelectField'
import CheckboxField from './CheckboxField'

import { CLUBS_Q, HAVE_CLUBS } from './constants'
import { updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'

var PAGE_DONE = '';
var FIELD_NAMES = null;

class Clubs extends React.Component {
	constructor(props) {
		super(props)
		this._saveAndContinue = this._saveAndContinue.bind(this)
		this._buildNextState = this._buildNextState.bind(this)
		this._buildResetClubsFields = this._buildResetClubsFields.bind(this)
		this._didTheyCheckHaveClubs = this._didTheyCheckHaveClubs.bind(this)
		this._addClub = this._addClub.bind(this)
	}
	_saveAndContinue(e){
		e.preventDefault();
		
		let { dispatch, _profile } = this.props;
		let form = _.omitBy( _profile, (v, k) => k.includes('list') ),
			formNotEmpty = false,
			nextState = {};

		this._didTheyCheckHaveClubs();

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
			this._addClub();
		}

		form = {...form, ...nextState};

		dispatch( saveApplication(form, 'clubs', _profile.oneApp_step) );
	}

	_buildResetClubsFields(){
		let field_name = [];	

		_.each( CLUBS_Q, (obj) => {
			let pick = _.pick(obj, 'name');
			field_name.push(pick.name);
		});

		FIELD_NAMES = field_name;
	}

	_didTheyCheckHaveClubs(){
		let { dispatch, _profile } = this.props;
		let have = _profile.have_clubs || 0; // set have_clubs if not set

		if( !_profile.have_clubs || _.get(_profile, 'my_clubs.length', 0) === 0 ) have = 0; //if have_clubs is empty or awards is empty, have = 0

		dispatch( updateProfile({have_clubs: have}) );
	}

	_addClub(){
		let { dispatch, _profile } = this.props,
			nextState = this._buildNextState();

		dispatch( updateProfile(nextState) );
	}

	_buildNextState(){
		let { dispatch, _profile } = this.props,
			award = {}, nextState = {}, awards = [];

		// if my_clubs exists in profile, it'll be an array
		if( _.get(_profile,'my_clubs') ) awards = _profile.my_clubs.slice(); // make copy of my_clubs

		// loop through each field, and add field value to current award obj (this object is what's saved to my_clubs list)
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

		// update or create my_clubs list with new award
		return {
			...nextState,
			my_clubs: awards,
		};
	}

	componentWillMount(){
		let { dispatch, route } = this.props;

		PAGE_DONE = route.id+'_form_done';

		dispatch( updateProfile({page: route.id}) );
		dispatch(clearChangedFields());
		this._buildResetClubsFields();
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

						<div className="page-head">Organizations & Clubs</div>

						<CheckboxField field={ HAVE_CLUBS } {...this.props} />

						{ _.get(_profile, 'have_clubs', 0) === 1 && 
							<div className="club-container">
								<div>The following questions are optional, but highly recommended</div>

								<br />

								{ CLUBS_Q.map((c) => {
									if( c.options ) return <SelectField key={c.name} field={c} {...this.props} />;
									return <TextField key={c.name} field={c} {...this.props} />;
								}) }
							</div>
						}

						<div className="action-btns">
							{ ( _profile.club_name && _profile.club_role && _profile.club_active_start_month && 
								_profile.club_active_start_year && _profile.club_active_end_month && _profile.club_active_end_year ) && 
									<div className="prelim-save" onClick={ this._addClub }>Add Another</div> }
									
							<SaveButton 
								_profile={_profile}
								page_done={PAGE_DONE} />
						</div>

						<br />

						<div>
							{ _.get(_profile, 'my_clubs.length') > 0 && 
								_profile.my_clubs.map((c, i) => <SingleClub key={c.club_name+i} item={c} {...this.props} />) }
						</div>

					</form>	

				</div>
			</DocumentTitle>
		);
	}
}

class SingleClub extends React.Component {
	constructor(props) {
		super(props)
		this._removeAccolade = this._removeAccolade.bind(this)
		this._editAccolade = this._editAccolade.bind(this)
	}
	_removeAccolade(){
		let { dispatch, _profile, item } = this.props,
			newList = null;

		// remove this item from list and save new list
		newList = _.reject(_profile.my_clubs.slice(), item);
		dispatch( updateProfile({my_clubs: newList}) );
	}

	_editAccolade(){
		let { dispatch, _profile, item } = this.props,
			found = null;

		found = _.find(_profile.my_clubs.slice(), item);	

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
						<span className="name">{ item.club_name }</span>
						<span>{ item.club_active_start_month || '' } { item.club_active_start_year || '' } - { item.club_active_end_month || '' } { item.club_active_end_year || '' }</span>
					</div>

					<div className="accord">{ item.club_role }</div>
					<br />
					<div>{ item.club_notes }</div>
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

export default connect(mapStateToProps)(Clubs);