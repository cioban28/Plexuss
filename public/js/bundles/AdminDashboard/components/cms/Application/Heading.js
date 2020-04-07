// /Application/Heading.js

import React from 'react'
import createReactClass from 'create-react-class'

import CustomModal from './../../../../utilities/customModal'

import './styles.scss'

export default createReactClass({
	getInitialState(){
		return {
			open: false,
		};
	},

	render(){
		let { route, descrip, title } = this.props,
			{ open } = this.state;

		return (
			<div className="heading">
				<div className="title">{ title || route.name }</div>
				{ (route && !route.atypical) && <div className="preview" onClick={ e => this.setState({open: true}) }>Preview Application</div> }
				{ descrip && <div className="descrip">{ descrip }</div> }

				{ open && <CustomModal>
								<div className={"preview-img "+route.id}>
									<div className="close"><span onClick={ e => this.setState({open: false}) }>&times;</span></div>
									<img src={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/preview_of_app_section_'+route.id+'.png'} />
								</div>
						</CustomModal> }
			</div>
		);
	}
});
