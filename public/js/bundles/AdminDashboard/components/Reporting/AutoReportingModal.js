import React, { Component } from 'react';
import styles from './styles.scss';
import moment from 'moment';
import DateTimePicker from 'react-datetime'
import Tooltip from 'react-tooltip'
import { isEqual } from 'lodash'

export default class AutoReportingModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            date: moment().format('YYYY/MM/DD'),
            time: '12:00 AM',
            date_valid: true,
            time_valid: true,
        }
    }

    componentWillReceiveProps(newProps) {
        const { reporting, onClose } = this.props,
            { reporting: newReporting } = newProps;

        if (!isEqual(reporting.auto_report_save_pending, newReporting.auto_report_save_pending) && !newReporting.auto_report_save_pending && newReporting.auto_report_save_status === 'success')
            onClose();

    }

    _onChange(key, value, format) {
        if (moment.isMoment(value)) {
            value = value.format(format);
        }

        this.setState({
            [key]: value,
            [key + '_valid']: this._validate(value, format),
        });
    }

    _validate(value, format) {
        const date = moment(value, format, true);

        return date.isValid();
    }

    _submitAutoReporting = () => {
        // Submit reporting
        const { reporting, saveAutoReporting } = this.props, 
              { date, time } = this.state,
              user_id = reporting.user_id;

        mixpanel.track("Admin_CRM_Reporting_Toggle_On");

        saveAutoReporting({ user_id, date, time });
    }

    _renderCalendarInput = (props, openCalendar) => {
        const { date_valid } = this.state;

        return (
            <div className='calendar-input-container'>
                <input {...props} className={date_valid === false ? 'input-invalid' : ''} placeholder="YYYY/MM/DD" value={this.state.date} />
                <div className='calendar-icon-button' onClick={openCalendar}><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/calendar-icon-50.png"/></div>
            </div>
        );
    }

    _renderClockInput = (props, openCalendar) => {
        const { time_valid } = this.state;

        return (
            <div className='calendar-input-container'>
                <input {...props} className={time_valid === false ? 'input-invalid' : ''} placeholder="12:00 AM" value={this.state.time} />
                <div className='calendar-icon-button' onClick={openCalendar}><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/clock-icon-50.png"/></div>
            </div>
        );
    }

    render() {
        const { onClose, reporting } = this.props,
            { time_valid, date_valid } = this.state,
            isValid = time_valid && date_valid;

        return (
            <div className='auto-reporting-modal'>
                <div className="modal-container">
                    <div className="close-button report-button" onClick={ onClose }>&times;</div>
                    <h5>
                        Choose time for reporting
                        <a className='auto-reporting-tooltip' data-tip data-for='auto-report-tooltip'>?</a>
                        <Tooltip id='auto-report-tooltip'>
                          <span>Starting from the date you selected, we will send you daily CRM reports</span>
                        </Tooltip>
                    </h5>
                    <div>
                        <div className='calendar-input-header'>Start Date</div>
                        <DateTimePicker onChange={(value) => this._onChange('date', value, 'YYYY/MM/DD')} closeOnSelect={true} renderInput={this._renderCalendarInput} timeFormat={false} />
                    </div>
                    <div>
                        <div className='calendar-input-header'>Time</div>
                        <DateTimePicker onChange={(value) => this._onChange('time', value, 'hh:mm A')} renderInput={this._renderClockInput} dateFormat={false} />
                    </div>
                    <div className={'auto-reporting-submit-button report-button' + (isValid ? '' : ' form-invalid')} onClick={isValid ? this._submitAutoReporting : null}>
                        Schedule Auto Reporting
                    </div>
                </div>
            </div>
        )
    }
}