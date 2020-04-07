// dashboard_container.js

import _ from 'lodash'
import React from 'react'
import { connect } from 'react-redux'

import * as actions from './../actions/dashboardActions'

import AdminTopBtn from './adminTopBtn'
import AdminDashBlock from './adminDashBlock'
import AdminToolBlock from './adminToolBlock'
import Loader from './../../utilities/loader'
import Display from './../../utilities/display'
import UpgradeAcctModal from './upgradeAcctModal'
import AdminFeatureBlock from './adminFeatureBlock'
import AdminConferenceBlock from './adminConferenceBlock'
import AdminAnnualMeterBlock from './adminAnnualMeterBlock'
import ThankyouForUpgradingModal from './thankyouForUpgradingModal'
import ExistingMemberReminderModal from './existingMemberReminderModal'
import createReactClass from 'create-react-class'

const Dashboard_Container = createReactClass({
	getInitialState(){
		return {
			periods: ['Annually', 'Quarterly', 'Monthly'],
			periodMap: {
				'Monthly': 12,
				'Quarterly': 4,
				'Annually': 1
			},
			currentPortal: ''
		};
	},

	componentWillMount() {
		let { dispatch, portals } = this.props,
			portalIdentifier = document.getElementById('react-route-to-portal-login-2');

		// if current portal is set and portal name identifier is currently on the DOM, update topnav portal name
		if( portals.current_portal && portals.current_portal.name && portalIdentifier ){
			portalIdentifier.innerHTML = portals.current_portal.name;

		}else if( portalIdentifier ){
			this.state.currentPortal = portalIdentifier.innerHTML.trim();
		}

		dispatch( actions.getDashboardData() );
	},

	goalConfirm() {
		var formData = $(this.refs.form_goal).serialize();
		this.props.dispatch( actions.setGoal(formData) );
	},

	render(){

		var { dispatch, dash, portals } = this.props,
			{ periods, periodMap, currentPortal } = this.state,
			{ appointment_set, conferences, num_of_applications, num_of_enrollments, is_sales, is_superuser, is_aor, show_upgrade_button, show_filter,
				approvedGoal, approvedMonthly, approvedQuarterly, approvedAnnually, approvedMonthlyPerc, approvedQuarterlyPerc, approvedAnnuallyPerc, premier_trial_end_date_ACTUAL,
				current_application_count, current_enrollement_count, application_perc, enrollement_perc, existing_client, expiresIn,
				inquiryCnt, recommendCnt, inquiryCntTotal, recommendCntTotal, approvedCnt, approvedCntTotal, chatCnt, messageCnt, textCnt,
				pendingCnt, pendingCntTotal, startToSchedule, startGoalSetting, activeMeter} = dash,
				currPortal = portals.current_portal.name || currentPortal;

		var inactive = (existing_client === 0 || num_of_applications && num_of_applications < 1 && num_of_enrollments && num_of_enrollments < 1);

		return (
			<div className='row collapse admin-main-dash-container'>

				{ dash.fetching ? <Loader /> : null }

				<div className="column small-12 large-11 small-centered admin-action-bar-container">
					<div className="row collapse">
						<div className="column small-12 text-right">
							<AdminTopBtn revealId="exp-student-modal" imgsrc="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png" text="EXPORT STUDENTS" />
							{ currPortal !== 'General' ?
								<AdminTopBtn href="/admin/filter" imgsrc="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png" text="TARGETING" />
								: null }
							<AdminTopBtn href="/admin/studentsearch" imgsrc="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/magnifier.png" text="ADVANCED SEARCH" />
						</div>

						<div className="column large-9 hide-for-small-only">
							{ conferences ? conferences.map((conference, i) => <AdminConferenceBlock key={conference.name} conference={conference} />) : null }
						</div>

						<div className="column small-12">
							<Display if={ appointment_set && !is_aor }>
								<div className="goal-meters-container">
									<div style={styles.meterContainer}>

										{/* adds a faded layer on top of progress meter if show_upgrade_button is false */}
										{ show_upgrade_button ? <div className="faded"></div> : null }

										<div style={styles.inline}>
											<div className="text-center goal-title">Handshake Goal</div>
											<div className="text-center approvedGoal"><h3>{approvedGoal*periodMap[activeMeter] || 0}</h3></div>
										</div>

										<div style={styles.inline}>
											<div className="clearfix">
												{ periods.map((per, i) => <MeterBtn key={per} activeMeter={activeMeter} period={per} setMeter={ () => dispatch(actions.setGoalMeter(per)) } />) }
											</div>

											{ activeMeter === 'Monthly' ? <ProgressMeter period={'annually'} perc={approvedMonthlyPerc} val={approvedMonthly} /> : null }
											{ activeMeter === 'Quarterly' ? <ProgressMeter period={'quarterly'} perc={approvedQuarterlyPerc} val={approvedQuarterly} /> : null }
											{ activeMeter === 'Annually' ? <ProgressMeter period={'monthly'} perc={approvedAnnuallyPerc} val={approvedAnnually} /> : null }
										</div>

										{ show_upgrade_button ? <div className="premier-end-date-txt">Premier trial ended on {premier_trial_end_date_ACTUAL || ''}</div> : null }

									</div>

									{ activeMeter === 'Annually' ?
										<AdminAnnualMeterBlock
											goal="Application Goal"
											active={activeMeter}
											perc={application_perc}
											num={num_of_applications}
											current_cnt={current_application_count}
											tooltip_content="Plexuss objective is to meet your annual application goal. By establishing your goal our services will adapt and provide targeted recommendations in order to meet and surpass your application expectations." />
										: null
									}

									{ activeMeter === 'Annually' ?
										<AdminAnnualMeterBlock
											goal="Enrollment Goal"
											active={activeMeter}
											perc={enrollement_perc}
											num={num_of_enrollments}
											current_cnt={current_enrollement_count}
											tooltip_content="Indicates how many students you would like to enroll from your engagement on Plexuss. This allows our system to adapt to your needs. Your premiere support representative will work closely with you in order to ensure success." />
										: null
									}

									{ is_superuser ? <div><div className="mod-goals-btn" onClick={() => dispatch( actions.openGoal() )}>Modify Goal</div></div> : null }
								</div>
							</Display>
						</div>

					</div>
				</div>

				<div id='admin_dashboard' className='small-12 medium-12 large-11 small-centered column'>

					{/* if a new admin has not yet set an appointment and is not super user */}
        			<Display if={ _.isFinite(appointment_set) && !appointment_set && !is_superuser }>
    					{ !startToSchedule ?
    						<div id="goal-setting" className="text-center">
								<h3>12 Month Goal</h3>
								<div className="row">
									<div id="start-here"
										 className="button ok-btn text-center start-btn"
										 onClick={() => dispatch( actions.setSchedule() )}>
											Start Here
					          		</div>

					          		{
					          			is_superuser ?
					          			<div id="appointment-confirm"
					          				 onClick={() => dispatch( actions.appointmentSet({'appointment_set': 1}) )}
					          				 className="button ok-btn text-center confirm-btn">
					          					Confirm Appointment
					          			</div>
					          			: null
					          		}
								</div>
							</div> :
							<div id="new-member-reminder-modal" style={{display: 'block'}}>
								<div className="row text-center">
									<h5>{"Welcome to Plexuss, in order to guarantee your success, if you haven't already,"}
										<br / >
										{" please schedule a time with your premiere support representative to establish your enrollment and application goal."}
									</h5>
								</div>
								<div className="row text-center">
									<h5>
										Choose a date by clicking the link below:
									</h5>
								</div>
								<div className="row text-center">
									<a href="https://plexuss.youcanbook.me/service/jsps/cal.jsp?cal=TYajJNYkhp7FodzADaXv" target="_blank">
										<button className="text-center modal-btn">
											<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/calendar_icon.png" alt=""/>Choose date
						      			</button>
						      		</a>
								</div>
							</div>
						}
					</Display>

					{/* only plexuss users coming from sales can see this */}
					<Display if={ is_superuser && startGoalSetting }>
						<form ref="form_goal">
							<div id="goal-setting-context" className={"row"}>

								<div className="column small-12 text-center">
									<h3>12 Month Goal</h3>
								</div>

								<div className="column small-12 text-center">
									<div className="clearfix" style={{maxWidth: '255px', margin: 'auto'}}>
										<div className="left text-center" style={{margin: '0 42px 0 0'}}>
											<input name="num_of_applications"
												   type="number"
												   defaultValue={2}
												   min={0}
												   max={500} />
											<label># of Applications</label>
										</div>

										<div className="left text-center">
											<input name="num_of_enrollments"
												   type="number"
												   defaultValue={2}
												   min={0}
												   max={500} />
											<label># of Enrollments</label>
										</div>
									</div>

									<div className="row">
										<div className="column small-12 medium-offset-4 medium-4 end">
											<label for="prem_start_end_date" className="text-center"><h5>Premier Start/End Date</h5></label>
											<input type="text"
												   name="premier_start_end_date"
												   placeholder="Enter premier start and end date"
												   className="dash-cal"
												   id="prem_start_end_date" />
										</div>
									</div>

									<div id="set-goal-btn" className="button radius" onClick={this.goalConfirm}>Set Goal</div>
								</div>

							</div>
						</form>
					</Display>


					<div className="manage-student-features-container">
						<div>
							<div className="dashboard-section-headers">Manage Students</div>

							<div className="row">
								<AdminDashBlock func="Inquiries" funcid="dash_inquiries" inActive={inactive} url="/admin/inquiries" newCnt={inquiryCnt} totalCnt={inquiryCntTotal} iconurl="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/inquiiries-icon.png" />
								<AdminDashBlock func="Recommended" funcid="dash_recommended" inActive={inactive} url="/admin/inquiries/recommendations" newCnt={recommendCnt} totalCnt={recommendCntTotal} iconurl="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/recommended-icon.png" expiresIn={expiresIn} />
								{ is_aor ? <AdminDashBlock func="Pending" funcid="dash_pending" inActive={inactive} url="/admin/inquiries/pending" newCnt={pendingCnt} totalCnt={pendingCntTotal} iconurl="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/accepted-icon.png" /> : null }
								<AdminDashBlock func="Handshake" funcid="dash_approved" inActive={inactive} url="/admin/inquiries/approved" newCnt={approvedCnt} totalCnt={approvedCntTotal} iconurl="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/accepted-icon.png" />
								{ !is_aor ? <AdminFeatureBlock {...dash} /> : null }
							</div>
						</div>

						<div className="dashboard-section-headers">Engage with Students</div>

						<div className="row">
							{ !is_aor ?<AdminDashBlock func="Go to Chat" funcid="dash_chat" inActive={inactive} url="/admin/chat" newCnt={chatCnt} iconurl="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/chat-db-icon.png" /> : null }
							<AdminDashBlock func="Go to Messages" funcid="dash_messages" inActive={inactive} newCnt={messageCnt} url="/admin/messages" iconurl="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/messages-icon.png" />
							<AdminDashBlock func="Go to Text Messages" funcid="dash_text_messages" inActive={inactive} newCnt={textCnt} url="/admin/textmsg" iconurl="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/text-msg-icon.png" />
						</div>

						<div className="dashboard-section-headers">Tools</div>

						{
							is_aor ?
							<div className="row">
								<AdminToolBlock func="Export Students" funcid="dash_export_stu" revealid="exp-student-modal" />
								<AdminToolBlock func="Targeting" funcid="dash_targeting" url="/admin/filter" />
								<AdminToolBlock func="Advanced Search" funcid="dash_adv_search" url="/admin/studentsearch" />
							</div> :
							<div className="row">
								<AdminToolBlock func="Content Management" funcid="dash_cms" url="/admin/content" hasRoute={true} />
							</div>
						}

						{ _.isFinite(appointment_set) && !appointment_set ? <div className="inactive-layer"></div> : null }

					</div>

					<div className="row">
						<div className="column small-12 supportemail">
							If you find an error or have suggestions, please email us at
							<a href="mailto:collegeservices@plexuss.com"> collegeservices@plexuss.com</a>
						</div>
					</div>

				</div>

				<UpgradeAcctModal />
	            <ThankyouForUpgradingModal />
	            <ExistingMemberReminderModal {...dash} />

			</div>

		);
	}
});

const MeterBtn = createReactClass({
	render(){
		let { setMeter, period, activeMeter } = this.props, meterBtn = styles.meterbtn;

		if( activeMeter === period ) meterBtn = styles.activeMeterBtn;

		return (
			<div style={meterBtn} onClick={setMeter}>
				{ period }
			</div>
		);
	}
});

const ProgressMeter = createReactClass({
	render(){
		let { period, perc, val } = this.props, displayValue = (perc || 0) + '%';

		return (
			<div className={'progress round'} style={{width: '200px'}}>
		  		<span className="meter" style={{width: displayValue}}>
		  			<a href="/admin/inquiries/approved">{val || 0}</a>
		  		</span>
		  		<span className="meter-perc">{displayValue}</span>
			</div>
		);
	}
});

const styles = {
	meterbtn: {
		'float': 'right',
		margin: '0 5px 0 0',
		fontSize: '11px',
		cursor: 'pointer',
	},
	activeMeterBtn: {
		'float': 'right',
		margin: '0 5px 0 0',
		fontSize: '11px',
		cursor: 'pointer',
		fontWeight: '600'
	},
	meterContainer: {
		display: 'inline-block',
		margin: '0 20px 0 0',
		position: 'relative',
	},
	inline: {
		display: 'inline-block',
	},
}

const mapStateToProps = (state, props) => {
	return {
		dash: state.dash,
		portals: state.portals,
	};
};

export default connect(mapStateToProps)(Dashboard_Container);
