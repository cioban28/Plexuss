// /College_Application/Family

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import SaveButton from './SaveButton'
import SelectField from './SelectField'

import { FAM_Q } from './constants'
import { updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'

var PAGE_DONE = '';

class Family extends React.Component {
	constructor(props) {
		super(props)
		this._saveAndContinue = this._saveAndContinue.bind(this)
	}

	_saveAndContinue(e){
		e.preventDefault();
		
		let { dispatch, _profile } = this.props;
		let form = _.omitBy( _profile, (v, k) => k.includes('list') );
		dispatch( saveApplication(form, 'family', _profile.oneApp_step) );
	}

	componentWillMount(){
		let { dispatch, route } = this.props;
		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch(clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
			let required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});
			if(required_route) browserHistory.push('/college-application/'+required_route.next + window.location.search);	
		}
	}

	render(){
		let { _profile, route } = this.props;

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container">

					<form onSubmit={ this._saveAndContinue }>

						<div className="page-head">Tell us a little about your family</div>

						{ FAM_Q.map((f) => <SelectField key={f.name} field={f} {...this.props} />) }

						<SaveButton 
							_profile={_profile}
							page_done={PAGE_DONE} />

					</form>	

				</div>
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Family);