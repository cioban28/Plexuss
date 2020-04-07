import React from 'react';

export default class SearchBar extends React.Component{
	
	constructor(props){
		super(props);

		this.state = {
			timeout: null
		}
	}
	// _search(e){
	// 	let val = e.target.value;
	// 	let {timeout} = this.state;

	// 	clearTimeout(timeout);

	// 	if(val === '') return;

	// 	let tm = setTimeout(() => {
	// 		// clearTimeout(timeout);
	// 		console.log('AJAX val: ' + val);
	// 	}, 800);

	// 	this.setState({ timeout: tm});

	// }
	_enter(e){
		let {search} = this.props;

		if (e.keyCode === 13) {
	       search(this.input.value);
	    }
	}
	render(){
		let {search} = this.props;

		return(
			<div className="_searchBar">
				<input name="search" placeholder="Search Scholarships..." ref={(input) => this.input=input } onKeyUp={(e) => this._enter(e)}/>
				<div className="mag" onClick={() => search(this.input.value)}>
					<div className="mag-glass"></div>
					<div className="mag-handle"></div>
				</div>
			</div>
		);
	}
}