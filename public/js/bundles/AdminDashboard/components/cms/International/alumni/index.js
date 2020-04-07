// index.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import AddAlumniForm from './components/addAlumniForm'
import AlumniProfileCard from './components/alumniProfileCard'

import { editAlum } from './../../../../actions/internationalActions'

const IntlAlumni = createReactClass({
	_initAlum(){
		return {alum_action: 'set'};
	},

	render(){
		let { dispatch, intl } = this.props;

		return (
			<DocumentTitle title="Admin Tools | International Students | Alumni">
				<div className="row i-container">
					<div className="column small-12 alum">
						<div className="alum-btn" onClick={ () => dispatch( editAlum(this._initAlum()) ) }>Add New International Alumni</div>
					</div>

					<div className="column small-12 medium-7">
						{ selectn('new_alumni', intl) && <AddAlumniForm {...this.props} /> }
					</div>

					<div className="column small-12 medium-5">
						{ selectn('new_alumni', intl) && <AlumniProfileCard _alum={intl.new_alumni || {}} {...this.props} /> }
					</div>

					<div className="column small-12 text-left">
						{ selectn('alumni_list', intl) ?
							intl.alumni_list.map((al) => <div className="alumni-card-wrapper" key={al.id}>
															<AlumniProfileCard editMode={true} _alum={al} {...this.props} />
														</div>)
						: null }
					</div>
				</div>


			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
	};
};

export default connect(mapStateToProps)(IntlAlumni);
