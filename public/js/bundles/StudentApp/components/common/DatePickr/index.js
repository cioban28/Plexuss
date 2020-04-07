// index.js

import $ from 'jquery'
import React from 'react'
import selectn from 'selectn'
import { isEmpty } from 'lodash'
import { connect } from 'react-redux'
import { toastr } from 'react-redux-toastr'
import { DateRange, Calendar, defaultRangesFuture, defaultRanges } from 'react-date-range'

import './styles.scss'

/*

	Accepted Props:

	* Required *
	_name: String
	_state: Object - store object
	_action: Function - used to save date to _state
	_format: String - a Moment accepted format (look up Moment)

	* Not required *
	_side: String - only '_left' or '_right' are accepted right now (determines which left/right prop to apply to cal)

*/
class DateRangePicker extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			openCal: false,
		}
		this._closeCal = this._closeCal.bind(this)
		this._manualChange = this._manualChange.bind(this)
		this._setDate = this._setDate.bind(this)
		this._formatForSettingDate = this._formatForSettingDate.bind(this)
	}

	_closeCal(e){
		let { _name } = this.props,
			_id = "#_dateRange_"+_name,
			_class = '.rdr-Calendar';

		if( $(e.target).closest(_id).length === 0 && $(e.target).closest(_class).length === 0 ) {
			this.setState({openCal: false});
		}
	}

	_manualChange(e){
		let { dispatch, _action, _name, _format } = this.props;

		dispatch( _action({
			[_name]: e.target.value.trim(),
			[_name+'_valid']: !!e.target.value,
		}) );
	}

	_setDate(date){
		let { dispatch, _action, _name, _format } = this.props;

		this.state.openCal = false;

		dispatch( _action({
			[_name]: date.format(_format || 'YYYY/MM/DD'),
			[_name+'_valid']: true,
		}) );
	}

	_formatForSettingDate(){
		let { _state, _name } = this.props,
			formatted = '';

		if( !isEmpty(_state[_name]) && _state[_name] != '0000-00-00' ) formatted = _state[_name].split('-').reverse().join('/');

		return formatted;
	}

	componentWillMount(){
		let { dispatch, _name } = this.props;

		// event handler to close cal on dom click
		document.addEventListener('click', this._closeCal);	
	}

	componentWillUnmount(){
		document.removeEventListener('click', this._closeCal);
	}

	render(){
		let { _state, _action, _name, _side, _label } = this.props,
			{ openCal } = this.state,
			_date = this._formatForSettingDate(),
			id = "_dateRange_"+_name,
			invalid = !_state[_name+'_valid'] && _.isBoolean(_state[_name+'_valid']);

        const dateValue = (!isEmpty(_state[_name]) && _state[_name] != '0000-00-00') ? _state[_name] : '';

		return (
			<div id="_datePickr">

				{ _label && <label htmlFor={id}>{ _label }</label> }

				<div className="cal-input-wrapper">
					<input
						id={id}
						name="dateRange"
						type="text"
						className={ invalid && 'has-error' }
						value={ dateValue }
						onChange={ this._manualChange }
						onFocus={ () => this.setState({openCal: true}) }
						placeholder="Format: YYYY-MM-DD" />

					<div 
						onClick={ () => this.setState({openCal: !openCal}) }
						className="cal-icon" />
				</div>

				<div className={"date-ranger "+(_side || '_right')+(openCal ? ' open' : '')}>
	                <Calendar
			            date={ now => _date }
			            onInit={ this._initDate }
			            onChange={ this._setDate } />
		        </div>

		        { invalid && <div className='date-err'>Cannot leave date empty</div> }

            </div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		dates: state.dates,
	};
}

export default connect(mapStateToProps)(DateRangePicker);