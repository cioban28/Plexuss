// index.js

import React from 'react'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'
import ReactSpinner from 'react-spinjs-fix';
//import EditModal from './editModal';
import SchTableRow from './schTableRow';


import {sortCol, getAllScholarships, deleteScholarship} from './../../../actions/scholarshipscmsActions';

class SchTable extends React.Component{
	constructor(props){
		super(props);

		this.state = {
			editModal: false,
			editItem: null,
			Targetdata: null
		}

		this._sortHandler = this._sortHandler.bind(this);
		this._edit = this._edit.bind(this);
		this._deleteScholarship = this._deleteScholarship.bind(this);

	}

	componentWillMount(){
		let {dispatch} = this.props;

		dispatch(getAllScholarships());
	}

	_sortHandler(direction, type){
		let {dispatch} = this.props;

		if(typeof type === "undefined" || type === '' || type === null)
			return;

		dispatch(sortCol(direction, type));
	}
	_edit(item){
		this.setState({editModal: true, editItem: item});
	}

	_deleteScholarship(id){
		let {dispatch} = this.props;
		dispatch(deleteScholarship(id));
	}

	render(){
		let {editModal, editItem} = this.state;
		let {dispatch,scholarships} = this.props;

		return (
			<DocumentTitle title="Admin Tools | Scholarship Management">
				<div className="_schTable">
				<div className="header-row clearfix">
					<div className="col-name">
						<div className="sch-sort-up" onClick={() => this._sortHandler("asc", "name")}></div>
						<div className="sch-sort-down" onClick={() => this._sortHandler("desc", "name")}></div>
						Name of Scholarship
					</div>
					<div className="col-amount">
						<div className="sch-sort-up" onClick={() => this._sortHandler("asc", "amount")}></div>
						<div className="sch-sort-down" onClick={() => this._sortHandler("desc", "amount")}></div>
						Amount
					</div>
					<div className="col-due">
						<div className="sch-sort-up" onClick={() => this._sortHandler("asc", "due")}></div>
						<div className="sch-sort-down" onClick={() => this._sortHandler("desc", "due")}></div>
						Deadline
					</div>
					<div className="col-created">
						<div className="sch-sort-up" onClick={() => this._sortHandler("asc", "created")}></div>
						<div className="sch-sort-down" onClick={() => this._sortHandler("desc", "created")}></div>
						Created At
					</div>
					<div className="col-actions">
						Actions
					</div>
				</div>

				<div className="abs-wrapper">
					{(scholarships.get_sch_pending || scholarships.search_sch_pending ) && <div className="spin-container"><ReactSpinner color="#24b26b" /></div>}
					{scholarships.scholarshipsList.length < 1 && !scholarships.get_sch_pending && !scholarships.search_sch_pending &&
						<div className="no-sch">No Scholarships Found</div>
					}

					{scholarships.scholarshipsList.map((item, i) => {
						return <SchTableRow openEdit={() => this._edit(item)}
										    deleteScholarship={this._deleteScholarship}
											item={item}
											key={'sch'+ i} />
					})}
				</div>
				{editModal && <EditModal item={editItem} close={() => this.setState({editModal: false})} />}

			</div>
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		scholarships: state.scholarships,
	};
};

export default connect(mapStateToProps)(SchTable);
