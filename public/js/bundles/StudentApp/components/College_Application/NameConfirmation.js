// /College_Application/NameConfirmation

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'

import TextField from './TextField'
import SaveButton from './SaveButton'
import RadioField from './RadioField'
import DatePickr from './../common/DatePickr'

import { NAME, ALTERNATE_NAME, ALTERNATE_TEXT, B_MONTH, B_DAY, B_YR } from './constants'
import { updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'

var PAGE_DONE = '';

class Contact_Info extends React.Component{
	constructor(props) {
		super(props)
		this._saveAndContinue = this._saveAndContinue.bind(this)
	}

	_saveAndContinue(e){
		e.preventDefault();
		
		let { dispatch, _profile, route } = this.props;

		if( _profile[PAGE_DONE] ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );
			dispatch( saveApplication(form, 'identity', _profile.oneApp_step) );
		}
	}

	componentWillMount(){
		let { dispatch, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch( clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, _profile, route } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
			browserHistory.push('/college-application/' + route.next + window.location.search);
		}
	}

	render(){
		let { _profile } = this.props;

		return (
			<div className="application-container">

				<form onSubmit={ this._saveAndContinue }>

					<div className="page-head">Please confirm your first/last name and birthday</div>

					{ NAME.map((n) => <TextField key={n.name} field={n} {...this.props} />) }

					<div>Have you used any other names?</div>
					<div>
						{ ALTERNATE_NAME.map((a) => <RadioField key={a.id} field={a} {...this.props} />) } 
					</div>

					{ selectn('alternate_name_used', _profile) === 'yes' && <TextField field={ALTERNATE_TEXT} {...this.props} /> }

					<label>What is your birthday?</label>

					{ _profile.init_done && 
						<div className="date-wrapper">
							<DatePickr
								_side="_left"
								_action={ updateProfile }
								_state={ _profile }
								_format={'YYYY-MM-DD'}
								_name="birth_date" />
						</div> }
					{ (_.isBoolean(_profile.save_success) && !_profile.save_success) && 
								<div className="error-msg">{_profile.save_err_msg || 'Error.'}</div> }
					<SaveButton 
						_profile={_profile}
						page_done={PAGE_DONE} />
				</form>

			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Contact_Info);