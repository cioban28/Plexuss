// index.js

import $ from 'jquery'
import React from 'react'
import moment from 'moment'
import selectn from 'selectn'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import { toastr } from 'react-redux-toastr'
import { DateRange, Calendar, defaultRangesFuture, defaultRanges } from 'react-date-range'

import * as actions from './../../actions/datesActions'
import createReactClass from 'create-react-class'

import './styles.scss'

/*
	Customizable:
		- date format
		- number of calendars
		- saving date - pass a function
		- toastr message
*/

const TOASTR_OPTIONS = {
	timeOut: 5000, // by setting to 0 it will prevent the auto close
}

const WARNING_TOASTR_OPTIONS = {
	timeOut: 0,
	iconType: 'warning',
	status: 'warning',
	removeOnHover: false,
}

const GET_NAME = 'getDateFor_';
const DEFAULT_RANGE_WIDTH = 140;
const SINGLE_CAL_WIDTH = 210; //used for when there are 2 or more cals
const LONE_CAL_WIDTH = 280; // used for when there is only one - calendar size is different when alone vs paired w/others for whatever reason

const DateRangePicker = createReactClass({
	getInitialState(){
		return {
			openCal: false,
		};
	},

	componentWillMount(){
		let { dispatch, identifier } = this.props,
			dateActionName = GET_NAME+identifier,
			dateObjName = identifier;

		// if there is an action that dateActionName refers to, call that function
		if( actions[dateActionName] ) dispatch( actions[dateActionName](dateObjName) );

		// event handler to close cal on dom click
		document.addEventListener('click', this._closeCal);
	},

	componentWillUnmount(){
		document.removeEventListener('click', this._closeCal);
	},

	_closeCal(e){
		if( $(e.target).closest('#date-range-picker-container').length === 0 )
			this.setState({openCal: false});
	},

	componentWillReceiveProps(np){
		let { dispatch, identifier } = this.props;

		if( selectn('saved', np.dates) ){

			switch(identifier){

				case 'pickACollege':
					toastr.warning('Warning!', 'Changing the date affects the Pick-a-college on Get Started.', WARNING_TOASTR_OPTIONS);
					break;

				default:
					toastr.success('Success!', 'Date range has been saved.', TOASTR_OPTIONS);
					break;
			}

			dispatch( actions.resetDateSaved() );
		}else if( selectn('err', np.dates) ){
			let msg = np.dates.err_msg || 'Please check to make sure your date range includes proper dates.';
			toastr.error('Error!', msg, TOASTR_OPTIONS);
			dispatch( actions.resetDateSaved() );
		}
	},

	_initDate(){
		//do something on cal init
	},

	_setDateRange(range){
		let { dispatch, identifier, customFormalDateFormat } = this.props,
			rangeObj = {},
			end_date= range.endDate.format( customFormalDateFormat || 'YYYY/MM/DD' ),
			start_date = range.startDate.format( customFormalDateFormat || 'YYYY/MM/DD' );

		rangeObj[identifier] = Object.assign({}, range, {
			end_date,
			start_date,
		});

		dispatch( actions.setRange(rangeObj) );
	},

	_setSingleDate(date){
		let { dispatch, identifier, customFormalDateFormat } = this.props,
			rangeObj = {},
			startDate = date,
			endDate = date,
			end_date= date.format( customFormalDateFormat || 'YYYY/MM/DD' ),
			start_date = date.format( customFormalDateFormat || 'YYYY/MM/DD' );

		rangeObj[identifier] = Object.assign({}, {
			startDate,
			endDate,
			start_date,
			end_date,
		});

		dispatch( actions.setRange(rangeObj) );
	},

	_manualChange(e){
		let { dispatch, dates: d, identifier } = this.props,
			rangeObj = {}, val = e.target.value, range = '';

		if( val && val.indexOf('-') > -1 && val.split(' ').length === 3 ){
			range = val.split(' ');

			rangeObj[identifier] = Object.assign({}, rangeObj, {
				startDate: moment(range[0]),
				endDate: moment(range[2]),
				start_date: range[0],
				end_date: range[2],
			});
		}

		dispatch( actions.setRange(rangeObj) );
	},

	_saveDate(e){
		let { onSave } = this.props;
		this.setState({openCal: false});
		onSave();
	},

	render(){
		let { dates: d, identifier, numOfCals, onSave, defaultRangeOption, calType, initDate } = this.props,
			{ openCal } = this.state,
			_numOfCals = numOfCals || 1,
			containerWidth = 0,
			start_date = d.today.dateFormatted,
			end_date = d.today.dateFormatted,
			thisDatesObj = d[identifier],
			date_val = '',
			default_range_option = null,
			range_option_is_set = 0;

			// if specific identifier is set, use those dates for input value
			if( thisDatesObj ){
				start_date = thisDatesObj.start_date;
				end_date = thisDatesObj.end_date;
			}

			// if 'future', set future default date options
			// if 'past', set past default options
			// else no options
			if( defaultRangeOption === 'future' ) default_range_option = defaultRangesFuture;
			else if( defaultRangeOption === 'past' ) default_range_option = defaultRanges;

			// if a default option is set, it will add 140px to the container
			range_option_is_set = default_range_option ? DEFAULT_RANGE_WIDTH : 0;
			containerWidth = range_option_is_set + (SINGLE_CAL_WIDTH * _numOfCals);

			// if calType is not range, means only a single cal with the ability to only select one date, no range
			if( calType !== 'range' ){
				containerWidth = LONE_CAL_WIDTH;
				date_val = start_date;
			}else{
				date_val = start_date + ' - ' + end_date;
			}

			// initialize date value if initDate is set
			if( initDate ) date_val = initDate;

		return (
			<div id="date-range-picker-container">

				<div className="cal-input-wrapper">
					<input
						id="_dateRange"
						name="dateRange"
						type="text"
						value={ date_val }
						onChange={ this._manualChange }
						onFocus={ () => this.setState({openCal: true}) }
						placeholder="Choose date" />

					{ d.date_set_pending ?
						<div className="save-spinner"><ReactSpinner config={spin} /></div>
						: <div
							onClick={ (e) => this.setState({openCal: !openCal}) }
							className="cal-icon" /> }
				</div>

				<input
					name="start_date"
					value={ thisDatesObj ? thisDatesObj.start_date : '' }
					type="hidden" />

				<input
					name="end_date"
					value={ thisDatesObj ? thisDatesObj.end_date : '' }
					type="hidden" />

				{ openCal ?
					<div className="date-ranger" style={{width: containerWidth+'px'}}>
						{ calType === 'range' ?
							<DateRange
								linkedCalendars={ true }
					            ranges={ default_range_option }
								calendars={ _numOfCals }
			                    onInit={ this._initDate }
			                    onChange={ this._setDateRange }
			                    theme={{
					            	Calendar : { width: 200 },
					            	PredefinedRanges : { marginLeft: 10, marginTop: 10 }
					            }} />
			                :
			                <Calendar
					            date={ now => now }
					            onInit={ this._initDate }
					            onChange={ this._setSingleDate } />
					    }

					    { onSave && <div className="calendar-actions">
					                	<div className="save sprite-item" onClick={ this._saveDate } />
					               	</div> }
			        </div>
	            : null }
            </div>
		);
	}
});

const spin = {
	width: 6,
	length: 15,
	radius: 15,
	scale: 0.25,
	color: '#24b26b'
};

const mapStateToProps = (state, props) => {
	return {
		dates: state.dates,
	};
}

export default connect(mapStateToProps)(DateRangePicker);
