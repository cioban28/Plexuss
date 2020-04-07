// create_rankings_container.js

import _ from 'lodash'
import React from 'react'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import Loader from './../../../utils/loader'
import SavedPin from './components/savedPin'
import RankPreview from './components/rankPreview'
import CreateRankForm from './components/createRankForm'

import { getPins, clearForm } from './../../../actions/cmsRankingsActions'

const CreateRankingsContainer = createReactClass({
	render(){
		let { dispatch, user, rankings } = this.props;

		return (
			<DocumentTitle title="Admin Tools | Rankings">
				<div className="ranking-component-container tools-section">

					<div className="ranking-lists-container ranking-component-inner">

						<div className="row ranking-list-header">
							<div className="column small-12 text-center">
								Your Ranking Lists
								<div className="add-new-ranking-btn" onClick={ () => dispatch( clearForm() ) }>
									Add a New Ranking
								</div>
							</div>
						</div>

						<div className="scrolling-list-container">
							{ rankings.pins.map((pin) => <SavedPin key={pin.title} pin={pin} />) }
						</div>

					</div>

					<div className="ranking-build-container ranking-component-inner row">
						<CreateRankForm />
						<RankPreview />
					</div>

					{ rankings.pending ? <Loader /> : null }

				</div>
			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		rankings: state.rankings,
	};
};

export default connect(mapStateToProps)(CreateRankingsContainer);
