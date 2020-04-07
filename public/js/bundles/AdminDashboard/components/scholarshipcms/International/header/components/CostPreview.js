// CostPreview.js

import React from 'react'
import createReactClass from 'create-react-class'

export default createReactClass({
	_formatCost(){
		let { intl, data } = this.props, fieldName = '';

		fieldName = intl.activeProgram+'_'+data.name;

		if( intl[fieldName] ){
			//if value is 4 chars or more, add comma after 3rd index starting from last index
			if( (''+intl[fieldName]).length > 3 ){
				var tmp = (''+intl[fieldName]).split('');
				tmp.splice(-3, 0, ',');
				return '$'+tmp.join('');
			}

			return '$'+intl[fieldName];
		}

		return 'N/A';
	},

	render(){
		let { intl, data } = this.props, cost = '';

		cost = this._formatCost();

		return (
			<div className="cost-row">
				<div className="name">{ data.label || '' }</div>
				<div className="cost">{ cost }</div>
			</div>
		);
	}
});
