import React from 'react';
import TinyMCE from 'react-tinymce';
import DatePicker from 'react-datepicker';
import moment from 'moment';

import "react-datepicker/dist/react-datepicker-cssmodules.css";

export default class AddEditModal_Step1 extends React.Component{
	
	constructor(props){
		super(props);
		this.state = {
			id: '',
			scholarship_name: '',
			scholarshipsub_name: '',
			amount: '',
			description: '',
			deadline: null,
			scholarship_name_valid: '',
			deadline_valid: true,
			amount_valid: '',
			filter:[],
			
		}
	}
	componentDidMount(){
		let {setInput,data} = this.props;
		//console.log(moment(data.deadline, 'MM/DD/YYYY'));
		/*console.log(data.scholarship_name);
		setInput("scholarship_name", data.scholarship_name);
		setInput("scholarshipsub_name", data.scholarshipsub_name);
		setInput("amount", data.amount);
		setInput("description", data.description);
		setInput("deadline", data.deadline);
		setInput("filter", data.filter);
		setInput("id", data.id);*/
	}
	
	componentWillMount(){
		let {setInput,data,typeDeadline,getDescription} = this.props;
		
		if(data.id!=''){ setInput("id", data.id);}
		//if(data.scholarship_name!=''){ setInput("scholarship_name", data.scholarship_name);}
		//if(data.scholarshipsub_name!=''){ setInput("scholarshipsub_name", data.scholarshipsub_name);}
		//if(data.amount!=''){ setInput("amount", data.amount);}
		//if(data.description!=''){ getDescription("description", data.description);}
		//if(data.deadline!=''){ setInput("deadline", data.deadline);}
	}
	
	render(){
		let {data,mystate,getDescription, setInput, typeDeadline} = this.props;
		let datep = null;
		if(data.deadline!= null){
			datep = moment(data.deadline, 'MM/DD/YYYY');
			
		}
		
		return(
			
			<div>
					<div className="clearfix">
					<div className="right-45">
						<TinyMCE ref={'content'}
								className="tinymce-editor"
								content={data.description ? data.description : mystate.description}
								config={{
									plugins: 'autolink link image lists print preview textcolor colorpicker',
								    toolbar: 'undo redo | forecolor backcolor | bold italic | link image | alignleft aligncenter alignright'
								}}
							onChange={(e) => getDescription(e)} />
					</div>
					</div>
					
					<br />
					<div>
						<label>
							Title
							<input name="scholarship_name" className={data.scholarship_name_valid === false ? 'error': ''} placeholder="" className="full" defaultValue={data.scholarship_name ? data.scholarship_name : mystate.scholarship_name} onChange={(e) => setInput("scholarship_name", e.target.value)}/>
							{data.scholarship_name_valid === false && <div className="error-txt">valid characters are a-z, 0-9, ., -, _, ', #, : </div> }
						</label>
					</div>
					<br />
					<div>
						<label>
							Subtitle
							<input name="scholarshipsub_name" className="full" placeholder="" onChange={(e) => setInput("scholarshipsub_name", e.target.value)} defaultValue={data.scholarshipsub_name ? data.scholarshipsub_name : mystate.scholarshipsub_name} />
						</label>
					</div>
					
					<br />
					<div className="clearfix">
						<div className="left half">
							<label>
								Amount
								<input name="amount" className={data.amount_valid === false ? 'error': ''} placeholder="" defaultValue={data.amount ? data.amount : mystate.amount} onChange={(e) => setInput("amount", e.target.value)} />
								{data.amount_valid === false && <div className="error-txt">invalid amount format </div> }
							</label>
						</div>
						<div className="right half">
							<label>
								Deadline
								<DatePicker
									selected={datep}
									onChange={(date) => setInput("deadline", date)}
									onChangeRaw={(e) => typeDeadline(e.target.value)}
									className={data.deadline_valid === false ? "error": ""}
									placeholderText="Deadline (MM/DD/YYYY)"
									ref={(datepkr) => this.dateinput = datepkr}
						        />
								{data.deadline_valid === false && <div className="error-txt">invalid deadline</div>}
							</label>	
						</div>
					</div>
					
				
			</div>
		);
	}
}