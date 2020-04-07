// portal_list.js

import React from 'react'
import ActivePortalItem from './activePortalItem'
import DeactivatedPortalItem from './deactivatedPortalItem'
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		let _this = this, { portals, is_active } = this.props;

		return (
			<div>
				{
	    			portals.map(function(obj, i){
	    				if( is_active ) return <ActivePortalItem key={i} portal={obj} {..._this.props} />
	    				else return <DeactivatedPortalItem key={i} portal={obj} {..._this.props} />
	    			})
	    		}
    		</div>
		);
	}
});
