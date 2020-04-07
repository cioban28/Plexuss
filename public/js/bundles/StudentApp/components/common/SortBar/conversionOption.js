// /SortBar/filterOption.js

import React from 'react'
import selectn from 'selectn'

export default class ConversionOption extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			open: false,
		}
		this._closeFilter = this._closeFilter.bind(this)
	}

	_closeFilter(e){
		let target = $(e.target),
			unique = this.props.conversion.title.split(' ').join('_');

		if( !target.hasClass('fo_'+unique) ) this.setState({open: false});
	}

	componentDidMount(){
		document.addEventListener('click', this._closeFilter);
	}

	componentWillUnmount(){
		document.removeEventListener('click', this._closeFilter);
	}

	render(){
		let { conversion, convertables, current_conversion_obj } = this.props,
			{ open } = this.state,
			unique = 'fo_' + conversion.title.split(' ').join('_');

		return(
			<div className={"sortbar_filter "+unique+(open ? ' open' : '')}>

				<div onClick={ () => this.setState({open: !open}) } className={unique}>
					{ current_conversion_obj.name || conversion.title }
					<div className={"arrow "+unique} />
				</div>

				<div className={'conversion_dropdown '+unique+(open ? '' : ' hide') }>

					{ (convertables && _.isArray(convertables) && convertables.length > 0) && 
							convertables.map((con) => <ConversionItem 
															key={ con.name } 
															item={ con } 
															{...this.props} />) }

				</div>
			</div>
		);
	}
}

class ConversionItem extends React.Component{
	constructor(props) {
		super(props)
		this._convert = this._convert.bind(this)
	}

	_convert(){
		let { dispatch, convertAction, item } = this.props;
		dispatch( convertAction(item) );
	}

	render(){
		let { item } = this.props;

		return (
			<div className="conversion-item" onClick={ this._convert }>
				{ item.name || '' }
			</div>
		);
	}
}