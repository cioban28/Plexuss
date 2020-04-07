// /SortBar/filterOption.js

import React from 'react'
import selectn from 'selectn'

export default React.createClass({
	getInitialState(){
		return {
			open: false,
		};
	},

	componentDidMount(){
		document.addEventListener('click', this._closeFilter);
	},

	componentWillUnmount(){
		document.removeEventListener('click', this._closeFilter);
	},

	_closeFilter(e){
		let target = $(e.target),
			unique = this.props.filter.title.split(' ').join('_');

		if( !target.hasClass('fo_'+unique) ) this.setState({open: false});
	},

	_filter(e){
		let { dispatch, filterAction, applied } = this.props;

		if( filterAction ){
			if( applied && _.isArray(applied) ) dispatch( filterAction(e.target.value, applied) ); // if list is passed, pass to action
			else dispatch( filterAction(e.target.value) ); // else just pass value
		}
	},

	_resetFilter(e){
		e.preventDefault();

		let { dispatch, filterAction, applied } = this.props;

		if( filterAction ){
			if( applied && _.isArray(applied) ) dispatch( filterAction(null, applied) ); // if list is passed, pass to action
			else dispatch( filterAction(null) ); // else just pass value
		}
	},

	render(){
		let { filter, applied } = this.props,
			{ open } = this.state,
			unique = 'fo_' + this.props.filter.title.split(' ').join('_'),
			is_applied = _.isArray(applied) ? applied.length > 0 : !!applied,
			applied_val = _.isArray(applied) ? (applied[0] || '') : (applied || '');

		return(
			<div className={"sortbar_filter "+unique+(open ? ' open' : '')}>
				<div onClick={ () => this.setState({open: !open}) } className={unique}>
					{ filter.title }
					<div className={"arrow "+unique} />
				</div>	

				<div className={'filter_dropdown '+unique+(open ? '' : ' hide') }>
					<select 
						value={ applied_val } 
						onChange={ this._filter } 
						className={ unique }>
							<option value="" disabled="disabled" className={unique}>Select one...</option>
							{ filter.options.map((f) => <option key={f.value || f.id} value={f.value || f.id} className={unique}>{f.name}</option>) }
					</select>

					{ (applied && _.isArray(applied) && applied.length > 0) && applied.map((fil) => <AppliedFilter 
																				key={fil} 
																				val={fil} 
																				uniqueClass={unique} 
																				{...this.props} />) }

					<div className={"text-right "+unique}>
						<button 
							className={"reset " + unique} 
							onClick={ this._resetFilter }
							disabled={ !is_applied }>Reset</button>
					</div>
				</div>
			</div>
		);
	}
});

const AppliedFilter = React.createClass({
	_getFilterObjFromFilterOptions(){
		let { val, filter } = this.props;
		return _.find(filter.options.slice(), {id: +val});
	},

	_removeAppliedFilter(){
		let { dispatch, filterAction, applied, val } = this.props;
		if( filterAction && applied ) dispatch( filterAction(val, applied) );
	},

	render(){
		let { val, uniqueClass } = this.props,
			applied_obj = this._getFilterObjFromFilterOptions();

		return (
			<div className={"applied-filter "+uniqueClass}>
				{ applied_obj.name || '' }
				<div className={"remove "+uniqueClass} onClick={ this._removeAppliedFilter }>&#10006;</div>
			</div>
		);
	}
});