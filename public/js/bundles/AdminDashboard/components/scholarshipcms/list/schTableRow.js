import React from 'react';
import moment from 'moment';

export default class SchTableRow extends React.Component{
	constructor(props){
		super(props);

		this.state ={
			viewDetails: false
		}
	}

	render(){
		let {viewDetails} = this.state;
		let {item, openEdit, deleteScholarship} = this.props;

		let d = new Date(item.created_at);
		let created_at =  ('0' + d.getDate()).slice(-2) + '/'
             			+ ('0' + (d.getMonth()+1)).slice(-2) + '/'
             			+ d.getFullYear();
		return (
				<div className="reg-row">
					<div className="row-sumarry clearfix">
						<div className="col-name">
							<div className="col-name-name">{item.scholarship_name}</div>
							<div className="col-name-provider">
								Scholarship provided by {item.provider_name}
							</div>
						</div>
						<div className="col-amount">${item.amount}</div>
						<div className="col-due">{item.deadline}</div>
						<div className="col-created">{created_at }</div>
						<div className="col-actions">
							<div className="sch-edit-btn" onClick={openEdit}>edit</div> 
							<div className="sch-del-btn" onClick={() => deleteScholarship(item.id)}>delete</div>
						</div>
					</div>	
					
				</div>

			);
	}
}