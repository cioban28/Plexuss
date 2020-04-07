import React, { Component } from 'react'

class Objective extends Component{
	constructor(props){
		super(props);
	}
	render(){
		let { objective, visible } = this.props;
		return(
			<div className="profile-widgets">
				<div className="widget-heading">
					<h2>Objective</h2>
				</div>
				<div className="widget-content">
					{!!visible ? objective &&
						<ul className="claimfame-list">
							<li>
								<p> I would like to get a/an <strong>{objective.degree_name}</strong> in <strong>{objective.major_name}</strong></p>
							</li>
							<li>
								<p>My dream would be to one day work as a(n) <strong>{objective.profession_name || 'Undecided'}</strong></p>
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
export default Objective;