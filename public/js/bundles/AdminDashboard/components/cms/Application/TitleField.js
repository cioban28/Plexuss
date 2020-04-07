// /Application/Family.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		let { college } = this.props;

		return (
			<div className="prompt">

				<div className="prompt-body">
					<label>Title</label>
					<input
						type="text"
						name={'read_only'}
						value={college.school_name + ' needs more info before you can apply'}
						readOnly={true} />
				</div>

			</div>
		);
	}
});
