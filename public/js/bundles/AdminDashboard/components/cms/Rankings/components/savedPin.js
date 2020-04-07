// savedPin.js

import _ from 'lodash'
import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import { editPin, removePin } from './../../../../actions/cmsRankingsActions'

const SavedPin = createReactClass({
	render(){
		let { dispatch, rankings, pin } = this.props;

		return (
			<div className="row rank-item-container">
				<div className="column small-10 small-centered">

					<div className="rank-title">{pin.title}</div>

					<div className="ranking-item-options-container">
						<div
							className="edit-ranking-pin-btn list-operation"
							onClick={ () => dispatch( editPin(pin) ) }>
								Edit
						</div>

						<div className="list-operation"> | </div>

						<div
							className="remove-ranking-pin-btn list-operation"
							onClick={ () => dispatch( removePin(pin) ) }>
								Remove
						</div>
					</div>

				</div>
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		rankings: state.rankings,
	};
};

export default connect(mapStateToProps)(SavedPin);
