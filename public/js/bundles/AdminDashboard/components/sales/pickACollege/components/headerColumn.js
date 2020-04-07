// headerColumn.js

import React from 'react'
import { connect } from 'react-redux'

import { sortCol } from './../../../../actions/pickACollegeActions'
import createReactClass from 'create-react-class'

const HeaderColumn = createReactClass({
	_sort(){
		let { dispatch, header } = this.props;

		//if column has a sortType, sort it
		if( header.sortType ) dispatch( sortCol(header) );
	},

	render(){
		let { header } = this.props;

		return (
			<div className={"column small-12 medium-"+header.colSize}>

				{ header.sortType ?
					<div>
						<div></div>
						<div></div>
					</div>
				: null }

				<div onClick={ this._sort }>
					{ header.name === 'arrow' ? <div className="promote sprite-item" /> : header.name }
				</div>
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		pickACollege: state.pickACollege,
	};
}

export default connect(mapStateToProps)(HeaderColumn);
