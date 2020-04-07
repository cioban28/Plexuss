import React, { Component } from 'react'
import ReactDom from 'react-dom'
import { connect } from 'react-redux'
import axios from 'axios'
import moment from 'moment'
import '../styles.scss'

class Cards extends Component {

	render() {
		const { events } = this.props;

		const start_time = (start_time_val) => {
			let H = +start_time_val.substr(0, 2)
			let h = H % 12 || 12
			let ampm = (H < 12 || H === 24) ? " AM" : " PM"
			start_time_val = h + start_time_val.substr(2, 3) + ampm
			return start_time_val
		}

		const end_time = (end_time_val) => {
			let H = +end_time_val.substr(0, 2)
			let h = H % 12 || 12
			let ampm = (H < 12 || H === 24) ? " AM" : " PM"
			end_time_val = h + end_time_val.substr(2, 3) + ampm
			return end_time_val
		}
		const event_date = (start_date_val, end_date_val) => {
			let start_date = moment(start_date_val, 'YYYY-MM-DD').format('MMMM DD, YYYY')
			let end_date = moment(end_date_val, 'YYYY-MM-DD').format('MMMM DD, YYYY')
			let event_date_val
			if(start_date == end_date){
				event_date_val = start_date
			}else{
				event_date_val = start_date+" - "+end_date
			}
			return event_date_val
		}
		const truncate = (str) => {
			if (str.length() > 100)
	        	return str.substr(0, 99);
	        else
	        	return str;
		}

    const ifEventStartOrEndTimeNotEmpty = (startTime, endTime) => startTime !== '00:00:00' && endTime !== '00:00:00';

		return (
				<div className="column small-12 cards" data-reactid=".0.0.2.0.3.0">
					{
						events.length != 0 && events.map((event, index) => (
							<div className="column small-12 medium-6 large-4 event-card" data-reactid=".0.0.2.0.3.0.$0" key={index}>
								<div className="scrollbox" data-reactid=".0.0.2.0.3.0.$0.0">
									<div className="event-content" data-reactid=".0.0.2.0.3.0.$0.0.0">
										<div className="pic_sec" data-reactid=".0.0.2.0.3.0.$0.0.0.0">
											<img src={event.event_image} data-reactid=".0.0.2.0.3.0.$0.0.0.0.0"/>
										</div>
										<div className="taxes_sec" data-reactid=".0.0.2.0.3.0.$0.0.0.1">
											<h1 data-reactid=".0.0.2.0.3.0.$0.0.0.1.0">
												{event.event_title}
											</h1>
										</div>
										<div className="spanner_sec" data-reactid=".0.0.2.0.3.0.$0.0.0.3">
											{
												ifEventStartOrEndTimeNotEmpty(event.event_start_time, event.event_end_time) && <span className='no-margin'>
													<img src="/images/clock.png" data-reactid=".0.0.2.0.3.0.$0.0.0.3.0"/>
													<span data-reactid=".0.0.2.0.3.0.$0.0.0.3.1">
														<span data-reactid=".0.0.2.0.3.0.$0.0.0.3.1.0">
															{event.event_start_time}
														</span>
														<span data-reactid=".0.0.2.0.3.0.$0.0.0.3.1.1">
															-
														</span>
														<span data-reactid=".0.0.2.0.3.0.$0.0.0.3.1.2">
															{event.event_end_time}
														</span>
													</span>
												</span>
											}
											<br data-reactid=".0.0.2.0.3.0.$0.0.0.3.2"/>
											<img src="/images/cal.png" data-reactid=".0.0.2.0.3.0.$0.0.0.3.3"/>
											<span data-reactid=".0.0.2.0.3.0.$0.0.0.3.4">
												{event_date(event.event_start_date, event.event_end_date)}
											</span>
										</div>
										<div className="btn_div" data-reactid=".0.0.2.0.3.0.$0.0.0.4">
											<a target="_blank" href={event.event_url} className="register_btn" data-reactid=".0.0.2.0.3.0.$0.0.0.4.0">
												Register
											</a>
										</div>
									</div>
								</div>
							</div>
							))
					}
				</div>
		);
	}
}

export default Cards;
