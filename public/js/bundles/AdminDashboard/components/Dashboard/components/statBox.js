// /Dashboard/components/statBox.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import { initStats } from './../../../actions/dashboardActions'

class StatBox extends Component{
	constructor(props){
		super(props);
	}

	componentWillMount(){
		let { dispatch, stat: _s } = this.props;
		dispatch( initStats(_s.name) );
	}

	render(){
		let { dash, stat: _s } = this.props,
			_val = _s[_s.val_name] || 0,
			_label = _s.label ? _s.label : (_val || 'N/A'),
			pending = dash[_s.name+'_pending'],
			_topStat = !_s.img && _val;

		if( !_s.img ) _topStat += _s.postText;
		if( !_s.label ) _label += _s.postText;

		return (
			<div className={_s.containerClass}>

				{/* tooltip text*/}
				<div className="tooltip-desc">
					<div className="arrow" />
					{ Tips[_s.name] }
				</div>

				{/* top portion of stat box */}
				<div className={'top-stat '+_s.name}>
					{ pending ? <div className="loader" /> : _topStat }
				</div>

				{/* bottom portion of stat box */}
				<div className={'bottom-stat '+_s.name}>
					{ !pending && _label }
					{/* if tooltip prop -- show tooltip icon with tooltip*/}	
					{ (!pending && !!Tips[_s.name]) && <div className="tooltip-qu">?</div> }
				</div>

			</div>
		);
	}
}

const PenHandStatTip = (
		<div>
			<p><span className="bold">Pending</span> refers to the students who meet your targeting criteria and have been notified of your interest to recruit them.</p>
			<p><span className="bold">Handshake</span> refers to students who accept your invitation for further engagement.</p>
			<p>This metric allows you to optimize your brand by setting up more campaigns.</p>
		</div> 
);

const PageviewTip = (
		<div>
			<p>This number indicates the number of students that visit your college page on Plexuss.</p>
		</div>
);

const Tips = {
	'pendingHandshake_stat': PenHandStatTip,
	'pageviews_stat': PageviewTip,
};

const mapStateToProps = (state, props) => {
	return {
		dash: state.dash,
	};
};

export default connect(mapStateToProps)(StatBox);