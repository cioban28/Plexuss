import React from 'react';
import TinyMCE from 'react-tinymce';
import DatePicker from 'react-datepicker';
import moment from 'moment';

import "react-datepicker/dist/react-datepicker-cssmodules.css";

export default class AddEditModal_Step1 extends React.Component{
	
	constructor(props){
		super(props);
	}
	componentDidMount(){
		let {setInput} = this.props;
	}
	
	render(){
		let {data, next, prev, getDescription, setInput, typeDeadline} = this.props;

		return(
			<div>
				<div className="title2">Scholarship Information</div>
				<div className="clearfix">
					<div className="left-45">
						<input className={data.scholarship_name_valid === false ? 'error': ''} name="scholarship_name" placeholder="Scholarship Name" value={data.scholarship_name} onChange={(e) => setInput("scholarship_name", e.target.value)} />
						{data.scholarship_name_valid === false && <div className="error-txt">valid characters are a-z, 0-9, ., -, _, ', #, : </div> }
						
						<input className={data.submission_id_valid === false ? 'error': ''} name="submission_id" placeholder="Scholarship Submision ID (optional)" value={data.submission_id} onChange={(e) => setInput("submission_id", e.target.value)} />
						{data.submission_id_valid === false && <div className="error-txt">submission id must be numeric </div> }

						<input className={data.website_valid === false ? 'error': ''} name="website" placeholder="Website" value={data.website} onChange={(e) => setInput("website", e.target.value)} />
						{data.website_valid === false && <div className="error-txt">valid characters: a-z, 0-9, ., -, _, ', #, : </div> }

						<input className={data.amount_valid === false ? 'error': ''} name="amount" placeholder="Max Amount (eg 2000 or 2000.00)"  value={data.amount} onChange={(e) => setInput("amount", e.target.value)}/>
						{data.amount_valid === false && <div className="error-txt">invalid amount format </div> }

						<input className={data.numberof_valid === false ? 'error': ''} name="number" placeholder="Number of Awards (eg 10)"  value={data.numberof} onChange={(e) => setInput("numberof", e.target.value)}/>
						{data.numberof_valid === false && <div className="error-txt">number of awards must be numeric</div> }

						<DatePicker
						        selected={data.deadline}
						        onChange={(date) => setInput("deadline", date)}
						        onChangeRaw={(e) => typeDeadline(e.target.value)}
						        className={data.deadline_valid === false ? "error": ""}
						        placeholderText="Deadline (MM/DD/YYYY)"
						        ref={(datepkr) => this.dateinput = datepkr}
						        />
						{data.deadline_valid === false ? <div className="error-txt">invalid deadline</div> : null}

						<select name="reccuring" defaultValue={data.recurring} onChange={(e) => setInput("recurring", e.target.value)}>
							<option value="">Not Recurring</option>
							<option value="1">Monthly</option>
							<option value="2">Yearly</option>
							<option value="3">Biannual</option>
						</select>
						
					</div>
					<div className="right-55">
						<textarea name="description" value={data.description} placeholder="Enter Scholarship description..." onChange={(e) => setInput("description", e.target.value)}/>
					</div>
				</div>
			</div>
		);
	}
}