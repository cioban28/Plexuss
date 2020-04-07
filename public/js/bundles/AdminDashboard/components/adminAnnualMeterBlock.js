import React from 'react'
import Tooltip from './../../utilities/tooltip'
import createReactClass from 'create-react-class'

export default createReactClass({
	render() {
		var { goal, tooltip_content, perc, current_cnt, num, active } = this.props;

		return (
			<div style={styles.container}>
				<div style={{display: 'inline-block'}}>
					<div className="text-center goal-title">
						{goal || ''}
						<Tooltip toolTipStyling={styles.ttip} tipStyling={styles.tip}>
							{tooltip_content}
						</Tooltip>
					</div>

					<div className="text-center"><h3>{num? num : 0}</h3></div>
				</div>
				<div style={styles.inline}>
					<div className="progress round" style={styles.meterWidth}>
						<span className="meter" style={{width:""+ (perc? perc : 0) +"%"}}>
							<a href="/admin/inquiries/approved">{current_cnt? current_cnt : 0}</a>
						</span>
				  		<span className="meter-perc">{perc? '' + perc + '%' : '0%' }</span>
					</div>
				</div>
			</div>
		);
	}
});

const styles = {
	container: {
		display: 'inline-block',
		margin: '0 20px 0 0'
	},
	meterWidth: {
		width: '200px'
	},
	inline: {
		display: 'inline-block'
	},
	ttip: {
		color: '#797979',
		border: '1px solid #797979'
	},
	tip: {
		width: '250px'
	}
}
