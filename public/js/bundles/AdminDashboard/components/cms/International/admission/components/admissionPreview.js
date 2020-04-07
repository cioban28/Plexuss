// admissionPreview.js

import React from 'react'
import moment from 'moment'
import createReactClass from 'create-react-class'

export default createReactClass({
	_formatCost(){
		let { intl, dates, data } = this.props,
			fieldName = '',
			is_amt = false;

		fieldName = intl.activeProgram+'_'+data.name;

		// if deadline is date, format date to casual version, else rolling admission, remove underscore
		// else if admission available, just return that value
		// else it is a number, so format number
		if( data.name === 'application_deadline' ){
			if( intl[fieldName] === 'rolling_admissions' ) return intl[fieldName] ? intl[fieldName].split('_').join(' ') : 'N/A';
			return dates[fieldName] ? dates[fieldName].startDate.format('MMM D, YYYY') : moment(dates.today.dateFormatted).format('MMM D, YYYY');
		}else if( data.name === 'admissions_available' ){
			return intl[fieldName] || 'N/A';
		}else{
			//if field name is application_fee, then preceded a $ before result
			if( fieldName.indexOf('fee') > -1 ) is_amt = true;

			if( intl[fieldName] ){
				//if value is 4 chars or more, add comma after 3rd index starting from last index
				if( (''+intl[fieldName]).length > 3 ){
					var tmp = (''+intl[fieldName]).split('');
					tmp.splice(-3, 0, ',');
					return is_amt ? '$' + (tmp.join('')) : tmp.join('');
				}

				return is_amt ? '$'+intl[fieldName] : intl[fieldName];
			}
		}

		return 'N/A';
	},

	_getPercentage(){
		let { intl, data } = this.props,
			fieldName = intl.activeProgram+'_'+data.name;

		return (Math.ceil((+intl[fieldName] / +intl[intl.activeProgram+'_num_of_applicants'])*100) || 0)+'%';
	},

	render(){
		let { intl, data } = this.props, cost = '', percent = '';

		cost = this._formatCost();
		percent = data.showPercentage ? this._getPercentage() : '';

		return (
			<div className="cost-row-outer">
				<div className={"cost-row-inner admissions " + data.name}>
					<div className="name">{ data.label || '' }</div>
					<div className="cost">{ cost }</div>
				</div>
				{ data.showPercentage ?
					<div className="cost-row-inner admissions">
						<div className="name">{data.showPercentage}</div>
						<div className="cost">{percent}</div>
					</div> : null }
			</div>
		);
	}
});
