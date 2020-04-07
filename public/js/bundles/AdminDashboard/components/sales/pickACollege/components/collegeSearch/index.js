// index.js

import React from 'react'
import { connect } from 'react-redux'

import { searchForCollege, addSchoolToPriorityList, openSearch } from './../../../../../actions/pickACollegeActions'
import createReactClass from 'create-react-class'

const SearchForCollege = createReactClass({
	render(){
		let { dispatch, pickACollege: p } = this.props;

		return ( p.openSearch ) ?
			<div className="search-college-container">
				<div className="search-wrapper">
					<div className="input-wrapper">
						<input
							id="_searchSchools"
							name="search"
							type="text"
							defaultValue=""
							onChange={ (e) => dispatch( searchForCollege(e.target.value) ) }
							placeholder="Search a college" />
					</div>

					{ p.pending ? <div className="text-center pending">Loading...</div> : null }

					{
						p.searchResults && p.searchResults.length > 0 ?
						<div className="results stylish-scrollbar">
							{ p.searchResults.map( (res, i) => <Result key={i+res.school_name} {...this.props} school={res} /> ) }
						</div>
						: null
					}
				</div>
			</div>
			:
			null
	}
});

const Result = createReactClass({
	getInitialState(){
		return {
			url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/',
		};
	},

	render(){
		let { dispatch, school } = this.props,
			{ url } = this.state,
			styles = {backgroundImage: 'url('+url+school.logo_url+')'};

		return (
			<div className="result" onClick={ () => dispatch( addSchoolToPriorityList(school) ) }>
				<div><div style={styles} /></div>
				<div>
					<div className="name">{ school.school_name }</div>
					<div className="location">{ school.city + ', ' + school.state }</div>
				</div>
				<div className="aor">AOR</div>
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		pickACollege: state.pickACollege,
	};
}

export default connect(mapStateToProps)(SearchForCollege);
