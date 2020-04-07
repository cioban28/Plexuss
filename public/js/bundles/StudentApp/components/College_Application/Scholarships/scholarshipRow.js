import React from 'react';
import { isEmpty, isNil } from 'lodash';

export default class ScholarshipRow extends React.Component{
	
	constructor(props){
		super(props);

		this.state = {
			showDesc: false,
		}
	}

	render(){
		const {showDesc} = this.state;
        const {selectMode, onSelect, item, isSelected} = this.props;
		const {scholarship_name, provider_name, amount, deadline, description, website, ro_id} = item;
        const addButtonClasses = 'sch-col sch-col-add ' + (isSelected === true ? 'yes' : 'no');

		return (

			<div className="sch-table-result-wrapper ">
				<div className="_scholarshipRow sch-table-result clearfix">
					<div className="sch-col sch-col-name">
                        { (!isNil(ro_id) && !isEmpty(website)) 
						      ? <a href={website} target="_blank">
                                    <div className="sch-name sch-linkout">{scholarship_name}</div>
                                </a>

                              : <div className="sch-name">{scholarship_name}</div>
                        }
						<div className="sch-provider">Scholarship provided by {provider_name}</div>

						<div className="sch-view-details" onClick={() => this.setState({showDesc: !showDesc})}>VIEW DETAILS</div> 
						<div className={showDesc ? "sch-details-arrow up" : "sch-details-arrow down"}    onClick={() => this.setState({showDesc: !showDesc})}></div>
					</div>
					<div className="sch-col sch-col-amount">
                        { (amount === 0 || !amount)
                            ? <div className="sch-amount">&nbsp;</div>
                            : <div className="sch-amount">${amount}</div> }
					</div>
					<div className="sch-col sch-col-due">
						<div className="sch-due">{deadline}</div>
					</div>
                    { selectMode === true && 
                        <div className={addButtonClasses}  onClick={() => onSelect(item)}>
                            <div className='sch-add-button'>{ isSelected === true ? 'Added' : '+' }</div>
                        </div> }
					{/* <div className="sch-col sch-col-usd">
						<div className="sch-usd">USD</div>
					</div> */}
				</div>
				
				{showDesc && 
					<div className="sch-result-details-cont"> 
						<div className='sch-desc-title sch-due-mobile'>Deadline</div>
						<div className='sch-desc  sch-due-mobile'>{deadline} </div>
						<div className="sch-desc-title mt20 ">Description</div>
						<div className="sch-desc">
							{description}
						</div>

						
					</div>}
			</div>
		);
	}
}