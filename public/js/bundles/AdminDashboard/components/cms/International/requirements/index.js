// index.js

import React from 'react'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import ReqPreview from './components/reqPreview'
import ProgramHeader from './../components/programHeader'
import IntlRequirement from './components/intlRequirement'

import { REQUIREMENTS } from './../constants'
import { saveIntlData } from './../../../../actions/internationalActions'

const IntlRequirements = createReactClass({
	_formValid(){
		let { intl } = this.props,
			programs = [intl.program],
			valid = false;

		return valid;
	},

	render(){
		let { intl } = this.props,
			formValid = this._formValid();

		return (
			<DocumentTitle title="Admin Tools | International Students | Requirements">
				<div className="row i-container">
					<div className="column small-12 medium-7">

						<ProgramHeader />

						<div className="intl-reqs">
							{ REQUIREMENTS.map((r) => <IntlRequirement key={r.name} req={r} {...this.props} /> ) }
						</div>

					</div>

					<div className="column small-12 medium-5">

						<div className="intl-reqs-preview">
							{ REQUIREMENTS.map((r) => <ReqPreview key={r.name} req={r} {...this.props} />) }
						</div>

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

export default connect(mapStateToProps)(IntlRequirements);
