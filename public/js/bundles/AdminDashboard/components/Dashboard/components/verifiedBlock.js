// /Dashboard/components/verifiedBlock.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import Tooltip from './../../../../utilities/tooltip'

import { initStats } from './../../../actions/dashboardActions'
import { veriInnerTooltip, verifiedInnerTip, THOUSAND } from './../constants'

class VerifiedBlock extends Component{
	constructor(props){
		super(props);
		this._formatCount = this._formatCount.bind(this);
	}

	componentWillMount(){
		let { dispatch, block: b } = this.props;
		dispatch( initStats(b.name) );
	}

	_formatCount(val){
		var intVal = parseInt(val);
		
		if( val == '0' || intVal < THOUSAND ) return val;

		return ((intVal / THOUSAND).toFixed(1)) + 'K';
	}

	render(){
		let { dash, block: b } = this.props,
			pending = dash[b.name+'_pending'],
			newName = b.name+'Cnt',
			totalName = b.name+'CntTotal',
			_new = this._formatCount(b[newName] || '0'),
			_total = this._formatCount(b[totalName] || '0');

		return (
			<div className="verified-container">
				<a href={ b.route }>
					<div className="verfied-cont-container">
						<div className="total-count">
							{ pending ? <div className="loader total" /> : _total } Total
						</div>

						{/* icon on left side */}
						<div className={'verified-icon '+b.name} />
						
						<div className="veri-rightside">
							{/* number of new */}
							<div className="large-num">
								{ b.noNew ? 
									<span>&nbsp;</span> : 
									<span>{ pending ? <div className="loader" /> : _new }</span>
								}
							</div>

							{/* the title and tooltip */}
							<div className="veri-title">
								{b.label || 'None'}
								<VerifiedTip {...b} />
							</div>
						</div>
					</div>
				</a>
			</div>
		);
	}
}

const VerifiedTip = (props) => {
	let { tip_name, tip } = props;

	return (
		<Tooltip toolTipStyling={veriInnerTooltip} tipStyling={verifiedInnerTip}>
			<div className="veri-inner-tip">
				<div className="veri-inner-arrow"></div> 
				<h5><span className="verified-check-white">&#10003;</span> {tip_name || ''}</h5>
				<p>{tip || ''}</p>
			</div>
		</Tooltip>
	);
}

const mapStateToProps = (state, props) => {
	return {
		dash: state.dash,
	};
};

export default connect(mapStateToProps)(VerifiedBlock);