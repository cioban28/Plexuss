// actionBar.js

import React from 'react'
import { connect } from 'react-redux'
import { DateRange } from 'react-date-range'

import SearchForCollege from './collegeSearch'
import DateRangePicker from './../../../dateRangePicker'

import { openSearch } from './../../../../actions/pickACollegeActions'
import { setRangeFor_pickACollege } from './../../../../actions/datesActions'
import createReactClass from 'create-react-class'

const INDENTIFIER = 'pickACollege';

const ActionBar = createReactClass({
	getInitialState(){
		return {
			openCal: false,
		};
	},

	_onSaveDate(e){
		let { dispatch, dates } = this.props,
			saveTheDate = {
				start_date: dates.today.dateFormatted,
				end_date: dates.today.dateFormatted,
			};

		// saveTheDate contains todays date by default
		// if date has been set for this identifier, then saveTheDate use that object
		if( dates[INDENTIFIER] ) saveTheDate = dates[INDENTIFIER];

		dispatch( setRangeFor_pickACollege(saveTheDate) );
	},

	render(){
		let { dispatch, pickACollege: p, noDate, noExport } = this.props,
			{ openCal } = this.state;

		return (
			<div id="_pick_college_action_bar">
				<div className="left-actions">
					<div>Pick a College</div>

					<div>
						{
							!p.openSearch ?
							<button
								onClick={ () => dispatch( openSearch(true) ) }
								className="button add">
									<span>{'+'}</span> {'ADD'}
							</button>
							:
							<button
								onClick={ () => dispatch( openSearch(false) ) }
								className="button add close">
									<span>{'-'}</span> {'CLOSE'}
							</button>
						}
					</div>

					<SearchForCollege />
				</div>

				<div className="right-actions">
					{ !noDate && <DateRangePicker
									numOfCals={2}
									onSave={ this._onSaveDate }
									defaultRangeOption={'future'}
									calType={'range'}
									identifier={INDENTIFIER} /> }

					{ !noExport && <button
									onClick={ () => console.log('export') }
									className="button export">EXPORT</button> }
				</div>
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		pickACollege: state.pickACollege,
		dates: state.dates,
	};
}

export default connect(mapStateToProps)(ActionBar);
