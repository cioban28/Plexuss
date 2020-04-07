import React, { Component } from 'react'

class Occupation extends Component{
	constructor(props){
		super(props);
	}
	render(){
		let { occupation, visible } = this.props;
		return(
			<div className="profile-widgets">
				<div className="widget-heading">
					<h2>Occupation</h2>
				</div>
				<div className="widget-content">
					{!!visible ? occupation &&
						<ul className="claimfame-list">
							<li>
								<p>I currently work as a(n) <strong>{occupation.occupation_name || 'Undecided'}</strong></p>
							</li>
						</ul>
						:
						<span className="private-section">This section is private</span>
					}
				</div>
			</div>
		)
	}
}
export default Occupation;