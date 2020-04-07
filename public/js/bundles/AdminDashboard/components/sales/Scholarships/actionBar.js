import React from 'react';
import {connect} from 'react-redux';
import TinyMCE from 'react-tinymce';

import {searchScholarships} from './../../../actions/scholarshipsActions';

import AddModal from './addModal';
import SearchBar from './searchBar';


class ActionBar extends React.Component{
	constructor(props){
		super(props);

		this.state = {
			viewAddModal: false
		}

		this._search = this._search.bind(this);
	}
	_search(input){
		let {dispatch} = this.props;

		dispatch(searchScholarships(input));
	}

	render(){
		let {viewAddModal} = this.state;
		let {scholarships} = this.props;

		return (
			<div className="actionbar">
				<div className="title">Plexuss Scholarship Backend</div>
			
				<div className="clearfix">
					<div className="add-sch-btn" onClick={() => this.setState({viewAddModal: !viewAddModal})}>
						<span>+</span>ADD NEW SCHOLARSHIP
					</div>
					<div className="sch-search-cont">
						<SearchBar search={this._search} />	
					</div>
				</div>

				{viewAddModal &&
					<AddModal close={() => this.setState({viewAddModal: false})} /> }
			</div>
		);
	}

} 
const mapStateToProps = (state, props) => {
	return{
		scholarships: state.scholarships
	}
}

export default connect(mapStateToProps)(ActionBar);