// programHeader.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import { PROGRAMS } from './../constants'

import { setProgramHeader } from './../../../../actions/internationalActions'

import './styles.scss'

const ProgramHeader = createReactClass({
	_selectedPrograms(){
		let { intl } = this.props;
		return _.filter(PROGRAMS, p => !!intl.program[p.id]);
	},

	render(){
		let { dispatch, intl, customStateObj, customDispatchMethod } = this.props,
			_obj = customStateObj || intl,
			_func = customDispatchMethod || setProgramHeader,
			selected_programs = this._selectedPrograms();

		return (
			<div>
				<div className="options">

					{ selected_programs.map((p, i) => <div key={ p.id }>
														 <div
															className={ (_obj.activeProgram === p.id || '') && 'active' }
															onClick={ e => dispatch( _func(p.id) ) }>
																{ p.name }
														</div>
														{ i+1 !== selected_programs.length && <div className="divider">|</div> }
													</div>) }

				</div>

				<br />
			</div>
		);
	}
})

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
	};
};

export default connect(mapStateToProps)(ProgramHeader);
