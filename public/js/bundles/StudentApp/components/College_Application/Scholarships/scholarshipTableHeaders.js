import React from 'react';

export default class ScholarshipTableHeaders extends React.Component{
	constructor(props){
		super(props);
	}

	render(){
        const { selectMode } = this.props;

		return (
			<div className="_scholarshipTableHeaders sch-table-headers clearfix">
				<div className="sch-col sch-col-name">
					<div className="sch-sort-arrows" data-col="name"><div className="sch-sort-up"></div><div className="sch-sort-down"></div></div>Name
				</div>
				<div className="sch-col sch-col-amount">
					<div className="sch-sort-arrows" data-col="amount"><div className="sch-sort-up"></div><div className="sch-sort-down"></div></div>Amount
				</div>
				<div className="sch-col sch-col-due">
					<div className="sch-sort-arrows"  data-col="due"><div className="sch-sort-up"></div><div className="sch-sort-down"></div></div>Deadline
				</div>
                { selectMode === true &&
                    <div className="sch-col sch-col-due">
                        <div className="sch-sort-arrows" data-col="add"><div className="sch-sort-up"></div><div className="sch-sort-down"></div></div>Add
                    </div> }
				{/*<div className="sch-col sch-col-usd sch-usd-dropdown-btn">
					{/*<div className="sch-drop-down-arrow"></div>**}<span className="sch-usd-img">$</span><span className="sch-usd-txt">USD</span>
					<div className="sch-usd-dropdown">
						<div>USD</div>
						<div>CURR1</div>
						<div>CURR2</div>
						<div>CURR3</div>
						<div>CURR4</div>
						<div>CURR5</div>

					</div>
				</div> */}

			</div>
		)
	}
}