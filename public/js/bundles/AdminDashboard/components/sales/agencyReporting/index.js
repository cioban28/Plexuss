// index.js

import React from 'react'

import ReactSpinner from 'react-spinjs-fix'

import DateRangePicker from '../../dateRangePicker'

import { connect } from 'react-redux'

import { bindActionCreators } from 'redux'

import styles from './styles.scss'

import moment from 'moment'

import Notes from './components/notes'

import LoginAsLink from './components/loginAsLink'

import { orderBy } from 'lodash'

import * as agencyReportingActions from '../../../actions/agencyReportingActions.js'

class AgencyReporting extends React.Component {
	constructor(props) {
		super(props);

		this._getAgencyReporting = this._getAgencyReporting.bind(this);
        this._toggleByLastLogin = this._toggleByLastLogin.bind(this);
		this._exportClick = this._exportClick.bind(this);

        this.state = {
            lastLoginSort: 'asc',
        }
	}

	componentDidMount() {
		let { updateAgencyReporting } = this.props,
			start = moment().subtract(30, 'days').format('YYYY/MM/DD'),
			end = moment().format('YYYY/MM/DD');

		updateAgencyReporting(start, end);
	}

    _toggleByLastLogin() {
        const { lastLoginSort } = this.state,
              { agencyReporting, updateData } = this.props,
              report = agencyReporting.report;

        if (!report) return;

        let reportData = report.data;

        reportData = orderBy(reportData, ['last_logged_in'], [lastLoginSort]);

        report.data = reportData;

        this.setState({ lastLoginSort: (this.state.lastLoginSort == 'asc' ? 'desc' : 'asc') });
        updateData({ report });
    }

	_getAgencyReporting() {
		let { dates, agencyReporting, updateAgencyReporting } = this.props;

		updateAgencyReporting(dates.agencyReporting.start_date, dates.agencyReporting.end_date);
	}

	_exportClick() {
		let { dates, agencyReporting } = this.props,
			dateRange = agencyReporting.start_date + ' - ' + agencyReporting.end_date;
		this._exportTableToCSV('sales_agency_reporting.csv', dateRange);
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
		let { agencyReporting } = this.props,
			report = agencyReporting.report,
			table = null;

		if (!report) return null;

		table = report.data.map((agency, index) => {
			return (
				<tr key={index}>
					<td><LoginAsLink link={agency.login_as} /></td>
					<td>{agency.company}</td>
					<td>{agency.agent_name}</td>
					<td>{agency.location}</td>
					<td>{agency.start_date}</td>
					<td>{agency.opportunities > 0 ? <span className='green'>{agency.opportunities}</span> : <span className='red'>{Math.abs(agency.opportunities)}</span>}</td>
					<td>{agency.applications > 0 ? <span className='green'>{agency.applications}</span> : <span className='red'>{Math.abs(agency.applications)}</span>}</td>
					<td>{agency.enrolled > 0 ? <span className='green'>{agency.enrolled}</span> : <span className='red'>{Math.abs(agency.enrolled)}</span>}</td>
					<td>{agency.removed}</td>
					<td>{agency.last_logged_in + ' hours ago'}</td>
					<td><Notes notes={agency.plexuss_note} agency_id={agency.id} /></td>
				</tr>
			)
		});

		return table;
	}

	componentWillReceiveProps(newProps) {
		let { dates, agencyReporting } = this.props,
			{ dates: newDate, agencyReporting: newAgencyReporting } = newProps;
	}

	render() {
		let { agencyReporting } = this.props,
			table_data = this._generateTableData(),

			loading = agencyReporting.agency_reporting_pending || agencyReporting.save_plexuss_note_pending,

			export_btn_classes = 'export-report-btn ' + ( loading ? '' : 'active' );

		return (
			<div>
				<div className='reporting-header'>
					<div className='title'>
						<h3>Reporting</h3>
					</div>

					<div className='right-side-container'>
						<DateRangePicker
							numOfCals={2}
							onSave={ this._getAgencyReporting }
							defaultRangeOption={'future'}
							calType={'range'}
							identifier={'agencyReporting'}
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
								<th>Login as</th>
								<th>Company</th>
								<th>Agent</th>
								<th>Location</th>
								<th>Start Date</th>
								<th>Opportunities</th>
								<th>Completed Apps</th>
								<th>Enrollments</th>
								<th>Removed</th>
								<th style={{cursor: 'pointer'}} onClick={this._toggleByLastLogin}>&#8597; Last Login</th>
								<th>Notes</th>
							</tr>
							{ table_data }
						</tbody>
					</table>
				</div>

				{ loading &&
					<div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		agencyReporting: state.agencyReporting,
		dates: state.dates,
	};
}

const mapDispatchToProps = (dispatch) => {
	return bindActionCreators(agencyReportingActions, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(AgencyReporting);
