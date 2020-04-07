// /College_Application/Declarations.js

import React from 'react'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import MyCollege from './MyCollege'
import SaveButton from './SaveButton'
import ReactSpinner from 'react-spinjs-fix'
import CheckboxField from './CheckboxField'

import { updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'

var PAGE_DONE = '';

class Declarations extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			schools: [],
			dec_page_index: 0,
		}
		this._saveAndContinue = this._saveAndContinue.bind(this)
		this._buildDeclarations = this._buildDeclarations.bind(this)
	}

	_buildDeclarations(_np){
		let { _profile } = this.props;	
		let _profile_state = _np || _profile;

		if( _.get(_profile_state, 'applyTo_schools.length') ){
			// grab only the school that have declarations
			let decl_schools = _.filter(_profile_state.applyTo_schools, school => _.get(school, 'declarations.length'));

			this.state.schools = decl_schools.map(sc => {
				// create checkbox field obj for each declaration
				let fields = sc.declarations.map(decs => {
					return {
						id: 'declaration_'+decs.id,
						name: 'declaration_'+decs.id,
						label: decs.language,
						is_default: true,
					};
				});

				return {
					...sc,
					declaration_fields: fields,
				};
			});
		}
	}

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile } = this.props;

		if( _profile[PAGE_DONE] ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );
			dispatch( saveApplication(form, 'declaration', _profile.oneApp_step) );
		}
	}

	componentWillMount(){
		let { dispatch, route } = this.props;

		this._buildDeclarations();
		
		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch(clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile: _p } = this.props,
			{ _profile: _np } = np;

		// check for changes in applyTo_schools array
		if( !_np.get_priority_schools_err && (_p.init_priority_schools_done !== _np.init_priority_schools_done && _np.init_priority_schools_done) ){
				this._buildDeclarations(_np);
		}

		// after saving, reset saved and go to next route
		if( _np.save_success !== _p.save_success && _np.save_success ){
			dispatch( resetSaved() );
			
			if( _np.coming_from ) browserHistory.goBack();
			else{
				let required_route = _.find(_p.req_app_routes.slice(), {id: route.id});
				if(required_route) browserHistory.push('/college-application/'+required_route.next + window.location.search);
			}
		}
	}

	render(){
		let { _profile, route } = this.props,
			{ schools, dec_page_index } = this.state;

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container">

					<form onSubmit={ this._saveAndContinue }>
						<div className="page-head">{route.name} ({schools.length > 0 ? dec_page_index+1 : 0}/{schools.length || 0})</div>

						{ schools[dec_page_index] && 
							<div className="my-colleges-list full">
								<MyCollege key={schools[dec_page_index].college_id} i={1} college={schools[dec_page_index]} />
								<div className="my-college-name">{schools[dec_page_index].school_name || ''}</div>
							</div> }

						{ _.get(schools, 'length') > 0 && 
							schools[dec_page_index].declaration_fields.map(dec => <CheckboxField key={dec.id} field={dec} injectHTML={dec.label} {...this.props} />) }

						{ !_profile.init_priority_schools_done && <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

						<div className="action-btns">
							{/* prev btn */}
							{ (_.get(schools, 'length', 0) > 1 && dec_page_index !== 0) && 
								<div 
									onClick={ e => this.setState({dec_page_index: --dec_page_index}) }
									className="prelim-save">Previous</div> }

							{/* next btn */}
							{ (_.get(schools, 'length', 0) !== (dec_page_index+1) && schools.length > 1) && 
								<div 
									onClick={ e => this.setState({dec_page_index: ++dec_page_index}) }
									className="prelim-save">Next</div> }

							{/* save btn */}
							{ _.get(schools, 'length', 0) === (dec_page_index+1) && 
								<SaveButton 
									_profile={_profile}
									page_done={PAGE_DONE} /> }
						</div>

						
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

export default connect(mapStateToProps)(Declarations);