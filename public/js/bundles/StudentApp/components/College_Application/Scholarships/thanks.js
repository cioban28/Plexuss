import React from 'react'
import {connect} from 'react-redux'


class Scholarship_thanks extends React.Component{
	
	constructor(props){
		super(props);
	}

	render(){
		let {route, _profile} = this.props;

		return(
			<div className="_ScholarshipsThanks">

				<div className="congrats-title">Congratulations!</div>

				<div className="congrats-msg">
					Congratulations on submitting your scholarships.
				</div>
				<div className="congrats-msg">
					You can now manage your scholarships and check on the status in your portal.
				</div>

				<div className="mt50">
					<a className="portal-btn" href="/portal/scholarships">Take me to my portal</a>
					<a className="app-btn" href={"/college-application/colleges"}>Continue College Application</a>
					
				</div>

			</div>
		);
	}
};

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile
	};
}

export default connect(mapStateToProps)(Scholarship_thanks);