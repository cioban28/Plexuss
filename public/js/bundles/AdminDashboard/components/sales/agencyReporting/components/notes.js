// notes.js

import React from 'react'

import ReactSpinner from 'react-spinjs-fix'

import { connect } from 'react-redux'

import { bindActionCreators } from 'redux'

import styles from './styles.scss'

import moment from 'moment'

import CustomModal from './../../../../../StudentApp/components/common/CustomModal'

import Toaster from 'react-toastr'

import * as agencyReportingActions from '../../../../actions/agencyReportingActions.js'

class Notes extends React.Component {
	constructor(props) {
		super(props);

		this.state = { show_modal: false, notes: null };

		this._openModal = this._openModal.bind(this);
		this._closeModal = this._closeModal.bind(this);
		this._savePlexussNote = this._savePlexussNote.bind(this);
	}

	componentWillMount() {
		const { notes } = this.props;
		this.setState({ notes });
	}

	componentWillReceiveProps(newProps) {
		const { agencyReporting } = this.props,
			  { agencyReporting: newReporting } = newProps;

		if (agencyReporting.save_plexuss_note_pending != newReporting.save_plexuss_note_pending && newReporting.save_plexuss_note_pending) {
			this.setState({ show_modal: false });
		}

		if (this.state.new_note && agencyReporting.save_plexuss_note_pending != newReporting.save_plexuss_note_pending && newReporting.save_plexuss_note_pending === false) {
			this.setState({ show_modal: false, notes: this.state.new_note, new_notes: null });
		}
	}

	_openModal() {
		this.setState({ show_modal: true });
	}

	_closeModal() {
		this.setState({ show_modal: false });
	}

	_savePlexussNote() {
		const { agency_id, savePlexussNote } = this.props,
			note = this.input.value;

		this.setState({ new_note: note });

		savePlexussNote(agency_id, note);
	}

	render() {
		let show_modal = this.state.show_modal,
			notes = this.state.notes;

		if (!notes) {
			notes = <div className='edit-icon'> </div>
		}

		return (
			<div onClick={this._openModal}>
				<div className='notes' title={(typeof notes === 'string') ? notes : ''}>
					{ notes }
				</div>
				{ show_modal &&
					<CustomModal closeMe={this._closeModal}>
						<div className="modal agency-notes-modal">
							<h4>Notes</h4>
							<textarea ref={(input) => { this.input = input; }} rows="8" cols="50" defaultValue={(typeof notes === 'string') ? notes : ''} />
							<div onClick={() => { this._savePlexussNote(); }} className="notes-save-button">Save</div>
						</div>
					</CustomModal>
				}
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		agencyReporting: state.agencyReporting,
		dates: state.dates,
	};
}

const mapDispatchToProps = (dispatch) => {
	return bindActionCreators(agencyReportingActions, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(Notes);
