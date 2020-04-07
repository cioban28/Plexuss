// appDeadline.js

import React from 'react'
import selectn from 'selectn'
import moment from 'moment'
import DateRangePicker from './../../../../dateRangePicker'
import createReactClass from 'create-react-class'

import { editHeaderInfo } from './../../../../../actions/internationalActions'

const IDENTIFIER = '_application_deadline';

export default createReactClass({
	componentWillReceiveProps(np){
		let { dispatch, dates, intl } = this.props,
			field = intl.activeProgram+IDENTIFIER,
			prop = 'start_date';

		//if both this and next states for identifier are set and they're not equal, then update
		if( selectn(prop, dates[field]) !== selectn(prop, np.dates[field]) ){
			let obj = {};
			obj[field] = np.dates[field][prop];
			dispatch( editHeaderInfo(obj) );
		}
	},

	_updateDeadline(e){
		let { dispatch } = this.props,
			deadline = {};

		deadline[e.target.getAttribute('name')] = e.target.value;

		dispatch( editHeaderInfo(deadline) );
	},

	_isDeadlineDate(){
		let { intl, dates } = this.props,
			fieldName = intl.activeProgram+IDENTIFIER;

        const momentDate = moment(selectn(fieldName, intl), 'YYYY/MM/DD');

		return momentDate.isValid();;
	},

	render(){
		let { intl, dates } = this.props,
			fieldName = intl.activeProgram+IDENTIFIER,
			deadline_is_date = this._isDeadlineDate();

		return (
			<div>
				<label className="app-deadline">
					<div>Application Deadline</div>

					<label className="deadline-radio">
						<input
							name={ fieldName }
							onChange={ this._updateDeadline }
							checked={ deadline_is_date }
							value={ deadline_is_date ? selectn(fieldName, intl) : dates.today.dateFormatted }
							type="radio" />

						<div className="date-picker-wrapper">
							<DateRangePicker identifier={ fieldName } />
						</div>
					</label>

					<label>
						<input
							name={ fieldName }
							value="rolling_admissions"
							onChange={ this._updateDeadline }
							checked={ !deadline_is_date }
							type="radio" />

						<span>Rolling Admissions</span>
					</label>

				</label>
			</div>
		);
	}
});
