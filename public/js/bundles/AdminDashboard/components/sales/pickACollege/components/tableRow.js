// tableRow.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import { toastr } from 'react-redux-toastr'
import createReactClass from 'create-react-class'

import Tooltip from './../../../../../utilities/tooltip'
import CustomModal from './../../../../../utilities/customModal'

import { saveChanges, removeSchool, resetSavedRow } from './../../../../actions/pickACollegeActions'

const toastrOptions = {
	timeOut: 5000, // by setting to 0 it will prevent the auto close
};

const TableRow = createReactClass({
	getInitialState(){
		return {
			editMode: false,
			showModal: false,
		};
	},

	componentWillMount(){
		document.addEventListener('click', this._editModeOff);
	},

	componentWillUnmount(){
		document.removeEventListener('click', this._editModeOff);
	},

	componentWillReceiveProps(np){
		let { dispatch, row } = this.props;

		// if the most recently updated row is this instance, proceed
		if( selectn('recently_edited_school_id', np.pickACollege) === row.college_id ){
			let id = np.pickACollege.recently_edited_school_id,
				nextRow = null,
				msg = 'Changes have been successfully saved.';

			if( selectn('prioritySchools', np.pickACollege) ){
				nextRow = _.find(np.pickACollege.prioritySchools, {college_id: id});
			}

			// if nextRow is found, proceed to showing success/err msg and then resetting
			if( nextRow ){
				if( nextRow.saved ){
					msg = nextRow.saved_msg || msg;
					toastr.success('Success!', msg, toastrOptions);
					//dispatch( resetSavedRow(id) );
				}else if( nextRow.err ){
					let msg = nextRow.err_msg || msg;
					toastr.error('Error!', msg, toastrOptions);
					dispatch( resetSavedRow(id) );
				}
			}
		}
	},

	// shouldComponentUpdate(np, ns){
	// 	// console.log('np: ', np);
	// 	// console.log('ns: ', ns);
	// 	// return true;
	// 	let { pickACollege: p } = ns,
	// 		{ row } = this.props;

	// 	// if prioritySchools is set and recently_edited_school_id is set and is equal to this row's id, then update
	// 	if( selectn('prioritySchools', np.pickACollege) && selectn('recently_edited_school_id', np.pickACollege) === row.college_id ){
	// 		console.log('np id: ', np.pickACollege.recently_edited_school_id);
	// 		console.log('row id: ', row.college_id);
	// 		console.log('---------------------');
	// 		return true;
	// 	}

	// 	return false;
	// },

	_editModeOff(e){
		let { row } = this.props,
			id = e.target.id;

		// if clicked elem is not the contract or goal field and editMode is true, close it
		if( id !== '_contract_'+row.college_id &&
			id !== '_goal_'+row.college_id &&
			id !== '_save_'+row.college_id &&
			id !== '_edit_'+row.college_id &&
			id !== '_remove_'+row.college_id &&
			this.state.editMode ){
				this.setState({editMode: false});
		}
	},

	// save instant edits to this instance's state
	_editCol(e){
		let newState = {};

		newState[e.target.getAttribute('name')] = +e.target.value;
		this.setState(newState);
	},

	// triggered by save btn click -
	// creates new object based on row and what is saved in this instance's state for contract and goals
	_saveChanges(){
		let { dispatch, row, pickACollege: p } = this.props,
			rowData = _.omit(this.state, 'editMode'),
			newRow = Object.assign({}, row, rowData),
			type = null;

		type = _.find(p.contractTypes, {id: +newRow.contract});

		//if type if found (it should), save the new contract name
		if( type ) newRow.contract_name = type.name;

		this._resetState(); //reset current state values
		dispatch( saveChanges(newRow) ); //save to store
		this.setState({editMode: false}); //turn off edit mode
	},

	// close the modal and send this row to be deleted from list
	_removeSchool(){
		let { dispatch, row } = this.props;
		this.setState({showModal: false});
		dispatch( removeSchool(row) );
	},

	//using this components state as a cache because nothing gets saved until the save
	//button is clicked, so _resetState gets triggered after save button click
	_resetState(){
		let rowData = _.omit(this.state, 'editMode');

		_.forOwn(rowData, (value, key) => {
			rowData[key] = null;
		});

		this.setState(rowData);
	},

	// handle promote click - if not yet promoted, promote, else demote
	_promote(){
		let { dispatch, row } = this.props,
			promote = row.promoted ? 0 : 1,
			newRow = Object.assign({}, row, {promoted: promote});

		dispatch( saveChanges(newRow) );
	},

	render(){
		let { dates, pickACollege: p, row } = this.props,
			{ editMode, contract, goal, showModal, promoted } = this.state,
			rand = Math.floor((Math.random() * 1000000) + 1),
			tooltip = styles.tooltip,
			start_date = dates.today.dateForHidden,
			end_date = dates.today.dateForHidden,
			hidden_promoted = row.promoted;

		if( _.isNumber(promoted) ) hidden_promoted = promoted;

		if( dates.pickACollege_dates ){
			start_date = dates.pickACollege_dates.startDateForHidden;
			end_date = dates.pickACollege_dates.endDateForHidden;
		}

		if( row.promoted ) tooltip = Object.assign({}, tooltip, {color: '#fff'});

		return (
			<form ref={'_form'}>
				<div className={'row collapse tableRow'+(row.promoted ? ' promoted' : '')}>

					{/* arrow */}
					<div className="column small-12 medium-1">
						<div
							onClick={ this._promote }
							className="promote sprite-item" />

						<input type="hidden" name="promoted" value={hidden_promoted || 0} />
						<input type="hidden" name="start_date" value={ start_date } />
						<input type="hidden" name="end_date" value={ end_date } />
						<input type="hidden" name="priority_id" value={ row.id } />
					</div>

					{/* school */}
					<div className="column small-12 medium-2">
						<Tooltip
							tipStyling={ styles.tip }
							toolTipStyling={ tooltip }
							customText={row.name || 'N/A'}>
								<div style={styles.school}>Balance: <b>${row.balance || 'N/A'}</b></div>
						</Tooltip>
					</div>

					{/* contract - editable */}
					<div className="column small-12 medium-1">
						{ editMode ?
							<select
								id={ '_contract_'+row.college_id }
								name="contract"
								onChange={ this._editCol }
								value={ (contract || row.contract) || '' }>
									<option value="" disabled="disabled">{'Select one...'}</option>
									{p.contractTypes.map( (ct, i) => <option key={i+ct.name+'_'+rand} value={ct.id}>{ct.name}</option> )}
							</select>
							:
							(row.contract_name || 'N/A')
						}
					 </div>

					{/* price and aor - non editable */}
					 <div className="column small-12 medium-1">{row.aor || 'N/A'}</div>
					 <div className="column small-12 medium-1">{row.price && _.isNumber(+row.price) ? '$'+row.price : 'N/A'}</div>

					{/* goals - editable */}
					<div className="column small-12 medium-1">
						{ editMode ?
							<input
								id={ '_goal_'+row.college_id }
								name="goal"
								type="number"
								min={0}
								max={9999}
								step={5}
								value={ (goal || row.goal) || '' }
								onChange={ this._editCol }
								placeholder="Enter goal.." />
							: (row.goal || '0')
						}
					 </div>

					{/* rows views, picks, conversions, handshakes - non are editable */}
					 <div className="column small-12 medium-1">{_.isNumber(+row.views) ? row.views : 'N/A'}</div>
					 <div className="column small-12 medium-1">{_.isNumber(+row.picks) ? row.picks : 'N/A'}</div>
					 <div className="column small-12 medium-1">{_.isNumber(+row.conversion) ? row.conversion+'%' : 'N/A'}</div>
					 <div className="column small-12 medium-1">{_.isNumber(+row.handshakes) ? row.handshakes : 'N/A'}</div>

					{/* actions - sets cols in edit mode */}
					<div className="column small-12 medium-1 actions-col">
						{ p.save_pending ? <div><ReactSpinner config={spin} /></div> : null }
						{ !editMode && !p.save_pending ? <div id={'_edit_'+row.college_id} className="edit sprite-item" onClick={ () => this.setState({editMode: true}) } /> : null }
						{ editMode && !p.save_pending ? <div id={'_save_'+row.college_id} className="save sprite-item" onClick={ this._saveChanges } /> : null }
						{ editMode && !p.save_pending ? <div id={'_remove_'+row.college_id} className="remove sprite-item" onClick={ () => this.setState({showModal: true}) } /> : null }
					</div>

					{ showModal ?
						<CustomModal backgroundClose={ () => this.setState({showModal: false}) }>
							<div className="remove-modal">
								<div className="modal-question">Are you sure you want to remove this school?</div>
								<div className="modal-actions">
									<div onClick={ this._removeSchool }>Confirm</div>
									<div onClick={ () => this.setState({showModal: false}) }>Cancel</div>
								</div>
							</div>
						</CustomModal> : null }

				</div>
			</form>
		);
	}
});

const spin = {
	width: 6,
	length: 12,
	radius: 12,
	scale: 0.25,
	color: '#202020'
};

const styles = {
	tooltip: {
		fontSize: '14px',
		color: '#24b26b',
		border: 'none',
		margin: '0px',
		width: 'initial',
		height: 'initial',
		verticalAlign: 'initial',
		borderRadius: 'initial',
		textAlign: 'left',
		textOverflow: 'ellipsis',
	    whiteSpace: 'nowrap',
	    overflow: 'hidden',
	    display: 'block',
	},
	tip: {
		padding: '5px 10px',
	}
};

const mapStateToProps = (state, props) => {
	return {
		pickACollege: state.pickACollege,
		dates: state.dates,
	};
}

export default connect(mapStateToProps)(TableRow);
