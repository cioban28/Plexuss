// index.js

import React from 'react'

import ReactSpinner from 'react-spinjs-fix'

import DateRangePicker from '../dateRangePicker'

import { connect } from 'react-redux'

import { bindActionCreators } from 'redux'

import styles from './styles.scss'

import moment from 'moment'

import { orderBy, isEmpty } from 'lodash'

import AutoReportingModal from './AutoReportingModal'

import * as reportingActions from '../../actions/reportingActions.js'

import Tooltip from 'react-tooltip'

class Email extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            openAutoReportingModal: false,
        }
    }

    componentDidMount() {
        const { updateReporting, updateData } = this.props,
            user_id = document.getElementById('AdminDashboard_Component').dataset.user_id,
            dateRange = this._getInitialDates();

        mixpanel.track("Admin_CRM_Reporting_Show");

        updateReporting(dateRange);
        updateData({ user_id });
    }

    _updateSort = (columnName) => {
        const { updateReportingOrder, reporting } = this.props;

        if (isEmpty(reporting.report)) {
            return;
        }

        updateReportingOrder(columnName);
    }

    _getInitialDates() {
        const initialDateRange = {};

        // initialDateRange.start_date = moment().subtract(1, 'days').format('MM/DD/YYYY');
        initialDateRange.start_date = moment().format('MM/DD/YYYY');
        initialDateRange.end_date = moment().format('MM/DD/YYYY');

        return initialDateRange;
    }

    _getReporting = () => {
        const { dates, reporting, updateReporting } = this.props,

            dateRange = {
                start_date: moment(dates.reporting.start_date, 'MM/DD/YYYY').format('YYYY/MM/DD'),
                end_date: moment(dates.reporting.end_date, 'MM/DD/YYYY').format('YYYY/MM/DD'),
            }

        mixpanel.track('Admin_CRM_Reporting_Change_Dates', {
            'Begin Date': dateRange.start_date,
            'End Date': dateRange.end_date,
        });

        updateReporting(dateRange);
    }

    _exportClick = () => {
        let { dates } = this.props,
            dateRangeString = null;

        if (dates.reporting) {
            dateRangeString = dates.reporting.start_date + ' - ' + dates.reporting.end_date;

            mixpanel.track("Admin_CRM_Reporting_Export", {
                'Begin Date': dates.reporting.start_date,
                'End Date': dates.reporting.end_date,
            });
        }

        if (!dateRangeString) {
            const dateRange = this._getInitialDates();
            dateRangeString = dateRange.start_date + ' - ' + dateRange.end_date;

            mixpanel.track("Admin_CRM_Reporting_Export", {
                'Begin Date': dateRange.start_date,
                'End Date': dateRange.end_date,
            });
        }

        this._exportTableToCSV('crm_reporting.csv', dateRangeString);
    }

    _exportTableToCSV(filename, dateRange) {
        let csv = [],
            rows = document.querySelectorAll('.reporting-wrapper table tr'),
            row_string = '';

        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 1; j < cols.length; j++) {
                if (typeof cols[j].innerText == 'string')
                    row.push(cols[j].innerText.replace(',', ';'));
            }

            row_string = row.join(",");

            csv.push(row_string);
        }

        csv.push(null, dateRange);

        this._downloadCSV(csv.join("\n"), filename);
    }

    _downloadCSV(csv, filename) {
        let csvFile;
        let downloadLink;

        csvFile = new Blob([csv], {type: "text/csv"});
        downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);

        downloadLink.click();
    }

    _generateTableData() {
        let { reporting } = this.props,
            report = reporting.report,
            table = null;

        if (!report) return null;

        table = report.map((agent, index) => {
            return (
                <tr key={index}>
                    <td>{agent.name}</td>
                    <td>{agent.total_calls}</td>
                    <td>{agent.completed_calls}</td>
                    <td>{agent.avg_duration}</td>
                    <td>{agent.total_duration}</td>
                    <td>{agent.total_texts}</td>
                    <td>{agent.sent_texts}</td>
                    <td>{agent.received_texts}</td>
                </tr>
            )
        });

        return table;
    }

    _buildSortButton = (columnName) => {
        return <span className='sort-icon' onClick={() => this._updateSort(columnName)} />
    }

    render() {
        let { reporting, dates, updateData, saveAutoReporting } = this.props,
            { openAutoReportingModal } = this.state,
            table_data = this._generateTableData(),
            loading = reporting.report_pending || reporting.auto_report_save_pending,
            dateRange = dates.reporting ? dates.reporting : this._getInitialDates(),
            current_date_range = dateRange.start_date + ' - ' + dateRange.end_date,
            export_btn_classes = 'export-report-btn ' + ( loading ? '' : 'active' );

        return (
            <div>
                <div className='admin-reporting-header'>
                    <div className='header-container'>
                        <h3>CRM Web Reporting</h3>
                        <div className='auto-reporting-button'
                             onClick={() => this.setState({ openAutoReportingModal: true })}>
                                Turn on Auto Reporting

                                <a className='auto-reporting-tooltip' data-tip data-for='auto-reporting-tooltip'>?</a>
                                <Tooltip id='auto-reporting-tooltip' type='info'>
                                  <span>Receive reoccurring automatic emails related to your CRM report</span>
                                </Tooltip>
                        </div>
                    </div>

                    <div className='right-side-container'>
                        <DateRangePicker
                            initDate={current_date_range}
                            customFormalDateFormat={'MM/DD/YYYY'}
                            numOfCals={2}
                            onSave={ this._getReporting }
                            defaultRangeOption={'future'}
                            calType={'range'}
                            identifier={'reporting'}
                        />

                        <div className={ export_btn_classes } onClick={ loading ? () => {} : this._exportClick }>
                            <div className='export-icon'></div>
                            <div className='export-text'>EXPORT</div>
                        </div>
                    </div>
                </div>

                <div className='reporting-wrapper'>
                    <table>
                        <tbody>
                            <tr>
                                <th>{this._buildSortButton('name')}Agent</th>
                                <th>
                                    {this._buildSortButton('total_calls')}# of Calls
                                     <a className='auto-reporting-tooltip' data-tip data-for='total-calls-tooltip'>?</a>
                                    <Tooltip id='total-calls-tooltip' type='info'>
                                      <span>Total number of attempted calls made by the admin</span>
                                    </Tooltip>
                                </th>
                                <th>
                                    {this._buildSortButton('completed_calls')}Completed Calls
                                    <a className='auto-reporting-tooltip' data-tip data-for='completed-calls-tooltip'>?</a>
                                    <Tooltip id='completed-calls-tooltip' type='info'>
                                      <span>Number of successful calls made by the admin</span>
                                    </Tooltip>
                                </th>
                                <th>
                                    {this._buildSortButton('avg_duration')}Avg. Duration
                                    <a className='auto-reporting-tooltip' data-tip data-for='avg-duration-tooltip'>?</a>
                                    <Tooltip id='avg-duration-tooltip' type='info'>
                                      <span>Average duration from calls made by the admin in HH:mm:ss format</span>
                                    </Tooltip>
                                </th>
                                <th>
                                    {this._buildSortButton('total_duration')}Total Duration
                                    <a className='auto-reporting-tooltip' data-tip data-for='total-duration-tooltip'>?</a>
                                    <Tooltip id='total-duration-tooltip' type='info'>
                                      <span>Total duration from calls made by the admin in HH:mm:ss format</span>
                                    </Tooltip>
                                </th>
                                <th>{this._buildSortButton('total_texts')}# of Texts</th>
                                <th>{this._buildSortButton('sent_texts')}Texts Sent</th>
                                <th>{this._buildSortButton('received_texts')}Texts Received</th>
                            </tr>
                            { table_data }
                        </tbody>
                    </table>
                </div>

                { loading &&
                    <div className="spin-container" style={{ zIndex: 2000 }}><ReactSpinner color="#24b26b" /></div> }

                { openAutoReportingModal &&
                    <AutoReportingModal
                        updateData={updateData}
                        saveAutoReporting={saveAutoReporting}
                        reporting={reporting}
                        onClose={() => this.setState({ openAutoReportingModal: false })} /> }

            </div>
        );
    }
}

const mapStateToProps = (state, props) => {
    return {
        reporting: state.reporting,
        dates: state.dates,
    };
}

const mapDispatchToProps = (dispatch) => {
    return bindActionCreators(reportingActions, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(Reporting);
