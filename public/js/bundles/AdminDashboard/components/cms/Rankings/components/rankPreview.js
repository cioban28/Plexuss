// rankPreview.js

import React from 'react'
import { connect } from 'react-redux'
import selectn from 'selectn'
import createReactClass from 'create-react-class'

const RankPreview = createReactClass({
	render(){
		let { rankings } = this.props,
			{ activePin: p } = rankings,
			tmp_img = selectn('image', p) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/'+p.image : null;

		if( selectn('image.name', p) ) tmp_img = URL.createObjectURL(p.image);

		return (
			<div className="build-preview-container column small-12 large-6">
				<div className="preview-window">

					<div className="pin-preview">
						<div className="row">
							<div className="column small-12 text-center">
								{p.title || '[Title of Ranking]'}
							</div>
						</div>

						<div className="row">
							<div className="column small-6">
								<div>RANKED</div>
								<div className="rank-num">#{p.rank_num || 'N/A'}</div>
							</div>
							<div className="column small-6 text-center r_img">
								<img src={tmp_img} alt="Ranking pin logo" />
							</div>
							<div className="column small-12 descript">
								{p.rank_descript || ''}
							</div>
							<div className="column small-12">
								<a href={p.source || ''} target="_blank">See full article</a>
							</div>
						</div>
					</div>

				</div>
				<div className="view-rankings-btn text-center">
					{ rankings.slug || p.slug ?
						<a href={'/college/' + (rankings.slug || p.slug) + '/ranking'} target="_blank">View your rankings</a>
						:
						<span>View your rankings</span>
					}
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

export default connect(mapStateToProps)(RankPreview);
