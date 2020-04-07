import React from 'react'
import { connect } from 'react-redux'

import StatBox from './statBox'
import DashMessage from './dashMessage'
import ImportantMsgModal from './importantMsgModal'
import Carousel from './../../Base/Carousel/carousel'
import DelayRender from './../../Base/DelayShow/delayShow'
import CustomModal from './../../../../utilities/customModal'
import ImageBanner from './../../Base/ImageBannerWOverlay/imageBannerWOverlay'

import { initStats } from './../../../actions/dashboardActions'
import createReactClass from 'create-react-class';
/**************************************************
*	Banner component, used in Dashboard
*	picture in back
*	stats placed in front
*	messge box placed in front
* 	recieves stats ajax call -- action getDashboardData
*	-- subscribed (through parent component) to adminStore.js
**************************************************/

const DashBanner = createReactClass({
	getInitialState(){
		return{
			open: false,
			msgSent: false,
		}
	},

	componentWillMount(){
		let { dispatch } = this.props;
		dispatch( initStats('admin_data') );
		dispatch( initStats('announcements') );
	},

	_sendImportantMessage(e){
		e.preventDefault();

		var _this = this;
		var data = {msg: $('#important_msg_body').val()};

		$('.important-message-notices').html('Sending...');

		$.ajax({
			url:  '/admin/ajax/sendAdminUrgentMatterMsg',
			type: 'POST',
			data: data,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(response){
			_this.setState({msgSent: true});
		});
	},

	render(){
		let { fname,
			  fetching,
			  statsBlocks,
			  member_since,
			  current_status,
			  show_upgrade_button,
			  requested_upgrade } = this.props.dash,
			{ open, msgSent } = this.state;

		/* upgrade button */
		var upgradeCont = '';
		if(this.props.requested_upgrade === 1){
			$('upgradeInProc').show();
			$('upgradeNotice').hide();
			upgradeCont = 	<div className="upgradeInProc">
								<div className="upgradeTitle">Upgrade In Process</div>

								A Plexuss Premier Support
								Representative will be in contact
								with you shortly.
								<br/>
								<a className="button alreadyReq">OK</a>
							</div>;
		}else{
			$('upgradeInProc').hide();
			$('upgradeNotice').show();
			upgradeCont =  	<div className="upgradeNotice">
				 				<div className="upgradeTitle">I am ready to Upgrade </div>


								Notify Premier Support Representative
								<br/>
								<a className="button sendReq">Send</a>
							</div>;
		}

		return (
			<div className="banner-wrapper clearfix">

				{/* banner container -- blur adds strange margin -- container cuts this off */}
				<ImageBanner min="1" max="25" />

				{/* contains all stats */}
				<div className="dashboard-stats-container clearfix">
					<div className=" row dash-centered-row clearfix">

						{/* left side: stats title and details */}
						<div className="column large-5 medium-12 small-12 stats-leftSide">
							<span className="title2 hello-cont">Hi, {fname || 'Loading...'}</span>

							<div className="stats-cur-log-container">
									<DashStats
										titleName="Member Since"
										descTxt={member_since}
										fetching={fetching}/>

									<DashStats
										titleName="Membership Status"
										descTxt={current_status}
										fetching={fetching}
										upgradeBtn={show_upgrade_button}
										reqUp={requested_upgrade}/>

									{/*  Upgrade notification modal */}
									<div className="upgradeNotify-ad">{upgradeCont}</div>
							</div>

							{/* important msg note and button*/}
							<div  id="important_msg_btn">
								<span
									className="exclaim"
									onClick={ e => this.setState({open: true}) }>!</span>

								<span className="exclaim-text">
									For urgent matters, <span className="imp-msg-link" onClick={ e => this.setState({open: true}) }>click</span> to send a message
								</span>
							</div>
						</div>

						{/* right side: stat boxes */}
						<div className="column large-7 medium-12 small-12 stats-rightSide clearfix">
							<div className="row clearfix">
								{ statsBlocks.map(s => <StatBox key={s.name} stat={s} />) }
							</div>

							{/* Annoucements: will show when new announcements */}
							{ !fetching && <DelayRender delayTime={3000}>
												<Carousel numbers={true} dismiss={true} />
											</DelayRender> }
						</div>
					</div>
				</div>{/* end stats container */}

				{/* message box for important messages */}
				{ open && <ImportantMsgModal
							msgSent={ msgSent }
							sendMsg={ this._sendImportantMessage }
							close={ e => this.setState({ open : false}) } /> }
			</div>

		);
	}

});

/*********************************
*   the details under the Stats title
*********************************/
function DashStats(props){
	return (
		<div className="all-in-a-line">
			<div className="desc-bold">{props.titleName || ''}:</div>
			{props.fetching ? <div className="desc-txt">Loading...</div> :
				<div className="desc-txt">{props.descTxt}
				{ props.upgradeBtn && props.upgradeBtn === 0 && props.titleName === 'Membership Status' ?

					<button className="upgrade-btn">
						{ props.reqUp && props.reqUp === 0 ?
							'Upgrade' : 'Upgrade in Progress'
						}
					</button> : ''
				}
				</div>
			}
		</div>
	);
}

const mapStateToProps = (state, props) => {
	return {
		dash: state.dash,
	};
};

export default connect(mapStateToProps)(DashBanner);
