// /College_Application/SelectedItem.js

import React from 'react'

import { isEmpty } from 'lodash'

import { updateProfile } from './../../actions/Profile'

class SelectedItem extends React.Component {
	constructor(props) {
		super(props)
		this._getItem = this._getItem.bind(this)
		this._removeItem = this._removeItem.bind(this)
	}

    _getItem(){
        const { _profile, static_list, } = this.props,
        		id = typeof(this.props.id) === "number" ? this.props.id : this.props.id.id
        return _.find(_profile[static_list]) && _.find(_profile[static_list].slice(), {id});
    }

	_removeItem(){
		let { dispatch, _profile, name ,id } = this.props;
		let newList = _.pull(_profile[name].slice(), id); // remove this major from majors
		dispatch( updateProfile({[name]: newList}) );
	}

	componentWillMount(){
		let { _profile, id, static_list } = this.props;
		// if id is number, we don't have item details yet, other than the id, so use id to get item dets from countries_list
		if( _profile[static_list] ) this.setState({item:_.find(_profile[static_list].slice(), {id})})
	}

	componentWillReceiveProps(np){
		let { _profile, id, init_name, static_list } = this.props;
	}

	render(){
        let item = this._getItem() 

        if (isEmpty(item)) return null;
		return (
			<div className="selected-country" onClick={ () => this._removeItem() }>
				<div />
				{ item.name }
			</div>
		);
	}
}

export default SelectedItem;