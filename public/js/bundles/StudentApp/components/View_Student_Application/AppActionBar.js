// /View_Student_Application/index.js

import React from 'react'
import { Link } from 'react-router'

import './actionBarStyles.scss'

export default React.createClass({
	render(){
		let { su } = this.props;

		return (
			<div id="_AppActionBar">
				<div>
					<div className="action" onClick={ () => window.close() }>Close</div>
					<div>Viewing Application</div>
				</div>

				<div>
					{ _.get(su, 'is_plexuss', false) ? <div className="action"><Link to="/college-application/">Edit Application</Link></div> : null }
					{/*<div className="action pdf"><a href={'/generatePDF?url='+window.location.href} target="_blank">Download as PDF</a></div>*/}
				</div>
			</div>
		);
	}
});