import React from 'react';

export default class AddEditModal_Step3 extends React.Component{
	
	constructor(props){
		super(props);
	}

	componentDidMount(){
		let {setInput} = this.props;
	}
	
	render(){
		let {data, prev, next, editProvider, setInput, countries, providers} = this.props;

		return(
			<div>	
				<div className="title2">Provider Information</div>
				<select className="full" defaultValue={data.provider_id} onChange={(e) => setInput("provider_id", e.target.value)}>
					<option value="">New Provider</option>
					{providers.map((item, i) => {
						return <option key={"p"+i} value={item.id}>{item.company_name}</option>
					})}
				</select>

				{( data.provider_id === '' || editProvider) &&
					<div>
						<input className={data.provider_name_valid === false ? 'error': ''}   className='full' name="provider_name" placeholder="Company Name" value={data.provider_name} onChange={(e) => setInput("provider_name", e.target.value)}/>
						{data.scholarship_name_valid === false && <div className="error-txt">valid characters are a-z, 0-9, ., -, _, ', #, : </div> }
						
						<div className="clearfix">
							<div className="left half">
								<input className={data.contact_fname_valid === false ? "error": ""} name="contact_fname" placeholder="Contact First Name"  value={data.contact_fname} onChange={(e) => setInput("contact_fname", e.target.value)}/>
								{data.contact_fname_valid === false && <div className="error-txt">valid characters are a-z, 0-9, ., -, _, ', #, : </div> }
							</div>
							<div className="right half">
								<input className={data.contact_lname_valid === false ? "error" : ""} name="contact_lname" placeholder="Contact Last Name" value={data.contact_lname} onChange={(e) => setInput("contact_lname", e.target.value)}/>
								{data.contact_lname_valid === false && <div className="error-txt">valid characters are a-z, 0-9, ., -, _, ', #, : </div> }
							</div>
						</div>

						<div className="clearfix">
							<div className="left half">
								<input className={data.provider_phone_valid === false ? "error": ""} name="provider_phone" placeholder="Phone" value={data.provider_phone} onChange={(e) => setInput("provider_phone", e.target.value)}/>
								{data.provider_phone_valid === false && <div className="error-txt">valid characters are a-z, 0-9, ., -, _, ', #, : </div> }
							</div>
							<div className="right half">
								<input className={data.provider_email_valid === false ? "error" : ""} name="provider_email" placeholder="Email" value={data.provider_email} onChange={(e) => setInput("provider_email", e.target.value)}/>
								{data.provider_email_valid === false && <div className="error-txt">valid characters are a-z, 0-9, ., -, _, ', #, : </div> }
							</div>
						</div>
						<input className={data.provider_address_valid === false ? 'error full' : 'full'} name="provider_address" placeholder="Address..." value={data.provider_address} onChange={(e) => setInput("provider_address", e.target.value)}/>
						{data.provider_address_valid === false && <div className="error-txt">valid characters are a-z, 0-9, ., -, _, ', #, :  </div>}

						<div className="clearfix">	
							<div className="third left">
								<input className={data.provider_city_valid === false ? "error": ""} name="provider_city" placeholder="City" value={data.provider_city} onChange={(e) => setInput("provider_city", e.target.value)}/>
								{data.provider_city_valid === false && <div className="error-txt">valid characters are a-z, 0-9, ., -, _, ', #, :  </div>}	
							</div>
							<div className="third left">
								<input name="provider_state" placeholder="State" value={data.provider_state} onChange={(e) => setInput("provider_state", e.target.value)}/>
							</div>
							<div className="third right">
								<input className={data.provider_zip_valid === false ? 'error' : ''} name="provider_zip" placeholder="Zip" value={data.provider_zip}  onChange={(e) => setInput("provider_zip", e.target.value)}/>
								{data.provider_zip_valid === false && <div className="error-txt">invalid zip </div>}
							</div>
							<select className="full" name="provider_country" defaultValue={data.provider_country}  onChange={(e) => setInput("provider_country", e.target.value)}>
								{countries.map((item, i) => {
									return <option key={"c"+i} value={item.id} data-code={item.country_code}>{item.country_name}</option>
								})}
							</select>

						</div>
					</div>}	
				</div>
		);
	}
}